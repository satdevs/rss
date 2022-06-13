<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\HoroscopesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\HoroscopesTable Test Case
 */
class HoroscopesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\HoroscopesTable
     */
    protected $Horoscopes;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Horoscopes',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Horoscopes') ? [] : ['className' => HoroscopesTable::class];
        $this->Horoscopes = $this->getTableLocator()->get('Horoscopes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Horoscopes);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\HoroscopesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\HoroscopesTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
