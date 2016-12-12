<?php
namespace App\Model\Table;

use App\Model\Entity\IcOrdenCompraDistribucion;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * IcPartida Model
 */
class IcOrdenCompraDistribucionTable extends Table
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
        $this->table('dbo.OCLNDISTRIBUCION');
        $this->displayField('NUMDISTRIB');
        $this->primaryKey('NUMDISTRIB');

        $this->belongsTo('IcOrdenCompra', ['foreignKey' => 'IDOC', 'joinType' => 'INNER']);
        $this->belongsTo('IcOrdenCompraItem', ['foreignKey' => 'IDOCLINEA', 'joinType' => 'INNER']);
        $this->belongsTo('IcTipoDoc', ['foreignKey' => 'TIPODISTRIB', 'joinType' => 'INNER']);
    }
}
