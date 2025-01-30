<?php

namespace app\widgets;

use DateTime;
use Yii;
use Intervention\Image\ImageManagerStatic as Image;
use yii\base\Widget;
use yii\caching\FileCache;
use yii\helpers\ArrayHelper;

class Tools extends Widget {
  public function __construct() {
  }
  public function init() {
    parent::init();
  }
  public function getUsia($date) {
    $datetime1 = new DateTime($date);
    $datetime2 = new DateTime();
    $diff = $datetime1->diff($datetime2);
    return $diff->y . " tahun " . $diff->m . " bulan " . $diff->d . " hari";
  }
  public function asSpellOut($val) {
    $val = round($val, 2);
    $terbilang = $des = '';
    if (strpos(Yii::$app->formatter->asSpellOut($val), 'titik') > 0 || !empty(strpos(Yii::$app->formatter->asSpellOut($val), 'titik'))) {
      $ar = explode('titik', Yii::$app->formatter->asSpellOut($val));
      if (count($ar) <= 2) {
        $mentah = explode('.', $val);
        if (strlen($mentah[1]) == 1) {
          $des = $ar[1] . ' puluh ';
        } else {
          $des = $ar[1];
        }
        $terbilang = $ar[0] . ' rupiah ' . $des . ' sen ';
      }
    } else {
      $terbilang = Yii::$app->formatter->asSpellOut($val) . ' rupiah';
    }
    return $terbilang;
  }
  public function asCurrency($val) {
    return ($val > 0) ? Yii::$app->formatter->asCurrency($val, 'Rp.') : '';
  }
  public function reverseCurrency($formattedAmount){ // asumsi currency indonesia
    // Yii::error(json_encode($formattedAmount));
     $rawAmount = str_replace("\u{00a0}", ' ', $formattedAmount);
    if (strpos($formattedAmount, 'Rp') !== false) {
      $rawAmount = str_replace(['Rp', ' '], '', $rawAmount);
    }
    $rawAmount = str_replace('.', '', $rawAmount);
    $rawAmount = str_replace(',', '.', $rawAmount);
    return (float) $rawAmount;
  }
  // public function reverseCurrency($formattedAmount) {
  //   // Convert to UTF-8 if it's not already (to avoid issues with invalid UTF-8 characters)
  //   if (!mb_check_encoding($formattedAmount, 'UTF-8')) {
  //     $formattedAmount = mb_convert_encoding($formattedAmount, 'UTF-8');
  //   }
  //   // Clean the formatted amount string
  //   $rawAmount = str_replace("\u{00a0}", ' ', $formattedAmount);
  //   if (strpos($formattedAmount, 'Rp') !== false) {
  //     // Remove 'Rp' and spaces safely
  //     $rawAmount = str_replace(['Rp', ' '], '', $rawAmount);
  //   }
  //   // Remove dots (thousands separator in Indonesia) and replace commas with dots (decimal separator)
  //   $rawAmount = str_replace('.', '', $rawAmount);
  //   $rawAmount = str_replace(',', '.', $rawAmount);
  //   // Return the final amount as a float
  //   return (float) $rawAmount;
  // }
  public function sumCurrency($data) {
    $dd = collect($data)->map(function ($e) {
      $d = $this->reverseCurrency(($e));
      return $d;
    })->toArray();
    return Yii::$app->formatter->asCurrency(array_sum($dd));
  }
  public function toRoman($number) {
    $map = ['M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1];
    $returnValue = '';
    while ($number > 0) {
      foreach ($map as $roman => $int) {
        if ($number >= $int) {
          $number -= $int;
          $returnValue .= $roman;
          break;
        }
      }
    }
    return $returnValue;
  }
  public function quarterly($day) {
    $start_quarter = ceil(date('m', strtotime("$day")) / 3);
    $start_month = ($start_quarter * 3) - 2;
    $start_year = date('Y', strtotime("$day"));
    return $monday = date('Y-m-d', strtotime('first monday ' . $start_year . '-' . $start_month));
  }
  public function getcurrentroleuser() {
    $currentrole = \Yii::$app->authManager->getRolesByUser(\Yii::$app->user->id);
    foreach ($currentrole as $roles) {
      $role[] = ['name' => $roles->name];
    }
    return ArrayHelper::map($role, 'name', 'name');
  }
  public function isAdmin() {
    $role = $this->getcurrentroleuser();
    return (ArrayHelper::keyExists('admin', $role)) ? true : false;
  }
  public function isAdminOrOperator() {
    $role = $this->getcurrentroleuser();
    return (ArrayHelper::keyExists('admin', $role) || ArrayHelper::keyExists('operator', $role)) ? true : false;
  }
  public function isAdminOrVendor() {
    $role = $this->getcurrentroleuser();
    return (ArrayHelper::keyExists('admin', $role) || ArrayHelper::keyExists('vendor', $role)) ? true : false;
  }
  public function dow() {
    $hari = [1 => 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
    setlocale(LC_ALL, 'id_ID');
    $today = (86400 * (date("N")));
    for ($i = 0; $i < 7; $i++) {
      $days[] = $hari[date('N', strtotime(strftime('%A', time() - $today + ($i * 86400))))];
      // $days[] = strftime('%A', time() - $today + ($i*86400));
    }
    return $days;
  }
  public function curl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 999);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($ch);
    if ($data === false) {
      $error = curl_error($ch);
      curl_close($ch);
      return (object)['error' => $error];
    } else {
      $data = json_decode($data);
      curl_close($ch);
      return $data;
    }
  }
  public function curlWithCache($url, $cacheDuration = 3600, $method = null) {
    $cache = new FileCache(); // You can use other cache components as needed
    $cacheKey = 'curl_response_' . md5($url);
    $cachedResponse = $cache->get($cacheKey);
    if ($cachedResponse === false) {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, isset($method));
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
  public function upload($base64Data, $filename) {
    $extensions = [
      'image/svg+xml' => 'svg',
      'image/bmp' => 'bmp',
      'image/png' => 'png',
      'image/jpeg' => 'jpg',
      'image/gif' => 'gif',
      'application/pdf' => 'pdf', // Added support for PDF
    ];
    list($imageType, $base64Data) = explode(';', $base64Data);
    list(, $base64Data) = explode(',', $base64Data);
    $imageType = str_replace('data:', '', $imageType);
    if (isset($extensions[$imageType])) {
      $extension = $extensions[$imageType];
      $binaryData = base64_decode($base64Data);
      $filename .= '.' . $extension;
      $filePath = Yii::getAlias('@uploads') . $filename;
      file_put_contents($filePath, $binaryData);
      if (in_array($extension, ['png', 'jpg', 'jpeg', 'gif'])) {
        $this->resizeImageToMaxSize($filePath, 512 * 1024);
        // $filename=$this->convertavif($filePath);
      }
      return $filename;
    } else {
      echo "Unsupported file format: $imageType";
      die;
    }
  }
  public function resizeImageToMaxSize($filePath, $maxSize) {
    $supportedExtensions = ['png', 'jpg', 'jpeg', 'gif'];
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    if (in_array($extension, $supportedExtensions)) {
      $image = imagecreatefromstring(file_get_contents($filePath));
      if ($image !== false) {
        $originalSize = filesize($filePath);
        $targetQuality = 90; // Initial quality setting
        $step = 10; // Step for adjusting quality
        while ($originalSize > $maxSize && $targetQuality >= 10) {
          ob_start();
          imagejpeg($image, null, $targetQuality);
          $compressedData = ob_get_contents();
          ob_end_clean();
          file_put_contents($filePath, $compressedData);
          $originalSize = filesize($filePath);
          $targetQuality -= $step;
        }
        imagedestroy($image);
      }
    }
  }
  public function convertavif($imageFile, $targetDir = null, $quality = 50) {
    if ($targetDir === null) {
      $targetDir = Yii::getAlias('@uploads');
    }
    $image = Image::make($imageFile);
    $fileName = pathinfo($imageFile, PATHINFO_FILENAME);
    $targetFilePath = $targetDir . DIRECTORY_SEPARATOR . $fileName . '.avif';
    $image->encode('avif', $quality)->save($targetFilePath);
    unlink($imageFile);
    return $fileName . '.avif';
  }
  public function extractKTPInfo($ktpNumber) { // 16 digit KTP INDONESIA
    $info = [];
    $info['kode_provinsi'] = substr($ktpNumber, 0, 2);
    $info['kode_kabupaten'] = $info['kode_provinsi'] . substr($ktpNumber, 2, 2);
    $info['kode_kecamatan'] = $info['kode_kabupaten'] . substr($ktpNumber, 4, 2);
    $tanggal_lahir = substr($ktpNumber, 6, 2);
    $info['bulan_lahir'] = substr($ktpNumber, 8, 2);
    $tahun_lahir = substr($ktpNumber, 10, 2);
    $info['tahun_lahir'] = ($tahun_lahir < 20) ? '20' . $tahun_lahir : '19' . $tahun_lahir;
    $info['tanggal_lahir'] = ($tanggal_lahir > 40) ? $tanggal_lahir - 40 : $tanggal_lahir;
    $info['tgl_lahir'] = $info['tahun_lahir'] . '-' . $info['bulan_lahir'] . '-' . strlen($info['tanggal_lahir']) < 2 ? '0' . $info['tanggal_lahir'] : $info['tanggal_lahir'];
    $info['nomor_urut'] = substr($ktpNumber, 12, 4);
    $info['jenis_kelamin'] = ($tanggal_lahir > 31) ? 'Perempuan' : 'Laki-laki';
    $info['sex'] = ($tanggal_lahir > 31) ? 'Wanita' : 'Pria';
    $info['sex_en'] = ($tanggal_lahir > 31) ? 'Female' : 'Male';
    $info['usia'] = $this->getUsia($info['tahun_lahir'] . '-' . $info['bulan_lahir'] . '-' . $info['tanggal_lahir']);
    return $info;
  }
  public function isVendor() {
    if (is_array(Yii::$app->session->get('roles')) && !empty(Yii::$app->session->get('roles'))) {
      $role = Yii::$app->session->get('roles');
    } else {
      $role = $this->getcurrentroleuser();
    }
    return (ArrayHelper::keyExists('vendor', $role)) ? true : false;
  }
}
