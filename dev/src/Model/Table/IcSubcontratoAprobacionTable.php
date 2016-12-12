<?php
namespace App\Model\Table;

use App\Model\Entity\IcSubcontratoAprobacion;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * IcSubcontratoAprobacion Model
 */
class IcSubcontratoAprobacionTable extends Table
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
        $this->table('dbo.SUBCONTAPROBACION');
        $this->displayField('IDAPROBADOR');
        $this->primaryKey('IDSUBCONTAPROBACION');

        $this->belongsTo('IcSubcontrato', [
            'foreignKey' => 'IDSUBCONT',
        ]);

    }
}
