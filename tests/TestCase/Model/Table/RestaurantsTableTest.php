<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\RestaurantsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\RestaurantsTable Test Case
 */
class RestaurantsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\RestaurantsTable
     */
    protected $Restaurants;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Restaurants',
        'app.Rests',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Restaurants') ? [] : ['className' => RestaurantsTable::class];
        $this->Restaurants = $this->getTableLocator()->get('Restaurants', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Restaurants);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\RestaurantsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\RestaurantsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
