<?php

use yii\db\Migration;

class m261017_091600_create_trial_form_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // Table minikompetisi_trial_question (Soal-soal trial produk)
        $this->createTable('{{%minikompetisi_trial_question}}', [
            'id' => $this->primaryKey(),
            'minikompetisi_id' => $this->integer()->notNull(),
            'item_id' => $this->integer()->notNull(), // Produk yang ditrial
            'pertanyaan' => $this->text()->notNull(),
            'tipe_jawaban' => $this->integer()->notNull()->defaultValue(1), // 1: skala 1-5, 2: ya/tidak, 3: teks
            'urutan' => $this->integer()->defaultValue(0),
            'created_at' => $this->dateTime(),
            'created_by' => $this->integer(),
        ], $tableOptions);

        // Table minikompetisi_trial_answer (Jawaban trial dari operator lapangan)
        $this->createTable('{{%minikompetisi_trial_answer}}', [
            'id' => $this->primaryKey(),
            'question_id' => $this->integer()->notNull(),
            'penawaran_id' => $this->integer()->notNull(), // Vendor/penawaran yang ditrial
            'jawaban' => $this->text()->notNull(),
            'catatan' => $this->text(),
            'tanggal_isi' => $this->date(),
            'pengisi' => $this->string(255), // Nama operator lapangan
            'created_at' => $this->dateTime(),
            'created_by' => $this->integer(),
        ], $tableOptions);

        // Note: SQLite doesn't support foreign key constraints via ALTER TABLE
        // Relations are handled at application level via Yii2 ActiveRecord
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%minikompetisi_trial_answer}}');
        $this->dropTable('{{%minikompetisi_trial_question}}');
    }
}
