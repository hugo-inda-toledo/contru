<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    public $components = [
    ];

    public $helpers = [
         'Paginator' => ['templates' => 'paginator-templates'],
         'Form' => ['templates' => 'form-templates'],
         'Html' => ['templates' => 'html-templates'],
         ];

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * @return void
     */
    public function initialize()
    {
        $this->loadComponent('Flash');
        $this->loadComponent('Auth', [
            'authenticate' => [
            'Form' => [
                    'fields' => ['username' => 'email', 'password' => 'password']
                ]
            ],
            'loginAction' => [
                'plugin' => false,
                'controller' => 'Users',
                'action' => 'login'
            ],
            'loginRedirect' => [
                'controller' => 'Users',
                'action' => 'home'
            ],
            'logoutRedirect' => [
                'controller' => 'Users',
                'action' => 'login',
            ],
            'unauthorizedRedirect' => [
                'controller' => 'Cameras',
                'action' => 'index',
                'prefix' => false
            ],
            'authError' => 'Usted no esta autorizado para acceder a ese modulo.',
            'flash' => [
                'element' => 'error'
            ],
        ]);
        $this->loadComponent('Breadcrumb');

    }

    /**
     * Beforefilter
     * @param  Event  $event [description]
     * @return [type]        [description]
     * @author Carlos Vásquez <carlos.vasquez@ideauno.cl>
     */
    public function beforeFilter(Event $event)
    {
        

        // cucho: hace un log de la funcion ejecutada
        $this->hacerLog();
        // cucho: fin
        
        if($this->request->session()->read('Auth.User'))
        {
            // comentar esta linea para que funcione auth
            // $this->Auth->allow();
            // funciones que no requieren permiso
            if (! ($this->request->params['controller'] == 'Users' && (
                $this->request->params['action'] == 'login' ||
                $this->request->params['action'] == 'logout' ||
                $this->request->params['action'] == 'forgottenPassword' ||
                $this->request->params['action'] == 'restorePassword' ||
                $this->request->params['action'] == 'home'
                ))) {

                // hugo: verifica el acceso al controlador y la acción
                $this->verifyAccess();
                // hugo: fin

            }
        }
        else
        {
            if (! ($this->request->params['controller'] == 'Users' && (
                $this->request->params['action'] == 'login' ||
                $this->request->params['action'] == 'logout' ||
                $this->request->params['action'] == 'forgottenPassword' ||
                $this->request->params['action'] == 'restorePassword'
                //$this->request->params['action'] == 'home'
                ))) {

                $this->redirect('/users/logout');

            }
            
        }
    }

    public function verifyAccess()
    {
        $access = false;

        //echo $this->request->params['controller'].' '.$this->request->params['action'];
        if(count($this->request->session()->read('groups')) > 0)
        {
            foreach($this->request->session()->read('groups') as $group)
            {
                foreach($group->permissions as $perm)
                {
                    if($perm->controller == $this->request->params['controller'] && $perm->action == $this->request->params['action'])
                    {
                        $access = true;
                        //echo 'existe';
                        break;
                    }
                }

                if($access == true)
                {
                    break;
                }
            }
        }

        if($access == false)
        {
            $this->Flash->error('No tienes acceso a esta pagina. Contacta al administrador del sitio para obtener acceso.');
            return $this->redirect($this->referer());
        }
    }

    public function beforeRender(Event $event)
    {
        //set breabcrumbs
        $second_breadcrumb = $this->Breadcrumb->getBreadcrumbByControllerName($this->request->params['controller']);
        $third_breadcrumb = $this->Breadcrumb->getBreadcrumbByActionName($this->request->params['action']);
        $this->set(compact('second_breadcrumb', 'third_breadcrumb'));
        $last_building_info = $this->request->session()->read('Config.last_building_info');
        $this->set(compact('last_building_info'));
    }

    /**
     * FUncion que verifica los permisos
     * @author Sebastian Elgueda
     * @email  sebastian.elgueda@ideauno.cl
     * @return [type]                       [description]
     */
    public function check_group() {
        //Comentar esta linea para que funcionen
        // return true;
        $this->loadModel('Acos');
        $this->loadModel('AcosGroups');
        //Buscar nombre controlador
        $controller = $this->request->params['controller'];
        //Buscar nombre función
        $action = $this->request->params['action'];
        //Buscar grupo usuario
        $group_id = $this->Auth->user('group_id');
        //Revisar si el grupo del usuario tiene permisos globales
        $perm_glob = $this->AcosGroups->find('all',['conditions' => ['group_id' => $group_id,'aco_id' => 1]])->first();
        debug($perm_glob);
        if(!is_null($perm_glob) && $perm_glob->permission) {
            return true;
        }

        //Buscamos si tiene permisos para el controlador
        //Buscamos el aco del controlador
        $aco_contr = $this->Acos->find('all', ['conditions' => ['alias' => $controller]])->first();
        //Permiso del controlador
        $perm_contr = $this->AcosGroups->find('all',['conditions' => ['group_id' => $group_id,'aco_id' => $aco_contr->id]])->first();
        debug($perm_contr);

        if(!is_null($perm_contr) && $perm_contr->permission) {
            return true;
        }
        //Vemos si existe el permiso para la acción
        //Buscamos el aco del controlador
        $aco_act = $this->Acos->find('all', ['conditions' => ['alias' => $action,'parent_id' => $aco_contr->id]])->first();
        //Permiso del controlador
        $perm_act = $this->AcosGroups->find('all',['conditions' => ['group_id' => $group_id,'aco_id' => $aco_act->id]])->first();
        if(!is_null($perm_act) && $perm_act->permission) {
            return true;
        }

        return false;

    }

    /**
     * redireccionar al inicio dependiendo del tipo de usuario
     * @param  [type] $group_id [description]
     * @return [type] url [description]
     * @author Omar Sepulveda <omar.sepulveda@ideauno.cl>
     */
    public function redirect_home($group_id)
    {
        // Si tiene una obra se debe redireccionar al dashboard
        if($this->request->session()->read('Config.last_building_sf_id')!=null){
            $this->redirect(['controller' => 'buildings', 'action' => 'dashboard', $this->request->session()->read('Config.last_building_sf_id')]);
        }
        switch ($group_id) {
            case USR_GRP_ADMIN:
                return $this->redirect(['controller' => 'Users', 'action' => 'index']);
                break;
            case USR_GRP_JEFE_RRHH:
                return $this->redirect(['controller' => 'Assists', 'action' => 'index']);
                break;
            case USR_GRP_ASIS_RRHH:
                return $this->redirect(['controller' => 'Assists', 'action' => 'index']);
                break;
            case USR_GRP_VISITADOR:
                return $this->redirect(['controller' => 'Schedules', 'action' => 'index']);
                break;
            case USR_GRP_ADMIN_OBRA:
                return $this->redirect(['controller' => 'Schedules', 'action' => 'index']);
                break;
            case USR_GRP_OFI_TEC:
                return $this->redirect(['controller' => 'Schedules', 'action' => 'index']);
                break;
            case USR_GRP_COORD_PROY:
                return $this->redirect(['controller' => 'Buildings', 'action' => 'index']);
                break;
            case USR_GRP_GE_GRAL:
                return $this->redirect(['controller' => 'Buildings', 'action' => 'index']);
                break;
            case USR_GRP_GE_FINAN:
                return $this->redirect(['controller' => 'Buildings', 'action' => 'index']);
                break;
            default:
                return $this->redirect(['controller' => 'Users', 'action' => 'index']);
                break;
        }
    }

    /**
     * Hace log
     * @return [type] [description]
     * @author Carlos Vásquez <carlos.vasquez@ideauno.cl>
     */
    private function hacerLog()
    {
        // datos del usuario
        $usuario = $this->request->session()->read('Auth');

        // OJO! este if es para trakear lo sin login,
        // ideal quitar en producción
        if (! isset($usuario)) {
            $usuario['User'] = ['id' => null, 'group_id' => null];
        }
        // no hace log para las notificaciones ni para login
        if (isset($usuario['User']['id']) && $this->request->here() != '/') {
            // se crea el registro a guardar
            $registro_log = array(
                'user_id' => $usuario['User']['id'],
                'group_id' => $usuario['User']['group_id'],
                'model' => $this->request->params['controller'],
                'method' => $this->request->params['action'],
                'text' => $_SERVER["REQUEST_URI"],
                'data' => (!empty($this->request->data)) ? json_encode($this->request->data) : null
                );
            // se importa el modelo y se guarda
            $this->loadModel('Histories');
            $history = $this->Histories->newEntity();
            $history = $this->Histories->patchEntity($history, $registro_log);
            $this->Histories->save($history);
        }
    }

}
