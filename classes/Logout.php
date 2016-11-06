<?php
class Logout {
	protected $ci;
	protected $db;

	public function __construct(Slim\Container $ci) {
		$this->ci = $ci;
		$this->db = $ci->db;
	}

	public function Get($request, $response, $args) {

		$session = new \SlimSession\Helper;
		$session::destroy();

		return $response->withRedirect('/');
	}
}
?>
