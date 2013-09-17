<script>
	$(function() {
		$(".date").datepicker({
		dateFormat: "dd.mm.yy",
		regional: "tr",
	});
	$("button").click(function() {
		var items = $("#filter").find("input");
		var get = "";
		for (var i=0; i < items.length; i++) {
			if (items[i].value.length > 0)
				get += "&" + items[i].name + "=" + items[i].value;
		}
		window.location = "<?=url_for('users')?>" + get;
	});
	});
</script>

<h2>Kullanıcı Listesi</h2>
<p><a href="<?=url_for('user/add')?>">Kullanıcı Ekle</a></p>

<div id="list">
	
	<table>
		<tr>
		<? if ($settings['authentication'] == 'id_number' || $settings['authentication'] == 'id_number_passport' || isset($settings['fields']['id_number'])): ?>
			<th rowspan="<?=$rowspan?>"><?=order_link('users', 'name', 'Ad')?></th>
			<th rowspan="<?=$rowspan?>"><?=order_link('users', 'surname', 'Soyad')?></th>
			<th rowspan="<?=$rowspan?>"><?=order_link('users', 'id_number', 'TC Kimlik No')?></th>
		<? endif; ?>
		<? if ($settings['authentication'] == 'sms'): ?>
			<th rowspan="2"><?=order_link('users', 'gsm', 'GSM')?></th>
			<th rowspan="2"><?=order_link('users', 'last_sms', 'Son Şifre Alma')?></th>
			<th colspan="4">SMS Limiti</th>
		<? endif; ?>
		<? if ($settings['authentication'] == 'manual_password'): ?>
			<th rowspan="<?=$rowspan?>"><?=order_link('users', 'username', $settings['username'])?></th>
		<? endif; ?>
		<? if ($settings['authentication'] == 'id_number_passport'): ?>
			<th rowspan="<?=$rowspan?>"><?=order_link('users', 'username', 'Pasaport No')?></th>
		<? endif; ?>
			<th rowspan="<?=$rowspan?>"><?=order_link('users', 'last_login', 'Son Oturum Açma')?></th>
			<th rowspan="<?=$rowspan?>"><?=order_link('users', 'expires', 'Şifre Son Geçerlilik')?></th>
			<th rowspan="<?=$rowspan?>">İşlem</th>
		</tr>
	<? if ($settings['authentication'] == 'sms'): ?>
		<tr>
			<th><?=order_link('users', 'daily_limit', 'Günlük')?></th>
			<th><?=order_link('users', 'weekly_limit', 'Haftalık')?></th>
			<th><?=order_link('users', 'monthly_limit', 'Aylık')?></th>
			<th><?=order_link('users', 'yearly_limit', 'Yıllık')?></th>
		</tr>
	<? endif; ?>
	<? foreach($users as $user): ?>
		<tr>
		<? if ($settings['authentication'] == 'id_number' || $settings['authentication'] == 'id_number_passport' || isset($settings['fields']['id_number'])): ?>
			<td class="left"><?=$user->name?></td>
			<td class="left"><?=$user->surname?></td>
			<td><?=$user->id_number?></td>
		<? endif; ?>
		<? if ($settings['authentication'] == 'sms'): ?>
			<td><?=$user->gsm?></td>
			<td><?=format_date($user->last_sms)?></td>
			<td><?=$user->sms_day.'/'.$user->daily_limit?></td>
			<td><?=$user->sms_week.'/'.$user->weekly_limit?></td>
			<td><?=$user->sms_month.'/'.$user->monthly_limit?></td>
			<td><?=$user->sms_year.'/'.$user->yearly_limit?></td>
		<? endif; ?>
		<? if ($settings['authentication'] == 'manual_password' || $settings['authentication'] == 'id_number_passport'): ?>
			<td><?=$user->username?></td>
		<? endif; ?>
			<td><?=format_date($user->last_login)?></td>
			<td><?=format_date($user->expires)?></td>
			<td>
				<a href="<?=url_for('user', $user->id)?>">Görüntüle</a> | 
				<a href="<?=url_for('user', $user->id, 'update')?>">Düzenle</a> | 
				<a href="<?=url_for('user', $user->id, 'delete')?>" onclick="if (confirm('Emin misiniz?')) { return true; } return false;">Sil</a>
			</td>
		</tr>
	<? endforeach?>
	</table>

	<?=$pager?>

</div>

<div id="filter">
<? if ($settings['authentication'] == 'id_number' || $settings['authentication'] == 'id_number_passport' || isset($settings['fields']['id_number'])): ?>
	<div class="item">
		Ad<br>
		<input type="text" class="large" name="name" value="<?=isset($get['name']) ? $get['name'] : ''?>">
	</div>
	<div class="item">
		Soyad<br>
		<input type="text" class="large" name="surname" value="<?=isset($get['surname']) ? $get['surname'] : ''?>">
	</div>
	<div class="item">
		TC Kimlik No<br>
		<input type="text" class="large" name="id_number" value="<?=isset($get['id_number']) ? $get['id_number'] : ''?>">
	</div>
<? endif; ?>
<? if ($settings['authentication'] == 'sms'): ?>
	<div class="item">
		GSM<br>
		<input type="text" class="small" name="gsm" value="<?=isset($get['gsm']) ? $get['gsm'] : ''?>">
	</div>
	<div class="item">
		Son Şifre Alma Tarihi<br>
		<input type="text" class="small date" name="last_sms" value="<?=isset($get['last_sms']) ? $get['last_sms'] : ''?>">	
	</div>
<? endif; ?>
<? if ($settings['authentication'] == 'manual_password'): ?>
	<div class="item">
		<?=$settings['username']?><br>
		<input type="text" class="large" name="username" value="<?=isset($get['username']) ? $get['username'] : ''?>">
	</div>
<? endif; ?>
	<div class="item">
		Şifre Son Geçerlilik Tarihi<br>
		<input type="text" class="small date" name="expires" value="<?=isset($get['expires']) ? $get['expires'] : ''?>">	
	</div>
	<button type="button">Filtrele</button>
</div>