<?php
defined('BASEPATH') OR exit('No direct script access allowed');

public function send_request
(
	string $url = NULL,
	string $method = 'GET',
	array $params = [],
	$body = [],
	string $body_type = 'json',
	array $headers = [],
	$token = FALSE,
	string $token_type = 'bearer',
	bool $return_header = FALSE,
	bool $json_decode = TRUE
)
{
	$ch = curl_init();

	if ($params) $url = sprintf("%s?%s", $url, http_build_query($params));
	switch ($method){
		case "POST":
		 	curl_setopt($ch, CURLOPT_POST, 1);
		 	if ($body) curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
		 	break;
		case "GET":
			if ($body) $url = sprintf("%s?%s", $url, http_build_query($body));		 					
			break;
		default:
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
			if ($body) curl_setopt($ch, CURLOPT_POSTFIELDS, $body);	
	}

	switch ($body_type){
		default:
			$headers[] = 'Accept: application/json';
			break;
	}

	if($token) {
		switch ($token_type){
			case 'basic':
				curl_setopt($ch, CURLOPT_USERPWD, $token[0] . ':' . $token[1]);
				break;
			default:
				$headers[] = 'Authorization: Bearer ' . $token;
				break;
		}
	}

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	if ($return_header) {
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_NOBODY, 1);
		$output = curl_exec($ch);
		curl_close($ch);

		$headers = [];
		$output = rtrim($output);
		$data = explode("\n",$output);
		$headers['status'] = $data[0];
		array_shift($data);

		foreach($data as $part){
		    $middle = explode(":",$part,2);
		    if ( !isset($middle[1]) ) { $middle[1] = NULL; }
		    $headers[trim($middle[0])] = trim($middle[1]);
		}
		return $headers;
	}

	$response = curl_exec($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	if ($response === false) {
		$response = curl_error($ch);
		curl_close($ch);
		return [$response];
	}
	curl_close($ch);
	if ($json_decode) $response = json_decode($response, TRUE);
	return $response;
}