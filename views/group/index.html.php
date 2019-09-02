<h2>Grup Listesi</h2>
<p><a href="<?=url_for('group/add')?>">Grup Ekle</a></p>

<div>
    <table>
        <tr>
            <th>ID</th>
            <th>İsim</th>
            <th colspan="2">İşlemler</th>
        </tr>
        <? foreach($groups as $group): ?>
            <tr>
                <td><?=$group->id?></td>
                <td><?=$group->name?></td>
                <td><a href="<?=url_for('group', $group->id)?>/edit">Düzenle</a></td>
                <td><a href="<?=url_for('group', $group->id)?>/delete">Sil</a></td>
            </tr>
        <? endforeach; ?>
    </table>
</div>
