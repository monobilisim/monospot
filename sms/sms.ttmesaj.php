<?php

function send_sms($user, $password)
{
	// Kullanıcı bilgileri
	$api_username = '';
	$api_password = '';
	$api_origin = '';

	global $settings;
	require_once 'nusoap/lib/nusoap.php';
	$client = new nusoap_client("http://ws.ttmesaj.com/service1.asmx?wsdl", 'wsdl');
	$message = $settings['name'] . ' WiFi hizmeti icin sifreniz ' . $password . ' olarak tanimlanmistir.';
	$params = array(
		'username' => $api_username,
		'password' => $api_password,
		'numbers' => '90' . $user->gsm,
		'message' => $message,
		'origin' => $api_origin,
	);

	$response = $client->call('sendSingleSMS', $params);

	if (strpos($response['sendSingleSMSResult'], '*OK*') !== false)
		return true;
	else
		return false;
}

function HTTPPoster($url, $data)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	$result = curl_exec($ch);
	return $result;
}