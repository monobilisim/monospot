<?
$action = empty($group->id) ? url_for('group/add') : url_for('group', $group->id, 'update');
?>

<script>
    $(function() {
        $("#expires").datetimepicker({
            dateFormat: "dd.mm.yy -",
            regional: "tr",
        });
    });
</script>

<h2><?= empty($group->id) ? "Grup Ekle" : "Grup Düzenle ID:".$group->id ?></h2>

<? if (!empty($errors)): ?>
    <ul class="errors">
        <? foreach($errors as $error): ?>
        <li><?=$error?>
            <? endforeach; ?>
    </ul>
<? endif; ?>

<form action="<?=$action?>" method="POST">
    <div id="group">
        <div class="item">
            <div class="label">Grup Adı</div>
            <input size="40" type="text" class="medium" name="group[name]" value="<?=$group->name?>">
        </div>
        <div class="item">
            <div class="label">MAC Adresleri</div>
            <textarea rows="20" cols="30" name="group[macs]"><?=$group->macs?></textarea>
        </div>
    </div>

    <h4>Grup Ayarları</h4>
    <?php
    if ($_POST) {
        $settings = $_POST;
    } else {
        global $settings;
    }
    ?>

    <? include __DIR__ . '/../settings_form_inner.html.php'; ?>
</form>
