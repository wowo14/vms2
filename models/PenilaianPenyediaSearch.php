<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PenilaianPenyedia;
class PenilaianPenyediaSearch extends PenilaianPenyedia{
    public function rules()
    {
        return [
            [['id', 'dpp_id', 'created_by', 'updated_by'], 'integer'],
            [['unit_kerja','bast','bast_diterimagudang', 'nama_perusahaan', 'alamat_perusahaan', 'paket_pekerjaan', 'lokasi_pekerjaan', 'nomor_kontrak', 'jangka_waktu', 'tanggal_kontrak', 'metode_pemilihan', 'details', 'pengguna_anggaran', 'pejabat_pembuat_komitmen', 'created_at', 'updated_at'], 'safe'],
            [['nilai_kontrak'], 'number'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = PenilaianPenyedia::find()->cache(self::cachetime(), self::settagdep('tag_penilaianpenyedia'));
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
            'tanggal_kontrak' => $this->tanggal_kontrak,
            'nilai_kontrak' => $this->nilai_kontrak,
            'dpp_id' => $this->dpp_id,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'unit_kerja', $this->unit_kerja])
            ->andFilterWhere(['like', 'nama_perusahaan', $this->nama_perusahaan])
            ->andFilterWhere(['like', 'alamat_perusahaan', $this->alamat_perusahaan])
            ->andFilterWhere(['like', 'paket_pekerjaan', $this->paket_pekerjaan])
            ->andFilterWhere(['like', 'lokasi_pekerjaan', $this->lokasi_pekerjaan])
            ->andFilterWhere(['like', 'nomor_kontrak', $this->nomor_kontrak])
            ->andFilterWhere(['like', 'jangka_waktu', $this->jangka_waktu])
            ->andFilterWhere(['like', 'metode_pemilihan', $this->metode_pemilihan])
            ->andFilterWhere(['like', 'details', $this->details])
            ->andFilterWhere(['like', 'pengguna_anggaran', $this->pengguna_anggaran])
            ->andFilterWhere(['like', 'bast', $this->bast])
            ->andFilterWhere(['like', 'bast_diterimagudang', $this->bast_diterimagudang])
            ->andFilterWhere(['like', 'pejabat_pembuat_komitmen', $this->pejabat_pembuat_komitmen]);
        return $dataProvider;
    }
}
