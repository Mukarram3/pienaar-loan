<?php

namespace App\Lib;

class CurlRequest
{

    /**
    * GET request using curl
    *
    * @return mixed
    */
	public static function curlContent($url,$header = null)
	{
        $parts = parse_url($url);
        parse_str($parts['query'], $query);

        $username = $query['username'] ?? null;
        $password = $query['password'] ?? null;
        $message  = $query['message'] ?? null;
        $to       = $query['msisdn'] ?? null;
        $baseUrl = $parts['scheme'] . '://' . $parts['host'] . ($parts['path'] ?? '');
        $messages = [
            ['to' => $to, 'body' => $message],
        ];

        $headers = ['Content-Type: application/json'];
        if ($header && is_array($header)) {
            foreach ($header as $key => $value) {
                $headers[] = "$key: $value";
            }
        } elseif ($username && $password) {
            $headers[] = 'Authorization: Basic ' . base64_encode("$username:$password");
        }

        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $baseUrl,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => json_encode($messages),
                CURLOPT_TIMEOUT => 20,
                CURLOPT_CONNECTTIMEOUT => 10,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
        }
        catch (\Exception $exception){
            dd($exception->getMessage());
        }

        return [
            'url' => $baseUrl,
            'to' => $to,
            'message' => $message,
            'username' => $username,
            'password' => $password,
            'headers' => $headers,
            'http_status' => $httpCode,
            'server_response' => $response,
            'error' => $error,
        ];


	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	    if ($header) {
	    	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	    }
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    $result = curl_exec($ch);
	    curl_close($ch);
	    return $result;
	}


    /**
    * POST request using curl
    *
    * @return mixed
    */
	public static function curlPostContent($url, $postData = null,$header = null)
	{
	    if (is_array($postData)) {
	        $params = http_build_query($postData);
	    } else {
	        $params = $postData;
	    }
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	    if ($header) {
	    	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	    }
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    $result = curl_exec($ch);
	    curl_close($ch);
	    return $result;
	}
}
