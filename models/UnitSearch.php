<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Unit;
class UnitSearch extends Unit{
    public function rules()
    {
        return [
            [['id', 'is_vip', 'aktif'], 'integer'],
            [['kode', 'unit', 'fk_instalasi', 'logo'], 'safe'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = Unit::find()->cache(self::cachetime(), self::settagdep('tag_unit'));
        $dataProvider = new ActiveDataProvider([
            'query' => $query,'sort' => ['defaultOrder' => ['id' => SORT_DESC]],

        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'is_vip' => $this->is_vip,
            'aktif' => $this->aktif,
        ]);

        $query->andFilterWhere(['like', 'kode', $this->kode])
            ->andFilterWhere(['like', 'unit', $this->unit])
            ->andFilterWhere(['like', 'fk_instalasi', $this->fk_instalasi])
            ->andFilterWhere(['like', 'logo', $this->logo]);
        return $dataProvider;
    }
}
