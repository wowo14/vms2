<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ReportPenyedia;

/**
 * ReportPenyediaSearch represents the model behind the search form of `app\models\ReportPenyedia`.
 */
class ReportPenyediaSearch extends ReportPenyedia
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'penyedia_id', 'penilaian_id', 'created_by', 'updated_by'], 'integer'],
            [['nama_penyedia', 'alamat', 'kota', 'telepon', 'produk_ditawarkan', 'jenis_pekerjaan', 'nama_paket', 'bidang', 'source', 'created_at', 'updated_at'], 'safe'],
            [['nilai_evaluasi'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ReportPenyedia::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'penyedia_id' => $this->penyedia_id,
            'penilaian_id' => $this->penilaian_id,
            'nilai_evaluasi' => $this->nilai_evaluasi,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'nama_penyedia', $this->nama_penyedia])
            ->andFilterWhere(['like', 'alamat', $this->alamat])
            ->andFilterWhere(['like', 'kota', $this->kota])
            ->andFilterWhere(['like', 'telepon', $this->telepon])
            ->andFilterWhere(['like', 'produk_ditawarkan', $this->produk_ditawarkan])
            ->andFilterWhere(['like', 'jenis_pekerjaan', $this->jenis_pekerjaan])
            ->andFilterWhere(['like', 'nama_paket', $this->nama_paket])
            ->andFilterWhere(['like', 'bidang', $this->bidang])
            ->andFilterWhere(['like', 'source', $this->source])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);

        return $dataProvider;
    }
}
