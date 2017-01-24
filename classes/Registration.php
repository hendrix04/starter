<?php

include('../config/password.conf');

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
		return $this->view->render($response, 'registration/main.html', $args);
	}

	public function Post($request, $response, $args) {
		$inputdata = $request->getParsedBody();
		$savedata = [];
		$errormessage = [];

		$v = new Valitron\Validator($inputdata, [
			'username',
			'email',
			'password',
			'confirmpassword',
			'g-recaptcha-response',
		]);

		Valitron\Validator::addRule('uniqueusername', [$this, 'UniqueUsername'], 'must be unique.');
		Valitron\Validator::addRule('recaptcha', [$this, 'ValidateCaptcha'], 'is incorrect.');

		$validationrules = [
			'required' => [
				['username'],
				['email'],
				['password'],
				['confirmpassword'],
				['g-recaptcha-response'],
			],
			'equals' => [
				['password', 'confirmpassword'],
			],
			'email' => [
				['email'],
			],
			'uniqueusername' => [
				['username'],
			],
			'recaptcha' => [
				['g-recaptcha-response'],
			],
		];

		# Add the rules
		$v->rules($validationrules);

		# Then the labels
		$v->labels([
			'g-recaptcha-response' => 'reCaptcha',
			'confirmpassword' => 'Confirm Password',
		]);

		if($v->validate()) {

			# Save the data that we don't need to manipulate
			# Just so that I remember, this is a poor mans
			# hash slice.
			$savedata = array_intersect_key($inputdata, array_flip([
				'username',
				'email',
			]));

			$savedata['password'] = password_hash($inputdata['password'], PASSWORD_DEFAULT);

			$permissioninfo = $this->db->table('permission')->find('user', 'level');

			# Set the default permission level for this person which is user...
			$savedata['permissionid'] = $permissioninfo->id;

			$insertId = $this->db->table('user')->insert($savedata);
		}
		else {
			foreach ($v->errors() as $fielderror) {
				foreach ($fielderror as $individualerror) {
					array_push($errormessage, $individualerror);
				}
			}
		}

		if (count($errormessage) > 0) {
			$args['errors'] = $errormessage;
			return $this->Get($request, $response, $args);
		}
		else {

			return $response->withRedirect('/');
		}
		
	}

	public function ValidateCaptcha($field, $value, $params, $fields) {
		global $recaptchasecret;
		try {
			$url = 'https://www.google.com/recaptcha/api/siteverify';
			$data = [
				'secret'   => $recaptchasecret,
				'response' => $value,
				'remoteip' => $_SERVER['REMOTE_ADDR']
			];

			$options = [
				'http' => [
					'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					'method'  => 'POST',
					'content' => http_build_query($data) 
				]
			];

			$context  = stream_context_create($options);
			$result = file_get_contents($url, false, $context);
			return (json_decode($result)->success) ? true : false;
		}
		catch (Exception $e) {
			return null;
		}
	}

	public function UniqueUsername($field, $value, $params, $fields) {
		if ($this->db->table('user')->find($value, 'username') !== null) {
			return false;
		}

		return true;
	}
}
?>
