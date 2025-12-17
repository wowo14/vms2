<?php
namespace app\controllers;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseStringHelper;
use yii\web\Controller as C;
use yii\web\Response;
use yii\web\ForbiddenHttpException;
use app\models\Setting;
class Controller extends C{
    public static function hashurl($params){//array params urls
        return BaseStringHelper::base64Urlencode(
            json_encode(
               $params
            )
        );
    }
    public function decodeurl($params){//objects
        return json_decode(BaseStringHelper::base64UrlDecode($params));
    }
    public function upload($base64Data, $filename){
        return Yii::$app->tools->upload($base64Data, $filename);
    }
    public function isVendor(){
        return Yii::$app->tools->isVendor();
    }
    public function isAdmin(){
        return Yii::$app->tools->isAdmin();
    }
    public function isAdminOrVendor(){
        return Yii::$app->tools->isAdminOrVendor();
    }
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        if (!Yii::$app->user->isGuest) {
            $userId = Yii::$app->user->id;
            $setting = Setting::findOne(['type' => 'persetujuan_paktaintegritas']);
            if ($setting) {
                $json = json_decode($setting->value, true);
                $approved = false;
                if (isset($json['user_id'][0][$userId])) {
                    foreach ($json['user_id'][0][$userId] as $log) {
                        if ($log['status'] === 'accept') {
                            $approved = true;
                            break;
                        }
                    }
                }

                // Jika belum approve → redirect ke halaman pakta integritas
                if (!$approved && !in_array($action->id, ['pakta-integritas','logout','login'])) {
                    return $this->redirect(['site/pakta-integritas']);
                }
            }else{
                $json = [
                    'tahun'   => date('Y'),
                    'user_id' => []
                ];
                $record = new Setting();
                $record->type  = 'persetujuan_paktaintegritas';
                $record->value = json_encode($json);
                $record->active = 1;
                $record->save(false);
            }
        }
        return true;
    }
}