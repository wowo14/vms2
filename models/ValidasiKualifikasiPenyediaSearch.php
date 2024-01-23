<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ValidasiKualifikasiPenyedia;
class ValidasiKualifikasiPenyediaSearch extends ValidasiKualifikasiPenyedia{
    public function rules()
    {
        return [
            [['id', 'penyedia_id', 'is_active', 'created_by', 'updated_by'], 'integer'],
            [['paket_pengadaan_id', 'keperluan', 'created_at', 'updated_at'], 'safe'],
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
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'penyedia_id' => $this->penyedia_id,
            'is_active' => $this->is_active,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'paket_pengadaan_id', $this->paket_pengadaan_id])
            ->andFilterWhere(['like', 'keperluan', $this->keperluan])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);
        return $dataProvider;
    }
}
