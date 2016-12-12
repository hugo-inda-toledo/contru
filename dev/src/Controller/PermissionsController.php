<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

/**
 * PermissionsController Controller
 *
 * @property \App\Model\Table\PermissionsTable $Permissions */
class PermissionsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'order' => ['Permissions.controller' => 'ASC', 'Permissions.action' => 'ASC'],
            'limit' => 200
        ];

        $this->set('permissions', $this->paginate($this->Permissions));
        $this->set('_serialize', ['groups']);
    }

    function add()
    {
        $permission = $this->Permissions->newEntity();

        if ($this->request->is('post')) {
            
            //debug($this->request->data);
            $permissions = $this->Permissions
                            ->find()
                            ->where(['Permissions.controller' => $this->request->data['controller'], 'Permissions.action' => $this->request->data['action']])
                            ->count();

            //echo $permissions;
            if($permissions == 0)
            {
                $permission = $this->Permissions->patchEntity($permission, $this->request->data);
                if($this->Permissions->save($permission)) 
                {
                    $this->Flash->success('El permiso ha sido guardado.');
                    return $this->redirect(['action' => 'index']);
                    
                } else {
                    $this->Flash->error('El permiso no ha sido guardado, intentalo nuevamente.');
                }
            }
            else
            {
                $this->Flash->warning('No pueden existir permisos con similar controlador y acción');
            }
        }

        $this->set(compact('permission'));
        $this->set('_serialize', ['permission']);
    }
}