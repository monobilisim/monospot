<?php

function send_sms($user, $password)
{
	// Turatel kullanıcı bilgileri
	$api_username = 'bilkent';
	$api_password = '28072017';
	$api_originator = 'TESTTRTL.';

	global $settings;
	$data = '<?xml version="1.0" encoding="utf-8" ?><MainmsgBody><Command>0</Command><PlatformID>1</PlatformID><ChannelCode>687</ChannelCode><UserName>' . $api_username . '</UserName><PassWord>' . $api_password . '</PassWord><Mesgbody>' . $settings['name'] . ' WiFi hizmeti icin sifreniz ' . $password . ' olarak tanimlanmistir.</Mesgbody><Numbers>' .  $user->gsm . '</Numbers><Type>1</Type><Originator>' . $api_originator . '</Originator></MainmsgBody>';
	$url = 'http://processor.smsorigin.com/xml/process.aspx';
	$result = HTTPPoster($url, $data);

	if (substr($result, 0, 3) == 'ID:')
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

?>