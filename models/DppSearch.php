<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Dpp;
class DppSearch extends Dpp{
    public function rules()
    {
        return [
            [['id', 'created_by', 'updated_by'], 'integer'],
            [['nomor_dpp', 'paket_id', 'kode','pejabat_pengadaan', 'admin_pengadaan', 'tanggal_dpp', 'bidang_bagian', 'status_review', 'is_approved', 'nomor_persetujuan', 'created_at', 'updated_at'], 'safe'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = Dpp::find()->cache(self::cachetime(), self::settagdep('tag_dpp'));
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->joinWith(['paketpengadaan p']);
        $query->where(['p.tanggal_reject' => NULL, 'p.alasan_reject' => NULL])->orWhere(['p.tanggal_reject' => '', 'p.alasan_reject' => '']);
        $query->joinWith(['unit u']);
        $query->joinWith(['reviews r']);
        $query->joinWith(['pejabat p2']);
        $query->joinWith(['staffadmin s']);
        if(self::isStaffpp()){
            $query->andWhere(['s.id_user' => Yii::$app->user->identity->id]);
        }
        if(self::isPP()){
            $chief=\app\models\Pegawai::findOne(self::profile('kepalapengadaan'));
            if(!$chief->id_user==Yii::$app->user->identity->id){
                $query->andWhere(['p2.id_user' => Yii::$app->user->identity->id]);
            }
        }
        if(self::isStaff()){
            $query->andWhere(['p.created_by' => Yii::$app->user->identity->id]);
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);
        $query->andFilterWhere(['like', 'nomor_dpp', $this->nomor_dpp])
            ->andFilterWhere(['like', 'bidang_bagian', $this->bidang_bagian])
            ->andFilterWhere(['like', 'p.nama_paket', $this->paket_id])
            ->andFilterWhere(['like', 'p2.nama', $this->pejabat_pengadaan])
            ->andFilterWhere(['between', 'tanggal_dpp', ($this->range($this->tanggal_dpp, 's')), ($this->range($this->tanggal_dpp, 'e'))])
            ->andFilterWhere(['like', 's.nama', $this->admin_pengadaan])
            ->andFilterWhere(['like', 'dpp.kode', $this->kode])
            ->andFilterWhere(['like', 'status_review', $this->status_review])
            ->andFilterWhere(['like', 'is_approved', $this->is_approved])
            ->andFilterWhere(['like', 'nomor_persetujuan', $this->nomor_persetujuan]);
        return $dataProvider;
    }
}
