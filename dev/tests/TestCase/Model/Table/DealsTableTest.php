<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\DealsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\DealsTable Test Case
 */
class DealsTableTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.deals',
        'app.workers',
        'app.assists',
        'app.budgets',
        'app.buildings',
        'app.users',
        'app.groups',
        'app.approvals',
        'app.budget_approvals',
        'app.budget_states',
        'app.observations',
        'app.currency',
        'app.bonuses',
        'app.bonus_types',
        'app.user_modifieds',
        'app.budget_items',
        'app.units',
        'app.completed_tasks',
        'app.schedules',
        'app.user_createds',
        'app.progress',
        'app.payment_statements',
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
        $config = TableRegistry::exists('Deals') ? [] : ['className' => 'App\Model\Table\DealsTable'];        $this->Deals = TableRegistry::get('Deals', $config);    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Deals);

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
