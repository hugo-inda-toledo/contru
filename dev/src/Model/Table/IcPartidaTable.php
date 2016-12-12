<?php
namespace App\Model\Table;

use App\Model\Entity\IcPartida;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * IcPartida Model
 */
class IcPartidaTable extends Table
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
        $this->table('dbo.PARTIDASBODEGA');
        $this->displayField('NOMBRE');
        $this->primaryKey('IDPARTIDA');
    }
}
