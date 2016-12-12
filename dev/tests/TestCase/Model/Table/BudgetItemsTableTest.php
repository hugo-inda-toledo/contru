<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\BudgetItemsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\BudgetItemsTable Test Case
 */
class BudgetItemsTableTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.budget_items',
        'app.budgets',
        'app.buildings',
        'app.users',
        'app.groups',
        'app.approvals',
        'app.budget_approvals',
        'app.observations',
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
        'app.renditions',
        'app.rendition_items',
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
        $config = TableRegistry::exists('BudgetItems') ? [] : ['className' => 'App\Model\Table\BudgetItemsTable'];        $this->BudgetItems = TableRegistry::get('BudgetItems', $config);    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->BudgetItems);

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
