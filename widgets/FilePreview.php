<?php
namespace app\widgets;
use app\assets\AppAsset;
use Yii;
use yii\base\Widget;
use yii\helpers\{Html, Url};
class FilePreview extends Widget {
    public $model; // The model containing the file
    public $attribute; // The attribute for the file
    public $options = []; // Additional options for the rendered element
    public function run() {
        $view = $this->getView();
        AppAsset::register($view, ['position' => \yii\web\View::POS_BEGIN]);
        $file = Yii::getAlias('@web/uploads/') . $this->model->{$this->attribute};
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        if ($extension === 'pdf') {
            echo '<div id="pdf-viewer-container" width="100%" height="600"></div>';
            $jsScript = $this->generatePdfViewerScript($file);
            $view->registerJs($jsScript, \yii\web\View::POS_END);
        } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            echo Html::a(
                Html::img(Url::to([$file]), array_merge(['width' => '100%', 'height' => 'auto'], $this->options)),
                Url::to($file),
                ['target' => '_blank']
            );
        } else {
            echo 'File not found / Unsupported file type';
        }
    }
    private function generatePdfViewerScript($file) {
        $pdfFile = Url::to([$file], true);
        return <<<JS
        var pdfViewer = document.getElementById("pdf-viewer-container");
        var pdfFile = "{$pdfFile}";
        var initialPage = 1;
        var loadingTask = pdfjsLib.getDocument(pdfFile);
        loadingTask.promise.then(function(pdf) {
            pdf.getPage(initialPage).then(function(page) {
                var scale = 0.55;
                var viewport = page.getViewport({ scale: scale });
                var canvas = document.createElement("canvas");
                var context = canvas.getContext("2d");
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                pdfViewer.appendChild(canvas);
                page.render({ canvasContext: context, viewport: viewport });
            });
        });
    JS;
    }
}
