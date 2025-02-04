<?php
?>
<table>
    <?php if ($model::isPPK() || $model::isAdmin()): ?>
        <tr>
            <td><a href="/dpp/penilaianppk?id=<?= $model->id ?>">Penilaian penyedia oleh PPK</a></td>
        </tr>
        <tr>
        <?php endif;
    if ($model::isPP() || $model::isAdmin()):
        ?>
        <tr>
            <td><a href="/dpp/penilaianolehpejabat?id=<?= $model->id ?>">Penilaian penyedia oleh Pejabat Pengadaan</a></td>
        </tr>
    <?php endif; ?>
</table>