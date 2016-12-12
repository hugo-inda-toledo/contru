<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Auth\DefaultPasswordHasher;

/**
 * User Entity.
 */
class User extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'group_id' => true,
        'email' => true,
        'password' => true,
        'first_name' => true,
        'lastname_f' => true,
        'lastname_m' => true,
        'celphone' => true,
        'address' => true,
        'active' => true,
        'group' => true,
        'buildings_users' => true,
        'approvals' => true,
        'budget_approvals' => true,
        'observations' => true,
        'temp_pass' => true,
    ];

    protected function _setPassword($password)
    {
        return (new DefaultPasswordHasher)->hash($password);
    }   

    protected function _getFullName()
    {
        return sprintf("%s %s %s", $this->_properties['first_name'], $this->_properties['lastname_f'], $this->_properties['lastname_m']);
    }

}
