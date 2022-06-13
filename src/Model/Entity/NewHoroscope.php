<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * NewHoroscope Entity
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $key
 * @property int|null $year
 * @property string|null $date
 * @property string|null $content
 * @property int|null $counter
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 */
class NewHoroscope extends Entity
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
        'name' => true,
        'ckey' => true,
        'year' => true,
        'month' => true,
        'day' => true,
        'date' => true,
        'content' => true,
        'counter' => true,
        'created' => true,
        'modified' => true,
    ];
}
