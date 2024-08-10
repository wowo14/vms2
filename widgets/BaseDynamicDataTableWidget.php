<?php
namespace app\widgets;
use yii\widgets\InputWidget;
use yii\helpers\Html;
use yii\bootstrap4\Modal;
abstract class BaseDynamicDataTableWidget extends InputWidget{
    public $params;
    public $filterFields = [];
    public $authToken = null;
    public function run()
    {
        $this->authToken=$this->options['authToken']??null;
        $this->params=[
            'columnsJson' => json_encode($this->options['columns']),
            'ajaxUrl' => $this->options['ajaxUrl'],
            'authToken' => $this->authToken ? json_encode($this->authToken) : 'null',
            'multiple' => $this->options['multiple']?'true':'false',
            'defaultOrderJson' => json_encode($this->options['defaultOrder']??[1, 'asc']),
            'columnTarget' => json_encode($this->options['columnTarget']??1),
        ];
        $this->filterFields = $this->options['filterFields']??[];
        return $this->renderInput() . $this->renderModal();
    }
    protected function renderInput()
    {
        $inputId = $this->options['id'];
        return Html::activeTextInput($this->model, $this->attribute, [
            'readonly' => $this->options['readonly']??true,
            'value' => $this->value,
            'id' => $inputId,
            'class' => 'form-control'
        ]) . Html::hiddenInput($this->attribute, $this->value, ['id' => $inputId . '-hidden']);
    }
    protected function renderModal()
    {
        $filters = $this->renderFilters();
        $js = $this->getJs($this->params);
        $this->view->registerJs($js);
        ob_start();
        Modal::begin([
            'id' => 'data-modal-' . $this->id,
            'size' => Modal::SIZE_LARGE,
            'title' => $this->options['title'] ?? '',
            'footer' => Html::button('Apply', ['class' => 'btn btn-primary', 'id' => 'apply-selection-' . $this->id]),
        ]);
        echo '<div class="modal-body">' . $filters . '</div>';
        Modal::end();
        return ob_get_clean();
    }
    protected function renderFilters()
    {
        $filters = '';
        foreach ($this->filterFields as $field) {
            $filters .= Html::input('text', $field, '', [
                'class' => 'form-control filter-input',
                'placeholder' => $field,
                'name' => $field
            ]);
        }
        return $filters;
    }
    /*
     * $params[] = [
        'columnsJson' => $columnsJson,
        'ajaxUrl' => $ajaxUrl,
        'authToken' => $authToken,
        'multiple' => $multiple,
        'defaultOrderJson' => $defaultOrderJson,
        'columnTarget' => $columnTarget
     ]
     */
    protected abstract function getJs($params);
}
