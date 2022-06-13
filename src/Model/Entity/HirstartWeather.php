<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * HirstartWeather Entity
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property \Cake\I18n\FrozenTime $pubdate
 * @property string $category
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 */
class HirstartWeather extends Entity
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
        'description' => true,
        'pubdate' => true,
        'category' => true,
        'imageUrl' => true,
        'imageType' => true,
        'author' => true,
        'guid' => true,
        'created' => true,
        'modified' => true,
    ];
}
