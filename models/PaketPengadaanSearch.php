<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PaketPengadaan;
class PaketPengadaanSearch extends PaketPengadaan{
    public function rules()
    {
        return [
            [['id', 'created_by','tahun_anggaran', 'approval_by'], 'integer'],
            [['nomor','admin_ppkom','unit', 'linksirup','created_at','updated_at', 'tanggal_dpp','tanggal_persetujuan','nomor_persetujuan','kategori_pengadaan','addition','tanggal_paket', 'tanggal_reject','alasan_reject', 'nama_paket', 'kode_program', 'kode_kegiatan', 'kode_rekening', 'ppkom', 'metode_pengadaan'], 'safe'],
            [['pagu'], 'number'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = PaketPengadaan::find()->cache(self::cachetime(),self::settagdep('tag_paketpengadaan'));
        if(self::isStaff()){
            $query->andWhere(['created_by' => Yii::$app->user->identity->id]);
        }
        if(self::isPPK()){
            $query->joinWith('pejabatppkom ppkom');
            $query->andWhere(['ppkom.id_user'=>Yii::$app->user->identity->id]);
        }
        if(self::isPP()){
            $query->joinWith('dpp.pejabat p');
            $query->andWhere(['p.id_user'=> Yii::$app->user->identity->id]);
        }
        if(self::isStaffpp()){
            $query->joinWith('dpp.staffadmin s');
            $query->andWhere(['s.id_user'=> Yii::$app->user->identity->id]);
        }
        $query->joinWith('unitnya u');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 10, // Set to 10 records per page
            ],
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'pagu' => $this->pagu,
            'created_by' => $this->created_by,
            'tahun_anggaran' => $this->tahun_anggaran,
            'approval_by' => $this->approval_by,
        ]);
        $query->andFilterWhere(['like', 'nomor', $this->nomor])
            ->andFilterWhere(['between', 'datetime(tanggal_paket)', ($this->range($this->tanggal_paket, 's')), ($this->range($this->tanggal_paket, 'e'))])
            ->andFilterWhere(['like', 'u.unit', $this->unit])
            ->andFilterWhere(['like', 'nama_paket', $this->nama_paket])
            ->andFilterWhere(['like', 'linksirup', $this->linksirup])
            ->andFilterWhere(['like', 'kode_program', $this->kode_program])
            ->andFilterWhere(['like', 'kode_kegiatan', $this->kode_kegiatan])
            ->andFilterWhere(['like', 'kode_rekening', $this->kode_rekening])
            ->andFilterWhere(['like', 'ppkom', $this->ppkom])
            ->andFilterWhere(['like', 'metode_pengadaan', $this->metode_pengadaan]);
        return $dataProvider;
    }
}
