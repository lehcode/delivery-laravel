<?php

/**
 * Created by Antony Repin
 * Date: 28.04.2017
 * Time: 16:12
 */

use Illuminate\Database\Seeder;
use App\Models\Language;

class LanguagesSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$langs = [
			['code' => 'ar', 'flag' => 'sa', 'name' => 'Arabic'],
			['code' => 'en', 'flag' => 'gb', 'name' => 'English'],
		];

		foreach ($langs as $lang) {
			Language::updateOrCreate(['locale' => $lang['code']], [
				'name' => $lang['name'],
				'locale' => $lang['code'],
				'flag' => $lang['flag']
			]);
		}
	}
}
