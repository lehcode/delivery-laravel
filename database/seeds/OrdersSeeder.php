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
use App\Models\ShipmentSize;
use Faker\Factory as Faker;
use Webpatser\Uuid\Uuid;

/**
 * Class OrdersSeeder
 */
class OrdersSeeder extends Seeder
{
	/**
	 * Default date format
	 */
	const DATE_FORMAT = 'Y-m-d H:i:s';

	const STATUSES = ['created', 'accepted', 'picked', 'delivered', 'completed', 'cancelled'];

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$this->command->info('Generating predefined Orders');
		$trips = Trip::all();
		$faker = Faker::create('en_GB');

		User\Customer::with('currentCity')->get()
			->each(function ($customer) use ($trips, $faker) {

				for ($i = 0; $i < rand(3, 9); $i++) {
					$trip = $trips->random()->with(['fromCity', 'destinationCity'])->first();

					$recipient = factory(Recipient::class)->create();

					if (!$recipient->isValid()) {
						$errors = $recipient->getErrors()->messages();
						foreach ($errors as $req => $error) {
							foreach ($error as $text) {
								throw new \Exception($text, 1);
							}
						}
					}

					$shipmentData = [
						'size_id' => ShipmentSize::all()->random()->id,
						'category_id' => ShipmentCategory::all()->random()->id,
					];

					$shipment = Shipment::create($shipmentData);

					if (!$shipment->isValid()) {
						$errors = $shipment->getErrors()->messages();
						foreach ($errors as $req => $error) {
							foreach ($error as $text) {
								throw new \Exception($text, 1);
							}
						}
					}

					do {
						$status = self::STATUSES[array_rand(self::STATUSES)];
					} while ($status === Order::STATUS_COMPLETED || $status === Order::STATUS_CANCELLED);

					$data = [
						'status' => $status,
						'customer_id' => $customer->id,
						'trip_id' => rand(1, 5) == 5 ? $trip->id : null,
						'recipient_id' => $recipient->id->string,
						'shipment_id' => $shipment->id,
						'payment_id' => null,
						'geo_start' => $this->makePoint($this->getGeoData($trip->fromCity()->first())),
						'geo_end' => $this->makePoint($this->getGeoData($trip->destinationCity()->first())),
						'price' => $faker->randomFloat(2, 49, 1999),
					];

					$order = factory(Order::class)->create($data);

					if (!$order->isValid()) {
						$errors = $order->getErrors()->messages();
						foreach ($errors as $req => $error) {
							foreach ($error as $text) {
								throw new \Exception($text, 1);
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
		//$result[] = (int)rand($geo[4][1] * $m, $geo[5][1] * $m);
		//$result[] = (int)rand($geo[4][0] * $m, $geo[7][0] * $m);

		$result[] = (float)rand($geo[4][1] * $m, $geo[5][1] * $m)/$m;
		$result[] = (float)rand($geo[4][0] * $m, $geo[7][0] * $m)/$m;

		return $result;

	}
}
