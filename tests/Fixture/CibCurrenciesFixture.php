<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CibCurrenciesFixture
 */
class CibCurrenciesFixture extends TestFixture
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
                'name' => 'Lorem ipsum dolor sit amet',
                'description' => 'Lorem ipsum dolor sit amet',
                'category' => 'Lorem ipsum dolor sit amet',
                'value' => 1,
                'pubDate' => '2022-03-11 12:19:33',
                'guid' => 'Lorem ipsum dolor sit amet',
                'created' => '2022-03-11 12:19:33',
                'modified' => '2022-03-11 12:19:33',
            ],
        ];
        parent::init();
    }
}
