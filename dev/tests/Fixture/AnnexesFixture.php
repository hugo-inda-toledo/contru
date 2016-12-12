<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AnnexesFixture
 *
 */
class AnnexesFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'user_creator_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'user_modifier_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'name' => ['type' => 'string', 'length' => 120, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
        'number' => ['type' => 'integer', 'length' => 10, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'fk_annexes_users1_idx' => ['type' => 'index', 'columns' => ['user_creator_id'], 'length' => []],
            'fk_annexes_users2_idx' => ['type' => 'index', 'columns' => ['user_modifier_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'idphones_UNIQUE' => ['type' => 'unique', 'columns' => ['id'], 'length' => []],
            'fk_annexes_users1' => ['type' => 'foreign', 'columns' => ['user_creator_id'], 'references' => ['users', 'id'], 'update' => 'noAction', 'delete' => 'noAction', 'length' => []],
            'fk_annexes_users2' => ['type' => 'foreign', 'columns' => ['user_modifier_id'], 'references' => ['users', 'id'], 'update' => 'noAction', 'delete' => 'noAction', 'length' => []],
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
            'user_creator_id' => 1,
            'user_modifier_id' => 1,
            'name' => 'Lorem ipsum dolor sit amet',
            'number' => 1,
            'created' => '2015-06-16 23:11:40',
            'modified' => '2015-06-16 23:11:40'
        ],
    ];
}
