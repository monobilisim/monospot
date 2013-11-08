<?php

function send_sms($user, $password, $clientmac)
{
	// JetSMS kullanıcı bilgileri
	$api_username = '';
	$api_password = '';
	$alphanumeric = '';

	global $settings;
	$xml = '<?xml version="1.0" encoding="iso-8859-9" ?><message-context type="smmgsd"><username>' . $api_username . '</username><password>' . $api_password . '</password><outbox-name>' . $alphanumeric . '</outbox-name><text>' . $settings['name'] . ' WiFi hizmeti icin sifreniz ' . $password . ' olarak tanimlanmistir.</text><gsmnos>' . $user->gsm . '</gsmnos></message-context>';
	$url = 'http://bioweb.biotekno.biz:8080/SMS-Web/xmlsms';
	$result = HTTPPoster($url, $xml);
	if (substr($result, 0, 2) == '00')
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