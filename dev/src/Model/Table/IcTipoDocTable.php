<?php
namespace App\Model\Table;

use App\Model\Entity\IcTipoDoc;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * IcTipoDoc Model
 */
class IcTipoDocTable extends Table
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
        $this->table('dbo.TIPODOC');
        $this->displayField('DESCRIPCION');
        $this->primaryKey('IDTIPODOC');
    }
}
