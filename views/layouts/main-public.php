<?php
use app\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
\yii\bootstrap4\BootstrapAsset::register($this);
\yii\bootstrap4\BootstrapPluginAsset::register($this);
\hail812\adminlte3\assets\FontAwesomeAsset::register($this);

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .navbar-custom {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            padding: 1rem 2rem;
        }
        .navbar-brand {
            font-weight: 700;
            color: #2d3436 !important;
            letter-spacing: -0.5px;
        }
        .btn-premium {
            background-color: #0d6efd;
            color: white;
            border-radius: 50px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: none;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.2);
        }
        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(13, 110, 253, 0.3);
            color: white;
        }
        footer {
            background: #2d3436;
            color: #dfe6e9;
            padding: 4rem 0 2rem;
            margin-top: 4rem;
        }
        .footer-logo {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            color: white;
        }
    </style>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column min-vh-100">
<?php $this->beginBody() ?>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top navbar-custom">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?= Url::to(['/dasarhukum/gallery']) ?>">
                <div class="bg-primary text-white p-2 rounded mr-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-file-contract"></i>
                </div>
                <span>DASAR HUKUM PBJ</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link px-3" href="<?= Url::to(['/dasarhukum/gallery']) ?>">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-premium ml-md-3 mt-3 mt-md-0 d-inline-block" href="<?= Url::to(['/site/login']) ?>">LOGIN ADMIN</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-4 mb-md-0">
                    <div class="footer-logo mb-2">Portal Dasar Hukum</div>
                    <p class="text-muted small mb-0">Platform legal informasi Pengadaan Barang & Jasa Pemerintah.</p>
                </div>
                <div class="col-md-6 text-md-right">
                    <p class="small mb-0">&copy; <?= date('Y') ?> VMS Portal. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
