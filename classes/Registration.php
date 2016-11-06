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
#			 'name' => $args['name']
#		]);
#		$row = print_r($this->db->table('user')->find(1), true);
#		$response->getBody()->write('<pre>' . $row);

#		return $response;
	}

	public function Post($request, $response, $args) {
		$data = $request->getParsedBody();

		$data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

		$insertId = $this->db->table('user')->insert($data);

		return $response->withRedirect('/'); 
	}
}
?>
