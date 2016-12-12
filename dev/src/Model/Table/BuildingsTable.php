<?php
namespace App\Model\Table;

use App\Model\Entity\Building;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;

/**
 * Buildings Model
 */
class BuildingsTable extends Table
{
    public static function defaultConnectionName()
    {
        return 'default';
    }

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('buildings');
        $this->displayField('id');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->hasOne('Budgets', [
            'foreignKey' => 'building_id'
        ]);
        $this->hasMany('BuildingsUsers', [
            'foreignKey' => 'building_id'
        ]);
        $this->hasOne('SfBuildings', [
            'foreignKey' => 'CodArn'
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
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create');
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
     * Lista de obras internas con presupuestos
     * @return array Obras con presupuestos | formato softland_id - budget_id
     */
    public function getBuildingsWithBudgets()
    {
        $buildings_budgets = $this->find('all', [
            'contain' => ['Budgets' =>
                function ($q) {
                    return $q
                        ->select(['id', 'building_id']);
                    }],
            'conditions' => ['Buildings.softland_id IS NOT NULL', 'Buildings.omit' => false]
        ]);
        $buildingsWithBudget = array();
        foreach ($buildings_budgets as $buildings_budget) {
            if (!empty($buildings_budget['budget'])) {
                $buildingsWithBudget[$buildings_budget['softland_id']]['budget_id'] = $buildings_budget['budget']['id'];
                $buildingsWithBudget[$buildings_budget['softland_id']]['active'] = $buildings_budget['active'];
            }
        }
        return $buildingsWithBudget;
    }

    /**
     * Lista de obras con nombre de softland
     * @return array Obras con nombre softland | formato building_id, codigo softland - nombre softland
     */
    public function getActiveBuildingsWithSoftlandInfo()
    {
        $this->SfBuildings = TableRegistry::get('SfBuildings');
        $buildings = $this->find('all')
            ->where(['Buildings.softland_id IS NOT NULL', 'Buildings.omit' => false])
            ->join([
                'table' => 'budgets',
                'alias' => 'Budgets',
                'conditions' => ['Budgets.building_id = Buildings.id']
            ])
            ->group(['Buildings.id']);
        $buildingsWithSoftlandInfo = array();
        foreach ($buildings as $building) {
            $sf_building = $this->SfBuildings->find('all', [
                'conditions' => ['SfBuildings.CodArn' => $building['softland_id']]
            ])->first();
            $buildingsWithSoftlandInfo[$building['id']] = $sf_building['CodArn'] . ' - ' . $sf_building['DesArn'];
        }
        return $buildingsWithSoftlandInfo;
    }

    /**
     * Obtiene el porcentaje de avance de la obra (función dummy mientras)
     *
     */
    function getPercentageBuilding($softland_id){
        $percentage = 80;
        return $percentage;
    }
}
