<?php
namespace App\Model\Table;

use App\Model\Entity\AssistsData;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AssistsData Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Buildings
 * @property \Cake\ORM\Association\BelongsTo $Softlands
 */
class AssistsDataTable extends Table
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

        $this->table('assists_data');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->belongsTo('Buildings', [
            'foreignKey' => 'building_id',
            'joinType' => 'INNER'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('nombres', 'create')
            ->notEmpty('nombres');

        $validator
            ->allowEmpty('appaterno');

        $validator
            ->allowEmpty('apmaterno');

        $validator
            ->allowEmpty('rut');

        $validator
            ->email('email')
            ->allowEmpty('email');

        $validator
            ->allowEmpty('direccion');

        $validator
            ->allowEmpty('telefono1');

        $validator
            ->date('fecha_nacimiento')
            ->allowEmpty('fecha_nacimiento');

        $validator
            ->date('fecha_ingreso')
            ->allowEmpty('fecha_ingreso');

        $validator
            ->allowEmpty('cargo');

        $validator
            ->date('vig_desde')
            ->allowEmpty('vig_desde');

        $validator
            ->date('vig_hasta')
            ->allowEmpty('vig_hasta');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        // $rules->add($rules->isUnique(['email']));
        $rules->add($rules->existsIn(['building_id'], 'Buildings'));
        return $rules;
    }

    public function getWorkersByBuildingId($building_id){
        $return = $this->find('all',[
            'conditions' => [
                'AssistsData.building_id' => $building_id
            ],
            'order' => [
                'AssistsData.nombres' => 'ASC'
            ]
        ])->toArray();
        return $return;
    }

}
