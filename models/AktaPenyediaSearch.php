<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AktaPenyedia;
class AktaPenyediaSearch extends AktaPenyedia {
    public function rules() {
        return [
            [['id',], 'integer'],
            [['jenis_akta', 'penyedia_id', 'created_by', 'updated_by', 'nomor_akta', 'tanggal_akta', 'notaris', 'file_akta', 'created_at', 'updated_at'], 'safe'],
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
        $query = AktaPenyedia::find()->cache(self::cachetime(), self::settagdep('tag_aktapenyedia'))->where($where);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
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
            'penyedia_id' => $this->penyedia_id,
        ]);
        $query->andFilterWhere(['like', 'jenis_akta', $this->jenis_akta])
            ->andFilterWhere(['like', 'nomor_akta', $this->nomor_akta])
            // ->andFilterWhere(['like', 'p.nama_perusahaan', $this->penyedia_id])
            ->andFilterWhere(['like', 'uc.username', $this->created_by])
            ->andFilterWhere(['like', 'up.username', $this->updated_by])
            ->andFilterWhere(['like', 'tanggal_akta', $this->tanggal_akta])
            ->andFilterWhere(['like', 'notaris', $this->notaris])
            ->andFilterWhere(['like', 'file_akta', $this->file_akta])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);
        return $dataProvider; //MARZU
    }
}
