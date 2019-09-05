<?php

// assets dizinindeki dosyalara gelen isteklerde dosyayi goster ve cik
$request_uri = $_SERVER[REQUEST_URI];
if (substr($request_uri, 0, 17) == '/monospot/assets/') {
    $extension = substr($request_uri, strrpos($request_uri, '.') + 1);
    $content_types = array(
        'txt' => 'text/plain',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'png' => 'image/png',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
    );
    if (array_key_exists($extension, $content_types)) {
        header('Content-Type: ' . $content_types[$extension]);
    } else {
        header('Content-Type: text/plain');
    }
    print file_get_contents(".$request_uri");
    exit();
}

date_default_timezone_set('Europe/Istanbul');

require 'lib/limonade.php';
require 'lib/idiorm.php';
require 'lib/paris.php';
require 'models/user.php';
require 'models/group.php';
include 'sms.php';
include 'custom_functions.php';

$global_settings = get_global_settings();
$settings = get_settings();
$hotspot = parse_ini_file('hotspot.ini');
$hotspot['_marka'] = strtolower(str_replace(' ', '', $hotspot['marka']));

session_start();

function get_settings()
{
    global $clientmac;

    if ($clientmac) {
        ORM::configure('sqlite:'. __DIR__ .'/db/hotspot.db');
        global $hotspot;
        set('hotspot', $hotspot);
        $group = Model::factory('Group')->where_like('macs', "%$clientmac%")->find_one();
        if ($group) {
            return $group->getSettings();
        }
    }

    return get_global_settings();
}

function get_global_settings()
{
    return include 'settings.inc';
}

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

	if ($hotspot['maksimum_kullanici'])
	{
		$cpdb = captiveportal_read_db();
		$concurrent = count($cpdb);
		if ($concurrent >= $hotspot['maksimum_kullanici'])
		{
			$max_user_reached = '<meta charset="utf-8"><div style="color:#e00;font-weight:bold;text-align:center">Maksimum kullanıcı sayısına ulaşıldı!</div>';
			return $max_user_reached;
		}
	}

	global $settings;

	$user = Model::factory('User')->create();
	$user->fillDefaults();
	set('settings', $settings);
	set('title', $settings['name'] . ' ' . t('welcome'));
	set('color', $settings['color']);
	set('user', $user);
	set('lang', isset($_SESSION['lang']) ? $_SESSION['lang'] : 'tr');
	if (isset($settings['sms']['simple_screen'])) set('form', 'sms_register');
	return html('layouts/captiveportal.html.php');
}

