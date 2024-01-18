<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Dpp;
class DppSearch extends Dpp{
    public function rules()
    {
        return [
            [['id', 'paket_id', 'created_by', 'updated_by'], 'integer'],
            [['nomor_dpp', 'tanggal_dpp', 'bidang_bagian', 'status_review', 'is_approved', 'nomor_persetujuan', 'created_at', 'updated_at'], 'safe'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = Dpp::find()->cache(self::cachetime(), self::settagdep('tag_dpp'));
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'tanggal_dpp' => $this->tanggal_dpp,
            'paket_id' => $this->paket_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'nomor_dpp', $this->nomor_dpp])
            ->andFilterWhere(['like', 'bidang_bagian', $this->bidang_bagian])
            ->andFilterWhere(['like', 'status_review', $this->status_review])
            ->andFilterWhere(['like', 'is_approved', $this->is_approved])
            ->andFilterWhere(['like', 'nomor_persetujuan', $this->nomor_persetujuan]);
        return $dataProvider;
    }
}
