<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TodaysFixture
 */
class TodaysFixture extends TestFixture
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
                'title' => 'Lorem ipsum dolor sit amet',
                'text' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'year' => 1,
                'month' => 1,
                'day' => 1,
                'datetime' => '2022-03-17 08:14:38',
                'pubdate' => 'Lorem ipsum dolor sit amet',
                'counter' => 1,
                'created' => '2022-03-17 08:14:38',
                'modified' => '2022-03-17 08:14:38',
            ],
        ];
        parent::init();
    }
}
