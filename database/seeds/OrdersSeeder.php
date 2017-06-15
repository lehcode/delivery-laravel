<?php

use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Trip;
use App\Models\Order;
use App\Models\City;
use Jenssegers\Date\Date;
use App\Models\Recipient;
use App\Models\Shipment;
use App\Models\ShipmentCategory;

/**
 * Class OrdersSeeder
 */
class OrdersSeeder extends Seeder
{
	/**
	 * Default date format
	 */
	const DATE_FORMAT = 'Y-m-d H:i:s';

	const STATUSES = ['created', 'accepted', 'picked', 'delivered', 'completed'];

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{

		$trips = Trip::all();

		User\Customer::all()->each(function ($customer) use ($trips) {

			for ($i = 0; $i < rand(3, 9); $i++) {
				$trip = $trips->random()->with(['fromCity', 'destinationCity'])->first();
				$recipient = factory(Recipient::class)->create();
				$shipment = factory(Shipment::class)->create([
					'category_id' => ShipmentCategory::all()->random()->id
				]);

				do {
					$status = self::STATUSES[array_rand(self::STATUSES)];
				} while ($status === 'completed');

				$data = [
					'status' => $status,
					'customer_id' => $customer->id,
					'trip_id' => $trip->id,
					'recipient_id' => $recipient->id,
					'shipment_id' => $shipment->id,
					'payment_id' => null,
					'geo_start' => $this->makePoint($this->getGeoData($trip->fromCity()->first())),
					'geo_end' => $this->makePoint($this->getGeoData($trip->destinationCity()->first())),
				];

				$order = factory(Order::class)->create($data);

				if (!is_null($order->validationErrors) && !empty($order->validationErrors)) {
					foreach ($order->validationErrors['messages'] as $messages) {
						foreach ($messages as $column => $errors) {
							foreach ($errors as $error) {
								throw new \Exception($column . ': ' . $error, 1);
							}
						}
					}
				}
			}
		});
	}

	/**
	 * @param City $city
	 *
	 * @return mixed
	 */
	private function getGeoData($city)
	{

		$srcCities = collect(CitiesSeeder::CITIES);
		$c = $srcCities->where('name', '=', $city->name)->first();
		$geo = $c['geo'];

		return $geo;
	}

	/**
	 * @param array $geo
	 *
	 * @return array|null
	 */
	private function makePoint(array $geo)
	{

		$result = null;

		foreach ($geo as $k => $p) {
			$geo[] = explode(', ', $p);
			unset($geo[$k]);
		}

		foreach ($geo as $k => $p) {
			foreach ($p as $kk => $v) {
				$geo[$k][$kk] = floatval($v);
			}
		}

		$m = 100000;

		$result[] = (int)rand($geo[4][1] * $m, $geo[5][1] * $m);
		$result[] = (int)rand($geo[4][0] * $m, $geo[7][0] * $m);

		return $result;

	}
}
