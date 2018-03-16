<?php

use yii\db\Migration;

/**
 * Handles the creation of table `md_doc_search`.
 */
class m180302_161643_create_searchable_table extends Migration
{
    protected $tableName = 'md_doc_search';

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $driverName = $this->db->driverName;
        if ($driverName !== 'mysql') {
            throw new \yii\base\NotSupportedException("$driverName is not supported");
        }

        $this->createTable($this->tableName, [
            'path' => $this->string(191),
            'title' => $this->string(),
            'content' => $this->text(),
            'changed_at' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->addPrimaryKey("{$this->tableName}_pk", $this->tableName, 'path');
        $this->execute("ALTER TABLE $this->tableName ADD FULLTEXT INDEX {$this->tableName}_fulltext_title (title)");
        $this->execute("ALTER TABLE $this->tableName ADD FULLTEXT INDEX {$this->tableName}_fulltext (content)");
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable($this->tableName);
    }
}
