<?php

require 'sms/api/ekomesaj/SmsApi.php'; // Ekomesaj ile aynı API

function send_sms($user, $password)
{
	// Kullanıcı bilgileri
	$api_username = '';
	$api_password = '';
    $api_sender = '';

	global $settings;
	$message = $settings['name'] . ' WiFi hizmeti icin sifreniz ' . $password . ' olarak tanimlanmistir.';

    $port = 9588;
    $smsApi = new SmsApi('panel.pratiksms.com.tr', $api_username, $api_password, $port);

    $request = new SendSingleSms();
    $request->title = 'Monospot';
    $request->content = $message;
    $request->number = $user->gsm;
    $request->encoding = 0;
    $request->sender = $api_sender;

    $response = $smsApi->sendSingleSms($request);

    if ($response->err == null) {
        return true;
    } else {
        return false;
    }
}
