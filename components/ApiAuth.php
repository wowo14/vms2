<?php
namespace app\components;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Yii;
use yii\base\Component;
use yii\web\UnauthorizedHttpException;

class ApiAuth extends Component
{
    private $secretKey = '7728287';
    private $algorithm = 'HS256';
    private $tokenExpiry = 3600; // 1 hour

    public function generateToken($user)
    {
        $payload = [
            'iss' => 'vms2-api',
            'aud' => 'vms2-client',
            'iat' => time(),
            'exp' => time() + $this->tokenExpiry,
            'user_id' => $user->id,
            'roles' => array_keys(Yii::$app->authManager->getRolesByUser($user->id)),
        ];

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    public function validateToken($token)
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, $this->algorithm));
            return (array) $decoded;
        } catch (\Exception $e) {
            throw new UnauthorizedHttpException('Invalid or expired token');
        }
    }

    public function getCurrentUser()
    {
        $token = Yii::$app->request->headers->get('Authorization');
        if (!$token) {
            throw new UnauthorizedHttpException('Authorization header missing');
        }

        $token = str_replace('Bearer ', '', $token);
        $payload = $this->validateToken($token);

        $user = \app\models\User::findOne($payload['user_id']);
        if (!$user) {
            throw new UnauthorizedHttpException('User not found');
        }

        return $user;
    }
}