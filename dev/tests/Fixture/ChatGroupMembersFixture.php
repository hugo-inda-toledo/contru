<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ChatGroupMembersFixture
 *
 */
class ChatGroupMembersFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'chat_group_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'user_creator_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'fk_chat_group_members_chat_group1_idx' => ['type' => 'index', 'columns' => ['chat_group_id'], 'length' => []],
            'fk_chat_group_members_users1_idx' => ['type' => 'index', 'columns' => ['user_creator_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fk_chat_group_members_chat_group1' => ['type' => 'foreign', 'columns' => ['chat_group_id'], 'references' => ['chat_groups', 'id'], 'update' => 'noAction', 'delete' => 'noAction', 'length' => []],
            'fk_chat_group_members_users1' => ['type' => 'foreign', 'columns' => ['user_creator_id'], 'references' => ['users', 'id'], 'update' => 'noAction', 'delete' => 'noAction', 'length' => []],
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
            'chat_group_id' => 1,
            'user_creator_id' => 1,
            'created' => '2015-06-16 23:25:22'
        ],
    ];
}
