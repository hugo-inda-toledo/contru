<?php
namespace App\Model\Table;

use App\Model\Entity\IcSubcontratoItem;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * IcPartida Model
 */
class IcSubcontratoItemTable extends Table
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
        $this->table('dbo.SUBCONTLINEA');
        $this->displayField('DESCRIPCION');
        $this->primaryKey('IDLINEA');

        $this->belongsTo('IcSubcontrato', ['foreignKey' => 'IDDOC', 'joinType' => 'INNER']);

        $this->belongsTo('IcUom', [
            'foreignKey' => 'IDUOM',
        ]);
    }
}
