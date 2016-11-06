<?php
class Login {
	protected $ci;
	protected $db;

	public function __construct(Slim\Container $ci) {
		$this->ci = $ci;
		$this->db = $ci->db;
	}

	public function Post($request, $response, $args) {
		$data = $request->getParsedBody();

		$row = $this->db->table('user')->where('username', '=', $data['username'])->first();

		if (password_verify($data['password'], $row->password)) {
			$session = new \SlimSession\Helper;

			$session->userid = $row->id;
			$session->username = $row->username;
			$session->permission = 'user';
			return $response->withRedirect('/');
		}
		else {
			return $response->withRedirect('/?error=1');
		}

		return $response;
	}
}
?>
