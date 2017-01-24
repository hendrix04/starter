<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
date_default_timezone_set('America/New_York');
require '../vendor/autoload.php';
require '../config/settings.inc';
require '../config/routes.inc';
require '../lib/middleware.php';

spl_autoload_register(function ($classname) {
	if (substr($classname, 0, 4) === "Lib\\") {
		require("../lib/" . substr($classname, 4) . ".php");
	}
	else {
		require ("../classes/" . $classname . ".php");
	}
});

$app = new \Slim\App(["settings" => $slimconfig]);

$container = $app->getContainer();

$container['db'] = function ($c) {
	$dbsettings = $c['settings']['db'];
	$connection = new \Pixie\Connection('mysql', $dbsettings);
	$qb = new \Pixie\QueryBuilder\QueryBuilderHandler($connection);
	return $qb;
};

$container['csrf'] = function ($c) {
    return new \Slim\Csrf\Guard;
};

$container['view'] = function ($c) {
	$settings = $c['settings']['twig'];

	$view = new \Slim\Views\Twig($settings['templatepath'], [
		'cache' => $settings['cache']
	]);
    
	// Instantiate and add Slim specific extension
	$basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
	$view->addExtension(new Slim\Views\TwigExtension($c['router'], $basePath));
	$view->addExtension(new Lib\TwigCSRF($c['csrf']));

	return $view;
};

$app->add($container->get('csrf'));

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

