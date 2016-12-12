<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AssistsDataTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\AssistsDataTable Test Case
 */
class AssistsDataTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\AssistsDataTable
     */
    public $AssistsData;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.assists_data',
        'app.buildings',
        'app.budgets',
        'app.users',
        'app.groups',
        'app.approvals',
        'app.buildings_users',
        'app.budget_approvals',
        'app.budget_states',
        'app.observations',
        'app.currency',
        'app.user_modifieds',
        'app.assists',
        'app.workers',
        'app.bonuses',
        'app.bonus_details',
        'app.budget_items',
        'app.units',
        'app.completed_tasks',
        'app.schedules',
        'app.user_createds',
        'app.progress',
        'app.payment_statements',
        'app.payment_statement_states',
        'app.payment_statement_approvals',
        'app.budget_items_payment_statements',
        'app.budget_items_schedules',
        'app.deal_details',
        'app.deals',
        'app.guide_entries',
        'app.iconstruye_imports',
        'app.user_uploaders',
        'app.guide_exits',
        'app.subcontracts',
        'app.invoices',
        'app.purchase_orders',
        'app.materials',
        'app.ic_consumo',
        'app.assist_types',
        'app.assists_assist_types',
        'app.currencies',
        'app.currencies_values',
        'app.valoresmonedas',
        'app.salary_reports',
        'app.sf_buildings',
        'app.softlands'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('AssistsData') ? [] : ['className' => 'App\Model\Table\AssistsDataTable'];
        $this->AssistsData = TableRegistry::get('AssistsData', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->AssistsData);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
