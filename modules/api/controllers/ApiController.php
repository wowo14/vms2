<?php

namespace app\modules\api\controllers;

use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\rest\Controller;
use yii\web\Response;

class ApiController extends Controller
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        /*
         * Response API hanya JSON.
         */
        $behaviors['contentNegotiator']['formats'] = [
            'application/json' => Response::FORMAT_JSON,
        ];

        /*
         * CORS harus diproses sebelum autentikasi.
         */
        $authenticator = $behaviors['authenticator'] ?? null;
        unset($behaviors['authenticator']);

        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => [
                    'http://localhost:3000',
                    'http://localhost:5173',
                    'http://localhost:8080',
                ],
                'Access-Control-Request-Method' => [
                    'GET',
                    'POST',
                    'PUT',
                    'PATCH',
                    'DELETE',
                    'OPTIONS',
                ],
                'Access-Control-Request-Headers' => [
                    '*',
                ],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Max-Age' => 86400,
            ],
        ];

        /*
         * Semua endpoint turunan membutuhkan Bearer Token,
         * kecuali request OPTIONS untuk CORS.
         */
        $behaviors['authenticator'] = [
            'class'  => HttpBearerAuth::class,
            'except' => ['options'],
        ];

        return $behaviors;
    }

    protected function successResponse(
        mixed $data = null,
        string $message = 'Request berhasil.',
        int $status = 200,
        ?array $meta = null
    ): array {
        $this->response->statusCode = $status;

        return [
            'status'  => $status,
            'message' => $message,
            'data'    => $data,
            'errors'  => null,
            'meta'    => $meta,
        ];
    }

    protected function errorResponse(
        string $message,
        int $status = 400,
        mixed $errors = null
    ): array {
        $this->response->statusCode = $status;

        return [
            'status'  => $status,
            'message' => $message,
            'data'    => null,
            'errors'  => $errors,
            'meta'    => null,
        ];
    }
}