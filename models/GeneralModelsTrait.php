<?php
namespace app\models;
use Yii;
use yii\caching\TagDependency;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseStringHelper;
trait GeneralModelsTrait {
    public function getHash() {
        $class = self::class;
        $class = strpos($class, 'Search') ? str_replace('Search', '', $class) : $class;
        return md5($class);
    }
    public function range($d, $type) { //filter condition date between
        $dates = explode(' - ', $d); //return array
        if ((bool) strtotime($dates[0]) && (bool) strtotime($dates[1])) {
            return $type == 's' ? $dates[0] . ' 00:00:00' : $dates[1] . ' 23:59:59';
        }
    }
    public static function where($condition, $params = []) {
        return self::find()->cache(self::cachetime(), self::settagdep('tag_' . self::getModelname()))->where($condition, $params = []);
    }
    public static function first($condition) {
        return self::where($condition)->one();
    }
    public static function last($condition) {
        return self::where($condition)->orderby(['id' => SORT_DESC])->limit(1)->one();
    }
    public static function findOne($condition) {
        $tags = self::settagdep('tag_' . self::getModelname());
        return static::findByCondition($condition)->cache(self::cachetime(), $tags)->one();
    }
    public static function collectAll($condition = null, $params = []) { //Collection
        if (isset($params) || isset($condition)) {
            return collect(self::where($condition, $params = [])->all())->sortByDesc('id');
        } else {
            return collect(self::find()->cache(self::cachetime(), self::settagdep('tag_' . self::getModelname()))->all())->sortByDesc('id');
        }
    }
    protected function findReplace($string, $find, $replace) {
        if (preg_match("/[a-zA-Z\_]+/", $find)) {
            return (string) preg_replace("/\{(\s+)?($find)(\s+)?\}/", $replace, $string);
        } else {
            throw new \Exception("Find statement must match regex pattern: /[a-zA-Z]+/");
        }
    }
    public static function hashid($params) { // id as primarykey
        $class = self::class;
        $pk = $class::primaryKey()[0];
        return BaseStringHelper::base64Urlencode(json_encode(['model' => $class, $pk => $params]));
    }
    public static function settingType($type) {
        return Setting::type($type);
    }
    public static function isAdmin() {
        return (ArrayHelper::keyExists('admin', Yii::$app->session->get('roles'))) ? true : false;
    }
    public static function isStaff() {
        return (ArrayHelper::keyExists('staff', Yii::$app->session->get('roles'))) ? true : false;
    }
    public static function optionsSettingtype($type, $callback) { //example $model::optionsSettingtype(string 'type', string|array|function);
        $settings = Setting::type($type);
        if ($callback instanceof \Closure) {
            Yii::error('callbak closure');
            return collect($settings)->map($callback)->toArray();
        } elseif (is_array($callback)) {
            Yii::error('callbak isarray');
            return collect($settings)->pluck(...$callback)->toArray();
        }
        return collect($settings)->pluck($callback)->toArray();
    }
    public static function getModelname() {
        $array = explode('\\', self::class);
        $modelClasses = str_replace('Search', '', end($array));
        return strtolower($modelClasses);
    }
    public function getUsercreated() {
        return $this->hasAttribute('created_by') ? $this->hasOne(User::class, ['id' => 'created_by'])->cache(self::cachetime(), self::settagdep('tag_user')) : '';
    }
    public function getUserupdated() {
        return $this->hasAttribute('updated_by') ? $this->hasOne(User::class, ['id' => 'updated_by'])->cache(self::cachetime(), self::settagdep('tag_user')) : '';
    }
    public function allColumn() {
        $ar = (new self)->attributes;
        foreach ($ar as $key => $value) {
            $ar2[] = $key;
        }
        return $ar2;
    }
    public static function settagdep($tag) {
        return new TagDependency(['tags' => $tag]);
    }
    public static function cachetime() {
        return 24 * 60 * 60;
    }
    public static function invalidatecache($tag) {
        $dep = new TagDependency(['tags' => $tag]);
        $dep->invalidate(Yii::$app->cache, $tag);
    }
    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        self::invalidatecache('tag_' . self::getModelname());
    }
    public function afterDelete() {
        parent::afterDelete();
        self::invalidatecache('tag_' . self::getModelname());
    }
    public static function profile($param) {
        $profile = collect(self::settingType('global'))->where('param', $param)->first();
        return $profile ? $profile->value : null;
    }
    public function getKab() {
        if ($this->hasAttribute('propinsi') && $this->hasAttribute('kota')) {
            $r = Yii::$app->regions->getData('kabupaten', $this->propinsi);
            return collect($r)->filter(
                fn ($el) => $el['id'] == $this->kota
            )->pluck('name', 'id')->toArray();
        }
    }
    public function getIncompanygrouporadmin() { // boolean
        if ($this->getIsVendor()) {
            if ($this->hasAttribute('penyedia_id')) {
                return $this->penyedia_id == Yii::$app->session->get('companygroup');
            } elseif (self::class == Penyedia::class) {
                return $this->id == Yii::$app->session->get('companygroup');
            }
        } else {
            return Yii::$app->tools->isAdmin();
        }
        return false;
    }
    public function getIsBase64Encoded($str) { //boolean
        return self::isBase64Encoded($str);
    }
    public static function isBase64Encoded($str) { //boolean
        $data = preg_replace('#^data:(image/[^;]+|application/pdf);base64,#', '', $str);
        return base64_encode(base64_decode($data, true)) === $data;
    }
    public function getVendor() {
        if ($this->hasAttribute('penyedia_id')) {
            return $this->hasOne(Penyedia::class, ['id' => 'penyedia_id'])->cache(self::cachetime(), self::settagdep('tag_penyedia'));
        }
    }
    public static function optthanggaran($id = null) {
        $years = [date('Y', strtotime('-1 year')), date('Y'), date('Y', strtotime('+1 year'))];
        $options = array_map(
            fn ($th) => ['id' => $th, 'value' => $th],
            array_reverse($years)
        );
        if (isset($id)) {
            return array_filter(
                $options,
                fn ($el) => $el['id'] == $id
            );
        }
        return $options;
    }
    public static function optiontahunanggaran($id = null) {
        return ArrayHelper::map(self::optthanggaran($id ?? null), 'id', 'value');
    }
    private function upload($base64Data, $filename) {
        return Yii::$app->tools->upload($base64Data, $filename);
    }
    public function getIsVendor() {
        return Yii::$app->tools->isVendor();
    }
    public function getVendors() {
        return Penyedia::collectAll(['active' => 1]);
    }
    public function getCoacode() {
        $coa = new KodeRekening();
        return $coa->coacode;
    }
    public function getProduks() {
        return Produk::where(['active' => 1])->asArray()->all();
    }
}
