<?php
namespace App\Model\Table;

use App\Model\Entity\IcConsumo;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SfMaterials Model
 */
class IcConsumoTable extends Table
{

    public static function defaultConnectionName()
    {
        return 'iconstruye';
    }

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('dbo.consumos');
        $this->displayField('NUMDOC');
        $this->primaryKey('IDDOC');  
    }
}
