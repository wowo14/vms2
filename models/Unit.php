<?php
namespace app\models;
use Yii;
class Unit extends \yii\db\ActiveRecord {
    use GeneralModelsTrait;
    public static function tableName() {
        return 'unit';
    }
    public function rules() {
        return [
            [['is_vip', 'aktif'], 'integer'],
            [['logo'], 'string'],
            [['kode', 'fk_instalasi'], 'string', 'max' => 200],
            [['unit'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'kode' => 'Kode',
            'unit' => 'Unit',
            'fk_instalasi' => 'Fk Instalasi',
            'is_vip' => 'Is Vip',
            'aktif' => 'Aktif',
            'logo' => 'Logo',
        ];
    }
}
