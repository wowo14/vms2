<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Negosiasi;
class NegosiasiSearch extends Negosiasi{
    public function rules()
    {
        return [
            [['id', 'penawaran_id', 'accept','pp_accept','penyedia_accept', 'created_by'], 'integer'],
            [['ammount'], 'number'],
            [['created_at'], 'safe'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = Negosiasi::find()->cache(self::cachetime(), self::settagdep('tag_negosiasi'));
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'penawaran_id' => $this->penawaran_id,
            'accept' => $this->accept,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
        ]);
        $query->andFilterWhere(['like', 'ammount', $this->ammount])
        ;
        return $dataProvider;
    }
}
