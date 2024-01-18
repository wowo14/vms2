<?php
use yii\db\Migration;
class m231215_035705_seedUser extends Migration
{
    public function safeUp()
    {
        $this->batchInsert(
            '{{%user}}',
            ['id', 'username', 'auth_key', 'password_hash', 'password_reset_token', 'email', 'status', 'created_at', 'updated_at'],
            [
                [1, 'admin', 'epTrgBpG2fNRjV8a_VD9h8A-Dx5-bkqO', '$2y$13$pwOiV7xjkUabZjowRCb6wOnGY3M3PLSXMCoINhN2fqZhxkK7OHPRW', null, 'admin@wawa.com', 10, 1677032075, 1677032075],
            ]
        );
    }
    public function safeDown()
    {
        $this->delete('{{%user}}', ['id' => 1]);
    }
}
