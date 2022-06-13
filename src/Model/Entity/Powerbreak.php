<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Powerbreak Entity
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $street
 * @property string|null $house_from
 * @property string|null $house_to
 * @property \Cake\I18n\FrozenDate|null $date
 * @property string|null $time_from
 * @property string|null $time_to
 * @property string|null $comment
 * @property string|null $comment2
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 */
class Powerbreak extends Entity
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
        'status' => true,
        'street' => true,
        'place' => true,
        'house_from' => true,
        'house_to' => true,
        'date' => true,
        'time_from' => true,
        'time_to' => true,
        'comment' => true,
        'comment2' => true,
        'created' => true,
        'modified' => true,
    ];
}
