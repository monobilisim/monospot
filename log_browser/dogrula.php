<?php

$file = $_POST['file'];
$exploded = explode('.', basename($file));
$log_type = $exploded[0];

chdir('/tmp');
exec('cp /logimza/' . $file . ' ./');
exec('tar zxvf ' . basename($file));
// Doğrulama komutu
exec('/usr/local/bin/openssl ts -verify -data /tmp/' . $log_type . '.log -in /tmp/' . $log_type . '.log.der -token_in -CAfile /usr/local/openssl/CA/cacert.pem -untrusted /usr/local/openssl/CA/tsacert.pem', $result);

if ($result[0] == 'Verification: OK') {
	$imza = file_get_contents($log_type . '.log.imza');
	echo '<p class="success">Doğrulama başarılı!</p>';
	echo '<div>' . nl2br($imza) . '</div>';
}
else {
	echo '<p class="error">Doğrulama başarısız!</p>';
}

exec('rm ' . $log_type . '.log*');
exit;