<?php

return [

	'' => [
		'controller' => 'main',
		'action' => 'index',
	],

	'account/login' => [
		'controller' => 'account',
		'action' => 'login',
	],

	'account/register' => [
		'controller' => 'account',
		'action' => 'register',
	],
    'account/confirm/{token:\w+}' => [
		'controller' => 'account',
		'action' => 'confirm',
	],
    'account/recovery' => [
        'controller' => 'account',
        'action' => 'recovery',
    ],
    'account/profile' => [
        'controller' => 'account',
        'action' => 'profile',
    ],
    'account/logout' => [
        'controller' => 'account',
        'action' => 'logout',
    ],

];