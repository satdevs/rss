<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\NamedaysTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\NamedaysTable Test Case
 */
class NamedaysTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\NamedaysTable
     */
    protected $Namedays;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Namedays',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Namedays') ? [] : ['className' => NamedaysTable::class];
        $this->Namedays = $this->getTableLocator()->get('Namedays', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Namedays);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\NamedaysTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
