<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\LotteriesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\LotteriesTable Test Case
 */
class LotteriesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\LotteriesTable
     */
    protected $Lotteries;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Lotteries',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Lotteries') ? [] : ['className' => LotteriesTable::class];
        $this->Lotteries = $this->getTableLocator()->get('Lotteries', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Lotteries);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\LotteriesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
