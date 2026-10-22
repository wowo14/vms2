<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "minikompetisi_trial_question".
 *
 * @property int $id
 * @property int $minikompetisi_id
 * @property int $item_id
 * @property string $pertanyaan
 * @property int $tipe_jawaban
 * @property int $urutan
 * @property string|null $created_at
 * @property int|null $created_by
 *
 * @property Minikompetisi $minikompetisi
 * @property MinikompetisiItem $item
 * @property MinikompetisiTrialAnswer[] $minikompetisiTrialAnswers
 */
class MinikompetisiTrialQuestion extends \yii\db\ActiveRecord
{
    const TIPE_SKALA = 1; // Skala 1-5
    const TIPE_YA_TIDAK = 2; // Ya/Tidak
    const TIPE_TEKS = 3; // Teks bebas

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'minikompetisi_trial_question';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['minikompetisi_id', 'item_id', 'pertanyaan', 'tipe_jawaban'], 'required'],
            [['minikompetisi_id', 'item_id', 'tipe_jawaban', 'urutan', 'created_by'], 'integer'],
            [['pertanyaan'], 'string'],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'minikompetisi_id' => 'Minikompetisi',
            'item_id' => 'Produk',
            'pertanyaan' => 'Pertanyaan',
            'tipe_jawaban' => 'Tipe Jawaban',
            'urutan' => 'Urutan',
            'created_at' => 'Dibuat Pada',
            'created_by' => 'Dibuat Oleh',
        ];
    }

    /**
     * Gets query for [[Minikompetisi]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMinikompetisi()
    {
        return $this->hasOne(Minikompetisi::class, ['id' => 'minikompetisi_id']);
    }

    /**
     * Gets query for [[Item]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(MinikompetisiItem::class, ['id' => 'item_id']);
    }

    /**
     * Gets query for [[MinikompetisiTrialAnswers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMinikompetisiTrialAnswers()
    {
        return $this->hasMany(MinikompetisiTrialAnswer::class, ['question_id' => 'id']);
    }

    /**
     * Get tipe jawaban labels
     */
    public static function getTipeJawabanLabels()
    {
        return [
            self::TIPE_SKALA => 'Skala 1-5',
            self::TIPE_YA_TIDAK => 'Ya/Tidak',
            self::TIPE_TEKS => 'Teks Bebas',
        ];
    }

    /**
     * Get tipe jawaban label
     */
    public function getTipeJawabanLabel()
    {
        return self::getTipeJawabanLabels()[$this->tipe_jawaban] ?? 'Unknown';
    }
}
