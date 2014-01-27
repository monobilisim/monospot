<?php

function send_sms($user, $password)
{
	// Kullanıcı bilgileri
	$api_username = '';
	$api_password = '';
	$api_header = '';

	global $settings;
	$message = $settings['name'] . ' WiFi hizmeti icin sifreniz ' . $password . ' olarak tanimlanmistir.';
	$params = array(
		'username' => $api_username,
		'password' => md5($api_password),
		'header' => $api_header,
		'isAvea' => 'false',
		'smsType' => 'TX',
		'smsMessage' => $message,
		'messageId' => str_replace('.', '', microtime(true)),
		'phoneNo' => '90' . $user->gsm,
	);
	$url = 'http://sms.avea.com.tr/kurumsalsms/smssender';
	$result = HTTPPoster($url, $params);
	$result_xml = new SimpleXMLElement($result);

	if ((string)$result_xml->SMS->RESULT === '0')
		return true;
	else
		return false;
}

function HTTPPoster($url, $params)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	//curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	$result = curl_exec($ch);
	return $result;
}
