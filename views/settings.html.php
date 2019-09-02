<h2>Ayarlar</h2>

<? if (!empty($errors)): ?>
    <ul class="errors">
        <? foreach($errors as $error): ?>
        <li><?=$error?>
            <? endforeach; ?>
    </ul>
<? endif; ?>

<form action="<?=url_for('settings')?>" method="POST">
    <? include 'settings_form_inner.html.php'; ?>
</form>
