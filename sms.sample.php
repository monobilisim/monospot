<?php

function send_sms($user, $password, $clientmac) {
	global $settings;
	$message = $settings['name'] . ' WiFi hizmeti icin sifreniz ' . $password . ' olarak tanimlanmistir.';
	$xml = '';
	$url = '';
	$result = HTTPPoster($url, $xml);
	$result_xml = new SimpleXMLElement($result);
	if ($result_xml->sent === true) {
		$sms = ORM::for_table('sms')->create();
		$sms->user_id = $user->id;
		$sms->mac = $clientmac;
		$sms->timestamp = time();
		$sms->save();
		$user->last_sms = time();
		$user->expires = strtotime('+' . $settings['valid_for'] . ' days');
		$user->save();
		return true;
	}
	else return false;
}

function HTTPPoster($url, $data) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	$result = curl_exec($ch);
	return $result;
}