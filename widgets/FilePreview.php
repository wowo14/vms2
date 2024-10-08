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
        if (!file_exists(Yii::getAlias('@uploads/') . $this->model->{$this->attribute})) {
            echo 'File not found';
            return;
        }
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        if ($extension === 'pdf') {
            $containerId = md5($this->model->{$this->attribute}); // Generate unique container ID
            echo "<div id=\"$containerId\" style=\"width:100%\" height=\"600\"></div>";
            $jsScript = $this->generatePdfViewerScript($file, $containerId); // Pass container ID
            $view->registerJs($jsScript, \yii\web\View::POS_END);
        } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'avif', 'webp'])) {
            echo Html::a(
                Html::img(Url::to($file), array_merge(['width' => '100%', 'height' => 'auto', 'class' => 'img-fluid'], $this->options)),
                Url::to($file),
                ['target' => '_blank']
            );
        } else {
            echo 'Unsupported file type';
        }
    }
    private function generatePdfViewerScript($file, $containerId) {
        $pdfFile = Url::to([$file], true);
        return <<<JS
        var pdfFile{$containerId} = "{$pdfFile}";
        var pdfViewer{$containerId} = document.getElementById("$containerId");
        var initialPage{$containerId} = 1;
        var loadingTask{$containerId}= pdfjsLib.getDocument(pdfFile{$containerId});
        loadingTask{$containerId}.promise.then(function(pdf) {
                pdf.getPage(initialPage{$containerId}).then(function(page) {
                    var scale = 1;
                    var viewport = page.getViewport({ scale: scale });
                    var canvas = document.createElement("canvas");
                    var context = canvas.getContext("2d");
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;
                    // set canvas style = width:inherit
                    canvas.style = "width:inherit";
                    pdfViewer{$containerId}.appendChild(canvas);
                    page.render({ canvasContext: context, viewport: viewport });
                });
        });
    JS;
    }
}
