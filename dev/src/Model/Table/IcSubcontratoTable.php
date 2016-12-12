<?php
namespace App\Model\Table;

use App\Model\Entity\IcSubcontrato;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * IcPartida Model
 */
class IcSubcontratoTable extends Table
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
        $this->table('dbo.SUBCONTRATOS');
        $this->displayField('NOMDOC');
        $this->primaryKey('IDDOC');

        $this->hasMany('IcSubcontratoItem', [
            'foreignKey' => 'IDDOC',
            'sort' => ['IcSubcontratoItem.NUMLINEA' => 'ASC']
        ]);

        $this->hasMany('IcSubcontratoAprobacion', [
            'foreignKey' => 'IDSUBCONT',
            'sort' => ['IcSubcontratoAprobacion.ORDENAPROBACION' => 'ASC']
        ]);

        $this->hasMany('IcSubcontratoDistribucion', [
            'foreignKey' => 'IDSUBCONT'
        ]);

        $this->belongsTo('IcSubcontratoTipo', [
            'foreignKey' => 'TIPOSUBCONTRATO',
        ]);

        $this->hasOne('IcSubcontratoConsolidado', [
            'foreignKey' => 'IDDOC',
        ]);
    }
}
