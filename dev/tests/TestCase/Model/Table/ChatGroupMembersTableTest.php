<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ChatGroupMembersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ChatGroupMembersTable Test Case
 */
class ChatGroupMembersTableTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.chat_group_members',
        'app.chat_groups',
        'app.users',
        'app.user_types',
        'app.charges',
        'app.survey_answers',
        'app.vacations'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('ChatGroupMembers') ? [] : ['className' => 'App\Model\Table\ChatGroupMembersTable'];        $this->ChatGroupMembers = TableRegistry::get('ChatGroupMembers', $config);    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ChatGroupMembers);

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
