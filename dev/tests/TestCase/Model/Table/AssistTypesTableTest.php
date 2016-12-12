<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AssistTypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\AssistTypesTable Test Case
 */
class AssistTypesTableTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.assist_types',
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
        'app.currencies_values',
        'app.assists_assist_types'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('AssistTypes') ? [] : ['className' => 'App\Model\Table\AssistTypesTable'];
        $this->AssistTypes = TableRegistry::get('AssistTypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->AssistTypes);

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
}
