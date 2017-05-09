<?php

return [
	'role_structure' => [
		'root' => [
			'users' => 'c,ra,ua,d,ud',
			'acl' => 'c,ra,ua,d,ud',
			'drivers' => 'c,ra,ua,d,ud',
			'customers' => 'c,ra,ua,d,ud',
			'recipients' => 'c,ra,ua,d,ud',
			'trips' => 'c,ra,ua,d,ud',
			'shipments' => 'c,ra,ua,d,ud',
			'routes' => 'c,ra,ua,d,ud',
			'orders' => 'c,ra,ua,d,ud',
			'payments' => 'c,ra,ua,d,ud',
			'payment_types' => 'c,ra,ua,d,ud',
			'cities' => 'c,ra,ua,d,ud',
		],
		'admin' => [
			'drivers' => 'c,ra,ua,d',
			'customers' => 'c,ra,ua,d',
			'recipients' => 'c,ra,ua,d',
			'trips' => 'c,ra,ua,d',
			'shipments' => 'c,ra,ua,d',
			'routes' => 'c,ra,ua,d',
			'orders' => 'c,ra,ua,d',
			'payments' => 'c,ra,ua,d',
			'payment_types' => 'c,ra,ua,d',
			'cities' => 'c,ra,ua,d',
		],
		'customer' => [
			'drivers' => 'ra',
			'customers' => 'ro,uo',
			'recipients' => 'c,ro,uo',
			'trips' => 'ro',
			'shipments' => 'c,ro',
			'orders' => 'c,ro,uo',
			'payments' => 'c,ro',
			'payment_types' => 'ra',
			'cities' => 'ra',
		],
		'driver' => [
			'drivers' => 'ro,uo',
			'customers' => 'ra',
			'trips' => 'c,ro,d',
			'shipments' => 'ro,uo',
			'orders' => 'ro,uo',
			'payments' => 'ro',
			'payment_types' => 'ra',
			'cities' => 'ra',
		],
		'recipient' => [
			'customers' => 'ro',
			'trips' => 'ro',
			'shipments' => 'ro',
			'orders' => 'ro',
			'cities' => 'ra',
		],
	],
	'permission_structure' => [
//        'api_user' => [
//            'profile' => 'c,ro,uo',
//        ],
	],
	'permissions_map' => [
		'c' => 'create',
		'ra' => 'read-all',
		'ro' => 'read-own',
		'ua' => 'update-all',
		'uo' => 'update-own',
		'd' => 'delete',
		'ud' => 'undelete',
	]
];
