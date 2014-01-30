<h2>Kullanıcı ID:<?=$user->id?></h2>

	<div id="user">
	<p>
		<div class="label">TC Kimlik No</div>
		<span class="value"><?=$user->id_number?></span>
	</p>
	<p>
		<div class="label">Ad</div>
		<span class="value"><?=$user->name?></span>
	</p>
	<p>
		<div class="label">Soyad</div>
		<span class="value"><?=$user->surname?></span>
	</p>
<? if (isset($settings['authentication']['sms'])): ?>
	<p>
		<div class="label">GSM</div>
		<span class="value"><?=$user->gsm?></span>
	</p>
	<p>
		<div class="label">Son Şifre Alma Tarihi</div>
		<span class="value"><?=format_date($user->last_sms)?></span>
	</p>
	<p>
		<div class="label">Günlük SMS Limiti</div>
		<span class="value"><?=$user->daily_limit?></span>
	</p>
	<p>
		<div class="label">Haftalık SMS Limiti</div>
		<span class="value"><?=$user->weekly_limit?></span>
	</p>
	<p>
		<div class="label">Aylık SMS Limiti</div>
		<span class="value"><?=$user->monthly_limit?></span>
	</p>
	<p>
		<div class="label">Yıllık SMS Limiti</div>
		<span class="value"><?=$user->yearly_limit?></span>
	</p>
<? endif; ?>
<? if (isset($settings['authentication']['manual_user'])): ?>
	<p>
		<div class="label">Kullanıcı adı</div>
		<span class="value"><?=$user->username?></span>
	</p>
<? endif; ?>
	<p>
		<div class="label">Son Oturum Açma Tarihi</div>
		<span class="value"><?=format_date($user->last_login)?></span>
	</p>
<? if (isset($settings['authentication']['sms']) || isset($settings['authentication']['manual_user'])): ?>
	<p>
		<div class="label">Şifre Son Geçerlilik Tarihi</div>
		<span class="value"><?=format_date($user->expires)?></span>
	</p>
<? endif; ?>
	<p>
		<a href="<?= url_for('user', $user->id, 'update'); ?>">Düzenle</a> |
		<a href="<?= url_for('user', $user->id, 'delete'); ?>" onclick="if (confirm('Emin misiniz?')) { return true; } return false;">Sil</a>
	</p>
</div>