<?php
namespace app\modules\api\v1\controllers;

use app\modules\api\controllers\ApiController;
use Yii;

class DefaultController extends ApiController
{
    public function actionIndex()
    {
        return [
            'name' => 'VMS2 API',
            'version' => '1.0',
            'description' => 'Vendor Management System API',
            'endpoints' => [
                'auth' => [
                    'POST /api/auth/login' => 'Login and get JWT token',
                    'POST /api/auth/refresh' => 'Refresh JWT token',
                    'GET /api/auth/me' => 'Get current user info',
                ],
                'dpp' => [
                    'GET /api/dpp' => 'List all DPPs',
                    'GET /api/dpp/{id}' => 'Get specific DPP',
                    'POST /api/dpp/create' => 'Create new DPP',
                    'PUT /api/dpp/update/{id}' => 'Update DPP',
                    'DELETE /api/dpp/delete/{id}' => 'Delete DPP',
                ],
                'paket' => [
                    'GET /api/paket' => 'List all paket',
                    'GET /api/paket/{id}' => 'Get specific paket',
                ],
                'penawaran' => [
                    'GET /api/penawaran' => 'List all penawaran',
                    'GET /api/penawaran/{id}' => 'Get specific penawaran',
                    'POST /api/penawaran/submit' => 'Submit penawaran',
                ],
                'penyedia' => [
                    'GET /api/penyedia' => 'List all penyedia',
                    'GET /api/penyedia/{id}' => 'Get specific penyedia',
                ],
            ],
            'documentation' => '/api/docs',
        ];
    }
}
