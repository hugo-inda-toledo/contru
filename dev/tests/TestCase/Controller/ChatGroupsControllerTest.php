<?php
namespace App\Test\TestCase\Controller;

use App\Controller\ChatGroupsController;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\ChatGroupsController Test Case
 */
class ChatGroupsControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.chat_groups',
        'app.users',
        'app.user_types',
        'app.charges',
        'app.survey_answers',
        'app.vacations',
        'app.chat_group_members',
        'app.messages'
    ];

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test view method
     *
     * @return void
     */
    public function testView()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test add method
     *
     * @return void
     */
    public function testAdd()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
