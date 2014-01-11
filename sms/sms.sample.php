<?php

function send_sms($user, $password)
{
	// Kullanıcı bilgileri
	$api_username = '';
	$api_password = '';

	global $settings;
	$message = $settings['name'] . ' WiFi hizmeti icin sifreniz ' . $password . ' olarak tanimlanmistir.';
	$xml = '';
	$url = '';
	$result = HTTPPoster($url, $xml);
	if (...)
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