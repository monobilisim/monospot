<?php

function send_sms($user, $password)
{
    // Mobildev kullanıcı bilgileri
    $api_username = '';
    $api_password = '';
    $api_client_id = '';

    global $settings;

    $message = $settings['name'] . ' WiFi hizmeti icin sifreniz ' . $password . ' olarak tanimlanmistir.';

    $message= urlencode($message);
    $gsm = $user->gsm;

    $url = "https://secure.mobilus.net/sms/gateway.asp?username=$api_username&company=$api_client_id&password=$api_password&action=0&message=$message&numbers=$gsm";

    $result = file_get_contents($url);

    if (substr($result, 0, 3) === "ID:") {
        return TRUE;
    } else {
        return FALSE;
    }
}