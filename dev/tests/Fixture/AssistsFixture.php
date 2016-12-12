<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AssistsFixture
 *
 */
class AssistsFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'budget_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => 'Identificador presupuesto', 'precision' => null, 'autoIncrement' => null],
        'worker_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => 'Identificador trabajador', 'precision' => null, 'autoIncrement' => null],
        'overtime' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'delay' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'user_created_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'user_modified_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        '_indexes' => [
            'budget_id' => ['type' => 'index', 'columns' => ['budget_id'], 'length' => []],
            'user_created_id' => ['type' => 'index', 'columns' => ['user_created_id'], 'length' => []],
            'worker_id' => ['type' => 'index', 'columns' => ['worker_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'assists_ibfk_1' => ['type' => 'foreign', 'columns' => ['budget_id'], 'references' => ['budgets', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
            'assists_ibfk_2' => ['type' => 'foreign', 'columns' => ['user_created_id'], 'references' => ['users', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
            'assists_ibfk_3' => ['type' => 'foreign', 'columns' => ['worker_id'], 'references' => ['workers', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
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
            'budget_id' => 1,
            'worker_id' => 1,
            'overtime' => 1,
            'delay' => 1,
            'created' => '2015-10-13 17:12:51',
            'modified' => '2015-10-13 17:12:51',
            'user_created_id' => 1,
            'user_modified_id' => 1
        ],
    ];
}
