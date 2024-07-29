<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PenugasanPemilihanpenyedia;
class PenugasanPemilihanpenyediaSearch extends PenugasanPemilihanpenyedia{
    public function rules()
    {
        return [
            [['id', 'dpp_id', 'pejabat', 'admin', 'created_by', 'updated_by'], 'integer'],
            [['nomor_tugas', 'tanggal_tugas', 'created_at', 'updated_at'], 'safe'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = PenugasanPemilihanpenyedia::find()->cache(self::cachetime(), self::settagdep('tag_penugasanpemilihanpenyedia'));
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'dpp_id' => $this->dpp_id,
            'tanggal_tugas' => $this->tanggal_tugas,
            'pejabat' => $this->pejabat,
            'admin' => $this->admin,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'nomor_tugas', $this->nomor_tugas]);
        return $dataProvider;
    }
}
