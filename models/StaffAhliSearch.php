<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\StaffAhli;
class StaffAhliSearch extends StaffAhli{
    public function rules()
    {
        return [
            [['id', 'penyedia_id', 'created_by', 'updated_by'], 'integer'],
            [['nama', 'tanggal_lahir', 'alamat', 'email', 'jenis_kelamin', 'pendidikan', 'warga_negara', 'lama_pengalaman', 'file', 'keahlian', 'created_at', 'updated_at', 'spesifikasi_pekerjaan'], 'safe'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = StaffAhli::find()->cache(self::cachetime(), self::settagdep('tag_staffahli'));
        $dataProvider = new ActiveDataProvider([
            'query' => $query,'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
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
        $query->andFilterWhere(['like', 'nama', $this->nama])
            ->andFilterWhere(['like', 'tanggal_lahir', $this->tanggal_lahir])
            ->andFilterWhere(['like', 'alamat', $this->alamat])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'jenis_kelamin', $this->jenis_kelamin])
            ->andFilterWhere(['like', 'pendidikan', $this->pendidikan])
            ->andFilterWhere(['like', 'warga_negara', $this->warga_negara])
            ->andFilterWhere(['like', 'lama_pengalaman', $this->lama_pengalaman])
            ->andFilterWhere(['like', 'file', $this->file])
            ->andFilterWhere(['like', 'keahlian', $this->keahlian])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at])
            ->andFilterWhere(['like', 'spesifikasi_pekerjaan', $this->spesifikasi_pekerjaan]);
        return $dataProvider;
    }
}
