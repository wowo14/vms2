<?php
namespace app\helpers;
use Hashids\Hashids;
class HashidsHelper extends \yii\base\Component {
    private $hashids;
    public function init() {
        $this->hashids = new Hashids('89asdunu!*@()@!(@SD', 10); // min length 10
    }
    public function encode($id) {
        return $this->hashids->encode($id);
    }
    public function decode($hash) {
        $decoded = $this->hashids->decode($hash);
        return $decoded[0] ?? null;
    }
}
