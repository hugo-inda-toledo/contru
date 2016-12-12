<?php
namespace App\Model\Table;

use App\Model\Entity\IcRecepcion;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * IcPartida Model
 */
class IcRecepcionTable extends Table
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
        $this->table('dbo.RECEPCIONES');
        $this->displayField('IDDOC');
        $this->primaryKey('IDDOC');

        $this->hasMany('IcRecepcionItem', [
            'foreignKey' => 'IDDOC',
            'joinType' => 'INNER',
            'sort' => ['IcRecepcionItem.NROLINEA' => 'ASC']
        ]);
    }
}
