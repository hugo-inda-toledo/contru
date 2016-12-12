<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Charges Controller
 *
 * @property \App\Model\Table\ChargesTable $Charges */
class ChargesController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->autoRender = false;
        $charges = $this->Charges->getSoftlandCharges();
        $total = $charges->count();
        $i = 0;
        foreach($charges as $k => $ch) {
            if(isset($charge)) {
                unset($charge);
            }
            $charge = $this->Charges->newEntity();
            $charge->softland_id = $k;
            $charge->name = $ch;
            $charge->max_amount_deals = rand(100000, 50000);
            $charge->max_amount_bonus = rand(100000, 50000);
            if($this->Charges->save($charge)) {
                $i++;
            }
        }
        if($total == $i) {
            $this->Flash->success(__('Cargos actualizados correctamente.'));
        } else {
                $this->Flash->error(__('Los cargos no han sido actualizados, intentalo nuevamente.'));
        }
        return $this->redirect(['controller' => 'buildings', 'action' => 'index']);
    }

    /**
     * View method
     *
     * @param string|null $id Charge id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $charge = $this->Charges->get($id);
        $this->set('charge', $charge);
        $this->set('_serialize', ['charge']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $charge = $this->Charges->newEntity();
        if ($this->request->is('post')) {
            $charge = $this->Charges->patchEntity($charge, $this->request->data);
            if ($this->Charges->save($charge)) {
                $this->Flash->success(__('El cargo ha sido guardada.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El cargo no ha sido guardada, intentalo nuevamente.'));
            }
        }
        $this->set(compact('charge'));
        $this->set('_serialize', ['charge']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Charge id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $charge = $this->Charges->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $charge = $this->Charges->patchEntity($charge, $this->request->data);
            if ($this->Charges->save($charge)) {
                $this->Flash->success(__('El cargo ha sido actualizado.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El cargo ha sido actualizado, intentalo nuevamente.'));
            }
        }
        $this->set(compact('charge'));
        $this->set('_serialize', ['charge']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Charge id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $charge = $this->Charges->get($id);
        if ($this->Charges->delete($charge)) {
            $this->Flash->success(__('El cargo fue eliminado'));
        } else {
            $this->Flash->error(__('El cargo no ha sido eliminado, intentalo nuevamente'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
