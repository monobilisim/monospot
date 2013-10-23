<?php

function send_sms($user, $password, $clientmac)
{
	// Posta Güvercini kulanıcı bilgileri
	$kullanici_kodu = '';
	$sifre = ''

	global $settings;
	$xml = '<SMS-InsRequest><CLIENT user="' . $kullanici_kodu . '" pwd="' . $sifre . '" /><INSERT to="' . $user->gsm . '" text="' . $settings['name'] . ' WiFi hizmeti icin sifreniz ' . $password . ' olarak tanimlanmistir." dt="" /></SMS-InsRequest>';
	$url = 'http://www.postaguvercini.com/api_xml/Sms_insreq.asp';
	$result = HTTPPoster($url, $xml);
	$result_xml = new SimpleXMLElement($result);
	if (isset($result_xml->INSERT['id'])) {
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