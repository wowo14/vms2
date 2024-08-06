<?php
namespace app\controllers;
use app\models\User;
use app\models\{LoginForm, ContactForm};
use app\widgets\ImageConverter;
use Yii;
use yii\filters\{AccessControl, VerbFilter};
use yii\helpers\{Url, Html, ArrayHelper, Json};
use yii\web\{ Response};

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
        $user=Yii::$app->user->identity;
        print_r($user->uservendor);
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
            $userData = collect($rawUser)->toArray();
            $user=User::findOne(['id'=>$userData['id']]);
            $userDataroles = collect($rawUser->roles)->pluck('item_name', 'item_name')->toArray();
                Yii::$app->session->set('roles', $userDataroles);
                Yii::$app->session->set('userData', $userData);
            if (array_key_exists('vendor', $userDataroles)) {
                Yii::$app->session->set('companygroup', $rawUser->uservendor->penyedia_id);
            }
            return $this->goBack();
        }
        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }
    public function actionConvertImages()
    {
        $sourceDir = Yii::getAlias('@app/web/uploads');
        $targetDir = Yii::getAlias('@app/web/uploads/avif');
        $converter = new ImageConverter();
        $converter->convertImagesToAvif($sourceDir, $targetDir);
        return 'Images have been converted to AVIF format.';
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
