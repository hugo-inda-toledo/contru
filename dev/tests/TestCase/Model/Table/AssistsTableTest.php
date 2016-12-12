<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AssistsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\AssistsTable Test Case
 */
class AssistsTableTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.assists',
        'app.budgets',
        'app.buildings',
        'app.buildings_users',
        'app.users',
        'app.groups',
        'app.buildings_users_users',
        'app.approvals',
        'app.budget_approvals',
        'app.budget_states',
        'app.observations',
        'app.currency',
        'app.bonuses',
        'app.workers',
        'app.completed_tasks',
        'app.schedules',
        'app.user_createds',
        'app.user_modifieds',
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
        'app.bonus_types',
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
        $config = TableRegistry::exists('Assists') ? [] : ['className' => 'App\Model\Table\AssistsTable'];
        $this->Assists = TableRegistry::get('Assists', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Assists);

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
