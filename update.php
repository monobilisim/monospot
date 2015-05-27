<?php

// versiyon 2.2
$result = ORM::for_table('user')->raw_query("SELECT name FROM sqlite_master WHERE type='table' AND name='permission'")->find_one();
if (!$result) {
	try {
		ORM::for_table('user')->raw_execute('ALTER TABLE user ADD gsm_permission INTEGER');
		ORM::for_table('user')->raw_execute('ALTER TABLE user ADD email_permission INTEGER');
		ORM::for_table('user')->raw_execute("CREATE TABLE 'permission' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'user_id' INTEGER, 'gsm' TEXT, 'email' TEXT, 'mac' TEXT, 'ip' TEXT, 'timestamp' INTEGER)");
	} catch (Exception $e) {
		die('Veritabani guncellemesi sirasinda bir hata olustu: ' .$e->getMessage());
	}

	foreach(array('tr', 'en') as $code)
	{
		$lang_sample = include 'lang/' . $code . '.sample.inc';
		$lang = include 'lang/' . $code . '.inc';
		foreach ($lang_sample as $key => $value) {
			if (!array_key_exists($key, $lang)) {
				$lang[$key] = $value;
			}
		}
		file_put_contents('lang/' . $code . '.inc', '<?php' . "\n\n" . 'return ' . var_export($lang, true) . ';');
	}
}