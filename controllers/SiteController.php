<?php
namespace app\controllers;
use app\models\{LoginForm, ContactForm};
use Yii;
use yii\filters\{AccessControl, VerbFilter};
use yii\helpers\{Url, Html, ArrayHelper, Json};
use yii\web\{Controller, Response};
class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'captcha'],
                'rules' => [
                    [
                        'actions' => ['logout', 'captcha'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                ],
                // 'denyCallback' => function ($rule, $action) {
                //     return $this->redirect(['site/login']);
                // },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post', 'get'],
                ],
            ],
        ];
    }
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'minLength' => 3,
                'maxLength' => 3,
                'width' => 130, 'height' => 55,
                'fixedVerifyCode' => null,
            ],
        ];
    }
    public function actionYamlcontent()
    {
        $icon = [];
        $path = \Yii::getAlias('@vendor/fortawesome/font-awesome/metadata/icons.yml');
        $array = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($path));
        foreach ($array as $k => $value) {
            $icon[] = ['key' => $k, 'value' => $value];
        }
        return collect($icon)->pluck('key');
    }
    public function actionIndex()
    {
        if (empty(Yii::$app->session->get('userData'))) {
            return $this->actionLogout();
        }
        return $this->render('index');
    }
    public function actionLogin()
    {
        $this->layout = 'main-login';
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $rawUser = Yii::$app->user->identity;
            // $peg=Pegawai::findOne(['id_user'=>$rawUser->id]);
            $userData = collect($rawUser)->toArray();
            $userDataroles = collect($rawUser->roles)->pluck('item_name', 'item_name')->toArray();
            // if($peg){
            //     $userData=array_merge($userData,['petugas'=>$peg->id]);
            // }
            // $userData=array_merge($userData,['hak_akses' => array_values($userDataroles)[0]]);
            Yii::$app->session->set('roles', $userDataroles);
            Yii::$app->session->set('userData', $userData);
            return $this->goBack();
        }
        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
    public function actionAbout()
    {
        return $this->render('about');
    }
    public function actionDepdropregion($param)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = $data = [];
        $selected = '';
        if (isset($_POST['depdrop_parents'])) {
            $parents = end($_POST['depdrop_parents']);
            if ($parents !== null && $parents != '') {
                $id = $parents;
                switch ($param) {
                    case 'kabupaten':
                    case 'kecamatan':
                    case 'kelurahan':
                        $data = Yii::$app->regions->getData($param, $id);
                        break;
                    default:
                        $data = Yii::$app->regions->getData('propinsi');
                        break;
                }
                foreach ($data as $i => $account) {
                    $out[] = ['id' => $account['id'], 'name' => $account['name']];
                    $selected = ($i == 0) ? $account['id'] : $selected;
                }
            }
        }
        return ['output' => $out, 'selected' => $selected];
    }
}
