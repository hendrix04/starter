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

		$row = $this->db->table(
				'user'
			)->select([
				'user.username',
				'user.password',
				'permission.level'
			])->join(
				'permission',
				'permission.id',
				'=',
				'user.permissionid'
			)->where(
				'username',
				'=',
				$data['username']
			)->first();

		if (password_verify($data['password'], $row->password)) {
			$session = new \SlimSession\Helper;

			$session->userid = $row->id;
			$session->username = $row->username;
			# yes, level is a terrible name here but get over it.
			$session->permission = $row->level;
			return $response->withRedirect('/');
		}
		else {
			return $response->withRedirect('/?error=1');
		}

		return $response;
	}
}
?>
