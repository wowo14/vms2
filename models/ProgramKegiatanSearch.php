<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ProgramKegiatan;
class ProgramKegiatanSearch extends ProgramKegiatan{
    public function rules()
    {
        return [
            [['id', 'tahun_anggaran', 'is_active', 'created_by', 'updated_by'], 'integer'],
            [['code', 'desc', 'parent', 'type', 'created_at', 'updated_at'], 'safe'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = ProgramKegiatan::find()->cache(self::cachetime(), self::settagdep('tag_programkegiatan'));
        $dataProvider = new ActiveDataProvider([
            'query' => $query,'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'tahun_anggaran' => $this->tahun_anggaran,
            'is_active' => $this->is_active,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);
        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'desc', $this->desc])
            ->andFilterWhere(['like', 'parent', $this->parent])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);
        return $dataProvider;
    }
}
