<?php
class DATABASE_CONFIG {
	var $default = array(
		'driver'		=> 'mysqli',
		'persistent'	=> false,
		'host'			=> 'dbm1-dev',
		'login'			=> 'dev_usr',
		'password'		=> 'traveler',
		'database'		=> 'toolbox',
		'prefix'		=> '',
	);

	var $luxurylink = array(
	    'driver'		=> 'mysqli',
		'persistent'	=> false,
		'host'			=> 'dbm1-dev',
		'login'			=> 'dev_usr',
		'password'		=> 'traveler',
		'database'		=> 'luxurylink',
		'prefix'		=> '',
	);

	var $family = array(
		'driver'		=> 'mysqli',
		'persistent'	=> false,
		'host'			=> 'dbm1-dev',
		'login'			=> 'dev_usr',
		'password'		=> 'traveler',
		'database'		=> 'family',
		'prefix'		=> '',
	);

	var $reporting = array(
		'driver'		=> 'mysqli',
		'persistent'	=> false,
		'host'			=> 'dbm1-dev',
		'login'			=> 'dev_usr',
		'password'		=> 'traveler',
		'database'		=> 'reporting',
		'prefix'		=> '',
	);

	var $vacationist = array(
		'driver'		=> 'mysqli',
		'persistent'	=> false,
		'host'			=> 'dbm1-dev',
		'login'			=> 'dev_usr',
		'password'		=> 'traveler',
		'database'		=> 'vacationist',
		'prefix'		=> ''
	);

	var $business_db2 = array(
		'driver'		=> 'mysqli',
		'persistent'	=> false,
		'host'			=> 'dbm1-dev',
		'login'			=> 'dev_usr_ro',
		'password'		=> 'traveler',
		'database'		=> 'toolbox',
		'prefix'		=> '',
	);

	var $default_ro = array(
		'driver'		=> 'mysqli',
		'persistent'	=> false,
		'host'			=> 'dbm1-dev',
		'login'			=> 'dev_usr_ro',
		'password'		=> 'traveler',
		'database'		=> 'toolbox',
		'prefix'		=> '',
	);
}