<?php
namespace app\models;
use Yii;
use mdm\admin\models\Assignment;
class Pegawai extends \yii\db\ActiveRecord {
    public $oldrecord;
    use GeneralModelsTrait;
    public static function tableName() {
        return 'tbl_pegawai';
    }
    public function rules() {
        return [
            [['nik', 'nama', 'alamat', 'telp', 'status', 'email', 'username', 'password'], 'required'],
            [['id_user'], 'integer'],
            [['nik'], 'unique', 'targetAttribute' => ['nik']],
            [['email'], 'email'],
            [['nama'], 'string', 'max' => 250],
            [['alamat', 'hak_akses', 'password'], 'string', 'max' => 255],
            ['telp', 'match', 'pattern' => '/^[0-9]{8,15}$/',],
            ['nik', 'match', 'pattern' => '/^[0-9]{8,16}$/',],
            [['status'], 'string', 'max' => 1],
            [['username'], 'string', 'max' => 50],
        ];
    }
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'nik' => 'NIK',
            'nama' => 'Nama',
            'email' => 'email',
            'alamat' => 'Alamat',
            'telp' => 'Telp',
            'status' => 'Status',
            'id_user' => 'Id User',
            'hak_akses' => 'Hak Akses',
            'username' => 'Username',
            'password' => 'Password',
        ];
    }
    public function signup($role) {
        if ($this->validate()) {
            $class = Yii::$app->getUser()->identityClass ?: 'mdm\admin\models\User';
            $user = new $class();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->status = 10;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            if ($user->save(false)) {
                $level = new AuthAssignment();
                $level->item_name = $role;
                $level->user_id = $user->id;
                $level->created_at = time();
                $level->save(false);
                $items[] = $level->item_name;
                $assign = new Assignment($level->user_id);
                $assign->assign($items);
                return $user;
            }
        }
        return null;
    }
    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($insert && $this->hasAttribute('username') && $this->hasAttribute('password') && $this->hasAttribute('hak_akses')) {
            if ($this->username && $this->password && $this->hak_akses && $this->status == 1) {
                $exists = User::where(['username' => $this->username])->exists();
                if (!$exists) {
                    $this->id_user = $this->signup($this->hak_akses)->id;
                }
            }
        } elseif (!$insert && $this->hasAttribute('username') && $this->hasAttribute('password') && $this->hasAttribute('hak_akses')) {
            $user = User::where(['username' => $this->username, 'id' => $this->id_user])->one();
            if ($user) {
                //update password, update hak akses
                $user->setPassword($this->password);
                $user->generateAuthKey();
                $user->save(false);
                if ($this->hak_akses) {
                    $level = AuthAssignment::find()->where(['user_id' => $this->id_user])->one();
                    $level->item_name = $this->hak_akses;
                    $level->save(false);
                    $items[] = $this->hak_akses;
                    $olditems[] = $this->oldrecord->hak_akses;
                    $assign = new Assignment($user->id);
                    $assign->revoke($olditems);
                    sleep(2);
                    $assign->assign($items);
                }
            }
        }
        if (!$insert && $this->status == 0) {
            $user = User::findOne($this->id_user);
            $user->status = 0;
            $user->save(false);
        }
        return true;
    }
    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        self::invalidatecache('tag_petugas');
        self::invalidatecache('tag_' . self::getModelname());
    }
    public function afterFind() {
        $this->oldrecord = clone $this;
        return parent::afterFind();
    }
    public function afterDelete() {
        parent::afterDelete();
        self::invalidatecache('tag_' . self::getModelname());
    }
}
