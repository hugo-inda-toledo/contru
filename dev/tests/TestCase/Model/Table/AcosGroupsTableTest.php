<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AcosGroupsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\AcosGroupsTable Test Case
 */
class AcosGroupsTableTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.acos_groups',
        'app.acos',
        'app.groups',
        'app.aros',
        'app.permissions',
        'app.users',
        'app.approvals',
        'app.budget_approvals',
        'app.budgets',
        'app.buildings',
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
        'app.observations'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('AcosGroups') ? [] : ['className' => 'App\Model\Table\AcosGroupsTable'];
        $this->AcosGroups = TableRegistry::get('AcosGroups', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->AcosGroups);

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
