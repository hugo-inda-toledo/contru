<?php
use Migrations\AbstractMigration;

class RenameUserModifiedToProgress extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('progress');
        $table->renameColumn('user_modified', 'user_modified_id');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('progress');
        $table->renameColumn('user_modified_id', 'user_modified');
    }
}
