<?php
namespace App\Model\Table;

use App\Model\Entity\Valoresmonedas;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Currencies Model
 */
class ValoresmonedasTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('valoresmonedas');
        $this->displayField('name');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        
        $this->belongsTo('Currencies', [
            'foreignKey' => 'currency_id',
            'joinType' => 'INNER'
        ]);
    }
}
