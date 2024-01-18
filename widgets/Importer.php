<?php
namespace app\widgets;
use yii\base\Widget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
class Importer extends Widget{
    public $searchModel;
    public $action;
    public $buttonlabel;
    public function init(){
        parent::init();
    }
    public function run(){
        $array=explode('\\',get_class($this->searchModel));
        $modelClasses=str_replace('Search','',end($array));
        $modellower=strtolower($modelClasses);
          $form = ActiveForm::begin([
            'options'=>['enctype'=>'multipart/form-data'],
            ]);
          $form->action=empty($this->action)?['import']:$this->action;
          echo '<div class=""><input type="file" id="'.$modellower.'-file" name="'.$modelClasses.'[file]">'.Html::submitButton(empty($this->buttonlabel)?'import':$this->buttonlabel, ['class' => 'btn btn-success']).'</div>';
          ActiveForm::end();
    }
}
?>