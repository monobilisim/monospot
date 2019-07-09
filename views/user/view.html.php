<h2>Kullanıcı ID:<?=$user->id?></h2>

	<div id="user">
<? if ($settings['custom_fields']): ?>
	<? foreach (explode("\n", $settings['custom_fields']) as $field): ?>
		<? $field = explode('|', $field); ?>
		<? if ($field[0] != 'gsm'): ?>
		<div class="item">
			<div class="label"><?=$field[1]?></div>
			<span class="value"><?=$user->{$field[0]}?></span>
		</div>
		<? endif; ?>
	<? endforeach; ?>
<? endif; ?>
<? if (isset($settings['authentication']['id_number']) || isset($settings['sms']['id_number'])): ?>
	<div class="item">
		<div class="label">Ad</div>
		<span class="value"><?=$user->name?></span>
	</div>
	<div class="item">
		<div class="label">Soyad</div>
		<span class="value"><?=$user->surname?></span>
	</div>
	<div class="item">
		<div class="label">TC Kimlik No</div>
		<span class="value"><?=$user->id_number?></span>
	</div>
<? endif; ?>
	<div class="item">
		<div class="label">E-posta</div>
		<span class="value"><?=$user->email?></span>
	</div>
<? if (isset($settings['authentication']['sms']) || strpos($settings['custom_fields'], 'gsm') !== false): ?>
	<div class="item">
		<div class="label">GSM</div>
		<span class="value"><?=$user->gsm?></span>
	</div>
	<div class="item">
		<div class="label">Son Şifre Alma Tarihi</div>
		<span class="value"><?=format_date($user->last_sms)?></span>
	</div>
	<div class="item">
		<div class="label">Günlük SMS Limiti</div>
		<span class="value"><?=$user->daily_limit?></span>
	</div>
	<div class="item">
		<div class="label">Haftalık SMS Limiti</div>
		<span class="value"><?=$user->weekly_limit?></span>
	</div>
	<div class="item">
		<div class="label">Aylık SMS Limiti</div>
		<span class="value"><?=$user->monthly_limit?></span>
	</div>
	<div class="item">
		<div class="label">Yıllık SMS Limiti</div>
		<span class="value"><?=$user->yearly_limit?></span>
	</div>
<? endif; ?>
<? if (isset($settings['authentication']['manual_user'])): ?>
	<div class="item">
		<div class="label">Kullanıcı adı</div>
		<span class="value"><?=$user->username?></span>
	</div>
<? endif; ?>
	<div class="item">
		<div class="label">Şifre Son Geçerlilik Tarihi</div>
		<span class="value"><?=format_date($user->expires)?></span>
	</div>
	<div class="item">
		<div class="label">Son Oturum Açma Tarihi</div>
		<span class="value"><?=format_date($user->last_login)?></span>
	</div>
	<div class="item">
		<div class="label">GSM İzin</div>
		<span class="value"><?=$user->gsm_permission === '1' ? 'Evet' : ($user->gsm_permission === '0' ? 'Hayır' : '')?></span>
	</div>
	<div class="item">
		<div class="label">E-posta İzin</div>
		<span class="value"><?=$user->email_permission === '1' ? 'Evet' : ($user->email_permission === '0' ? 'Hayır' : '')?></span>
	</div>
	<div class="actions">
		<a href="<?= url_for('user', $user->id, 'update'); ?>">Düzenle</a> |
		<a href="<?= url_for('user', $user->id, 'delete'); ?>" onclick="if (confirm('Emin misiniz?')) { return true; } return false;">Sil</a>
	</div>
</div>
