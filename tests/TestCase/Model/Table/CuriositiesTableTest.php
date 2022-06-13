<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CuriositiesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CuriositiesTable Test Case
 */
class CuriositiesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CuriositiesTable
     */
    protected $Curiosities;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Curiosities',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Curiosities') ? [] : ['className' => CuriositiesTable::class];
        $this->Curiosities = $this->getTableLocator()->get('Curiosities', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Curiosities);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\CuriositiesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
