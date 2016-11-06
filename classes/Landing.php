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

		if ($permission == 'user' || $permission == 'premium') {
			return $this->LoggedIn($request, $response, $args);
		}

		return $this->view->render($response, 'landing/main.html');
	}

	private function LoggedIn($request, $response, $args) {

		$tempvars = [
			'permission' => $request->getAttribute('permission'),
		];

		return $this->view->render($response, 'landing/authenticated.html', $tempvars);

	}
}
?>
