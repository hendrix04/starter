<?php

class Account {
	protected $ci;
	protected $db;
	protected $view;

	public function __construct(Slim\Container $ci) {
		$this->ci = $ci;
		$this->db = $ci->db;
		$this->view = $ci->view;
	}

	public function Get($request, $response, $args) {

		$row = $this->db->table('user')->where('username', '=', $request->getAttribute('username'))->first();

		$tempvars = [
			'username' => $row->username,
			'email' => $row->email,
			'permission' => $request->getAttribute('permission'),
		];

		if (isset($args['error'])) {
			$tempvars['error'] = $args['error'];
		}

		return $this->view->render($response, 'account/main.html', $tempvars);
	}

	public function Post($request, $response, $args) {
		$data = $request->getParsedBody();

		$row = $this->db->table('user')->where('username', '=', $request->getAttribute('username'))->first();

		if (password_verify($data['passwordcheck'], $row->password)) {
			$updatedata = [];
			if (isset($data['email'])) {
				$updatedata['email'] = $data['email'];
			}

			if (strlen($data['updatepassword']) > 3) {
				if ($data['updatepassword'] == $data['confirmpassword']) {
					$updatedata['password'] = password_hash($data['updatepassword'], PASSWORD_DEFAULT);
				}
				else {
					$args['error'] = "Passwords don't match.";
				}
			}

			$this->db->table('user')->where('username', '=',  $request->getAttribute('username'))->update($updatedata);
		}
		else {
			$args['error'] = 'Incorrect password entered';
		}

		return $this->Get($request, $response, $args);
	}
}
?>
