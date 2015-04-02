<?php
/**
 * Created by PhpStorm.
 * User: akis
 * Date: 01/04/15
 * Time: 14:37
 */

namespace Slim\Extras\Middleware
{

	use Src\PasswordUtil;

	class HttpBasicAuth extends \Slim\Middleware {

		/**
		 * @var string
		 */
		protected $realm;

		/**
		 * Constructor
		 *
		 * @param string $username The HTTP Authentication username
		 * @param string $password The HTTP Authentication password
		 * @param string $realm The HTTP Authentication realm
		 */
		public function __construct($realm = 'Protected Area Here')
		{

			$this->realm = $realm;
		}

		public function authenticate($username,$password)
		{
			if(isset($username) && isset($password)) {

				$passUtil = new \Src\PasswordUtil($username,$password);
			return $passUtil->verifyPassword() ;
			}else{

				$res = $this->app->response();
				$res->status(401);
				$res->body(json_encode(array('status' => "Username or Password missing")));


				return false;
			}
		}

		public  function denyAccess()
		{
			$res = $this->app->response();
			$res->body(json_encode(array('status' => "failedAuth")));
			$res->status(401);
			$res->header('WWW-Authenticate', sprintf('Basic realm="%s"', $this->realm));
		}
		/**
		 * Call
		 *
		 * This method will check the HTTP request headers for previous authentication. If
		 * the request has already authenticated, the next middleware is called. Otherwise,
		 * a 401 Authentication Required response is returned to the client.
		 */
		public function call()
		{
			$req = $this->app->request();

			$authUser = $req->headers('PHP_AUTH_USER');
			$authPass = $req->headers('PHP_AUTH_PW');
			if ($this->authenticate($authUser,$authPass)) {
				$this->next->call();
			} else {
				 $this->denyAccess();
			}
		}
	}
}