<?php

function send_sms($user, $password)
{
	// Posta Güvercini kullanıcı bilgileri
	$api_username = '';
	$api_password = '';

	global $settings;
	$xml = '<SMS-InsRequest><CLIENT user="' . $api_username . '" pwd="' . $api_password . '" /><INSERT to="' . $user->gsm . '" text="' . $settings['name'] . ' WiFi hizmeti icin sifreniz ' . $password . ' olarak tanimlanmistir." dt="" /></SMS-InsRequest>';
	$url = 'http://www.postaguvercini.com/api_xml/Sms_insreq.asp';
	$result = HTTPPoster($url, $xml);
	$result_xml = new SimpleXMLElement($result);

	if (isset($result_xml->INSERT['id']))
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