<?php
namespace App\Model\Table;

use App\Model\Entity\Acos;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Acos Model
 */
class AcosTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('acos');
        $this->displayField('alias');
        $this->primaryKey('id'); 

        $this->belongsToMany('Groups', [
            'through' => 'AcosGroups',
        ]);
    }
}
