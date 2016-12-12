<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\BudgetItemsSchedulesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\BudgetItemsSchedulesTable Test Case
 */
class BudgetItemsSchedulesTableTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.budget_items_schedules',
        'app.budget_items',
        'app.budgets',
        'app.buildings',
        'app.users',
        'app.groups',
        'app.approvals',
        'app.budget_approvals',
        'app.budget_states',
        'app.observations',
        'app.currency',
        'app.assists',
        'app.workers',
        'app.bonuses',
        'app.bonus_types',
        'app.user_modifieds',
        'app.completed_tasks',
        'app.schedules',
        'app.user_createds',
        'app.progress',
        'app.payment_statements',
        'app.deals',
        'app.deal_details',
        'app.renditions',
        'app.rendition_items',
        'app.currencies',
        'app.currencies_values',
        'app.units',
        'app.guide_entries',
        'app.iconstruye_imports',
        'app.files',
        'app.posts',
        'app.salaries',
        'app.user_uploaders',
        'app.guide_exits',
        'app.invoices',
        'app.purchase_orders'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('BudgetItemsSchedules') ? [] : ['className' => 'App\Model\Table\BudgetItemsSchedulesTable'];        $this->BudgetItemsSchedules = TableRegistry::get('BudgetItemsSchedules', $config);    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->BudgetItemsSchedules);

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
