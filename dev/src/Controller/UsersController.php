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
use Cake\Routing\Router;
/**
* Users Controller
*
* @property \App\Model\Table\UsersTable $Users */
class UsersController extends AppController
{
    use CellTrait;

    public function initialize()
    {
        parent::initialize();
    }

    public $components = array('Search.Prg','Cookie');
    /**
     * beforeFilter
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        // Allow users to register and logout.
        // You should not add the "login" action to allow list. Doing so would
        // cause problems with normal functioning of AuthComponent.
        $this->Auth->allow(['logout', 'login', 'forgottenPassword', 'restorePassword']);
    }

    /**
     * Pagina de inicio con bienvenida
     * @author Pablo Rivera <pablo.rivera@ideauno.cl
     */
    public function home()
    {
        // Matías Pardo 14/07 : Este código fue convertido en una Cell, en src/View/WelcomeCell.php y el antiguo código de home.ctp está en src/Template/Cell/Welcome/display.ctp
        // $user = $this->Users->get($this->request->session()->read('Auth.User.id'));
        // $configurations = $this->Users->Configurations->find()->where(['name' => 'bienvenida'])->first();

        $this->redirect_home($this->request->session()->read('Auth.User.group_id'));
        // fin Matías Pardo
    }

    /**
     * [__construct description]
     * @param  [type] $request  [description]
     * @param  [type] $response [description]
     * @author Carlos Vásquez <carlos.vasquez@ideauno.cl>
     */
    public function __construct($request = null, $response = null)
    {
        parent::__construct($request, $response);
        $this->permissions = array(
            'index' => array(
                USR_GRP_ADMIN,
                ),
            'view' => array(
                USR_GRP_ADMIN,
                ),
            'add' => array(
                USR_GRP_ADMIN,
                ),
            'edit' => array(
                USR_GRP_ADMIN,
                ),
            'editUser' => array(
                USR_GRP_ADMIN,
                ),
            'delete' => array(
                USR_GRP_ADMIN,
                ),
            'updatePassword' => array(
                USR_GRP_ADMIN,
                ),
            'updatePasswordAdmin' => array(
                USR_GRP_ADMIN,
                ),
            'restorePassword' => array(
                USR_GRP_ADMIN,
                ),
            'listEditStatus' => array(
                USR_GRP_ADMIN,
                ),
            'editStatus' => array(
                USR_GRP_ADMIN,
                ),
            'certificates' => array(
                USR_GRP_ADMIN,
                ),
            'home' => array(
                USR_GRP_ADMIN,
                ),
            'accountabilities' => array(
                USR_GRP_ADMIN,
                ),
            );
    }

