<?php
namespace app\controllers;
use app\models\{BackupUpload,User,LoginForm, ContactForm,PaketPengadaan};
use app\widgets\ImageConverter;
use Yii;
use yii\db\Expression;
use yii\filters\{AccessControl, VerbFilter};
use yii\helpers\{FileHelper,Url, Html, ArrayHelper, Json};
use yii\web\{Response,UploadedFile};
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
        if (empty(Yii::$app->session->get('userData'))) {
            return $this->actionLogout();
        }
        return $this->redirect('/site/dashboard');
        // return $this->render('index');
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
    public function actionDashboard() {
        $model=new PaketPengadaan();
        $collection = collect($model::Dashboard());
        $params = [
            'years' => $collection->unique('year')->pluck('year')->toArray(),
            'yearData' => $collection->groupBy('year')->map->count()->values()->toArray(),
            'paketselesai' => $collection->whereNotNull('pemenang')->count(),
            'paketbelom' => $collection->whereNull('pemenang')->count(),
            'totalpagu' => $collection->sum('pagu'),
            'metode' => $model->groupedData('metode_pengadaan',$collection),
            'kategori' => $model->groupedData('kategori_pengadaan',$collection),
            'bypp' => $model->groupedData('pejabat_pengadaan',$collection),
            'byadmin' => $model->groupedData('admin_pengadaan',$collection),
            'bybidang' => $model->groupedData('bidang_bagian',$collection),
        ];
        return $this->render('_dashboard', ['params' => $params]);
    }
    public function actionBackup()
    {
        $dbPath = Yii::$app->db->dsn;
        preg_match('/sqlite:(.*)/', $dbPath, $matches);
        if (isset($matches[1])) {
            $dbFile = Yii::getAlias($matches[1]);
        } else {
            Yii::$app->session->setFlash('error', 'Failed to determine the database file path.');
            return $this->redirect(['site/backup-restore']);
        }
        $backupDir = Yii::getAlias('@runtime/backups/');
        FileHelper::createDirectory($backupDir);
        $backupFile = $backupDir . 'backup_' . date('Ymd_His') . '.sqlite3';
        if (copy($dbFile, $backupFile)) {
            Yii::$app->session->setFlash('success', 'Database has been backed up successfully.');
        } else {
            Yii::$app->session->setFlash('error', 'Failed to backup the database.');
        }
        return $this->redirect(['site/backup-restore']);
    }
    public function actionBackupRestore(){
        $backupDir = Yii::getAlias('@runtime/backups/');
        if (!file_exists($backupDir)) {
            FileHelper::createDirectory($backupDir);
        }
        $files = FileHelper::findFiles($backupDir, ['only' => ['*.sqlite3'], 'recursive' => false]);
        $model = new BackupUpload();
        return $this->render('backup_restore', [
            'files' => $files,
            'model' => $model,
        ]);
    }
    public function actionDownloadBackup($fileName){
        $backupDir = Yii::getAlias('@runtime/backups/');
        $filePath = $backupDir . $fileName;
        if (file_exists($filePath)) {
            return Yii::$app->response->sendFile($filePath);
        } else {
            Yii::$app->session->setFlash('error', 'Backup file not found.');
            return $this->redirect(['site/backup-restore']);
        }
    }
    public function actionRestoreBackup($fileName){
        $backupDir = Yii::getAlias('@runtime/backups/');
        $filePath = $backupDir . $fileName;
        $dbPath = Yii::$app->db->dsn;
        preg_match('/sqlite:(.*)/', $dbPath, $matches);
        if (isset($matches[1])) {
            $dbFile = Yii::getAlias($matches[1]);
        } else {
            Yii::$app->session->setFlash('error', 'Failed to determine the database file path.');
            return $this->redirect(['site/backup-restore']);
        }
        if (file_exists($filePath) && copy($filePath, $dbFile)) {
            Yii::$app->cache->flush();
            Yii::$app->db->schema->refresh();
            Yii::$app->session->setFlash('success', 'Database has been restored from backup.');
        } else {
            Yii::$app->session->setFlash('error', 'Failed to restore the database.');
        }
        return $this->redirect(['site/backup-restore']);
    }
    public function actionDeleteBackup($fileName){
        $backupDir = Yii::getAlias('@runtime/backups/');
        $filePath = $backupDir . $fileName;
        if (file_exists($filePath)) {
            if (unlink($filePath)) {
                Yii::$app->session->setFlash('success', 'Backup file has been deleted successfully.');
            } else {
                Yii::$app->session->setFlash('error', 'Failed to delete the backup file.');
            }
        } else {
            Yii::$app->session->setFlash('error', 'Backup file not found.');
        }
        return $this->redirect(['site/backup-restore']);
    }
    public function actionUploadBackup(){
        $model = new BackupUpload();
        if (Yii::$app->request->isPost) {
            $model->backupFile = UploadedFile::getInstance($model, 'backupFile');
            if ($model->validate()) {
                $backupDir = Yii::getAlias('@runtime/backups/');
                $uploadedFile = $backupDir . $model->backupFile->baseName . '.' . $model->backupFile->extension;
                $model->backupFile->saveAs($uploadedFile);
                Yii::$app->session->setFlash('success', 'Backup file uploaded successfully.');
                return $this->redirect(['site/backup-restore']);
            }
        }
        return $this->redirect(['site/backup-restore']);
    }
}