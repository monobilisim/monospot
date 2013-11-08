<?php

date_default_timezone_set('Europe/Istanbul');

require 'lib/limonade.php';
require 'lib/idiorm.php';
require 'lib/paris.php';
require 'models/user.php';
include 'sms.php';

$settings = include('settings.inc');
$hotspot = parse_ini_file('hotspot.ini');
$hotspot['_marka'] = strtolower(str_replace(' ', '', $hotspot['marka']));

function configure()
{
	$dir = dirname(__FILE__);

	ORM::configure('sqlite:'.$dir.'/db/hotspot.db');

	option('views_dir', $dir.'/views');

	error_layout('layouts/captiveportal.html.php');

	global $hotspot;
	set('hotspot', $hotspot);
}

dispatch('', 'welcome');
function welcome()
{
	global $hotspot;
	if (date('Y', strtotime($hotspot['demo_bitis'])) != '2010' && time() > strtotime($hotspot['demo_bitis']))
	{
		$demo_expired = '<meta charset="utf-8"><div style="color:#e00;font-weight:bold;text-align:center">Hotspot demo süresi ' . $hotspot['demo_bitis'] . ' tarihinde dolmuştur.</div>';
		return $demo_expired;
	}

	// pfSense kodundan alındı: /usr/local/www/status_captiveportal.php
	if (file_exists('/var/db/captiveportal.db')) {
		$captiveportallck = lock('captiveportaldb');
		$cpcontents = file("/var/db/captiveportal.db", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		unlock($captiveportallck);
	} else
		$cpcontents = array();
	$concurrent = count($cpcontents);

	if ($hotspot['maksimum_kullanici'] && $concurrent >= $hotspot['maksimum_kullanici'])
	{
		$max_user_reached = '<meta charset="utf-8"><div style="color:#e00;font-weight:bold;text-align:center">Maksimum kullanıcı sayısına ulaşıldı!</div>';
		return $max_user_reached;
	}

	global $settings;
	$user = Model::factory('User')->create();
	$user->defaults();
	set('title', $settings['name'] . ' ' . t('welcome'));
	set('color', $settings['color']);
	set('user', $user);
	if (isset($settings['simple_screen'])) set('form', 'sms_register');
	return html('layouts/captiveportal.html.php');
}

dispatch_post('', 'welcome_post');
function welcome_post()
{
	global $settings, $clientmac, $clientip;
	if (isset($_POST['lang']))
	{
		$_SESSION['lang'] = strtolower($_POST['lang']);
		redirect_to('');
	}

	$user = null;
	if (isset($_POST['user']['gsm'])) $field = 'gsm';
	elseif (isset($_POST['user']['id_number'])) $field = 'id_number';
	elseif (isset($_POST['user']['username'])) $field = 'username';
	$user = Model::factory('User')->where($field, $_POST['user'][$field])->find_one();

	$form = '';
	$arg = '';

	// SMS ile giriş
	if (isset($_POST['user']['gsm']) && strlen($_POST['user']['gsm']) == 10)
	{
		if (!isset($_POST['user']['password'])) // Yeni kayıt ekranı
		{
			if (!$user) // Daha önceden kaydı yoksa yeni kayıt açıp gerekirse TC kimlik kontrolü yapıyoruz
			{
				$user = Model::factory('User')->create();
				$user->defaults();
				$user->fill($_POST['user']);

				if (isset($settings['sms_fields']['id_number']))
				{
					$user->name = tr_toUpper($user->name);
					$user->surname = tr_toUpper($user->surname);

					$bilgiler = array(
						"isim"      => $user->name,
						"soyisim"   => $user->surname,
						"dogumyili" => $_POST['birthyear'],
						"tcno"      => $user->id_number
					);

					$sonuc = tcno_dogrula($bilgiler);

					if ($sonuc != 'true')
					{
						captiveportal_logportalauth($user->id_number,$clientmac,$clientip,"FAILURE");
						$message = 'invalid_id_number';
						$form = 'sms_register';
					}
				}
			}

			if (!$form) // TC kimlik doğrulama varsa ondan geçmiş demektir
			{
				$message = password_request_check($user);
				if (!$message)
				{
					$password = generate_password();
					$user->password = $password;
					$user->save();
					if ($sent = send_sms($user, $password, $clientmac))
					{
						after_send_sms($user, $clientmac);
						$message = 'password_sent';
					}
					else
						$message = 'password_not_sent';
					$form = 'sms_login';
				}
				else
				{
					if ($message == 'min_interval') $arg = $settings['min_interval'];
					$form = 'sms_register';
				}
			}
		}
		else // Giriş ekranı
		{
			if (strlen($_POST['user']['password']) > 0) // giriş denemesi
			{
				if ($user->password == $_POST['user']['password'])
				{
					if (password_expired($user))
					{
						$message = 'password_expired_sms';
						$form = 'sms_login';
					}
					else
					{
						login($user, 'gsm');
					}
				}
				else
				{
					captiveportal_logportalauth($user->gsm,$clientmac,$clientip,"FAILURE");
					$user->password = '';
					$message = 'invalid_password';
					$form = 'sms_login';
				}
			}
			else // şifre isteği
			{
				$message = password_request_check($user);
				if (!$message)
				{
					$password = generate_password();
					$user->password = $password;
					$user->save();
					if ($sent = send_sms($user, $password, $clientmac))
					{
						after_send_sms($user, $clientmac);
						$message = 'password_sent';
					}
					else
						$message = 'password_not_sent';
				}
				else
				{
					if ($message == 'min_interval') $arg = $settings['min_interval'];
				}
				$form = 'sms_login';
			}
		}
	}
	// TC kimlik no ile giriş
	elseif (isset($_POST['user']['id_number']) && !isset($_POST['user']['gsm']))
	{
		if (!$user)
		{
			$user = Model::factory('User')->create();
			$user->defaults();
			$user->fill($_POST['user']);
			$user->name = tr_toUpper($user->name);
			$user->surname = tr_toUpper($user->surname);
		}

		$bilgiler = array(
			"isim"      => $user->name,
			"soyisim"   => $user->surname,
			"dogumyili" => $_POST['birthyear'],
			"tcno"      => $user->id_number
		);

		$sonuc = tcno_dogrula($bilgiler);

		if ($sonuc == 'true')
		{
			login($user, 'id_number');
		}
		else
		{
			captiveportal_logportalauth($user->id_number,$clientmac,$clientip,"FAILURE");
			$message = 'invalid_id_number';
			$form = 'id_number_login';
		}
	}
	// Kullanıcı adı ve şifre ile giriş
	elseif (isset($_POST['user']['username']))
	{
		if ($user)
		{
			if ($user->password == $_POST['user']['password'])
			{
				if (password_expired($user))
				{
					$message = 'password_expired';
				}
				else
				{
					login($user, 'username');
				}
			}
			else
			{
				captiveportal_logportalauth($user->username,$clientmac,$clientip,"FAILURE");
				$user->password = '';
				$message = 'invalid_password';
			}
		}
		else
		{
			$message = 'user_not_found';
		}
		$form = 'manual_user_login';
	}

	set('title', $settings['name'] . ' ' . t('welcome'));
	set('color', $settings['color']);
	set('message', $message);
	set('arg', $arg);
	set('form', $form);
	set('user', $user);
	return html('layouts/captiveportal.html.php');
}

run();


/* Functions */

function after_send_sms($user, $clientmac)
{
	global $settings;
	$sms = ORM::for_table('sms')->create();
	$sms->user_id = $user->id;
	$sms->mac = $clientmac;
	$sms->timestamp = time();
	$sms->save();
	$user->last_sms = time();
	$user->save();
}

function login($user, $field)
{
	global $settings, $clientmac, $clientip;

	$user->last_login = time();
	$user->expires = strtotime('+' . $settings['valid_for'] . 'days');
	$user->save();
	captiveportal_logportalauth($user->$field,$clientmac,$clientip,"LOGIN");
	$attributes['session_terminate_time'] = $user->expires;
	portal_allow($clientip, $clientmac, $user->$field, $password = null, $attributes);
}

function t($key, $arg = '')
{
	$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'tr';
	$messages = include dirname(__FILE__).'/lang/'.$lang.'.inc';
	return sprintf($messages[$key], $arg);
}

function generate_password()
{
	return $password = mt_rand(100000, 999999);
}

function password_expired($user)
{
	if ($user->expires < time()) return true;
	else return false;
}

function password_request_check($user)
{
	global $settings;

	# daily global limit
	$daily_global_count = ORM::for_table('sms')->where_raw("strftime('%Y-%m-%d', datetime(timestamp, 'unixepoch', 'localtime')) = strftime('%Y-%m-%d', 'now')")->count();
	if ($daily_global_count >= $settings['daily_global_limit']) return 'daily_global_limit';

	# minimum interval
	$seconds = time() - $user->last_sms;
	$minutes = floor($seconds/60);
	if ($minutes < $settings['min_interval']) return 'min_interval';

	# daily limit
	$daily_count = ORM::for_table('sms')->where('user_id', $user->id)->where_raw("strftime('%Y-%m-%d', datetime(timestamp, 'unixepoch', 'localtime')) = strftime('%Y-%m-%d', 'now')")->count();
	if ($daily_count >= $user->daily_limit) return 'daily_limit';

	# weekly limit
	$weekly_count = ORM::for_table('sms')->where('user_id', $user->id)->where_raw("strftime('%W', datetime(timestamp, 'unixepoch', 'localtime')) = strftime('%W', 'now')")->count();
	if ($weekly_count >= $user->weekly_limit) return 'weekly_limit';

	# monthly limit
	$monthly_count = ORM::for_table('sms')->where('user_id', $user->id)->where_raw("strftime('%Y-%m', datetime(timestamp, 'unixepoch', 'localtime')) = strftime('%Y-%m', 'now')")->count();
	if ($monthly_count >= $user->monthly_limit) return 'monthly_limit';

	# yearly limit
	$yearly_count = ORM::for_table('sms')->where('user_id', $user->id)->where_raw("strftime('%Y', datetime(timestamp, 'unixepoch', 'localtime')) = strftime('%Y', 'now')")->count();
	if ($yearly_count >= $user->yearly_limit) return 'yearly_limit';

	return false;
}

function tcno_dogrula($bilgiler) {

	$gonder = '<?xml version="1.0" encoding="utf-8"?>
	<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	<soap:Body>
	<TCKimlikNoDogrula xmlns="http://tckimlik.nvi.gov.tr/WS">
	<TCKimlikNo>'.$bilgiler["tcno"].'</TCKimlikNo>
	<Ad>'.$bilgiler["isim"].'</Ad>
	<Soyad>'.$bilgiler["soyisim"].'</Soyad>
	<DogumYili>'.$bilgiler["dogumyili"].'</DogumYili>
	</TCKimlikNoDogrula>
	</soap:Body>
	</soap:Envelope>';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,            "https://tckimlik.nvi.gov.tr/Service/KPSPublic.asmx" );
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt($ch, CURLOPT_POST,           true );
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_POSTFIELDS,    $gonder);
	curl_setopt($ch, CURLOPT_HTTPHEADER,     array(
	'POST /Service/KPSPublic.asmx HTTP/1.1',
	'Host: tckimlik.nvi.gov.tr',
	'Content-Type: text/xml; charset=utf-8',
	'SOAPAction: "http://tckimlik.nvi.gov.tr/WS/TCKimlikNoDogrula"',
	'Content-Length: '.strlen($gonder)
	));
	$gelen = curl_exec($ch);
	curl_close($ch);

	return strip_tags($gelen);
}

function tr_toUpper($veri) {
    return strtoupper (str_replace(array ('ı', 'i', 'ğ', 'ü', 'ş', 'ö', 'ç' ),array ('I', 'İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç' ),$veri));
}