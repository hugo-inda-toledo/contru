<?php
namespace App\Model\Table;

use App\Model\Entity\Worker;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Workers Model
 */
class SfWorkersStatusTable extends Table
{

    public static function defaultConnectionName()
    {
        return 'softland';
    }

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->view('softland.sw_vsnpEstadoPer');
        $this->displayField('Estado');
        $this->primaryKey('ficha');
        /*$this->belongsTo('SfWorkerBuildings', [
            'foreignKey' => 'ficha',
            'joinType' => 'INNER'
        ]);*/
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
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create');

        return $validator;
    }
}
