<?php

class Landing {
	protected $ci;
	protected $db;
	protected $view;

	public function __construct(Slim\Container $ci) {
		$this->ci = $ci;
		$this->db = $ci->db;
		$this->view = $ci->view;
	}

	public function Get($request, $response, $args) {

		$permission = $request->getAttribute('permission');

		switch ($permission) {
			case "user":
			case "premium":
			case "admin":
				return $this->LoggedIn($request, $response, $args);
				break;
			default:
				return $this->view->render($response, 'landing/main.html');
				break;	
		}
	}

	private function LoggedIn($request, $response, $args) {

		$tempvars = [
			'permission' => $request->getAttribute('permission'),
		];

		return $this->view->render($response, 'landing/authenticated.html', $tempvars);

	}
}
?>
