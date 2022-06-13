<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Restaurant Entity
 *
 * @property int $id
 * @property string $name
 * @property \Cake\I18n\FrozenTime $date_from
 * @property \Cake\I18n\FrozenTime $date_to
 * @property string $menu_from_to
 * @property string $days_text
 * @property string $text
 * @property string $prices
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Rest $rest
 */
class Restaurant extends Entity
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
        'date_from' => true,
        'date_to' => true,
        'menu_from_to' => true,
        'days_text' => true,
        'text' => true,
        'prices' => true,
        'created' => true,
        'modified' => true,
        'rest' => true,
    ];
}
