<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Pengurusperusahaan;
class PengurusperusahaanSearch extends Pengurusperusahaan{
    public function rules()
    {
        return [
            [['id', 'unit', 'is_vendor', 'is_internal', 'is_active', 'created_by', 'updated_by', 'user_id', 'penyedia_id'], 'integer'],
            [['nama', 'nik', 'alamat', 'email', 'telepon', 'nip', 'jabatan', 'instansi', 'created_at', 'updated_at', 'password'], 'safe'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = Pengurusperusahaan::find()->cache(self::cachetime(), self::settagdep('tag_pengurusperusahaan'));
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>['defaultOrder' => ['id' => SORT_DESC]],
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->where(['or',['is_vendor'=>1],['not',['penyedia_id'=>NULL]]]);
        $query->andFilterWhere([
            'id' => $this->id,
            'unit' => $this->unit,
            'is_vendor' => $this->is_vendor,
            'is_internal' => $this->is_internal,
            'is_active' => $this->is_active,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'user_id' => $this->user_id,
            'penyedia_id' => $this->penyedia_id,
        ]);
        $query->andFilterWhere(['like', 'nama', $this->nama])
            ->andFilterWhere(['like', 'nik', $this->nik])
            ->andFilterWhere(['like', 'alamat', $this->alamat])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'telepon', $this->telepon])
            ->andFilterWhere(['like', 'nip', $this->nip])
            ->andFilterWhere(['like', 'jabatan', $this->jabatan])
            ->andFilterWhere(['like', 'instansi', $this->instansi])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at])
            ->andFilterWhere(['like', 'password', $this->password]);
        return $dataProvider;
    }
}
