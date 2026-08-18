<?php
namespace app\modules\api\v1\controllers;

use app\models\Dpp;
use app\modules\api\controllers\ApiController;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class DppController extends ApiController
{
    public $modelClass = Dpp::class;

    public function actions()
    {
        $actions = parent::actions();
        // Disable default actions, implement custom
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        $query = Dpp::find();
        
        // Filter parameters
        $paket_id = Yii::$app->request->get('paket_id');
        $tahun = Yii::$app->request->get('tahun');
        
        if ($paket_id) {
            $query->andWhere(['paket_id' => $paket_id]);
        }
        
        if ($tahun) {
            $query->andWhere(['like', 'tanggal_terima', $tahun . '%', false]);
        }

        // Role-based filtering
        if (!Yii::$app->user->can('admin')) {
            $query->andWhere(['created_by' => Yii::$app->user->id]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->get('per_page', 20),
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        return $dataProvider;
    }

    public function actionView($id)
    {
        $model = Dpp::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('DPP not found');
        }

        // Check access
        if (!Yii::$app->user->can('admin') && $model->created_by != Yii::$app->user->id) {
            throw new \yii\web\ForbiddenHttpException('You do not have permission to view this DPP');
        }

        return $model;
    }

    public function actionCreate()
    {
        $model = new Dpp();
        $model->load(Yii::$app->request->bodyParams, '');
        
        if ($model->save()) {
            Yii::$app->response->statusCode = 201;
            return $model;
        } elseif ($model->hasErrors()) {
            Yii::$app->response->statusCode = 422;
            return ['errors' => $model->errors];
        } else {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
    }

    public function actionUpdate($id)
    {
        $model = Dpp::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('DPP not found');
        }

        // Check access
        if (!Yii::$app->user->can('admin') && $model->created_by != Yii::$app->user->id) {
            throw new \yii\web\ForbiddenHttpException('You do not have permission to update this DPP');
        }

        $model->load(Yii::$app->request->bodyParams, '');
        
        if ($model->save()) {
            return $model;
        } elseif ($model->hasErrors()) {
            Yii::$app->response->statusCode = 422;
            return ['errors' => $model->errors];
        } else {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }
    }

    public function actionDelete($id)
    {
        $model = Dpp::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('DPP not found');
        }

        // Check access
        if (!Yii::$app->user->can('admin') && $model->created_by != Yii::$app->user->id) {
            throw new \yii\web\ForbiddenHttpException('You do not have permission to delete this DPP');
        }

        if ($model->delete() === false) {
            throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }

        Yii::$app->response->statusCode = 204;
        return null;
    }
}