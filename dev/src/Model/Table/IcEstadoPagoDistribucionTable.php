<?php
namespace App\Model\Table;

use App\Model\Entity\IcEstadoPagoDistribucion;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * IcEstadoPagoDistribucion Model
 */
class IcEstadoPagoDistribucionTable extends Table
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
        $this->table('dbo.ESTADOPAGOLNDISTRIBUCION');
        $this->displayField('NOMDOC');
        $this->primaryKey('NUMDISTRIB');

        $this->belongsTo('IcSubcontrato', ['foreignKey' => 'IDSUBCONTRATO', 'joinType' => 'INNER']);
        $this->belongsTo('IcEstadoPago', ['foreignKey' => 'IDESTADOPAGO', 'joinType' => 'INNER']);
        $this->belongsTo('IcEstadoPagoItem', ['foreignKey' => 'IDESTADOPAGOLINEA', 'joinType' => 'INNER']);
    }
}
