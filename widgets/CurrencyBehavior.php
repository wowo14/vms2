<?php
namespace app\widgets;
use yii\base\Behavior;
use yii\db\ActiveRecord;
class CurrencyBehavior extends Behavior
{
    public $attributes = []; // Accepts multiple attributes
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'formatCurrencyToFloat',
            ActiveRecord::EVENT_AFTER_FIND => 'formatCurrencyFromFloat',
        ];
    }
    public function formatCurrencyToFloat()
    {
        foreach ($this->attributes as $attribute) {
            if (!empty($this->owner->$attribute)) {
                // Convert currency to float (assuming Indonesian format)
                $this->owner->$attribute = $this->currencyToFloat($this->owner->$attribute);
            }
        }
    }
    public function formatCurrencyFromFloat()
    {
        foreach ($this->attributes as $attribute) {
            if (!empty($this->owner->$attribute)) {
                // Convert float back to currency format
                $this->owner->$attribute = \Yii::$app->formatter->asCurrency($this->owner->$attribute);
            }
        }
    }
    protected function currencyToFloat($currency)
    {
        return \Yii::$app->tools->reverseCurrency($currency);
        // $normalized = preg_replace('/[Rp\s,.]/', '', $currency);
        // return (float) str_replace(',', '.', $normalized);
    }
}
