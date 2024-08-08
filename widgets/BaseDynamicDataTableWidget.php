<?php
namespace app\widgets;
use yii\widgets\InputWidget;
use yii\helpers\Html;
use yii\bootstrap4\Modal;
abstract class BaseDynamicDataTableWidget extends InputWidget
{
    public $ajaxUrl;
    public $columns = [];
    public $filterFields = [];
    public $multiple = false;
    public $authToken = null;
    public $defaultOrder = [[1, 'asc']];
    public function run()
    {
        return $this->renderInput() . $this->renderModal();
    }
    protected function renderInput()
    {
        $inputId = $this->options['id'];
        return Html::activeTextInput($this->model, $this->attribute, [
            'readonly' => true,
            'value' => $this->value,
            'id' => $inputId,
            'class' => 'form-control'
        ]) . Html::hiddenInput($this->attribute, $this->value, ['id' => $inputId . '-hidden']);
    }
    protected function renderModal()
    {
        $modalId = 'data-modal-' . $this->id;
        $tableId = 'data-table-' . $this->id;
        $columnsJson = json_encode($this->columns);
        $ajaxUrl = $this->ajaxUrl;
        $authToken = $this->authToken ? json_encode($this->authToken) : 'null';
        $multiple = $this->multiple ? 'true' : 'false';
        $filters = $this->renderFilters();
        $defaultOrderJson = json_encode($this->defaultOrder);
        $js = $this->getJs($modalId, $tableId, $columnsJson, $ajaxUrl, $authToken, $multiple, $defaultOrderJson);
        $this->view->registerJs($js);
        ob_start();
        Modal::begin([
            'id' => $modalId,
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
    protected abstract function getJs($modalId, $tableId, $columnsJson, $ajaxUrl, $authToken, $multiple, $defaultOrderJson);
}
