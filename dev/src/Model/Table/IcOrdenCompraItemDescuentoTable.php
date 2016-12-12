<?php
namespace App\Model\Table;

use App\Model\Entity\IcOrdenCompraItemDescuento;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * IcPartida Model
 */
class IcOrdenCompraItemDescuentoTable extends Table
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
        $this->table('dbo.OCLNDESCUENTO');
        $this->displayField('DESCRIPCION');
        $this->primaryKey('IDDESCUENTO');

        //$this->belongsTo('IcUom', ['foreignKey' => 'IDUOM', 'joinType' => 'INNER']);
    }
}
