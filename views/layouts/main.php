<?php
use yii\helpers\Url;
if (empty(Yii::$app->session->get('userData'))) {
    Yii::$app->user->logout();
    return (Url::to(['site/login']));
}
use yii\helpers\Html;
\hail812\adminlte3\assets\FontAwesomeAsset::register($this);
\hail812\adminlte3\assets\AdminLteAsset::register($this);
$this->registerCssFile('https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback');
\hail812\adminlte3\assets\PluginAsset::register($this)->add(['sweetalert2', 'toastr','popper']);
$assetDir = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
$publishedRes = Yii::$app->assetManager->publish('@vendor/hail812/yii2-adminlte3/src/web/js');
$this->registerJsFile($publishedRes[1].'/control_sidebar.js', ['depends' => '\hail812\adminlte3\assets\AdminLteAsset']);
$base = Url::home(true);
$soundpath = $base . 'uploads/notif.mp3';
$this->registerJs('
const baseurl="'.$base.'";
', yii\web\View::POS_HEAD);
$js=<<< JS
$(document).ready(function() {
var notif1=0;
var totalnotif1=0;
    $(".toast").toast('show');
    $(".alert").animate({opacity: 1.0}, 5000).fadeOut("slow");
    document.onkeyup = KeyCheck;
    function KeyCheck(e) {
        var KeyID = window.event ? event.keyCode : e.keyCode;
        if (KeyID == 113) { // F2 key
            $('a[href*="create"]').click();
        }
        if (KeyID == 27) {// esc key
            $("button[data-dismiss]").click();
        }
    }
    function notifbaru(){
        $.ajax({
            url:baseurl+"site/notif",
            method:"GET",
            dataType:"json",
            success:function(data){
                $("#count_notifbaru").html(data.paketbaru);
                if(notif1!=data.paketbaru){
                    totalnotif1+=data.paketbaru;
                    }
                notif1=data.paketbaru;
            }
        })
    }
    function play(){
        var audio = document.createElement("audio");
        audio.src = "' . $soundpath . '";
        audio.autoplay = true;
        audio.play();
    }
    setInterval(function(){
        notifbaru();
        if(totalnotif1!=notif1){
            // play();
            totalnotif1=notif1;
        }
    },6600);
});
JS;
$this->registerJs($js, yii\web\View::POS_END);
$this->registerCssFile('/css/site.css');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="hold-transition sidebar-mini">
<?php $this->beginBody() ?>
<div class="wrapper">
    <!-- Navbar -->
    <?= $this->render('navbar', ['assetDir' => $assetDir]) ?>
    <!-- /.navbar -->
    <!-- Main Sidebar Container -->
    <?= $this->render('sidebar', ['assetDir' => $assetDir]) ?>
    <!-- Content Wrapper. Contains page content -->
    <?= $this->render('content', ['content' => $content, 'assetDir' => $assetDir]) ?>
    <!-- /.content-wrapper -->
    <!-- Control Sidebar -->
    <?= $this->render('control-sidebar') ?>
    <!-- /.control-sidebar -->
    <!-- Main Footer -->
    <?= $this->render('footer') ?>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
