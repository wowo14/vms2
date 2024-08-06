<?php
namespace app\models;
use mdm\admin\models\Assignment;
use Yii;
use yii\db\Expression;
class Pengurusperusahaan extends Contacts
{
    use GeneralModelsTrait;
    public function rules()
    {
        return [
            [['nama', 'nik', 'alamat', 'telepon', 'jabatan'], 'required'],
            [['is_vendor', 'is_active', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at', 'user_id', 'penyedia_id'], 'safe'],
            [['nama', 'nik', 'alamat', 'telepon', 'nip', 'jabatan', 'password', 'instansi'], 'string', 'max' => 255],
            [['email'], 'email']
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nama' => 'Nama',
            'nik' => 'NIK',
            'alamat' => 'Alamat',
            'email' => 'Email',
            'user_id' => 'User ID',
            'penyedia_id' => 'Penyedia',
            'telepon' => 'Telepon',
            'jabatan' => 'Jabatan',
            'is_vendor' => 'Is Vendor',
            'is_active' => 'Is Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'password' => 'Password',
        ];
    }
    public function beforeSave($insert)
    {
        if ($insert) {
            //insert user
            if (!empty($this->password) && $this->is_active == 1) {
                $this->user_id = $this->signup()->id;
                $this->is_vendor = 1;
            }
        } else {
            //update password login
            if (!empty($this->password)) {
                $user = User::findOne(['email'=>$this->email,'id'=>$this->user_id]);
                if($user){
                    $user->setPassword($this->password);
                    $user->save(false);
                }else{
                    //create new users
                    $this->user_id = $this->signup()->id;
                    $this->is_vendor = 1;
                }
            }
        }
        if ($insert && $this->hasAttribute('created_at') && $this->hasAttribute('created_by')) {
            $this->created_at = $this->getbCurrentTimestamp();
            $this->created_by = Yii::$app->user->id ? Yii::$app->user->identity->id : '';
        } elseif (!$insert && $this->hasAttribute('updated_at') && $this->hasAttribute('updated_by')) {
            $this->updated_at = $this->getDbCurrentTimestamp();
            $this->updated_by = Yii::$app->user->id ? Yii::$app->user->identity->id : '';
        }
        return parent::beforeSave($insert);
    }
    public function signup() //vendor signup users
    {
        if ($this->validate()) {
            $class = Yii::$app->getUser()->identityClass ?: 'mdm\admin\models\User';
            $user = new $class();
            $user->username = $this->email;
            $user->email = $this->email;
            $user->status = 10;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            if ($user->save(false)) {
                $level = new AuthAssignment();
                $level->item_name = 'vendor';
                $level->user_id = $user->id;
                $level->created_at = time();
                $level->save(false);
                return $user;
                //assignment
                $items[] = 'vendor';
                $assign = new Assignment($user->id);
                $assign->assign($items);
                $assign->save();
            }
        }
        return null;
    }
}
