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

// Son MAC adresi sütunu eklendi.
$result = ORM::for_table('user')->raw_query("PRAGMA table_info(user)")->find_many();
$last_mac_column_exists = FALSE;
foreach ($result as $column) {
    if ($column->name === 'last_mac') {
        $last_mac_column_exists = TRUE;
        break;
    }
}
if (!$last_mac_column_exists) {
    try {
        ORM::for_table('user')->raw_execute('ALTER TABLE user ADD last_mac TEXT');
    } catch (Exception $e) {
        die('Veritabani guncellemesi sirasinda bir hata olustu: ' .$e->getMessage());
    }
}

// kullanıcı grupları eklendi
$result = ORM::for_table('user')->raw_query("SELECT name FROM sqlite_master WHERE type='table' AND name='group'")->find_one();
if (!$result) {
    try {
        ORM::for_table('user')->raw_execute('ALTER TABLE user ADD group_id INTEGER');
        ORM::for_table('user')->raw_execute("CREATE TABLE 'group' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'name' TEXT, 'macs' TEXT)");
    } catch (Exception $e) {
        die('Veritabani guncellemesi sirasinda bir hata olustu: ' .$e->getMessage());
    }
}
