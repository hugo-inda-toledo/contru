<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Auth\DefaultPasswordHasher;
use Cake\I18n\Time;
use Cake\Controller\Component;
use Cake\Network\Email\Email;
use Cake\Utility\Text;
use Cake\Utility\Security;
use Cake\View\CellTrait;

/**
* Users Controller
*
* @property \App\Model\Table\UsersTable $Users */
class AclController extends AppController
{
    use CellTrait;

    public $components = [
        'Acl' => [
            'className' => 'Acl.Acl'
        ]
    ];

    public function initialize()
    {
        parent::initialize();

        // $this->Auth->allow(); 
    }
    
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        // Allow users to register and logout.
        // You should not add the "login" action to allow list. Doing so would
        // cause problems with normal functioning of AuthComponent.
        $this->Auth->allow(['logout', 'login']);
    }

    /**
     * Indice con todas los permisos
     * @author Sebastian Elgueda
     * @email  sebastian.elgueda@ideauno.cl
     * @return [type]                       [description]
     */
    public function index()
    {
        $this->loadModel('Acos');        
        $this->loadModel('Groups');
        $acos = $this->Acos->find('all',['contain' => ['AcosGroups']]);

        $groups = $this->Groups->find('all');
                
        $this->set(compact('groups','acos'));
    }
/**
 * Añade permisos
 * @author Sebastian Elgueda
 * @email  sebastian.elgueda@ideauno.cl
 */
    public function addPermissions()
    {
        $this->layout = 'ajax';

        $this->loadModel('AcosGroups');
        $perm = $this->AcosGroups->find('all',['conditions' => ['group_id' => $this->request->data['group_id'],'aco_id' => $this->request->data['aco_id']]])->first();
        //Si no existe permiso, lo agregamos
        if(is_null($perm)) {
            $acosgroups = $this->AcosGroups->newEntity();
            $acosgroups = $this->AcosGroups->patchEntity($acosgroups, $this->request->data);
            if ($this->AcosGroups->save($acosgroups)) {
                $response = "Success: Permiso ha sido creado en el sistema.";
            } else {
                $response = "Error: Permiso no ha pudo ser creado en el sistema.";
            }            
        } 
        //Si es que existe, lo cambiamos
        else {
            $acosgroups = $this->AcosGroups->get([$perm->id]);
            if($this->request->data['permission'] == 2) {
                if($this->AcosGroups->delete($acosgroups)) {
                    $response = "Success: Permiso ha sido eliminado.";
                } else {
                    $response = "Error: Permiso no ha podido ser eliminado.";
                }
            } else {
                $acosgroups = $this->AcosGroups->patchEntity($acosgroups, $this->request->data);
                if ($this->AcosGroups->save($acosgroups)) {
                    $response = "Success: Permiso ha sido Actualizado.";
                } else {
                    $response = "Error: Permiso no ha podido ser Actualizado.";
                }    
            }
        }
        $this->set(compact('response'));
        $this->set('_serialize', 'response');
    }
    
}
