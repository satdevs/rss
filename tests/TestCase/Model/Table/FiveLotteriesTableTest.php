<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\FiveLotteriesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\FiveLotteriesTable Test Case
 */
class FiveLotteriesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\FiveLotteriesTable
     */
    protected $FiveLotteries;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.FiveLotteries',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('FiveLotteries') ? [] : ['className' => FiveLotteriesTable::class];
        $this->FiveLotteries = $this->getTableLocator()->get('FiveLotteries', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->FiveLotteries);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\FiveLotteriesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
