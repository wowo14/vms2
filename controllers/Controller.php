<?php
namespace app\controllers;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseStringHelper;
use yii\web\Controller as C;
use yii\web\Response;
use yii\web\ForbiddenHttpException;
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
}