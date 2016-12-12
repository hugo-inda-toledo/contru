<?php
namespace App\Model\Table;

use App\Model\Entity\GroupPermission;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AcosGroups Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Acos
 * @property \Cake\ORM\Association\BelongsTo $Groups
 */
class GroupsPermissionsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('groups_permissions');
        $this->displayField('id');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');

        $this->belongsTo('Permission', [
            'foreignKey' => 'permission_id'
        ]);
        $this->belongsTo('Groups', [
            'foreignKey' => 'group_id'
        ]);
    }
}
