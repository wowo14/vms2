<?php
namespace app\models;
use app\models\ValidasiKualifikasiPenyedia;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
class ValidasiKualifikasiPenyediaSearch extends ValidasiKualifikasiPenyedia{
    public function rules()
    {
        return [
            [['id', 'is_active', 'created_by', 'updated_by'], 'integer'],
            [['paket_pengadaan_id', 'penyedia_id', 'template','keperluan', 'created_at', 'updated_at'], 'safe'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = ValidasiKualifikasiPenyedia::find()->cache(self::cachetime(), self::settagdep('tag_validasikualifikasipenyedia'));
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
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
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);
        $query->andFilterWhere(['like', new Expression('pp.nomor || pp.nama_paket'), $this->paket_pengadaan_id])
            ->andFilterWhere(['like', new Expression('p.nama_perusahaan'), $this->penyedia_id])
            ->andFilterWhere(['like', 'keperluan', $this->keperluan])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);
        return $dataProvider;
    }
}
