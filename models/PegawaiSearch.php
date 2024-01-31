<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Pegawai;

class PegawaiSearch extends Pegawai
{
    public function rules()
    {
        return [
            [['id', 'id_user'], 'integer'],
            [['nik', 'nama', 'alamat', 'telp', 'status', 'hak_akses', 'username', 'password'], 'safe'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = Pegawai::find()->cache(self::cachetime(), self::settagdep('tag_pegawai'));
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ]
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'id_user' => $this->id_user,
        ]);
        $query->andFilterWhere(['like', 'nik', $this->nik])
            ->andFilterWhere(['like', 'nama', $this->nama])
            ->andFilterWhere(['like', 'alamat', $this->alamat])
            ->andFilterWhere(['like', 'telp', $this->telp])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'hak_akses', $this->hak_akses])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'password', $this->password]);
        return $dataProvider;
    }
}
