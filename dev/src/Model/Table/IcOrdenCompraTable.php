<?php
namespace App\Model\Table;

use App\Model\Entity\IcOrdenCompra;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * IcPartida Model
 */
class IcOrdenCompraTable extends Table
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
        $this->table('dbo.OC');
        $this->displayField('NOMOC');
        $this->primaryKey('IDOC');

        $this->hasOne('IcOrdenCompraConsolidado', [
            'foreignKey' => 'IDOC',
        ]);

        $this->hasOne('IcOrdenCompraCargo', [
            'foreignKey' => 'IDOC',
        ]);

        $this->hasMany('IcOrdenCompraItem', [
            'foreignKey' => 'IDOC',
            'sort' => ['IcOrdenCompraItem.NUMLINEAOC' => 'ASC']
        ]);

        $this->hasOne('IcRespaldoOcDistribucion', [
            'foreignKey' => 'IDOC',
        ]);

    }
}
