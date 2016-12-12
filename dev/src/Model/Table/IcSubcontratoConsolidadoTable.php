<?php
namespace App\Model\Table;

use App\Model\Entity\IcSubcontratoConsolidado;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * IcPartida Model
 */
class IcSubcontratoConsolidadoTable extends Table
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
        $this->table('dbo.SUBCONTCONSOLIDADO');
        $this->displayField('NOMDOC');
        $this->primaryKey('IDDOC');

        $this->belongsTo('IcSubcontrato', [
            'foreignKey' => 'IDDOC',
        ]);
    }
}
