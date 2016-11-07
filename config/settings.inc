<?php

# You will need a file in the same directory as this file called
# password.conf. This file should contain the 3 database setting
# variables below. This allows us to keep passwords out of git.
include('password.conf');

$slimconfig = array(
	'db' => array(
		'driver' => "mysql",
		'host' => "localhost",
		'username' => $databaseusername,
		'password' => $databasepassword,
		'database' => $databasename,
		'charset' => "utf8",
	),
	'twig' => array(
		'templatepath' => "../templates",
		# This can either be false for "don't cache"
		# or it can be a folder path to a caching dir...
		'cache' => false,
	),
	'displayErrorDetails' => true,
	'addContentLengthHeader' => false,
);

?>
