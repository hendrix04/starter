<?php

/*
The format here is important.
The keys point to specific files...
`projectname`/class/CLASSVARIABLE/method.php

$routes = array(
	'routepath' => array(
		'class' => 
		'methods' => array(
			'get' => 'user',
			'post' => 'all',
			'put' => 'premium',
		),
	),
);

- routepath can take anything that slim framework takes

- class is put into CLASSVARIABLE above which will load the class
  for that route. This should be all lowercase

- methods is an array that has what standard http method is available
  for a given route and maps to what permission level can access
  it. Right now I only invision 2 permission levels which are all
  and user. All would mean anyone can hit a route unauthenticated
  where as user must be logged in to access it. I could invision a
  future where there is a 'premium' level that would block off some
  features for non paid members.
*/


$routes = [
	'/' => [
		'class' => 'Landing',
		'methods' => [
			'get' => 'all',
		],
	],
	'/login' => [
		'class' => 'Login',
		'methods' => [
			'post' => 'all',
		],
	],
	'/logout' => [
		'class' => 'Logout',
		'methods' => [
			'get' => 'all',
		],
	],
	'/registration' => [
		'class' => 'Registration',
		'methods' => [
			'get' => 'all',
			'post' => 'all',
		],
	],
	'/account' => [
		'class' => 'Account',
		'methods' => [
			'get' => 'user',
			'post' => 'user',
		],
	],
];

?>
