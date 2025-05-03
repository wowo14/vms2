<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Attachment;
class AttachmentSearch extends Attachment{
    public function rules()
    {
        return [
            [['id', 'user_id', 'size', 'jenis_dokumen', 'updated_by', 'created_by'], 'integer'],
            [['name', 'uri', 'mime', 'type', 'created_at', 'updated_at'], 'safe'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = Attachment::find()->cache(self::cachetime(), self::settagdep('tag_attachment'));
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'size' => $this->size,
            'jenis_dokumen' => $this->jenis_dokumen,
            'updated_by' => $this->updated_by,
            'created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'uri', $this->uri])
            ->andFilterWhere(['like', 'mime', $this->mime])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);
        return $dataProvider;
    }
}
