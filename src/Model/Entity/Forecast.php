<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Forecast Entity
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $year
 * @property string|null $day
 * @property string|null $d
 * @property string|null $tmin
 * @property string|null $tmax
 * @property string|null $wx
 * @property string|null $sr
 * @property string|null $ss
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 */
class Forecast extends Entity
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
        'date' => true,
        'year' => true,
        'day' => true,
        'd' => true,
        'tmin' => true,
        'tmax' => true,
        'wx' => true,
        'sr' => true,
        'ss' => true,
        'created' => true,
        'modified' => true,
    ];
}
