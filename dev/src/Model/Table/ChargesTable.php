<?php
namespace App\Model\Table;

use App\Model\Entity\Charge;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * Charges Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Softlands */
class ChargesTable extends Table
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

        $this->table('charges');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');
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
            ->add('id', 'valid', ['rule' => 'numeric'])            ->allowEmpty('id', 'create');
        $validator
            ->requirePresence('name', 'create')            ->notEmpty('name');
        $validator
            ->add('amount', 'valid', ['rule' => 'numeric'])            ->requirePresence('amount', 'create')            ->notEmpty('amount');
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
        //$rules->add($rules->existsIn(['softland_id'], 'Softlands'));
        return $rules;
    }


    /**
     * Obtiene la lista de trabajadores de Softland por area de negocio
     * @param  int $building_id identificador obra
     * @return [array] trabajadores de area de negocio sotfland
     */ 
    public function getSoftlandCharges()
    {
        $sfWorkerCargos = TableRegistry::get('SfWorkerCargos');
        $sf_workers_cargos = $sfWorkerCargos->find('list');
        return $sf_workers_cargos;
    }
    
            
}
