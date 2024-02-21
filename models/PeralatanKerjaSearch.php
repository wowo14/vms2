<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Peralatankerja;
class PeralatanKerjaSearch extends Peralatankerja{
    public function rules()
    {
        return [
            [['id', 'penyedia_id', 'jumlah', 'created_by', 'updated_by'], 'integer'],
            [['nama_alat', 'kapasitas', 'merk_tipe', 'tahun_pembuatan', 'kondisi', 'lokasi_sekarang', 'status_kepemilikan', 'bukti_kepemilikan', 'created_at', 'updated_at', 'file'], 'safe'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = Peralatankerja::find()->cache(self::cachetime(), self::settagdep('tag_peralatankerja'));
        $dataProvider = new ActiveDataProvider([
            'query' => $query,'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'penyedia_id' => $this->penyedia_id,
            'jumlah' => $this->jumlah,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);
        $query->andFilterWhere(['like', 'nama_alat', $this->nama_alat])
            ->andFilterWhere(['like', 'kapasitas', $this->kapasitas])
            ->andFilterWhere(['like', 'merk_tipe', $this->merk_tipe])
            ->andFilterWhere(['like', 'tahun_pembuatan', $this->tahun_pembuatan])
            ->andFilterWhere(['like', 'kondisi', $this->kondisi])
            ->andFilterWhere(['like', 'lokasi_sekarang', $this->lokasi_sekarang])
            ->andFilterWhere(['like', 'status_kepemilikan', $this->status_kepemilikan])
            ->andFilterWhere(['like', 'bukti_kepemilikan', $this->bukti_kepemilikan])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at])
            ->andFilterWhere(['like', 'file', $this->file]);
        return $dataProvider;
    }
}
