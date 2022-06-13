<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TodaysTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TodaysTable Test Case
 */
class TodaysTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TodaysTable
     */
    protected $Todays;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Todays',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Todays') ? [] : ['className' => TodaysTable::class];
        $this->Todays = $this->getTableLocator()->get('Todays', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Todays);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\TodaysTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
