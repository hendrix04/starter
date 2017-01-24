<?php namespace Lib;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


class Middleware {

	protected $view;

	public function __construct($view) {
		$this->view = $view;
	}

	public function Authenticated($request, $response, $next) {

		$session = new \SlimSession\Helper;

		if ($session->get('permission', 'not logged in') != 'not logged in') {
			$request = $request->withAttribute('permission', $session->permission);
			$request = $request->withAttribute('username', $session->username);
			$request = $request->withAttribute('userid', $session->userid);
		}

		return $next($request, $response);
	}

	public function User($request, $response, $next) {

		switch ($request->getAttribute('permission')) {
			case "user":
			case "premium":
			case "admin":
				return $next($request, $response);
				break;
			default:
				return $this->view->render($response, 'error/notallowed.html');
		}
	}

	public function Premium($request, $response, $next) {

		switch ($request->getAttribute('permission')) {
			case "premium":
			case "admin":
				return $next($request, $response);
				break;
			default:
				return $this->view->render($response, 'error/notallowed.html');
		}
	}

	public function Admin($request, $response, $next) {

		switch ($request->getAttribute('permission')) {
			case "admin":
				return $next($request, $response);
				break;
			default:
				return $this->view->render($response, 'error/notallowed.html');
		}
	}

}
?>
