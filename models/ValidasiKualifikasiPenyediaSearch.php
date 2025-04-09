<?php
namespace app\models;
use app\models\ValidasiKualifikasiPenyedia;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
class ValidasiKualifikasiPenyediaSearch extends ValidasiKualifikasiPenyedia {
    // public $tgl_paket;
    public function rules() {
        return [
            [['id', 'is_active', 'created_by', 'updated_by'], 'integer'],
            [['paket_pengadaan_id', 'penyedia_id', 'tgl_paket', 'template', 'keperluan', 'created_at', 'updated_at'], 'safe'],
        ];
    }
    public function scenarios() {
        return Model::scenarios();
    }
    public function search($params) {
        $query = ValidasiKualifikasiPenyedia::find()->cache(self::cachetime(), self::settagdep('tag_validasikualifikasipenyedia'));
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['penyedia_id' => SORT_ASC, 'id' => SORT_DESC]],
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->joinWith(['paketpengadaan pp']);
        $query->joinWith(['vendor p']);
        $query->andFilterWhere([
            'id' => $this->id,
            'is_active' => $this->is_active,
            'penyedia_id' => $this->penyedia_id,
            'paket_pengadaan_id' => $this->paket_pengadaan_id,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);
        $query
            // ->andFilterWhere(['like', new Expression('pp.nomor || pp.nama_paket'), $this->paket_pengadaan_id])
            ->andFilterWhere(['between', 'datetime(pp.tanggal_paket)', $this->range($this->tgl_paket, 's'), $this->range($this->tgl_paket, 'e')])
            // ->andFilterWhere(['like', new Expression('p.nama_perusahaan'), $this->penyedia_id])
            ->andFilterWhere(['like', 'keperluan', $this->keperluan])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);
        return $dataProvider;
    }
}
