<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DraftRab;
class DraftRabSearch extends DraftRab{
    public function rules()
    {
        return [
            [['id', 'tahun_anggaran', 'created_by', 'updated_by', 'is_completed'], 'integer'],
            [['kode_program', 'nama_program', 'kode_kegiatan', 'nama_kegiatan', 'kode_rekening', 'uraian_anggaran', 'sumber_dana', 'created_at', 'updated_at'], 'safe'],
            [['jumlah_anggaran', 'sisa_anggaran'], 'number'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = DraftRab::find()->cache(self::cachetime(), self::settagdep('tag_draftrab'));
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'tahun_anggaran' => $this->tahun_anggaran,
            'jumlah_anggaran' => $this->jumlah_anggaran,
            'sisa_anggaran' => $this->sisa_anggaran,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'is_completed' => $this->is_completed,
        ]);

        $query->andFilterWhere(['like', 'kode_program', $this->kode_program])
            ->andFilterWhere(['like', 'nama_program', $this->nama_program])
            ->andFilterWhere(['like', 'kode_kegiatan', $this->kode_kegiatan])
            ->andFilterWhere(['like', 'nama_kegiatan', $this->nama_kegiatan])
            ->andFilterWhere(['like', 'kode_rekening', $this->kode_rekening])
            ->andFilterWhere(['like', 'uraian_anggaran', $this->uraian_anggaran])
            ->andFilterWhere(['like', 'sumber_dana', $this->sumber_dana])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);
        return $dataProvider;
    }
}
