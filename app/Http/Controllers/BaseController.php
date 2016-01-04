<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class BaseController extends Controller
{
	/**
	 *
	 */
	public function __construct ()
	{
		//Log::debug("New Request!");
		if (config('app.debug') === true)
		{
			//if(isset($data['password'])) $data['password'] = "****";

			$data = json_encode($this->input(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

			Log::debug("Posted data: ". $data );
		}

		// parent::__construct();
	}

	/**
	 * Does the file_get_contents for us
	 *
	 * now with more caching!
	 *
	 * @return mixed
	 */
	protected $input = NULL;
	protected $payload = NULL;
	protected $meta = NULL;

	public function input ()
	{
		if ($this->input === NULL)
		{
			$in = file_get_contents("php://input");
			if ($in != "" && $in != NULL)
			{
				$this->input = json_decode($in, true);
				if (json_last_error() != JSON_ERROR_NONE)
				{
					// we have to do a raw write here...
					http_response_code(400);
					if(config('app.debug') === true)
						Log::debug("Invalid JSON in this request: ". $in);
					echo json_encode(["message" => "Error: Malformed JSON"]);
					exit;
				}
				if (isset($this->input['payload']) && !empty($this->input['payload']))
				{
					$this->payload = $this->input['payload'];
				}
				if (isset($this->input['meta']) && !empty($this->input['meta']))
				{
					$this->meta = $this->input['meta'];
				}
			}
			else
			{
				$this->input = "";
			}
		}

		return $this->input;
	}

	/**
	 * @param string $in
	 * @param bool 	 $success
	 * @param int    $code
	 * @param array  $extraHeaders
	 *
	 * @return mixed
	 */

	public function response($in = "", $code = 200, $extraHeaders = [])
	{
		$meta = [
			'RequestTimestamp'	=> (string)round($_SERVER["REQUEST_TIME_FLOAT"], 4),
			'Duration'			=> (string)$this->executiontime(),
			'ResponseTimestamp'	=> (string)round(microtime(true), 4),
		];

		$output = [
			'Success' => true,
			'Meta'	  => $meta,
			'Payload' => $in
		];

		$outp = json_encode($output, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
		$in = json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

		if (config('app.debug') === true)
			Log::debug("Output: \r\n" . $outp );
		trim($outp);

		$response = (new Response($output, $code));
		$response->header('Content-Type', 'application/json; charset=UTF-8');
		$response->header('Access-Control-Allow-Origin', '*');

		if (is_array($extraHeaders) && count($extraHeaders) > 0)
		{
			foreach ($extraHeaders as $key=>$value)
			{
				$response->header($key, $value);
			}
		}

		return $response;
	}

    /**
	 * Why do the bomb()?
	 * This sets a generic plane for some nice low level logging
	 * As opposed to extending the generic laravel response::json
	 * This is /only/ called when there is an error.
	 *
	 * @param string $type
	 * @param string $input
	 * @param int    $code
	 *
	 * @return mixed
	 */
	public function bomb($type = 'message', $input = 'Unspecified Error', $code = 404, $errors = [])
	{
		/*
		 * Just as an example of the robustness of this function
		 * this bomb sets success to 0 in all cases.
	 	 * On the frontend, you simply have to check for the value of success
		 * and if it is 0, you know an error occurred. (also note the non 200 return code)
		 */
		$meta = [
			'RequestTimestamp'	=> (string)round($_SERVER["REQUEST_TIME_FLOAT"], 4),
			'Duration'			=> (string)$this->executiontime(),
			'ResponseTimestamp'	=> (string)round(microtime(true), 4),
		];

		$err = new stdClass;
		$err->Message = $type. ' ' .$input;
		$err->Meta = $errors;
		$err->Code = $code;

		$ErrMessage[] = $err;


		$output = [
			'Success' => false,
			'Meta'	  => $meta,
			'Errors' => $ErrMessage,
		];


		$outp = json_encode($output, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
		$out = json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

		// $response = (new Response($out, $code));
		$response = (new Response($out));

		$response->header('Content-Type', 'application/json; charset=UTF-8');
		$response->header('Access-Control-Allow-Origin', '*');
		return $response;
	}
}
