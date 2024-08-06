<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Ijinusaha;

class IjinusahaSearch extends Ijinusaha {
    public function rules() {
        return [
            [['id', 'is_active'], 'integer'],
            [['instansi_pemberi', 'created_by', 'updated_by', 'penyedia_id', 'nomor_ijinusaha', 'tanggal_ijinusaha', 'file_ijinusaha', 'tanggal_berlaku_sampai', 'kualifikasi', 'klasifikasi', 'created_at', 'updated_at', 'tags', 'jenis_ijin'], 'safe'],
        ];
    }
    public function scenarios() {
        return Model::scenarios();
    }
    public function search($params, $where = null) {
        if(Yii::$app->tools->isVendor()){
            if(is_array($where)){
                $where=array_merge($where,['penyedia_id' => Yii::$app->session->get('companygroup')]);
            }else{
                $where = ['penyedia_id' => Yii::$app->session->get('companygroup')];
            }
        }
        $query = Ijinusaha::where($where);
        $dataProvider = new ActiveDataProvider([
            'query' => $query, 'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->joinWith(['vendor p']);
        $query->joinWith(['usercreated uc']);
        $query->joinWith(['userupdated up']);
        $query->andFilterWhere([
            'id' => $this->id,
            'is_active' => $this->is_active,
            'penyedia_id' => $this->penyedia_id,
        ]);
        $query->andFilterWhere(['like', 'instansi_pemberi', $this->instansi_pemberi])
            ->andFilterWhere(['like', 'nomor_ijinusaha', $this->nomor_ijinusaha])
            // ->andFilterWhere(['like', 'p.nama_perusahaan', $this->penyedia_id])
            ->andFilterWhere(['like', 'uc.username', $this->created_by])
            ->andFilterWhere(['like', 'up.username', $this->updated_by])
            ->andFilterWhere(['like', 'tanggal_ijinusaha', $this->tanggal_ijinusaha])
            ->andFilterWhere(['like', 'file_ijinusaha', $this->file_ijinusaha])
            ->andFilterWhere(['like', 'tanggal_berlaku_sampai', $this->tanggal_berlaku_sampai])
            ->andFilterWhere(['like', 'kualifikasi', $this->kualifikasi])
            ->andFilterWhere(['like', 'klasifikasi', $this->klasifikasi])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at])
            ->andFilterWhere(['like', 'tags', $this->tags])
            ->andFilterWhere(['like', 'jenis_ijin', $this->jenis_ijin]);
        return $dataProvider;
    }
}
