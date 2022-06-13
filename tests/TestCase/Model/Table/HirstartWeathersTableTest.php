<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\HirstartWeathersTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\HirstartWeathersTable Test Case
 */
class HirstartWeathersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\HirstartWeathersTable
     */
    protected $HirstartWeathers;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.HirstartWeathers',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('HirstartWeathers') ? [] : ['className' => HirstartWeathersTable::class];
        $this->HirstartWeathers = $this->getTableLocator()->get('HirstartWeathers', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->HirstartWeathers);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\HirstartWeathersTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
