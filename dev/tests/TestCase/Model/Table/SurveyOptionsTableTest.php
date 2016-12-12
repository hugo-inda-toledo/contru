<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SurveyOptionsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SurveyOptionsTable Test Case
 */
class SurveyOptionsTableTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.survey_options',
        'app.surveys',
        'app.users',
        'app.user_types',
        'app.charges',
        'app.survey_answers',
        'app.vacations',
        'app.configurations',
        'app.users_creator',
        'app.users_modifier'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('SurveyOptions') ? [] : ['className' => 'App\Model\Table\SurveyOptionsTable'];        $this->SurveyOptions = TableRegistry::get('SurveyOptions', $config);    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->SurveyOptions);

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
