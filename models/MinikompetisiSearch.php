<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * MinikompetisiSearch represents the model behind the search form of `app\models\Minikompetisi`.
 */
class MinikompetisiSearch extends Minikompetisi
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'metode', 'status', 'created_by'], 'integer'],
            [['judul', 'tanggal', 'created_at', 'updated_at'], 'safe'],
            [['bobot_kualitas', 'bobot_harga'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
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
        $query = Minikompetisi::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'tanggal' => $this->tanggal,
            'metode' => $this->metode,
            'bobot_kualitas' => $this->bobot_kualitas,
            'bobot_harga' => $this->bobot_harga,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['ilike', 'judul', $this->judul]);

        return $dataProvider;
    }
}
