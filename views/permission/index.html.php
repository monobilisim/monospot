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
		window.location = "<?=url_for('permissions')?>" + get;
	});
});

$(function() {
	$("#timestamp_1").change(function() {
		$("#timestamp_2").val($(this).val());
	});
});
</script>

<h2>İzin Raporu</h2>
<div>

	<table>
		<tr>
			<th><?=order_link('permissions', 'user_id', 'Kullanıcı ID')?></th>
			<th><?=order_link('permissions', 'gsm', 'GSM')?></th>
			<th><?=order_link('permissions', 'email', 'E-posta')?></th>
			<th>MAC Adresi</th>
			<th>IP Adresi</th>
			<th><?=order_link('permissions', 'timestamp', 'Tarih')?></th>
		</tr>
	<? foreach($permissions as $permission): ?>
		<tr>
			<td><a href="<?=url_for('user', $permission->user_id)?>"><?=$permission->user_id?></a></td>
			<td><?=$permission->gsm?></td>
			<td><?=$permission->email?></td>
			<td><?=$permission->mac?></td>
			<td><?=$permission->ip?></td>
			<td><?=format_date($permission->timestamp)?></td>
		</tr>
	<? endforeach; ?>
	</table>

	<div id="filter">
		<div class="item">
			GSM<br>
			<input type="text" class="small" name="gsm" value="<?=isset($get['gsm']) ? $get['gsm'] : ''?>">
		</div>
		<div class="item">
			E-posta<br>
			<input type="text" class="large" name="email" value="<?=isset($get['email']) ? $get['email'] : ''?>">
		</div>
		<div class="item">
			MAC Adresi<br>
			<input type="text" class="small" name="mac" value="<?=isset($get['mac']) ? $get['mac'] : ''?>">
		</div>
		<div class="item">
			IP Adresi<br>
			<input type="text" class="small" name="ip" value="<?=isset($get['ip']) ? $get['ip'] : ''?>">
		</div>
		<div class="item">
			Tarih<br>
			<input type="text" class="small date" id="timestamp_1" name="timestamp_1" value="<?=isset($get['timestamp_1']) ? $get['timestamp_1'] : ''?>"> -
		<input type="text" class="small date" id="timestamp_2" name="timestamp_2" value="<?=isset($get['timestamp_2']) ? $get['timestamp_2'] : ''?>">
		</div>
		<button type="button">Filtrele</button>
	</div>

	<?=$pager?>

</div>
