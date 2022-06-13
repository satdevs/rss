<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * FiveLottery Entity
 *
 * @property int $id
 * @property string $year
 * @property string $week
 * @property \Cake\I18n\FrozenDate $pull_date
 * @property string $results5
 * @property string $results5price
 * @property string $results4
 * @property string $results4price
 * @property string $results3
 * @property string $results3price
 * @property string $results2
 * @property string $results2price
 * @property string $number1
 * @property string $number2
 * @property string $number3
 * @property string $number4
 * @property string $number5
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 */
class FiveLottery extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'year' => true,
        'week' => true,
        'pull_date' => true,
        'results5' => true,
        'results5price' => true,
        'results4' => true,
        'results4price' => true,
        'results3' => true,
        'results3price' => true,
        'results2' => true,
        'results2price' => true,
        'number1' => true,
        'number2' => true,
        'number3' => true,
        'number4' => true,
        'number5' => true,
        'created' => true,
        'modified' => true,
    ];
}
