<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
class PenawaranPengadaanSearch extends PenawaranPengadaan{
    public function rules()
    {
        return [
            [['id',  'penilaian', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['nomor', 'paket_id', 'penyedia_id', 'kode', 'tanggal_mendaftar', 'nilai_penawaran','ip_client', 'masa_berlaku', 'lampiran_penawaran', 'lampiran_penawaran_harga'], 'safe'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params,$where=null)
    {
        if(Yii::$app->tools->isVendor()){
            if(is_array($where)){
                $where=array_merge($where,['penyedia_id' => Yii::$app->session->get('companygroup')]);
            }else{
                $where = ['penyedia_id' => Yii::$app->session->get('companygroup')];
            }
        }
        $query = PenawaranPengadaan::where($where);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->joinWith(['paketpengadaan pp']);
        $query->joinWith(['vendor p']);
        $query->andFilterWhere([
            'id' => $this->id,
            'tanggal_mendaftar' => $this->tanggal_mendaftar,
            'penilaian' => $this->penilaian,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
            'penyedia_id' => $this->penyedia_id,
            'paket_id' => $this->paket_id,
        ]);
        $query->andFilterWhere(['like', 'nomor', $this->nomor])
            ->andFilterWhere(['like', 'kode', $this->kode])
            // ->andFilterWhere(['like', new Expression('pp.nomor || pp.nama_paket'),$this->paket_id])
            // ->andFilterWhere(['like', 'p.nama_perusahaan', $this->penyedia_id])
            ->andFilterWhere(['like', 'ip_client', $this->ip_client])
            ->andFilterWhere(['like', 'masa_berlaku', $this->masa_berlaku])
            ->andFilterWhere(['like', 'lampiran_penawaran', $this->lampiran_penawaran])
            ->andFilterWhere(['like', 'lampiran_penawaran_harga', $this->lampiran_penawaran_harga]);
        return $dataProvider;
    }
}
