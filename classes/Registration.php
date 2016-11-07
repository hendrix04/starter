<?php

class Registration {
	protected $ci;
	protected $db;
	protected $view;

	public function __construct(Slim\Container $ci) {
		$this->ci = $ci;
		$this->db = $ci->db;
		$this->view = $ci->view;
	}

	public function Get($request, $response, $args) {
		return $this->view->render($response, 'registration/main.html');#, [
	}

	public function Post($request, $response, $args) {
		$data = $request->getParsedBody();

		$data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

		$permissioninfo = $this->db->table('permission')->find('user', 'level');

		# Set the default permission level for this person which is user...
		$data['permissionid'] = $permissioninfo->id;

		$insertId = $this->db->table('user')->insert($data);

		return $response->withRedirect('/'); 
	}
}
?>
