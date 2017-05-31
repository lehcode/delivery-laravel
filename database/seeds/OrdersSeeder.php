<?php

use Illuminate\Database\Seeder;

use Faker\Factory as Faker;
use App\Models\User;
use App\Models\Trip;
use Jenssegers\Date\Date;
use Webpatser\Uuid\Uuid;
use App\Models\Recipient;
use App\Models\Shipment;
use App\Models\ShipmentCategory;
use App\Models\Route;

class OrdersSeeder extends Seeder
{
	const DATE_FORMAT = 'Y-m_d H:i:s';
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$faker = Faker::create('en_GB');

		$trips = Trip::all();
		$customers = User::all()->filter(function ($item) {
			if ($item->roles()->first()->name == User::ROLE_CUSTOMER) {
				return $item;
			}
		});

		$customers->each(function($u) use ($faker, $trips){
			
			$user = $u->customer()->first();
			$now = Date::now();

			//$category = ShipmentCategory::all()->random();
			$trip = $trips->random();

			$dptrDate = $now->addDays(rand(3, 10));
			$dlvrDate = Date::createFromFormat(self::DATE_FORMAT, $dptrDate->format(self::DATE_FORMAT));
			$dlvrDate->addDays(rand(1, 3));

			$recipient = factory(Recipient::class)->create();
			$shipment = factory(Shipment::class)->create([
				'category_id' => ShipmentCategory::all()->random()->id
			]);
			$route = factory(Route::class)->create();

			$user->order()->create([
				'id' => Uuid::generate(4),
				'departure_date' =>$dptrDate,
				'expected_delivery_date' => $dlvrDate,
				'customer_id' => $u->id,
				'trip_id' => $trip->id,
				'recipient_id' => $recipient->id,
				'shipment_id' => $shipment->id,
				'route_id' => $route->id,
				'geo_start' => null,
				'geo_end' => null,
			]);
			
		});
	}
}
