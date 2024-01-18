<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AktaPenyedia;
class AktaPenyediaSearch extends AktaPenyedia{
    public function rules()
    {
        return [
            [['id', 'penyedia_id', 'created_by', 'updated_by'], 'integer'],
            [['jenis_akta', 'nomor_akta', 'tanggal_akta', 'notaris', 'file_akta', 'created_at', 'updated_at'], 'safe'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = AktaPenyedia::find()->cache(self::cachetime(), self::settagdep('tag_aktapenyedia'));
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
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'jenis_akta', $this->jenis_akta])
            ->andFilterWhere(['like', 'nomor_akta', $this->nomor_akta])
            ->andFilterWhere(['like', 'tanggal_akta', $this->tanggal_akta])
            ->andFilterWhere(['like', 'notaris', $this->notaris])
            ->andFilterWhere(['like', 'file_akta', $this->file_akta])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);
        return $dataProvider;
    }
}
