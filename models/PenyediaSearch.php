<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Penyedia;
class PenyediaSearch extends Penyedia{
    public function rules()
    {
        return [
            [['id', 'active', 'is_cabang', 'created_by', 'updated_by'], 'integer'],
            [['npwp', 'nama_perusahaan', 'alamat_perusahaan', 'nomor_telepon', 'email_perusahaan', 'tanggal_pendirian', 'kategori_usaha', 'akreditasi', 'propinsi', 'kota', 'kode_pos', 'mobile_phone', 'website', 'alamat_kantorpusat', 'telepon_kantorpusat', 'fax_kantorpusat', 'email_kantorpusat', 'created_at', 'updated_at'], 'safe'],
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
                $where=array_merge($where,['id' => Yii::$app->session->get('companygroup')]);
            }else{
                $where = ['id' => Yii::$app->session->get('companygroup')];
            }
        }
        $query = Penyedia::where($where);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'active' => $this->active,
            'is_cabang' => $this->is_cabang,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);
        $query->andFilterWhere(['like', 'npwp', $this->npwp])
            ->andFilterWhere(['like', 'nama_perusahaan', $this->nama_perusahaan])
            ->andFilterWhere(['like', 'alamat_perusahaan', $this->alamat_perusahaan])
            ->andFilterWhere(['like', 'nomor_telepon', $this->nomor_telepon])
            ->andFilterWhere(['like', 'email_perusahaan', $this->email_perusahaan])
            ->andFilterWhere(['like', 'tanggal_pendirian', $this->tanggal_pendirian])
            ->andFilterWhere(['like', 'kategori_usaha', $this->kategori_usaha])
            ->andFilterWhere(['like', 'akreditasi', $this->akreditasi])
            ->andFilterWhere(['like', 'propinsi', $this->propinsi])
            ->andFilterWhere(['like', 'kota', $this->kota])
            ->andFilterWhere(['like', 'kode_pos', $this->kode_pos])
            ->andFilterWhere(['like', 'mobile_phone', $this->mobile_phone])
            ->andFilterWhere(['like', 'website', $this->website])
            ->andFilterWhere(['like', 'alamat_kantorpusat', $this->alamat_kantorpusat])
            ->andFilterWhere(['like', 'telepon_kantorpusat', $this->telepon_kantorpusat])
            ->andFilterWhere(['like', 'fax_kantorpusat', $this->fax_kantorpusat])
            ->andFilterWhere(['like', 'email_kantorpusat', $this->email_kantorpusat])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);
        return $dataProvider;
    }
}
