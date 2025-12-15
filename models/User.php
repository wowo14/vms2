<?php
namespace app\models;
use app\models\Contacts;
use mdm\admin\models\User as U;
class User extends U {
    use GeneralModelsTrait;
    public static function findIdentityByAccessToken($token, $type = null) {
        return static::findOne(['auth_key' => $token]);
    }
    public function getRoles() {
        return $this->hasMany(AuthAssignment::class, ['user_id' => 'id'])->cache(24 * 60 * 60, self::settagdep('tag_authassignment'));
    }
    public function getUservendor(){
        return $this->hasOne(Contacts::class,['user_id' => 'id'])->cache(24 * 60 * 60, self::settagdep('tag_contacts'));
    }
    public function getUserpegawai(){
        return $this->hasOne(Pegawai::class,['id_user' => 'id'])->cache(24 * 60 * 60, self::settagdep('tag_pegawai'));
    }
}