    /**
     * Función para Login
     * @author Matías Pardo <matias.pardo@ideauno.cl>
     */
    public function login()
    {
        $this->Cookie->config('encryption', false);
        $cookie = $this->Cookie->read('Auth.User');
        if (!is_null($cookie)) {
                $this->request->data = $cookie;
            }
        if (! empty($this->request->session()->read('Auth.User.id'))) {
            //$this->Flash->info('Ya se encuentra una sesión iniciada.');
            return $this->redirect($this->Auth->redirectUrl());
        }

        if ($this->request->is('post') || !is_null($cookie)) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->loadModel('Groups');
                $group = $this->Groups->get($user['group_id']);
                if($group->status && $user['active']) {
                    $this->Auth->setUser($user);
                    $this->_setCookie();
                    $usersTable = TableRegistry::get('Users');
                    $usuario = $usersTable->find('all', ['contain' => ['Groups' => ['Permissions']], 'conditions' => ['Users.id' => $user['id']]])->first();
                    $user_buildings = $usersTable->getUserBuildings($usuario->id);
                    $usuario->last_login = Time::now();
                    $usersTable->save($usuario);
                    $session = $this->request->session();
                    $session->write('Auth.User.buildings_id', $user_buildings);
                    //$session->write('Auth.User.group_name', $usuario->group['name']);
                    $session->write('groups', $usuario->groups);
                    $this->log(Configure::read('groups'));
                    if($this->request->data['remember']) {
                        $this->Cookie->write('Auth.User', $this->request->data);
                    }
                    $this->Flash->success('Sesión iniciada correctamente. ¡Bienvenido al sistema!');
                    return $this->redirect(['action' => 'home']);
                }
            }
            $this->Flash->error(__('Usuario o contraseña inválidos. Por favor intente otra vez'));
        }
    }

    /**
     * Función para Logout
     * @author Matías Pardo <matias.pardo@ideauno.cl>
     */
    public function logout()
    {
        if(!(empty($this->request->session()->read('Auth.User.id')))) {
            $this->Cookie->config('encryption', false);
            $this->Cookie->delete('Auth.User');
            $this->Flash->success('Ha cerrado su sesión correctamente. Hasta luego');
        } else {
            $this->Flash->error('No ha iniciado ninguna sesión.');
        }
        return $this->redirect($this->Auth->logout());
    }

    /**
     * [index description]
     * @return [type] [description]
     * @author Carlos Vásquez <carlos.vasquez@ideauno.cl>
     */
    public function index()
    {
        $users = $this->Users->find('all', [
            'limit' => 20,
            'order' => ['Users.lastname_m' => 'asc']
            ])->toArray();

        $this->set('users', $users);
        $this->paginate = [
            'contain' => ['Groups' => ['Permissions']]
        ];
        $this->set('users', $this->paginate($this->Users));
        $this->set('_serialize', ['users']);
    }

    /**
     * [view description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     * @author Carlos Vásquez <carlos.vasquez@ideauno.cl>
     */
    public function view($id = null)
    {
        //busca las obras de softland activas
        $buildings = $this->Users->BuildingsUsers->Buildings->getActiveBuildingsWithSoftlandInfo();
        $user = $this->Users->get($id, [
            'contain' => ['Groups' => ['Permissions']]
            ]);
        //busca obras asociadas al usuario
        $buildings_user = $this->Users->BuildingsUsers->find('all',['conditions' => ['BuildingsUsers.user_id' => $id]]);
        $buildings_id = array();
        foreach ($buildings_user as $building_user) {
            array_push($buildings_id, $building_user['building_id']);
        }
        //se agregan al user
        $user['building_id'] = $buildings_id;
        $this->set('user', $user);
        $this->set('buildings', $buildings);
    }

    /**
     * [add description]
     * @author Carlos Vásquez <carlos.vasquez@ideauno.cl>
     */
    public function add()
    {
        $buildings = $this->Users->BuildingsUsers->Buildings->getActiveBuildingsWithSoftlandInfo();
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $this->request->data['active'] = 1;
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                if (!empty($this->request->data['building_id'])) {
                    if ($this->request->data['group_id'] == USR_GRP_VISITADOR) {
                        foreach ($this->request->data['building_id'] as $key => $building_id) {
                            $building_user = $this->Users->BuildingsUsers->newEntity();
                            $building_user->user_id = $user->id;
                            $building_user->building_id = $building_id;
                            $this->Users->BuildingsUsers->save($building_user);
                        }
                    } else {
                        $building_user = $this->Users->BuildingsUsers->newEntity();
                        $building_user->user_id = $user->id;
                        $building_user->building_id = $this->request->data['building_id'];
                        $this->Users->BuildingsUsers->save($building_user);
                    }
                }

                $usersGroupsTable = TableRegistry::get('UsersGroups');
                $user_group = $usersGroupsTable->newEntity();

                $user_group->user_id = $user->id;
                $user_group->group_id = $user->group_id;
                $usersGroupsTable->save($user_group);

                $this->Flash->success(__('Usuario creado exitosamente.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('No se ha podido guardar el usuario, intente de nuevo.'));
            }
        }
        $groups = $this->Users->Groups->find('list', ['limit' => 200]);
        $users = $this->Users->find('list', ['limit' => 200]);
        $this->set(compact('user', 'groups', 'users', 'buildings'));
        $this->set('_serialize', ['user']);
    }

    /**
     * [edit description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     * @author Carlos Vásquez <carlos.vasquez@ideauno.cl>
     */
    public function edit($id = null)
    {
        //busca las obras de softland activas
        $buildings = $this->Users->BuildingsUsers->Buildings->getActiveBuildingsWithSoftlandInfo();
        $user = $this->Users->get($id, ['contain' => ['Groups']]);
        //busca obras asociadas al usuario y se agregan al user
        $user['building_id'] = $this->Users->getUserBuildings($id);

        $ids = array();
        foreach($user->groups as $group)
        {
            $ids[] = $group->id;
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            
            //debug($this->request->data);
            $this->request->data['user_modified_id'] = $this->request->session()->read('Auth.User.id');
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                if (!empty($this->request->data['building_id'])) {
                    //siempre se borran las obras asociadas y se cargan las nuevas
                    $this->Users->BuildingsUsers->deleteAll(['BuildingsUsers.user_id' => $id]);
                    
                    $get_visitor = false;

                    foreach($user->groups as $group)
                    {
                        if($group->group_keyword == 'visitador')
                        {
                            $get_visitor = true;
                            break;
                        }
                    }

                    if ($get_visitor == true) {
                        foreach ($this->request->data['building_id'] as $key => $building_id) {
                            $building_user = $this->Users->BuildingsUsers->newEntity();
                            $building_user->user_id = $user->id;
                            $building_user->building_id = $building_id;
                            $this->Users->BuildingsUsers->save($building_user);
                        }
                    } else {
                        $building_user = $this->Users->BuildingsUsers->newEntity();
                        $building_user->user_id = $user->id;
                        $building_user->building_id = $this->request->data['building_id'];
                        $this->Users->BuildingsUsers->save($building_user);
                    }
                }

                //se encuentra los perfiles que existian v/s los que se elimino la asociación en el selector multiple
                $exist = array();

                foreach($user->groups as $group)
                {
                    $exist[] = $group->id;
                }

                $new = array();
                foreach($this->request->data['UsersGroups']['group_id'] as $key => $value)
                {
                    //$new[] = $value;
                    if(!in_array($value, $exist))
                    {
                        $new[] = array('user_id' => $user->id, 'group_id' => $value);
                    }
                }

                $delete_ids = array_diff($exist, $this->request->data['UsersGroups']['group_id']);
                
                //Si existen diferencias, borralas
                if($delete_ids != null)
                {
                    $delete = '';
                    foreach($delete_ids as $key => $value)
                    {
                        $delete .= $value.', ';
                    }

                    $delete = substr($delete, 0, -2);

                    $this->loadModel('UsersGroups');
                    $this->UsersGroups->deleteAll(['group_id IN' => $delete, 'user_id' => $user->id]);
                }
                
                //si existen asociaciones nuevas de perfil, agregalas
                if(count($new) > 0)
                {
                    $users_groups = TableRegistry::get('UsersGroups');
                    $entities = $users_groups->newEntities($new);
                    $users_groups->saveMany($entities);
                }

                $this->Flash->success('Se ha editado la información del usuario correctamente.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('Ocurrió un error al editar la información del usuario. Por Favor, inténtelo nuevamente.');
            }
        }
        $this->loadModel('Groups');
        $groups = $this->Groups->find('list', ['keyField' => 'id', 'valueField' => 'name']);
        $users = $this->Users->find('list', ['limit' => 200]);
        $this->set(compact('user', 'groups', 'users', 'buildings', 'ids'));
        $this->set('_serialize', ['user']);
    }

    /**
     * edita el usuario actual
     * @author Pablo Rivera <pablo.rivera@ideauno.cl>
     */
    public function editUser()
    {
        $user = $this->Users->get($this->request->session()->read('Auth.User.id'));
        if ($this->request->is(['patch', 'post', 'put'])) {
            //hora y minutos para guardar
            $this->request->data['birth_date']['hour'] = '00';
            $this->request->data['birth_date']['minute'] = '00';
            //$this->request->data['user_modifier_id'] = $this->request->session()->read('Auth.User.id');
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success('El usuario ha sido guardado.');
                return $this->redirect(['action' => 'listEditStatus']);
            } else {
                $this->Flash->error('El usuario no ha sido guardado, ');
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * [delete description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     * @author Carlos Vásquez <carlos.vasquez@ideauno.cl>
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success('El usuario ha sido eliminado.');
        } else {
            $this->Flash->error('El usuario no ha sido eliminado, intentalo nuevamente.');
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Actualiza el password del usuario actual
     * @author Pablo Rivera <pablo.rivera@ideauno.cl>
     */
    public function updatePassword()
    {
        //busca el usuario actual
        $users = TableRegistry::get('Users');
        $user = $users->get($this->request->session()->read('Auth.User.id'), [
            'contain' => ['Groups']
        ]);
        if ($this->request->is('post') || $this->request->is('put')) {
            //verifica si el password actual coincide con el ingresado
            if (! (new DefaultPasswordHasher)->check($this->request->data['old_password'], $user['password'])) {
                $this->Flash->info("El password no coincide con el password actual");
            }
            else
            {

                if($this->request->data['confirm_password'] != $this->request->data['password'])
                {
                    $this->Flash->info("Las contraseñas son distintas");
                }
                else
                {
                    //actualiza los datos
                    $users->patchEntity($user, $this->request->data);
                    if ($users->save($user)) {
                        $this->Flash->success('Se ha cambiado el password correctamente.');
                    } else {
                        $this->Flash->error("No se pudo cambiar el password. Por favor, inténtenlo nuevamente.");
                    }
                    return $this->redirect(['action' => 'updatePassworde']);
                }
                
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }


    /**
     * Actualiza el password de cualquier usuario como administrador
     * @param $id id del usuario
     * @author Pablo Rivera <pablo.rivera@ideauno.cl>
     */
    public function updatePasswordAdmin($id)
    {
        //busca el usuario seleccionado
        $users = TableRegistry::get('Users');
        $user = $users->get($id, [
            'contain' => ['Groups']
        ]);
        if ($this->request->is('post') || $this->request->is('put')) {
            //actualiza los datos
            $users->patchEntity($user, $this->request->data);
            if ($users->save($user)) {
                $this->Flash->success('Se ha cambiado el password correctamente.');
            } else {
                $this->Flash->error("No se pudo cambiar el password. Por favor, inténtenlo nuevamente.");
            }
            return $this->redirect(['action' => 'index']);
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Mustra una lista de usuarios
     * @author Pablo Rivera <pablo.rivera@ideauno.cl
     */
    public function listEditStatus()
    {
        $users = $this->Users->find('all', [
            'contain' => ['Groups'],
            'order' => ['Users.created' => 'desc'],
            'limit' => 500,
            'recursive' => 1
            ])->toArray();
        $this->set(compact('users'));
    }


    /**
     * Actualiza el estado de cualquier usuario como administrador
     * @param $id id del usuario
     * @author Pablo Rivera <pablo.rivera@ideauno.cl
     */
    public function editStatus($id)
    {
        //busca el usuario
        $user = $this->Users->get($id);
        //cambia el estado del usuario
        $user->active = ($user->active) ?  false :  true;
        //guarda los cambios
        if($this->Users->save($user)) {
            $this->Flash->success('Se ha cambiado el estado');
        } else {
            $this->Flash->error("No se pudo cambiar el estado");
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Página para recuperar contraseña, vía mail
     * @author Matías Pardo <matias.pardo@ideauno.cl>
     */
    public function forgottenPassword()
    {
        //$this->layout = 'default';
        if ($this->request->is('post')) {
            // Si recibe un post, se verifica el mail
            $email = $this->request->data['email'];
            $user = $this->Users->findByEmail($email)->first();

            if (empty($user->email)) {
                $this->Flash->error("La dirección de correo no es válida.");
            } else {
                // Se genera una pass temporal
                $tempPass = Security::hash(Text::uuid());
                // Se guarda la pass temporal en la bd
                $user->temp_pass = $tempPass;
                $this->Users->save($user);

                $url = Router::url([
                    'controller' => 'Users',
                    'action' => 'restorePassword/'.$tempPass,
                    ],[
                    'full_base' => true
                    ]
                );

                // Se envía un mail con la pass Temporal (un link a restorePassword/[$tempPass])
                $email = new Email('default');
                $email->from(['ldz.cpo@gmail.com' => '[LDZ CPO] :: Recuperación de contraseña'])
                    ->to($user->email)
                    ->subject('Recuperar Contraseña')
                    ->emailFormat('html')
                    ->template('forget_password', 'ldz_default')
                    ->attachments([
                            'logo_login.png' => [
                                'file' => 'img/logo_login.png',
                                'mimetype' => 'image/png',
                                'contentId' => 'logo-id'
                            ]
                        ])
                    ->viewVars(['url' => $url])
                    ->send();

                if ($email) {
                    $this->Flash->success('Se ha enviado un correo a su mail para reestablecer la contraseña.');
                    return $this->redirect(['action' => 'login']);
                } else {
                    $this->Flash->error("No se pudo enviar el correo para reestablecer contraseña.");
                }

            }
        }
    }

    /**
     * Página para restaurar contraseña, a partir de la tempPass
     * @author Matías Pardo <matias.pardo@ideauno.cl>
     */
    public function restorePassword($tempPass = null)
    {
        //$this->layout = 'default';

        if (!isset($tempPass)) {
            return $this->redirect(['action' => 'login']);
        }
        else
        {
            $user = $this->Users
                ->find('all')
                ->where([
                    'Users.temp_pass' => $tempPass,
                ])
                ->first();

            if($user->temp_pass != $tempPass)
            {
                $this->Flash->error("Solicitud de reestablecimiento de contraseña ha fallado, intente recuperar contraseña nuevamente.");
                return $this->redirect(['action' => 'login']);
            }
            else
            {
                $this->set('user', $user);

                if($this->request->is('post')) {
                    $password = $this->request->data['password'];
                    $password2 = $this->request->data['password2'];
                    // Se verifica que las contraseñas ingresadas sean iguales
                    if ($this->request->data['password'] != $this->request->data['password2']) 
                    {
                        $this->Flash->error("Las dos contraseñas no coinciden, intente nuevamente.");
                    }
                    else
                    {
                        // Si lo son, se busca el id de usuario con la tempPass
                        //$user = $this->Users->findByTemp_pass($tempPass)->first();
                        // Se actualiza la password del usuario y se borra la tempPass
                        if (empty($user)) {
                            $this->Flash->error("Solicitud de reestablecimiento de contraseña caducada, intente recuperar contraseña nuevamente.");
                            return $this->redirect(['action' => 'login']);
                        } else {
                            $user->password = $this->request->data['password'];
                            $user->temp_pass = null;
                            // Se guarda la nueva contraseña
                            if ($this->Users->save($user)) {
                                $this->Flash->success("Se ha guardado la nueva contraseña.");
                                return $this->redirect(['action' => 'login']);
                            } else {
                                $this->Flash->error("Solicitud de reestablecimiento de contraseña ha fallado, intente recuperar contraseña nuevamente.");
                                return $this->redirect(['action' => 'login']);
                            }
                        }
                    }
                }
            }   
        }
    }

    protected function _setCookie() {
        if (!$this->request->data('remember_me')) {
            return false;
        }
        $data = [
            'username' => $this->request->data('username'),
            'password' => $this->request->data('password')
        ];
        $this->Cookie->write('RememberMe', $data, true, '+1 week');
        return true;
    }

}
