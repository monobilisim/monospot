<h2>Dil</h2>

<a href="<?=url_for('lang/tr')?>">TR</a> | 
<a href="<?=url_for('lang/en')?>">EN</a>

<form action="<?=url_for('lang/' . $code)?>" method="POST">
	<? foreach ($lang as $key => $val): ?>
	<p>
		<input type="text" style="width:800px" name="<?=$key?>" value="<?=$val?>">
	</p>
	<? endforeach; ?>
	<p>
		<input type="submit" value="Kaydet">
	</p>
</form>