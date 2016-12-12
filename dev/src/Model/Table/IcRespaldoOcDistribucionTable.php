<?php
namespace App\Model\Table;

use App\Model\Entity\IcRespaldoOcDistribucion;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * IcPartida Model
 */
class IcRespaldoOcDistribucionTable extends Table
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
        $this->table('dbo.RESPALDOOCDISTRIBUCION');
        $this->displayField('DESCRIPCION');
        $this->primaryKey('IDDISTRIBUCION');

        $this->belongsTo('IcRespaldo', ['foreignKey' => 'IDRESPALDO', 'joinType' => 'INNER']);
        $this->belongsTo('IcOrdenCompra', ['foreignKey' => 'IDOC', 'joinType' => 'INNER']);
    }
}
