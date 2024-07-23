<?php
namespace app\models;
use app\models\HistoriReject;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
class HistoriRejectSearch extends HistoriReject{
    public function rules()
    {
        return [
            [['id',  'user_id', 'created_at'], 'integer'],
            [['nomor', 'paket_id','nama_paket', 'alasan_reject', 'tanggal_reject', 'kesimpulan', 'tanggal_dikembalikan', 'tanggapan_ppk', 'file_tanggapan'], 'safe'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = HistoriReject::find()->cache(self::cachetime(), self::settagdep('tag_historireject'));
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                   'defaultOrder' => [
                       'id' => SORT_DESC
                   ]
               ]
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->joinWith(['paketpengadaan pp']);
        $query->andFilterWhere([
            'id' => $this->id,
            // 'paket_id' => $this->paket_id,
            'user_id' => $this->user_id,
            // 'tanggal_reject' => $this->tanggal_reject,
            // 'tanggal_dikembalikan' => $this->tanggal_dikembalikan,
            'created_at' => $this->created_at,
        ]);
        $query->andFilterWhere(['like', 'nomor', $this->nomor])
            ->andFilterWhere(['like', new Expression('pp.nomor || pp.nama_paket'),$this->paket_id])
            ->andFilterWhere(['between', 'histori_reject.tanggal_reject', ($this->range($this->tanggal_reject, 's')), ($this->range($this->tanggal_reject, 'e'))])
            ->andFilterWhere(['between', 'histori_reject.tanggal_dikembalikan', ($this->range($this->tanggal_dikembalikan, 's')), ($this->range($this->tanggal_dikembalikan, 'e'))])
            ->andFilterWhere(['like', 'nama_paket', $this->nama_paket])
            ->andFilterWhere(['like', 'alasan_reject', $this->alasan_reject])
            ->andFilterWhere(['like', 'kesimpulan', $this->kesimpulan])
            ->andFilterWhere(['like', 'tanggapan_ppk', $this->tanggapan_ppk])
            ->andFilterWhere(['like', 'file_tanggapan', $this->file_tanggapan]);
        return $dataProvider;
    }
}
