<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * FilesFixture
 *
 */
class FilesFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'post_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'rendition_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'user_creator_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'user_modifier_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'filename' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
        'file_path' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
        'type' => ['type' => 'string', 'length' => 45, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'fk_files_posts1_idx' => ['type' => 'index', 'columns' => ['post_id'], 'length' => []],
            'fk_files_users1_idx' => ['type' => 'index', 'columns' => ['user_creator_id'], 'length' => []],
            'fk_files_renditions1_idx' => ['type' => 'index', 'columns' => ['rendition_id'], 'length' => []],
            'fk_files_users2_idx' => ['type' => 'index', 'columns' => ['user_modifier_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fk_files_posts1' => ['type' => 'foreign', 'columns' => ['post_id'], 'references' => ['posts', 'id'], 'update' => 'noAction', 'delete' => 'noAction', 'length' => []],
            'fk_files_users1' => ['type' => 'foreign', 'columns' => ['user_creator_id'], 'references' => ['users', 'id'], 'update' => 'noAction', 'delete' => 'noAction', 'length' => []],
            'fk_files_renditions1' => ['type' => 'foreign', 'columns' => ['rendition_id'], 'references' => ['renditions', 'id'], 'update' => 'noAction', 'delete' => 'noAction', 'length' => []],
            'fk_files_users2' => ['type' => 'foreign', 'columns' => ['user_modifier_id'], 'references' => ['users', 'id'], 'update' => 'noAction', 'delete' => 'noAction', 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'latin1_swedish_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'post_id' => 1,
            'rendition_id' => 1,
            'user_creator_id' => 1,
            'user_modifier_id' => 1,
            'filename' => 'Lorem ipsum dolor sit amet',
            'file_path' => 'Lorem ipsum dolor sit amet',
            'type' => 'Lorem ipsum dolor sit amet',
            'created' => '2015-06-16 23:26:02',
            'modified' => '2015-06-16 23:26:02'
        ],
    ];
}
