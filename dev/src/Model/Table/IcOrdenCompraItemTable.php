<?php
namespace App\Model\Table;

use App\Model\Entity\IcOrdenCompraItem;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * IcPartida Model
 */
class IcOrdenCompraItemTable extends Table
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
        $this->table('dbo.OCLINEAS');
        $this->displayField('NOMBARTICULO');
        $this->primaryKey('IDOCLINEA');

        $this->belongsTo('IcUom', ['foreignKey' => 'IDUOM', 'joinType' => 'INNER']);

        $this->hasOne('IcOrdenCompraItemDescuento', [
            'foreignKey' => 'IDOCLINEA'
        ]);

        $this->hasMany('IcOrdenCompraDistribucion', [
            'foreignKey' => 'IDOCLINEA'
        ]);
    }
}
