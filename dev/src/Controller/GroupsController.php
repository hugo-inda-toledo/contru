<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

/**
 * Groups Controller
 *
 * @property \App\Model\Table\GroupsTable $Groups */
class GroupsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'order' => ['Groups.level' => 'DESC']
        ];

        $this->set('groups', $this->paginate($this->Groups));
        $this->set('_serialize', ['groups']);
    }

    /**
     * View method
     *
     * @param string|null $id Group id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $group = $this->Groups->get($id, [
            'contain' => ['Users', 'Permissions']
        ]);
        $this->set('group', $group);
        $this->set('_serialize', ['group']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $group = $this->Groups->newEntity();
        $this->loadModel('Permissions');
        $permissions = $this->Permissions->find('all', ['order' => ['controller' => 'ASC', 'action' => 'ASC']]);

        $x=0;
        foreach($permissions as $perm)
        {
            $permissions_list[$perm->controller][$perm->id] = $perm->permission_name;
        }

        if ($this->request->is('post')) {
            //debug($this->request->data);
            
            $keyword = strtolower($this->request->data['Groups']['name']);
            $keyword = str_replace(' ', '_', $keyword);
            $this->request->data['Groups']['group_keyword'] = $keyword;
            
            $group = $this->Groups->patchEntity($group, $this->request->data['Groups']);
            if ($this->Groups->save($group)) 
            {
                $assoc = array();
                foreach($this->request->data['GroupsPermissions']['permission_id'] as $key => $value)
                {
                    $assoc[] = array('group_id' => $group->id, 'permission_id' => $value);
                }
                
                $groups_permissions = TableRegistry::get('GroupsPermissions');
                $entities = $groups_permissions->newEntities($assoc);
                if($groups_permissions->saveMany($entities))
                {
                    $this->Flash->success('El perfil ha sido guardado.');
                    return $this->redirect(['action' => 'index']);
                }
                
            } else {
                $this->Flash->error('El perfil no se ha guardado, intentalo nuevamente.');
            }
        }

        $this->set('levels', array(1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7', 8 => '8', 9 => '9', 10 => '10'));
        $this->set('permissions', $permissions_list);
        $this->set(compact('group'));
        $this->set('_serialize', ['group']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Group id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $group = $this->Groups->get($id, [
            'contain' => ['Permissions']
        ]);

        $ids = array();
        foreach($group->permissions as $perm)
        {
            $ids[] = $perm->id;
        }

        $this->loadModel('Permissions');
        $permissions = $this->Permissions->find('all', ['order' => ['controller' => 'ASC', 'action' => 'ASC']]);

        $x=0;
        foreach($permissions as $perm)
        {
            $permissions_list[$perm->controller][$perm->id] = $perm->permission_name;
        }

        if ($this->request->is(['patch', 'post', 'put'])) 
        {
            $group = $this->Groups->patchEntity($group, $this->request->data);

            if ($this->Groups->save($group))
            {
                $exist = array();

                foreach($group->permissions as $perm)
                {
                    $exist[] = $perm->id;
                }

                $new = array();
                foreach($this->request->data['GroupsPermissions']['permission_id'] as $key => $value)
                {
                    //$new[] = $value;
                    if(!in_array($value, $exist))
                    {
                        $new[] = array('group_id' => $group->id, 'permission_id' => $value);
                    }
                }

                $delete_ids = array_diff($exist, $this->request->data['GroupsPermissions']['permission_id']);

                if($delete_ids != null)
                {
                    $delete = '';
                    foreach($delete_ids as $key => $value)
                    {
                        $delete .= $value.', ';
                    }

                    $delete = substr($delete, 0, -2);

                    $this->loadModel('GroupsPermissions');
                    $this->GroupsPermissions->deleteAll(['permission_id IN' => $delete, 'group_id' => $group->id]);
                }

                if(count($new) > 0)
                {
                    $groups_permissions = TableRegistry::get('GroupsPermissions');
                    $entities = $groups_permissions->newEntities($new);
                    $groups_permissions->saveMany($entities);
                }

                $this->Flash->success('El perfil ha sido actualizado.');
                return $this->redirect(['action' => 'index']);
            }
            else
            {
                $this->Flash->error('El perfil no ha sido actualizado, intentalo nuevamente.');
            }
        }

        //debug($group);
        $this->set('levels', array(1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7', 8 => '8', 9 => '9', 10 => '10'));
        $this->set('ids', $ids);
        $this->set('permissions', $permissions_list);
        $this->set(compact('group'));
        $this->set('_serialize', ['group']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Group id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Groups->Users->find('all',['conditions' => ['group_id' => $id]])->first();
        if(!is_null($user)) {
            $this->Flash->error('El perfil está siendo utilizado, no puede ser eliminado.');
            return $this->redirect(['action' => 'index']);
        }
        $group = $this->Groups->get($id);

        $this->loadModel('GroupsPermissions');

        if ($this->Groups->delete($group)) {
            $this->GroupsPermissions->deleteAll(['group_id' => $id]);
            $this->Flash->success('El perfil fue eliminado.');
        } else {
            $this->Flash->error('El perfil no pudo ser eliminado. Intente de nuevo.');
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Activate method
     *
     * @param string|null $id Group id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function activate($id = null)
    {        
        $group_find = $this->Groups->exists(['id' => $id]);
        if(!$group_find) {
            $this->Flash->error('El perfil no existe.');
        } else {
            $group = $this->Groups->get($id);
            $group->status = 1;
            $this->Groups->save($group);        
            $this->Flash->success('El perfil ha sido activado.');            
        }   
        
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Deactivate method
     *
     * @param string|null $id Group id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function deactivate($id = null)
    {
        $group_find = $this->Groups->exists(['id' => $id]);
        if(!$group_find) {
            $this->Flash->error('El perfil no existe.');
        } else {
            $group = $this->Groups->get($id);
            $group->status = 0;
            $this->Groups->save($group);
            $this->Flash->success('El perfil ha sido desactivado.');            
        }       
        
        
        return $this->redirect(['action' => 'index']);
    }
}