dispatch_post('', 'welcome_post');
function welcome_post()
{
	global $settings, $clientmac, $clientip;

	if (isset($_POST['lang']))
		$_SESSION['lang'] = strtolower($_POST['lang']);

	$form = '';
	$arg = '';

	// SMS ile giriş - yeni kayıt
	if ($_POST['form_id'] == 'sms_register')
	{
		if ((!isset($settings['sms']['international']) && strlen($_POST['user']['gsm']) == 10) ||
            isset($settings['sms']['international']))
		{
			$user = Model::factory('User')->where('gsm', $_POST['user']['gsm'])->find_one();

			if (!$user) // Daha önceden kaydı yoksa yeni kayıt açıp gerekirse TC kimlik kontrolü yapıyoruz
			{
				$user = Model::factory('User')->create();
				$user->fillDefaults();
				$user->fill($_POST['user']);

				if (isset($settings['sms']['id_number']))
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
			else
			{
				// daha önce kayıt olmuşsa bile gsm/e-posta izin bilgileri güncellensin
				$message = permission_process($user, 'sms', 'sms_register');
				if ($message)
				{
					$form = 'sms_register';
				}
				else
				{
					if (isset($settings['sms']['always_send_password']))
					{
						// Geçerli şifresi olsa bile yeni şifre gönder etkinse
						$_POST['form_id'] = 'sms_login';
						$_POST['password'] = '';
						return welcome_post();
					}
					else
					{
						$message = 'user_already_registered';
						$form = 'sms_login';
					}
				}
			}

            if (isset($settings['disallow_multiple_logins']))
            {
                // Aynı MAC adresi ile farklı kullanıcının giriş yapmasını engelle etkinse
                if (isset($settings['disallow_multiple_logins_for']))
                {
                    $mac_disallow_for = $settings['disallow_multiple_logins_for'];
                }
                else
                {
                    $mac_disallow_for = 30;
                }

                if (!empty($clientmac))
                {
                    $mac_sms = ORM::for_table('sms')->where('mac', $clientmac)->order_by_desc('timestamp')->find_one(); // Bu MAC adresi icin son gonderilen SMS
                    if ($mac_sms)
                    {
                        $mac_user = Model::factory('User')->find_one($mac_sms->user_id);
                        $mac_gsm = $mac_user->gsm;

                        if ($_POST['user']['gsm'] != $mac_gsm) // Bu MAC adresi icin son gonderilen SMS kullanicinin verdigi numaradan farkli bir numaraya gonderilmisse
                        {
                            $current_time = time();
                            $mac_time = $mac_sms->timestamp;
                            $mac_interval = $current_time - $mac_time;

                            if ($mac_interval <= ($mac_disallow_for * 86400)) // Son gonderilen SMS'ten beri yeterli sure gecmemisse
                            {
                                $mac_user = Model::factory('User')->find_one($mac_sms->user_id);
                                $message = 'multiple_logins_from_same_mac_address_disallowed';
                                $arg = $mac_gsm;
                                $form = 'sms_login';
                            }
                        }
                    }
                }
            }

			if (!$form) // TC kimlik doğrulama varsa ondan geçmiş demektir
			{
				$message = permission_process($user, 'sms', 'sms_register');
				if ($message)
				{
					$form = 'sms_register';
				}
				else
				{
					$message = send_password($user);
					if ($message == 'min_interval') $arg = $settings['min_interval'];
					$form = 'sms_login';
				}
			}
		}
	}

	// SMS ile giriş
	if ($_POST['form_id'] == 'sms_login')
	{
		$user = Model::factory('User')->where('gsm', $_POST['user']['gsm'])->find_one();

		if (!$user) // kayıt olmadan giriş ekranına gelmiş olabilir
		{
			$message = 'user_not_found';
			$form = 'sms_register';
		}
		else
		{
			if (strlen($_POST['password']) > 0) // giriş denemesi
			{
				if ($user->password == $_POST['password'])
				{
					if (password_expired($user))
					{
						$message = 'password_expired_sms';
					}
					else
					{
						login($user, 'gsm');
					}
				}
				else
				{
					captiveportal_logportalauth($user->gsm,$clientmac,$clientip,"FAILURE");
					$message = 'invalid_password';
				}
			}
			else // şifre isteği
			{
				$message = send_password($user);
				if ($message == 'min_interval') $arg = $settings['min_interval'];
			}
			$form = 'sms_login';
		}
	}

	// TC kimlik no ile giriş
	if ($_POST['form_id'] == 'id_number_login')
	{
		$user = Model::factory('User')->where('id_number', $_POST['user']['id_number'])->find_one();

		if (!$user)
		{
			$user = Model::factory('User')->create();
			$user->fillDefaults();
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
			$message = permission_process($user, 'id_number');
			if (!$message)
			{
				login($user, 'id_number');
			}
		}
		else
		{
			captiveportal_logportalauth($user->id_number,$clientmac,$clientip,"FAILURE");
			$message = 'invalid_id_number';
		}
		$form = 'id_number_login';
	}

	// Kullanıcı adı ve şifre ile giriş
	if ($_POST['form_id'] == 'manual_user_login')
	{
		$user = Model::factory('User')->where('username', $_POST['user']['username'])->find_one();

		if ($user)
		{
			$user->fill($_POST['user']);
			if ($user->password == $_POST['password'])
			{
				if (password_expired($user))
				{
					$message = 'password_expired';
				}
				else
				{
					$message = permission_process($user, 'manual_user');
					if (!$message)
					{
						login($user, 'username');
					}
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

	include 'captiveportal_custom.php';

    set('settings', $settings);
	set('title', $settings['name'] . ' ' . t('welcome'));
	set('color', $settings['color']);
	set('message', $message);
	set('arg', $arg);
	set('form', $form);
	if (!$form && isset($settings['sms']['simple_screen'])) set('form', 'sms_register');
	set('user', $user);
	set('lang', isset($_SESSION['lang']) ? $_SESSION['lang'] : 'tr');
	return html('layouts/captiveportal.html.php');
}

run();


/* Functions */

function permission_process(&$user, $method, $form_id = null)
{
	global $settings, $clientmac, $clientip;
	$message = '';

	// izin vermek ya da sözleşmeyi kabul etmek zorunlu ise kontrol et (javascript'te de kontrol var ama teorik olarak aşılabilir)
	if (isset($settings['contact'][$method]['gsm_permission_required']))
	{
		if (!isset($_POST['gsm_permission'])) $message = 'gsm_permission_required';
	}
	if (isset($settings['contact'][$method]['email_permission_required']))
	{
		if (!isset($_POST['email_permission'])) $message =  'email_permission_required';
	}
	// sms kayıt formu dışında sözleşme kontrolü yap
	if (isset($settings['terms']) && $form_id != 'sms_register')
	{
		if (!isset($_POST['terms'])) $message = 'terms_required';
	}

	// izinleri kaydet
	if (!$message)
	{
		// izin istenmişse 1 ya da 0 değerlerini ata, istenmemişse veritabanına NULL yazılacak
		if (isset($_POST['gsm_permission_asked']))
		{
			if (isset($_POST['gsm_permission'])) $user->gsm_permission = 1;
			else $user->gsm_permission = 0;
		}
		if (isset($_POST['email_permission_asked']))
		{
			$user->email = $_POST['user']['email'];
			if (isset($_POST['email_permission'])) $user->email_permission = 1;
			else $user->email_permission = 0;
		}

		if (isset($_POST['user']['gsm'])) $user->gsm = $_POST['user']['gsm'];
		if (isset($_POST['user']['email'])) $user->email = $_POST['user']['email'];
		$user->save();

		if (isset($_POST['gsm_permission']) || isset($_POST['email_permission']))
		{
			$permission = ORM::for_table('permission')->create();
			$permission->user_id = $user->id;
			if (isset($_POST['gsm_permission'])) $permission->gsm = $user->gsm;
			if (isset($_POST['email_permission'])) $permission->email = $user->email;
			$permission->mac = $clientmac;
			$permission->ip = $clientip;
			$permission->timestamp = time();
			$permission->save();
		}
	}

	return $message;
}
function send_password($user)
{
	global $settings, $clientmac;

	$message = password_request_check($user);
	if (!$message)
	{
		$password = generate_password();
		$user->password = $password;

		if ($sent = send_sms($user, $password, $clientmac))
		{
			$sms = ORM::for_table('sms')->create();
			$sms->user_id = $user->id;
			$sms->mac = $clientmac;
			$sms->timestamp = time();
			$sms->save();
			$user->last_sms = time();
			$user->expires = strtotime('+' . $settings['valid_for'] . ' days');
			$user->save();
			$message = 'password_sent';
		}
		else
		{
			$message = 'password_not_sent';
		}
	}
	return $message;
}

function login($user, $field)
{
	global $settings, $global_settings, $clientmac, $clientip;

	$user->last_login = time();
	$user->last_mac = $clientmac;
	$user->save();
	captiveportal_logportalauth($user->$field,$clientmac,$clientip,"LOGIN");
	$attributes = array();
    // Kullanıcının grubuna ait oturum geçerlilik süresinin global oturum geçerlilik süresinden (hard timeout) daha kısa olduğu durumlar için kullanıcıya özel olarak bu değeri atıyoruz
    if ($settings['session_timeout'] < $global_settings['session_timeout']) {
        $attributes['session_terminate_time'] = strtotime('+' . $settings['session_timeout'] . ' minutes');
    }
	// Şifre geçerlilik süresinin oturum geçerlilik süresinden daha kısa olduğu durumlar için kullanıcıya özel olarak bu değeri atıyoruz
	if ($user->expires && $user->expires < strtotime('+' . $settings['session_timeout'] . ' minutes'))
	{
		$attributes['session_terminate_time'] = $user->expires;
	}

	portal_allow($clientip, $clientmac, $user->$field, $password = null, $attributes);
	exit();
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
	$daily_global_count = ORM::for_table('sms')->where_raw("strftime('%Y-%m-%d', date(timestamp, 'unixepoch', 'localtime')) = date('now')")->count();
	if ($daily_global_count >= $settings['daily_global_limit']) return 'daily_global_limit';

	# minimum interval
	$seconds = time() - $user->last_sms;
	$minutes = floor($seconds/60);
	if ($minutes < $settings['min_interval']) return 'min_interval';

	# daily limit
	$daily_count = ORM::for_table('sms')->where('user_id', $user->id)->where_raw("date(timestamp, 'unixepoch', 'localtime') = date('now')")->count();
	if ($daily_count >= $user->daily_limit) return 'daily_limit';

	# weekly limit
	$weekly_count = ORM::for_table('sms')->where('user_id', $user->id)->where_raw("date(timestamp, 'unixepoch', 'localtime') >= date('now', 'weekday 0', '-7 days')")->count();
	if ($weekly_count >= $user->weekly_limit) return 'weekly_limit';

	# monthly limit
	$monthly_count = ORM::for_table('sms')->where('user_id', $user->id)->where_raw("strftime('%Y-%m', date(timestamp, 'unixepoch', 'localtime')) = strftime('%Y-%m', 'now')")->count();
	if ($monthly_count >= $user->monthly_limit) return 'monthly_limit';

	# yearly limit
	$yearly_count = ORM::for_table('sms')->where('user_id', $user->id)->where_raw("strftime('%Y', date(timestamp, 'unixepoch', 'localtime')) = strftime('%Y', 'now')")->count();
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

function tr_toUpper($string) {
    return strtoupper (str_replace(array ('ı', 'i', 'ğ', 'ü', 'ş', 'ö', 'ç' ),array ('I', 'İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç' ),$string));
}


function permission_checked($method, $form_id, $type) {
	global $settings;

	if ($_POST && $form_id == $_POST['form_id']) {
		return isset($_POST[$type . '_permission']);
	}
	else {
		return isset($settings['contact'][$method][$type . '_permission_checked']);
	}
}
