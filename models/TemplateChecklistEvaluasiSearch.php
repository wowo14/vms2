<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TemplateChecklistEvaluasi;
class TemplateChecklistEvaluasiSearch extends TemplateChecklistEvaluasi {
    public function rules() {
        return [
            [['id', 'created_by', 'updated_by'], 'integer'],
            [['template', 'jenis_evaluasi', 'element', 'created_at', 'updated_at'], 'safe'],
        ];
    }
    public function scenarios() {
        return Model::scenarios();
    }
    public function search($params) {
        $query = TemplateChecklistEvaluasi::find()->cache(self::cachetime(), self::settagdep('tag_templatechecklistevaluasi'));
        $dataProvider = new ActiveDataProvider([
            'query' => $query, 'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);
        $query->andFilterWhere(['like', 'template', $this->template])
            ->andFilterWhere(['like', 'jenis_evaluasi', $this->jenis_evaluasi])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);
        return $dataProvider;
    }
}
