<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Produk;
class ProdukSearch extends Produk{
    public function rules()
    {
        return [
            [['id', 'active', 'created_by', 'updated_by'], 'integer'],
            [['kode_kbki', 'nama_produk', 'merk', 'status_merk', 'nama_pemilik_merk', 'nomor_produk_penyedia', 'unit_pengukuran', 'jenis_produk', 'nilai_tkdn', 'nomor_sni', 'garansi_produk', 'spesifikasi_produk', 'layanan_lain', 'komponen_biaya', 'lokasi_tempat_usaha', 'keterangan_lainya', 'barcode', 'created_at', 'updated_at'], 'safe'],
            [['hargapasar', 'hargabeli', 'hargahps', 'hargalainya'], 'number'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = Produk::find()->cache(self::cachetime(), self::settagdep('tag_produk'));
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'active' => $this->active,
            'hargapasar' => $this->hargapasar,
            'hargabeli' => $this->hargabeli,
            'hargahps' => $this->hargahps,
            'hargalainya' => $this->hargalainya,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'kode_kbki', $this->kode_kbki])
            ->andFilterWhere(['like', 'nama_produk', $this->nama_produk])
            ->andFilterWhere(['like', 'merk', $this->merk])
            ->andFilterWhere(['like', 'status_merk', $this->status_merk])
            ->andFilterWhere(['like', 'nama_pemilik_merk', $this->nama_pemilik_merk])
            ->andFilterWhere(['like', 'nomor_produk_penyedia', $this->nomor_produk_penyedia])
            ->andFilterWhere(['like', 'unit_pengukuran', $this->unit_pengukuran])
            ->andFilterWhere(['like', 'jenis_produk', $this->jenis_produk])
            ->andFilterWhere(['like', 'nilai_tkdn', $this->nilai_tkdn])
            ->andFilterWhere(['like', 'nomor_sni', $this->nomor_sni])
            ->andFilterWhere(['like', 'garansi_produk', $this->garansi_produk])
            ->andFilterWhere(['like', 'spesifikasi_produk', $this->spesifikasi_produk])
            ->andFilterWhere(['like', 'layanan_lain', $this->layanan_lain])
            ->andFilterWhere(['like', 'komponen_biaya', $this->komponen_biaya])
            ->andFilterWhere(['like', 'lokasi_tempat_usaha', $this->lokasi_tempat_usaha])
            ->andFilterWhere(['like', 'keterangan_lainya', $this->keterangan_lainya])
            ->andFilterWhere(['like', 'barcode', $this->barcode])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);
        return $dataProvider;
    }
}
