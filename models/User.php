<?php
namespace app\models;
use mdm\admin\models\User as U;
class User extends U
{
    use GeneralModelsTrait;
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token]);
    }
    public function getRoles()
    {
        return $this->hasMany(AuthAssignment::class, ['user_id' => 'id'])->cache(24 * 60 * 60, self::settagdep('tag_authassignment'));
    }
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        self::invalidatecache('tag_' . self::getModelname());
    }
}
