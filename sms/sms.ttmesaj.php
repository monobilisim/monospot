<?php

function send_sms($user, $password)
{
	// TT Mesaj legacy gateway (FortiAuthenticator ile ayni endpoint)
	$api_username = '';
	$api_password = '';
	$api_origin = '';
	$api_url = 'http://otpurl.ttmesaj.com/SendSMS/SendSMSURL.aspx';

	global $settings;
	// TT Mesaj Turkce karakter kabul etmiyor
	$message = $settings['name'] . ' WiFi hizmeti icin sifreniz ' . $password . ' olarak tanimlanmistir.';

	$params = array(
		'un' => $api_username,
		'pw' => $api_password,
		'msg' => $message,
		'orgn' => $api_origin,
		'list' => '90' . $user->gsm,
	);

	$response = ttmesaj_post($api_url, http_build_query($params));
	if ($response === null)
		return false;

	// Hata ornegi: WP:-10,Kullanici adinizi...  Basari: *OK* veya WP:0
	return strpos($response, '*OK*') !== false || strpos($response, 'WP:0') !== false;
}

function ttmesaj_post($url, $data)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
	$result = curl_exec($ch);
	return $result === false ? null : $result;
}
