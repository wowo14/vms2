<?php
use yii\db\Migration;
class m250325_021402_alterpenilaianppk extends Migration {
    public function safeUp() {
        $table = 'penilaian_penyedia';
        $newColumns = [
            'bast' => $this->string(),
            'bast_diterimagudang' => $this->dateTime(),
        ];
        $schema = $this->db->getTableSchema($table);
        $existingColumns = array_keys($schema->columns);
        $missingColumns = array_diff(array_keys($newColumns), $existingColumns);
        if (!empty($missingColumns)) {
            foreach ($missingColumns as $colName) {
                $this->addColumn($table, $colName, $newColumns[$colName]);
            }
            $this->rebuildTable($table, $newColumns);
        }
    }
    private function rebuildTable($table, $newColumns) {
        $schema = $this->db->getTableSchema($table);
        $columns = [];
        foreach ($schema->columns as $col) {
            if ($col->name === 'id') {
                $columns['id'] = $this->primaryKey()->notNull();
            } elseif (array_key_exists($col->name, $newColumns)) {
                $columns[$col->name] = $newColumns[$col->name];
            } else {
                $columns[$col->name] = $this->getColumnType($col);
            }
        }
        foreach ($newColumns as $colName => $colType) {
            if (!isset($columns[$colName])) {
                $columns[$colName] = $colType;
            }
        }
        $tempTable = $table . '_temp';
        $this->createTable($tempTable, $columns);
        $columnNames = implode(', ', array_keys($schema->columns));
        $this->execute("INSERT INTO $tempTable ($columnNames) SELECT $columnNames FROM $table");
        $this->dropTable($table);
        $this->renameTable($tempTable, $table);
    }
    private function getColumnType($col) {
        if ($col->isPrimaryKey) {
            return $this->primaryKey()->notNull();
        }
        if ($col->phpType === 'integer') {
            return $this->integer();
        }
        if ($col->phpType === 'double') {
            return $this->double();
        }
        if ($col->phpType === 'boolean') {
            return $this->boolean();
        }
        if (strpos($col->dbType, 'text') !== false) {
            return $this->text();
        }
        return $this->string($col->size);
    }
    public function safeDown() {
        echo "m250325_021402_alterpenilaianppk cannot be reverted.\n";
        return false;
    }
}
