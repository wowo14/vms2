<?php
namespace app\modules\api\v1\controllers;

use app\models\User;
use app\modules\api\controllers\ApiController;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;

class AuthController extends ApiController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // Auth tidak butuh authentication untuk login, refresh, dan me
        $behaviors['authenticator']['except'] = ['login', 'refresh', 'me'];
        return $behaviors;
    }

    public function actionLogin()
    {
        $username = Yii::$app->request->post('username');
        $password = Yii::$app->request->post('password');

        if (!$username || !$password) {
            throw new BadRequestHttpException('Username and password are required');
        }

        $user = User::findByUsername($username);
        if (!$user || !$user->validatePassword($password)) {
            throw new UnauthorizedHttpException('Invalid credentials');
        }

        $token = Yii::$app->apiAuth->generateToken($user);

        return [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'roles' => array_keys(Yii::$app->authManager->getRolesByUser($user->id)),
            ],
            'expires_in' => 3600, // 1 hour
        ];
    }

    public function actionRefresh()
    {
        $user = Yii::$app->apiAuth->getCurrentUser();
        $token = Yii::$app->apiAuth->generateToken($user);

        return [
            'token' => $token,
            'expires_in' => 3600,
        ];
    }

    public function actionMe()
    {
        $user = Yii::$app->apiAuth->getCurrentUser();

        return [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'roles' => array_keys(Yii::$app->authManager->getRolesByUser($user->id)),
        ];
    }
}