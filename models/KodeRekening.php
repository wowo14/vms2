<?php
namespace app\models;
use Yii;
class KodeRekening extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public static function tableName()
    {
        return 'kode_rekening';
    }
    public function rules()
    {
        return [
            [['kode', 'rekening', 'tahun_anggaran'], 'required'],
            [['parent', 'is_active', 'tahun_anggaran', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'string'],
            [['kode', 'rekening'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'kode' => 'Kode',
            'rekening' => 'Rekening',
            'parent' => 'Parent',
            'is_active' => 'Is Active',
            'tahun_anggaran' => 'Tahun Anggaran',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }
    public function getCoacode() { //distinct
        return self::where(['is_active' => 1])->select('id,kode,rekening,tahun_anggaran')->orderBy('kode')->distinct()->all();
    }
    public static function getTree($cnd = NULL) {
        $data2 = [];
        $menu = self::where(['parent' => $cnd])->orderby('kode')->asArray()->all();
        foreach ($menu as $haha) {
            $row = [];
            $row['id']    = $haha['id'];
            $row['text']  = $haha['kode'] . '|' . $haha['rekening'];
            if (count(self::getTree($haha['id'])) > 0)
                $row['children'] = self::getTree($haha['id']);
            $data2[] = $row;
        }
        return $data2;
    }
    public static function copyto($from, $target) { //tahun
        // cek is exist
        $exists = self::where(['tahun_anggaran' => $target])->exists();
        if (!$exists) {
            $old = self::where(['tahun_anggaran' => $from])->all();
            if ($old) {
                foreach ($old as $o) {
                    $to = new KodeRekening();
                    $to->attributes = $o->attributes;
                    $to->tahun_anggaran = $target;
                    $to->save();
                }
                $message = 'success copy ' . $from . ' to ' . $target;
                $status = 'success';
            } else {
                $message = 'failed copy ' . $from . ' to ' . $target . ' because ' . $from . ' not found';
                $status = 'error';
            }
        }
        if ($exists) {
            $status = 'error';
            $message = 'failed copy ' . $from . ' to ' . $target . ' because ' . $target . ' already exist';
        }
        return ['status' => $status, 'message' => $message];
    }
}