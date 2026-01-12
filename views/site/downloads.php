<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $files array */

$this->title = 'Downloads';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-downloads">
    <h1 class="mb-4"><?= Html::encode($this->title) ?></h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (empty($files)): ?>
                <div class="alert alert-info">
                    Tidak ada file untuk diunduh saat ini.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" style="width: 50px;">#</th>
                                <th scope="col">Nama File</th>
                                <th scope="col" style="width: 150px;">Ukuran</th>
                                <th scope="col" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($files as $index => $file): ?>
                                <?php 
                                    $fileName = basename($file);
                                    $fileSize = Yii::$app->formatter->asShortSize(filesize($file));
                                ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <i class="fas fa-file-alt mr-2 text-primary"></i> 
                                        <?= Html::encode($fileName) ?>
                                    </td>
                                    <td><?= $fileSize ?></td>
                                    <td>
                                        <a href="<?= Url::to(['site/download-file', 'file' => $fileName]) ?>" class="btn btn-sm btn-success" data-pjax="0">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
