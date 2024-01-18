<?php
namespace app\widgets;
// use hail812\adminlte\assets\PluginAsset;
use yii\bootstrap4\Toast;
use yii\bootstrap4\Widget;
class FlashToast extends Widget
{
    public $alertTypes = [
        'error'   => 'alert-danger',
        'danger'  => 'alert-danger',
        'success' => 'alert-success',
        'info'    => 'alert-info',
        'warning' => 'alert-warning',
    ];
    public $closeButton = [];
    public function run()
    {
        $session = \Yii::$app->session;
        $flashes = $session->getAllFlashes();
        $appendClass = isset($this->options['class']) ? ' ' . $this->options['class'] : '';
        foreach ($flashes as $type => $flash) {
            if (!isset($this->alertTypes[$type])) {
                continue;
            }
            foreach ((array)$flash as $i => $message) {
                $options = array_merge($this->options, [
                    'id' => $this->getId() . '-' . $type . '-' . $i,
                    'class' => $this->alertTypes[$type] . $appendClass,
                ]);
                // Use FlashToast instead of Alert
                echo Toast::widget([
                    // 'options' => $options,
                    // 'type' => $type,
                    // 'title' => ucfirst($type), // You can customize the title as needed
                    // 'message' => $message,
                    'id'=>'myToast',
                    'title' => '',
                    'dateTime' => 'now',
                    'body' => $message,
                ]);
            }
            $session->removeFlash($type);
        }
        // Register Toastr assets
        // PluginAsset::register($this->getView());
    }
}
