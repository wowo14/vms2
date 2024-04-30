<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PengalamanPenyedia;
class PengalamanPenyediaSearch extends PengalamanPenyedia {
    public function rules() {
        return [
            [['id', 'penyedia_id', 'created_by', 'updated_by'], 'integer'],
            [['paket_pengadaan_id', 'link', 'pekerjaan', 'lokasi', 'instansi_pemberi_tugas', 'alamat_instansi', 'tanggal_kontrak', 'tanggal_selesai_kontrak', 'created_at', 'updated_at', 'file'], 'safe'],
            [['nilai_kontrak'], 'number'],
        ];
    }
    public function scenarios() {
        return Model::scenarios();
    }
    public function search($params) {
        $query = PengalamanPenyedia::find()->cache(self::cachetime(), self::settagdep('tag_pengalamanpenyedia'));
        $dataProvider = new ActiveDataProvider([
            'query' => $query, 'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'penyedia_id' => $this->penyedia_id,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'nilai_kontrak' => $this->nilai_kontrak,
        ]);
        $query->andFilterWhere(['like', 'paket_pengadaan_id', $this->paket_pengadaan_id])
            ->andFilterWhere(['like', 'link', $this->link])
            ->andFilterWhere(['like', 'pekerjaan', $this->pekerjaan])
            ->andFilterWhere(['like', 'lokasi', $this->lokasi])
            ->andFilterWhere(['like', 'instansi_pemberi_tugas', $this->instansi_pemberi_tugas])
            ->andFilterWhere(['like', 'alamat_instansi', $this->alamat_instansi])
            ->andFilterWhere(['like', 'tanggal_kontrak', $this->tanggal_kontrak])
            ->andFilterWhere(['like', 'tanggal_selesai_kontrak', $this->tanggal_selesai_kontrak])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at])
            ->andFilterWhere(['like', 'file', $this->file]);
        return $dataProvider;
    }
}
