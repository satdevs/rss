<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * FiveLotteriesFixture
 */
class FiveLotteriesFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'year' => 'Lo',
                'week' => 'Lo',
                'pull_date' => '2022-03-11',
                'results5' => 'Lorem ipsum dolor ',
                'results5price' => 'Lorem ipsum dolor ',
                'results4' => 'Lorem ipsum dolor ',
                'results4price' => 'Lorem ipsum dolor ',
                'results3' => 'Lorem ipsum dolor ',
                'results3price' => 'Lorem ipsum dolor ',
                'results2' => 'Lorem ipsum dolor ',
                'results2price' => 'Lorem ipsum dolor ',
                'number1' => 'Lorem ipsum dolor ',
                'number2' => 'Lorem ipsum dolor ',
                'number3' => 'Lorem ipsum dolor ',
                'number4' => 'Lorem ipsum dolor ',
                'number5' => 'Lorem ipsum dolor ',
                'created' => '2022-03-11 14:29:20',
                'modified' => '2022-03-11 14:29:20',
            ],
        ];
        parent::init();
    }
}
