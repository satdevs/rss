<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Nameday Entity
 *
 * @property int $id
 * @property int $month
 * @property int $day
 * @property string|null $name
 * @property string $days
 * @property string $gender
 * @property string $meaning
 * @property string $source
 * @property string|null $description
 * @property int $othernameday_count
 * @property string $details
 * @property string $nicknames
 */
class Nameday extends Entity
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
        'month' => true,
        'day' => true,
        'name' => true,
        'days' => true,
        'gender' => true,
        'meaning' => true,
        'source' => true,
        'description' => true,
        'othernameday_count' => true,
        'details' => true,
        'nicknames' => true,
    ];
}
