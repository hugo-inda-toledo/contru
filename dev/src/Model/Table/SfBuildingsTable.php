<?php
namespace App\Model\Table;

use App\Model\Entity\Building;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Buildings Model
 */
class SfBuildingsTable extends Table
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
        $this->table('softland.cwtaren');
        $this->displayField('DesArn');
        $this->primaryKey('CodArn');
        //$this->addBehavior('Timestamp');
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
            
        // $validator
        //     ->requirePresence('name', 'create')
        //     ->notEmpty('name');
            
        // $validator
        //     ->requirePresence('description', 'create')
        //     ->notEmpty('description');

        // return $validator;
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
        // return $rules;
    }


    public function beforeFind(Event $event, $query)
    {
        $query->cache(function ($q) {
            $asdf = 'sfbuildings-' . md5(serialize($q->clause('where')));
            return $asdf;
        }, 'config_cache_sfbuildings');
    }

    /**
     * afterSave.
     *
     * @param Cake\Event\Event $event The afterSave event
     * @return void
     */
    public function afterSave(Event $event)
    {
        Cache::clearGroup('sfbuildings','config_cache_sfbuildings');
    }

    /**
     * afterDlete.
     *
     * @param Cake\Event\Event $event The afterSave event
     * @return void
     */
    public function afterDlete(Event $event)
    {
        Cache::clearGroup('sfbuildings','config_cache_sfbuildings');
    }

}
