<?php
namespace app\controllers;
use app\models\{Dpp,Setting,BackupUpload,User,LoginForm, ContactForm,PaketPengadaan};
use app\widgets\ImageConverter;
use Yii;
use yii\db\Expression;
use yii\filters\{AccessControl, VerbFilter};
use yii\helpers\{FileHelper,Url, Html, ArrayHelper, Json};
use yii\web\{Response,UploadedFile};
use yii\base\DynamicModel;
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
    public function actionDownloads()
    {
        $path = Yii::getAlias('@app/web/downloads');
        $files = [];
        $title = 'File Template Pengadaan Terbaru';
        if (is_dir($path)) {
            $files = FileHelper::findFiles($path, ['recursive' => false]);
        }
        return $this->render('downloads', [
            'files' => $files,'title'=>$title
        ]);
    }
    public function actionDownloadFile($file)
    {
        $path = Yii::getAlias('@app/web/downloads') . '/' . $file;
        if (file_exists($path)) {
            return Yii::$app->response->sendFile($path);
        }
        throw new \yii\web\NotFoundHttpException("File not found.");
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
                    $out[] = ['id' => $account['id'], 'name' => $account['nama']];
                    $selected = ($i == 0) ? $account['id'] : $selected;
                }
            }
        }
        return ['output' => $out, 'selected' => $selected];
    }
    public function actionDashboard() {
        $model=new PaketPengadaan();
        $year = Yii::$app->request->get('year');
        // Get all available years for the dropdown
        $allYears = $model->getExistingYears()->pluck('year')->sortDesc()->values()->toArray();

        $collection = collect($model->getDashboard($year));
        
        $params = [
            'years' => $collection->unique('year')->pluck('year')->toArray(),
            'allYears' => $allYears,
            'selectedYear' => $year,
            'yearData' => $collection->groupBy('year')->map->count()->values()->toArray(),
            'paketselesai' => $collection->whereNotNull('pemenang')->count(),
            'paketbelom' => $collection->whereNull('pemenang')->count(),
            'totalpagu' => $collection->sum('pagu'),
            'totalpenyerapan' => $collection->sum('hasilnego'),
            'metode' => $model->groupedData('metode_pengadaan',$collection),
            'kategori' => $model->groupedData('kategori_pengadaan',$collection),
            'bypp' => $model->groupedData('pejabat_pengadaan',$collection),
            'byadmin' => $model->groupedData('admin_pengadaan',$collection),
            'bybidang' => $model->groupedData('bidang_bagian',$collection),
        ];
        // batasi dashboard 3th tahun terakhir
        return $this->render('_dashboard', ['params' => $params]);
    }
    public function actionNotif(){
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model=new PaketPengadaan();
        $dpp=new Dpp();
        $params = [
            'paketbaru'=>(float)$model->notifpaketbaru,
            'belumditugaskan'=>(float)$dpp->belumditugaskan,
            'paketreject'=>(float)$model->paketreject,
            'totalnotif'=> (float)$model->notifpaketbaru+ (float)$dpp->belumditugaskan+(float)$model->paketreject,
        ];
        return $params;
    }
    public function actionPaktaIntegritas(){
        $model = new DynamicModel([
            'tahun', 'user_id','status'
        ]);
        $model->addRule(['tahun'], 'required')
            ->addRule(['tahun'], 'integer')
            ->addRule(['user_id'], 'required')
            ->addRule(['status'], 'required'); // user_id wajib
        // Ambil isi pakta integritas dari table setting
        $setting = Setting::findOne(['type' => 'splash_paktaintegritas']);
        $paktaText = $setting ? $setting->value : 'Belum ada isi pakta integritas.';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $record = Setting::findOne(['type' => 'persetujuan_paktaintegritas']);
            $now = date('Y-m-d H:i:s');
            // data baru untuk user yang klik
            $newEntry = [
                $model->user_id => [
                    [
                        'timestamp' => $now,
                        'status'    => $model->status
                    ]
                ]
            ];
            if ($record) {
                $json = json_decode($record->value, true);
                // cek tahun
                if ($json['tahun'] != $model->tahun) {
                    // kalau beda tahun, reset struktur
                    $json = [
                        'tahun'   => $model->tahun,
                        'user_id' => [$newEntry]
                    ];
                } else {
                    // merge dengan user lain
                    if (isset($json['user_id'][0][$model->user_id])) {
                        // GANTI seluruh data user_id dengan entry baru (bukan append)
                        $json['user_id'][0][$model->user_id] = [
                            [
                                'timestamp' => $now,
                                'status'    => $model->status
                            ]
                        ];
                    } else {
                        // tambahkan user baru
                        $json['user_id'][0][$model->user_id] = [
                            [
                                'timestamp' => $now,
                                'status'    => $model->status
                            ]
                        ];
                    }
                }
                $record->value = json_encode($json);
            } else {
                // record baru
                $json = [
                    'tahun'   => $model->tahun,
                    'user_id' => [$newEntry]
                ];
                $record = new Setting();
                $record->type  = 'persetujuan_paktaintegritas';
                $record->value = json_encode($json);
            }
            if ($record->save(false)) {
                Yii::$app->session->setFlash('success', 'Persetujuan Pakta Integritas tersimpan');
                return $this->redirect(['site/dashboard']);
            }
        }
        return $this->render('pakta-integritas', [
            'model' => $model,
            'paktaText' => $paktaText
        ]);
    }
    public function actionBackup(){
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