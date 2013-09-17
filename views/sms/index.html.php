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
		window.location = "<?=url_for('sms')?>" + get;
	});
});
  
$(function() {
	$("#timestamp_1").change(function() {
		$("#timestamp_2").val($(this).val());
	});
});
</script>

<h2>SMS Raporu</h2>
<div id="list">
  
  <table>
    <tr>
      <th><?=order_link('sms', 'gsm', 'Kullanıcı GSM')?></th>
      <th>MAC Adresi</th>
      <th><?=order_link('sms', 'timestamp', 'Tarih')?></th>
    </tr>
  <? foreach($smss as $sms): ?>
    <tr>
      <td><a href="<?=url_for('user', $sms->user_id)?>"><?=$sms->gsm?></a></td>
      <td><?=$sms->mac?></td>
      <td><?=format_date($sms->timestamp)?></td>
    </tr>
  <? endforeach; ?>
  </table>
  
  <?=$pager?>
  
</div>

  <div id="filter">
  <div class="item">
    Kullanıcı GSM<br>
    <input type="text" class="small" name="gsm" value="<?=isset($get['gsm']) ? $get['gsm'] : ''?>">
  </div>
  <div class="item">
    MAC Adresi<br>
    <input type="text" class="small" name="mac" value="<?=isset($get['mac']) ? $get['mac'] : ''?>">
  </div>
  <div class="item">
    Tarih<br>
    <input type="text" class="small date" id="timestamp_1" name="timestamp_1" value="<?=isset($get['timestamp_1']) ? $get['timestamp_1'] : ''?>"> - 
	<input type="text" class="small date" id="timestamp_2" name="timestamp_2" value="<?=isset($get['timestamp_2']) ? $get['timestamp_2'] : ''?>">
  </div>
  <button type="button">Filtrele</button>
</div>
