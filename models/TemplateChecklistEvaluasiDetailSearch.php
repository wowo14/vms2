<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TemplateChecklistEvaluasiDetail;
class TemplateChecklistEvaluasiDetailSearch extends TemplateChecklistEvaluasiDetail{
    public function rules()
    {
        return [
            [['id', 'header_id', 'created_by', 'updated_by'], 'integer'],
            [['uraian', 'created_at', 'updated_at'], 'safe'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = TemplateChecklistEvaluasiDetail::find()->cache(self::cachetime(), self::settagdep('tag_templatechecklistevaluasidetail'));
        $dataProvider = new ActiveDataProvider([
            'query' => $query,'sort' => ['defaultOrder' => ['id' => SORT_DESC]],

        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'header_id' => $this->header_id,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'uraian', $this->uraian])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);
        return $dataProvider;
    }
}
