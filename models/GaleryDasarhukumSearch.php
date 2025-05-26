<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\GaleryDasarhukum;
class GaleryDasarhukumSearch extends GaleryDasarhukum{
    public function rules()
    {
        return [
            [['id', 'is_active', 'created_by', 'updated_by'], 'integer'],
            [['judul', 'summary', 'foto', 'file_pdf', 'tags', 'created_at', 'updated_at', 'kategori', 'nomor', 'tanggal_ditetapkan', 'penerbit'], 'safe'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = GaleryDasarhukum::find()->cache(self::cachetime(), self::settagdep('tag_galerydasarhukum'));
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'is_active' => $this->is_active,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'judul', $this->judul])
            ->andFilterWhere(['like', 'summary', $this->summary])
            ->andFilterWhere(['like', 'foto', $this->foto])
            ->andFilterWhere(['like', 'file_pdf', $this->file_pdf])
            ->andFilterWhere(['like', 'tags', $this->tags])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at])
            ->andFilterWhere(['like', 'kategori', $this->kategori])
            ->andFilterWhere(['like', 'nomor', $this->nomor])
            ->andFilterWhere(['like', 'tanggal_ditetapkan', $this->tanggal_ditetapkan])
            ->andFilterWhere(['like', 'penerbit', $this->penerbit]);
        return $dataProvider;
    }
}
