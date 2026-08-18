<?php
namespace app\behaviors;

use yii\behaviors\RateLimiter;
use yii\web\Request;

class ApiRateLimiter extends RateLimiter
{
    public $user = null;
    public $rateLimit = 100; // requests per hour
    public $timePeriod = 3600; // 1 hour

    public function init()
    {
        $this->rateLimit = $this->rateLimit;
        $this->timePeriod = $this->timePeriod;
        parent::init();
    }

    public function getUserId($request)
    {
        // Gunakan IP address atau user ID dari token
        $token = $request->headers->get('Authorization');
        if ($token) {
            try {
                $payload = Yii::$app->apiAuth->validateToken(str_replace('Bearer ', '', $token));
                return $payload['user_id'];
            } catch (\Exception $e) {
                // Fallback ke IP
            }
        }
        return $request->userIP;
    }
}