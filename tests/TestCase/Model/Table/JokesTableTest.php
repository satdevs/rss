<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\JokesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\JokesTable Test Case
 */
class JokesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\JokesTable
     */
    protected $Jokes;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Jokes',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Jokes') ? [] : ['className' => JokesTable::class];
        $this->Jokes = $this->getTableLocator()->get('Jokes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Jokes);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\JokesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
