<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "minikompetisi_trial_answer".
 *
 * @property int $id
 * @property int $question_id
 * @property int $penawaran_id
 * @property string $jawaban
 * @property string|null $catatan
 * @property string|null $tanggal_isi
 * @property string|null $pengisi
 * @property string|null $created_at
 * @property int|null $created_by
 *
 * @property MinikompetisiTrialQuestion $question
 * @property MinikompetisiPenawaran $penawaran
 */
class MinikompetisiTrialAnswer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'minikompetisi_trial_answer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['question_id', 'penawaran_id', 'jawaban'], 'required'],
            [['question_id', 'penawaran_id', 'created_by'], 'integer'],
            [['jawaban', 'catatan'], 'string'],
            [['tanggal_isi', 'created_at'], 'safe'],
            [['pengisi'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question_id' => 'Pertanyaan',
            'penawaran_id' => 'Penawaran (Vendor)',
            'jawaban' => 'Jawaban',
            'catatan' => 'Catatan',
            'tanggal_isi' => 'Tanggal Mengisi',
            'pengisi' => 'Pengisi',
            'created_at' => 'Dibuat Pada',
            'created_by' => 'Dibuat Oleh',
        ];
    }

    /**
     * Gets query for [[Question]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(MinikompetisiTrialQuestion::class, ['id' => 'question_id']);
    }

    /**
     * Gets query for [[Penawaran]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPenawaran()
    {
        return $this->hasOne(MinikompetisiPenawaran::class, ['id' => 'penawaran_id']);
    }
}
