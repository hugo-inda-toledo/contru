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
class SfImptoRentasTable extends Table
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
        $this->table('softland.sii_impto2dacat');
        $this->displayField('Tramo');
        $this->primaryKey('Tramo');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        // $validator
        //     ->add('id', 'valid', ['rule' => 'numeric'])
        //     ->allowEmpty('id', 'create');

        return $validator;
    }
}
