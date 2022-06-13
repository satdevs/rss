<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CibCurrenciesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CibCurrenciesTable Test Case
 */
class CibCurrenciesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CibCurrenciesTable
     */
    protected $CibCurrencies;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.CibCurrencies',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('CibCurrencies') ? [] : ['className' => CibCurrenciesTable::class];
        $this->CibCurrencies = $this->getTableLocator()->get('CibCurrencies', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->CibCurrencies);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\CibCurrenciesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
