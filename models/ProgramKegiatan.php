<?php
namespace app\models;
use Yii;
use yii\helpers\ArrayHelper;class ProgramKegiatan extends \yii\db\ActiveRecord {
    use GeneralModelsTrait;
    public static function tableName() {
        return 'program_kegiatan';
    }
    public function rules() {
        return [
            [['code', 'desc', 'type', 'tahun_anggaran', 'is_active'], 'required'],
            [['tahun_anggaran', 'is_active', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'string'],
            [['code', 'desc', 'parent', 'type'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'desc' => 'Desc',
            'parent' => 'Parent',
            'type' => 'Type',
            'tahun_anggaran' => 'Tahun Anggaran',
            'is_active' => 'Is Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }
    public function getCodename() {
        return $this->code . '||' . $this->desc;
    }
    public static function optionprogram($code = null, $th = null) {
        $where = ['is_active' => 1];
        $where = isset($th) ? array_merge($where, ['tahun_anggaran' => $th]) : $where;
        $where = isset($code) ? array_merge($where, ['code' => $code]) : $where;
        Yii::error(json_encode($where));        return ArrayHelper::map(self::where($where)->all(), 'code', 'codename');
    }
    public static function getTree($cnd = NULL) {
        $data2 = [];
        $program = self::where(['parent' => $cnd])->orderby('code')->asArray()->all();
        foreach ($program as $haha) {
            $row = [];
            $row['id']    = $haha['id'];
            $row['text']  = $haha['code'] . '|' . $haha['desc'];
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
                    $to = new ProgramKegiatan();
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
