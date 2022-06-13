<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Today Entity
 *
 * @property int $id
 * @property string $title
 * @property string $text
 * @property int $year
 * @property int $month
 * @property int $day
 * @property \Cake\I18n\FrozenTime $datetime
 * @property string $pubdate
 * @property int $counter
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 */
class Today extends Entity
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
        'title' => true,
        'text' => true,
        'year' => true,
        'month' => true,
        'day' => true,
        'datetime' => true,
        'pubdate' => true,
        'counter' => true,
        'created' => true,
        'modified' => true,
    ];
}
