<?php
namespace App\Model\Table;

use App\Model\Entity\IcEstadoPagoAprobacion;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * IcEstadoPagoAprobacion Model
 */
class IcEstadoPagoAprobacionTable extends Table
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
        $this->table('dbo.ESTADOPAGOAPROBACION');
        $this->displayField('IDAPROBADOR');
        $this->primaryKey('IDESTADOPAGOAPROBACION');

        $this->belongsTo('IcEstadoPago', [
            'foreignKey' => 'IDDOC',
        ]);

    }
}
