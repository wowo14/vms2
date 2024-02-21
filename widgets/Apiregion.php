<?php
namespace app\widgets;
use app\models\Setting;
use app\widgets\Tools;
use Yii;
use yii\base\Widget;
use yii\caching\FileCache;
class Apiregion extends Widget
{
    private $regionApi;
    private $runtimePath;
    public function __construct(){
      $this->runtimePath= Yii::getAlias('@runtime');
      $this->regionApi='https://www.emsifa.com/api-wilayah-indonesia/api/';
    }
    public function curlWithCache($url,$method=null){
        return (new Tools)->curlWithCache($url,$method);
    }
    public function getData($type, $id = null){
        $filePath = $this->runtimePath . "/$type" . ($id ? $id : '') . '.json';
        if (file_exists($filePath)) {
            $content = file_get_contents($filePath);
            $data = json_decode($content, true);
        } else {
            $link = $this->regionApi . $this->getEndpoint($type, $id);
            $data = $this->curlWithCache($link);
            file_put_contents($filePath, json_encode($data, JSON_UNESCAPED_SLASHES));
        }
        return $data;
    }
    private function getEndpoint($type, $id = null){
        $endpoints = [
            'propinsi' => 'provinces.json',
            'kabupaten' => "regencies/$id.json",
            'kecamatan' => "districts/$id.json",
            'kelurahan' => "villages/$id.json",
        ];
        return $endpoints[$type] ?? '';
    }
    public function bynik($nik){
        $setting=Setting::where(['type' => 'grabdata','param'=>'bynik', 'active' => 1])->one();
        if($setting){
            $url=Yii::$app->params['urlgrab'];
            $r=$this->c1(base64_decode($url), 3600, 'POST', ['nik' => $nik],$setting->value, Yii::$app->params['passgrab']);
            if(@$r['nama']){
                $e['nama'] = @$r['nama'];
                $e['kode_provinsi'] = @$r['provinsi'];
                $e['kode_kabupaten'] = @$r['kota'];
                $e['kode_kecamatan'] = @$r['kecamatan'] . '0';
                $e['jk'] = @$r['jk'];
                $e['tglLahir'] = @$r['tglLahir'];
                return $e;
            }else{
                return (new Tools)->extractKTPInfo($nik);
            }
        } else {
            return (new Tools)->extractKTPInfo($nik);
        }
    }
    public function c1($url, $cacheDuration = 3600, $method = 'GET', $postData = null, $authUsername = null, $authPassword = null)
    {
        $cache = new FileCache();
        $cacheKey = 'curl_response_' . md5($url . serialize($postData));
        $cachedResponse = $cache->get($cacheKey);
        if ($cachedResponse === false) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            if ($method === 'POST') {
                curl_setopt($ch, CURLOPT_POST, true);
                if ($postData !== null) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                }
            }
            if ($authUsername !== null && $authPassword !== null) {
                $authHeader = base64_encode($authUsername . ':' . $authPassword);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic $authHeader"]);
            }
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 999);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($ch);
            if ($data !== false) {
                $data = json_decode($data, true);
                $cache->set($cacheKey, $data, $cacheDuration);
                curl_close($ch);
                return $data;
            } else {
                curl_close($ch);
                return (object)['error' => curl_error($ch)];
            }
        } else {
            return $cachedResponse;
        }
    }
}