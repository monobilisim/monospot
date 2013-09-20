<!DOCTYPE html>
<html>
<head>	
	<meta charset="utf-8">
	<title><?=$hotspot['marka']?></title>
	<link href="assets/css/style.css" rel="stylesheet" />
	<link href="assets/css/smoothness/jquery-ui-1.10.1.custom.min.css" rel="stylesheet" />
	<script src="assets/js/jquery-1.9.1.js"></script>
	<script src="assets/js/jquery-ui-1.10.1.custom.min.js"></script>
	<script src="assets/js/jquery.ui.datetimepicker-tr.js"></script>
	<script src="assets/js/jquery-ui-timepicker-addon.js"></script>
</head>
<body>
	<div class="container">
	
	<h1><?=$hotspot['marka']?> Yönetim Paneli</h1>
	
	<?php
	if (date('Y', strtotime($hotspot['demo_bitis'])) != '2010')
		echo '<div style="color:#e00;font-weight:bold">Hotspot demo süresi ' . $hotspot['demo_bitis'] . ' tarihinde dolacaktır.</div>';
	?>
	
	<div id="nav">
		<a href="<?=url_for('users')?>">Kullanıcı Listesi</a> | 
	<? if ($settings['authentication'] == 'sms'): ?>
		<a href="<?=url_for('sms')?>">SMS Raporu</a> | 
	<? endif; ?>
		<a href="<?=url_for('settings')?>">Ayarlar</a> | 
		<a href="/<?=$hotspot['marka_url']?>/log_browser/">Loglar</a> | 
		<a href="/index.php?logout">Çıkış</a>
	</div>
	
	<? if (isset($_SESSION['message'])): ?>
		<div class="message"><?=$_SESSION['message']?></div>
	<? unset($_SESSION['message']); endif; ?>
		
	<div class="content">
		<?= $content; ?>
	</div>
	
	<footer>
		<p>© <?=$hotspot['firma']?> 2013</p>
	</footer>
	
	</div>
</body>
</html>