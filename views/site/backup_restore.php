<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<div class="backup-restore-buttons">
    <!-- Tombol untuk Backup Database -->
    <?= Html::a('Backup Database', ['site/backup'], [
        'class' => 'btn btn-success',
        'data-confirm' => 'Are you sure you want to backup the database?',
        'data-method' => 'post',
    ]) ?>
</div>
<hr>
<h3>Backup History</h3>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Filename</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($files as $file): ?>
            <tr>
                <td><?= basename($file) ?></td>
                <td>
                    <?= Html::a('Download', ['site/download-backup', 'fileName' => basename($file)], [
                        'class' => 'btn btn-primary',
                    ]) ?>
                    <?= Html::a('Restore', ['site/restore-backup', 'fileName' => basename($file)], [
                        'class' => 'btn btn-warning',
                        'data-confirm' => 'Are you sure you want to restore this backup?',
                        'data-method' => 'post',
                    ]) ?>
                    <?= Html::a('Delete', ['site/delete-backup', 'fileName' => basename($file)], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this backup?',
                            'method' => 'post',
                        ],
                    ]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<hr>
<div class="upload-backup">
    <h3>Upload Backup File for Restore</h3>
    <?php $form = ActiveForm::begin([
        'action' => ['site/upload-backup'],
        'options' => ['enctype' => 'multipart/form-data']
    ]); ?>
    <?= $form->field($model, 'backupFile')->fileInput() ?>
    <div class="form-group">
        <?= Html::submitButton('Upload and Restore', ['class' => 'btn btn-danger']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
