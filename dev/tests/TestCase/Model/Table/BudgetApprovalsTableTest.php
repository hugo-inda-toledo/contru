<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\BudgetApprovalsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\BudgetApprovalsTable Test Case
 */
class BudgetApprovalsTableTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.budget_approvals',
        'app.budgets',
        'app.buildings',
        'app.users',
        'app.groups',
        'app.approvals',
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
        'app.budget_items',
        'app.units',
        'app.deals',
        'app.guide_entries',
        'app.iconstruye_imports',
        'app.files',
        'app.posts',
        'app.renditions',
        'app.rendition_items',
        'app.salaries',
        'app.user_uploaders',
        'app.guide_exits',
        'app.invoices',
        'app.purchase_orders',
        'app.payment_statements',
        'app.currencies',
        'app.currencies_values'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('BudgetApprovals') ? [] : ['className' => 'App\Model\Table\BudgetApprovalsTable'];        $this->BudgetApprovals = TableRegistry::get('BudgetApprovals', $config);    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->BudgetApprovals);

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
