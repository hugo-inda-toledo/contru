<?php
namespace App\Model\Table;

use App\Model\Entity\IcEstadoPago;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * IcEstadoPagoTable Model
 */
class IcEstadoPagoTable extends Table
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
        $this->table('dbo.ESTADOPAGO');
        $this->displayField('NOMDOC');
        $this->primaryKey('IDDOC');

        $this->belongsTo('IcSubcontrato', ['foreignKey' => 'IDSUBCONTRATO', 'joinType' => 'INNER']);
        $this->belongsTo('IcEstadoDoc', ['foreignKey' => 'IDESTADODOC', 'joinType' => 'INNER']);
        $this->belongsTo('IcTipoDoc', ['foreignKey' => 'IDTIPODOC', 'joinType' => 'INNER']);
        $this->belongsTo('IcFactura', ['foreignKey' => 'IDFACTURACOMPRADOR', 'joinType' => 'INNER']);
        $this->hasMany('IcEstadoPagoItem', [
            'foreignKey' => 'IDDOC'
        ]);
        $this->hasMany('IcEstadoPagoAprobacion', [
            'foreignKey' => 'IDESTADOPAGO'
        ]);
    }
}
