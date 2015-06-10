<script>
	$(function() {
		$(".date").datepicker({
			dateFormat: "dd.mm.yy",
			regional: "tr",
		});
	});
</script>

<h2>Kullanıcı Listesi</h2>
<p><a href="<?=url_for('user/add')?>">Kullanıcı Ekle</a></p>

<div>

	<table>
		<tr>
		<? if ($settings['custom_fields']): ?>
			<? foreach (explode("\n", $settings['custom_fields']) as $field): ?>
				<? $field = explode('|', $field); ?>
				<? if ($field[0] != 'gsm'): ?>
				<th rowspan="<?=$rowspan?>"><?=order_link('users', $field[0], $field[1])?></th>
				<? endif; ?>
			<? endforeach; ?>
		<? endif; ?>
		<? if (isset($settings['authentication']['id_number']) || isset($settings['sms']['id_number'])): ?>
			<th rowspan="<?=$rowspan?>"><?=order_link('users', 'name', 'Ad')?></th>
			<th rowspan="<?=$rowspan?>"><?=order_link('users', 'surname', 'Soyad')?></th>
			<th rowspan="<?=$rowspan?>"><?=order_link('users', 'id_number', 'TC Kimlik No')?></th>
		<? endif; ?>
		<? if (isset($settings['authentication']['sms']) || strpos($settings['custom_fields'], 'gsm') !== false): ?>
			<th rowspan="2"><?=order_link('users', 'gsm', 'GSM')?></th>
			<th rowspan="2"><?=order_link('users', 'last_sms', 'Son Şifre Alma')?></th>
			<th colspan="4">SMS Limiti</th>
		<? endif; ?>
		<? if (isset($settings['authentication']['manual_user'])): ?>
			<th rowspan="<?=$rowspan?>"><?=order_link('users', 'username', 'Kullanıcı adı')?></th>
		<? endif; ?>
			<th rowspan="<?=$rowspan?>"><?=order_link('users', 'expires', 'Şifre Son Geçerlilik')?></th>
			<th rowspan="<?=$rowspan?>"><?=order_link('users', 'last_login', 'Son Oturum Açma')?></th>
			<th rowspan="<?=$rowspan?>">GSM İzin</th>
			<th rowspan="<?=$rowspan?>">E-posta İzin</th>
			<th rowspan="<?=$rowspan?>" style="width:140px">İşlem</th>
		</tr>
	<? if (isset($settings['authentication']['sms'])): ?>
		<tr>
			<th><?=order_link('users', 'daily_limit', 'Günlük')?></th>
			<th><?=order_link('users', 'weekly_limit', 'Haftalık')?></th>
			<th><?=order_link('users', 'monthly_limit', 'Aylık')?></th>
			<th><?=order_link('users', 'yearly_limit', 'Yıllık')?></th>
		</tr>
	<? endif; ?>

	<? foreach($users as $user): ?>
		<tr>
		<? if ($settings['custom_fields']): ?>
			<? foreach (explode("\n", $settings['custom_fields']) as $field): ?>
				<? $field = explode('|', $field); ?>
				<? if ($field[0] != 'gsm'): ?>
				<td><?=$user->$field[0]?></td>
				<? endif; ?>
			<? endforeach; ?>
		<? endif; ?>
		<? if (isset($settings['authentication']['id_number']) || isset($settings['sms']['id_number'])): ?>
			<td class="left"><?=$user->name?></td>
			<td class="left"><?=$user->surname?></td>
			<td><?=$user->id_number?></td>
		<? endif; ?>
		<? if (isset($settings['authentication']['sms'])): ?>
			<td><?=$user->gsm?></td>
			<td><?=format_date($user->last_sms)?></td>
			<td><?=$user->sms->day ? $user->sms->day.'/'.$user->daily_limit : ''?></td>
			<td><?=$user->sms->week ? $user->sms->week.'/'.$user->weekly_limit : ''?></td>
			<td><?=$user->sms->month ? $user->sms->month.'/'.$user->monthly_limit : ''?></td>
			<td><?=$user->sms->year ? $user->sms->year.'/'.$user->yearly_limit : ''?></td>
		<? endif; ?>
		<? if (isset($settings['authentication']['manual_user'])): ?>
			<td><?=$user->username?></td>
		<? endif; ?>
			<td><?=format_date($user->expires)?></td>
			<td><?=format_date($user->last_login)?></td>
			<td><?=$user->gsm_permission === '1' ? 'Evet' : ($user->gsm_permission === '0' ? 'Hayır' : '')?></td>
			<td><?=$user->email_permission === '1' ? 'Evet' : ($user->email_permission === '0' ? 'Hayır' : '')?></td>
			<td>
				<a href="<?=url_for('user', $user->id)?>">Görüntüle</a> |
				<a href="<?=url_for('user', $user->id, 'update')?>">Düzenle</a> |
				<a href="<?=url_for('user', $user->id, 'delete')?>" onclick="if (confirm('Emin misiniz?')) { return true; } return false;">Sil</a>
			</td>
		</tr>
	<? endforeach?>
	</table>

	<div id="filter">
		<form action="" method="GET">
		<input type="hidden" name="filter" value="1">
	<? if (isset($settings['authentication']['id_number']) || isset($settings['sms']['id_number'])): ?>
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
	<? if (isset($settings['authentication']['sms'])): ?>
		<div class="item">
			GSM<br>
			<input type="text" class="small" name="gsm" value="<?=isset($get['gsm']) ? $get['gsm'] : ''?>">
		</div>
		<div class="item">
			Son Şifre Alma Tarihi<br>
			<input type="text" class="small date" name="last_sms" value="<?=isset($get['last_sms']) ? $get['last_sms'] : ''?>">
		</div>
	<? endif; ?>
	<? if (isset($settings['authentication']['manual_user'])): ?>
		<div class="item">
			Kullanıcı adı<br>
			<input type="text" class="large" name="username" value="<?=isset($get['username']) ? $get['username'] : ''?>">
		</div>
	<? endif; ?>
	<? if ($settings['custom_fields']): ?>
		<? foreach (explode("\n", $settings['custom_fields']) as $field): ?>
			<? $field = explode('|', $field); ?>
				<div class="item">
				<?=$field[1]?><br>
				<input type="text" class="large" name="<?=$field[0]?>" value="<?=isset($get[$field[0]]) ? $get[$field[0]] : ''?>">
		<? endforeach; ?>
	<? endif; ?>
		<div class="item">
			Şifre Son Geçerlilik Tarihi<br>
			<input type="text" class="small date" name="expires" value="<?=isset($get['expires']) ? $get['expires'] : ''?>">
		</div>
		<div class="item">
			GSM İzin<br>
			<select name="gsm_permission">
				<option value=""></option>
				<option value="1"<?=isset($get['gsm_permission']) && $get['gsm_permission'] === '1' ? ' selected="selected"' : ''?>>Evet</option>
				<option value="0"<?=isset($get['gsm_permission']) && $get['gsm_permission'] === '0' ? ' selected="selected"' : ''?>>Hayır</option>
			</select>
		</div>
		<div class="item">
			E-posta İzin<br>
			<select name="email_permission">
				<option value=""></option>
				<option value="1"<?=isset($get['email_permission']) && $get['email_permission'] === '1' ? ' selected="selected"' : ''?>>Evet</option>
				<option value="0"<?=isset($get['email_permission']) && $get['email_permission'] === '0' ? ' selected="selected"' : ''?>>Hayır</option>
			</select>
		</div>
		<input type="submit" value="Filtrele">
		</form>
	</div>

	<?=$pager?>

</div>