<?php
namespace App\Model\Table;

use App\Model\Entity\Worker;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Workers Model
 */
class SfRentaAfpsTable extends Table
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
        $this->table('softland.sw_afp');
        $this->displayField('nombre');
        $this->primaryKey('CodAfp');
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

    public function beforeFind(Event $event, $query)
    {
        $query->cache(function ($q) {
            $tmp = $q->where();
            $tmp =array('sql' => $tmp->sql(), 'params' => $tmp->valueBinder()->bindings());
            $asdf = 'sfworkers-' . md5(serialize($tmp));
            return $asdf;
        }, 'config_cache_sfworkers');
    }

    /**
     * afterSave.
     *
     * @param Cake\Event\Event $event The afterSave event
     * @return void
     */
    public function afterSave(Event $event)
    {
        Cache::clearGroup('sfworkers','config_cache_sfworkers');
    }

    /**
     * afterDlete.
     *
     * @param Cake\Event\Event $event The afterSave event
     * @return void
     */
    public function afterDlete(Event $event)
    {
        Cache::clearGroup('sfworkers','config_cache_sfworkers');
    }
}
