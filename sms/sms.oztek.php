<?php

function send_sms($user, $password)
{
	// Kullanıcı bilgileri
	$api_username = '905052615188';
	$api_password = '172UET';
	$api_userno = '1006078';
	$api_originator = 'ACADEMICHSP';

	global $settings;
	$message = $settings['name'] . ' WiFi hizmeti icin sifreniz ' . $password . ' olarak tanimlanmistir.';
	$xml = 'data=<sms>
        <kno>' . $api_userno . '</kno>
        <kulad>' . $api_username . '</kulad>
        <sifre>' . $api_password . '</sifre>
        <gonderen>' . $api_originator . '</gonderen>
        <telmesajlar>
		<telmesaj>
		<tel>' . $user->gsm . '</tel>
		<mesaj>' . $message . '</mesaj>
		</telmesaj>
		</telmesajlar>
		</sms>';
	$url = 'http://www.ozteksms.com/panel/smsgonderposttekli.php';
	$result = HTTPPoster($url, $xml);
	if (substr($result, 0, 1) === '1')
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