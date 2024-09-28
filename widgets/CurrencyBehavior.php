<?php
namespace app\widgets;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use Yii;
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
            // Yii::error('to float');
                $this->owner->$attribute = $this->currencyToFloat($this->owner->$attribute);
            }
        }
    }
    public function formatCurrencyFromFloat()
    {
        foreach ($this->attributes as $attribute) {
            $value = $this->owner->$attribute;
            if (!empty($value) && is_numeric($value)) {
                $floatValue = (float) $value;
                if (is_finite($floatValue)) {
                    // Yii::error($value);
                    // Yii::error('to currency');
                    $this->owner->$attribute = \Yii::$app->formatter->asCurrency($floatValue);
                }
            }
        }
    }
    protected function currencyToFloat($currency)
    {
        // Yii::error($currency);
        return Yii::$app->tools->reverseCurrency($currency);
        // $normalized = preg_replace('/[Rp\s,.]/', '', $currency);
        // return (float) str_replace(',', '.', $normalized);
    }
}
