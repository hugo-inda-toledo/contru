<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Building Entity.
 */
class SfBuilding extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'CodArn' => true,
        'DesArn' => true,
    ];
}
