<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
date_default_timezone_set('America/New_York');
require '../vendor/autoload.php';
require '../config/settings.inc';
require '../config/routes.inc';
require '../config/middleware.php';

spl_autoload_register(function ($classname) {
	require ("../classes/" . $classname . ".php");
});

$app = new \Slim\App(["settings" => $slimconfig]);

$container = $app->getContainer();

$container['db'] = function ($c) {
	$dbsettings = $c['settings']['db'];
	$connection = new \Pixie\Connection('mysql', $dbsettings);
	$qb = new \Pixie\QueryBuilder\QueryBuilderHandler($connection);
	return $qb;
};

$container['view'] = function ($c) {
	$settings = $c['settings']['twig'];

	$view = new \Slim\Views\Twig($settings['templatepath'], [
		'cache' => $settings['cache']
	]);
    
	// Instantiate and add Slim specific extension
	$basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
	$view->addExtension(new Slim\Views\TwigExtension($c['router'], $basePath));

	return $view;
};

$middleware = new Middleware($container['view']);

# Register all of the routes
foreach ($routes as $route => $info) {
	foreach ($info['methods'] as $method => $permission) {
		$function = '\\' . $info['class'] . ':' . ucfirst($method);

		if ($permission != 'all') {
			$middleware = array(new Middleware($container['view']), ucfirst($permission));
			$app->$method($route, $function)->add($middleware);
		}
		else {
			$app->$method($route, $function);
		}
	}
}

$authmiddleware = array(new Middleware($container['view']), "Authenticated");
$app->add($authmiddleware);

$app->add(new \Slim\Middleware\Session([
	'name' => 'theforge',
	'autorefresh' => true,
	'lifetime' => '1 hour'
]));

$app->run();

?>

