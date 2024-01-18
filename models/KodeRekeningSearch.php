<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\KodeRekening;
class KodeRekeningSearch extends KodeRekening{
    public function rules()
    {
        return [
            [['id', 'parent', 'is_active', 'tahun_anggaran', 'created_by', 'updated_by'], 'integer'],
            [['kode', 'rekening', 'created_at', 'updated_at'], 'safe'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = KodeRekening::find()->cache(self::cachetime(), self::settagdep('tag_koderekening'));
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'parent' => $this->parent,
            'is_active' => $this->is_active,
            'tahun_anggaran' => $this->tahun_anggaran,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'kode', $this->kode])
            ->andFilterWhere(['like', 'rekening', $this->rekening])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);
        return $dataProvider;
    }
}
