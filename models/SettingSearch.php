<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Setting;
class SettingSearch extends Setting {
    public function rules() {
        return [
            [['id', 'active'], 'integer'],
            [['type', 'param', 'value'], 'safe'],
        ];
    }
    public function scenarios() {
        return Model::scenarios();
    }
    public function search($params) {
        $query = Setting::find()->cache(self::cachetime(), self::settagdep('tag_setting'));
        $dataProvider = new ActiveDataProvider([
            'query' => $query, 'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'active' => $this->active,
        ]);
        $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'param', $this->param])
            ->andFilterWhere(['like', 'value', $this->value]);
        return $dataProvider;
    }
}
