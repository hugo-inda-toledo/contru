<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Currencies Controller
 *
 * @property \App\Model\Table\CurrenciesTable $Currencies */
class CurrenciesController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('currencies', $this->paginate($this->Currencies));
        $this->set('_serialize', ['currencies']);
    }

    /**
     * View method
     *
     * @param string|null $id Currency id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $currency = $this->Currencies->get($id, [
            'contain' => ['Budgets']
        ]);
        $this->set('currency', $currency);
        $this->set('_serialize', ['currency']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $currency = $this->Currencies->newEntity();
        if ($this->request->is('post')) {
            $currency = $this->Currencies->patchEntity($currency, $this->request->data);
            if ($this->Currencies->save($currency)) {
                $this->Flash->success('La divisa ha sido guardada.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('La divisa no pudo ser guardada, intentalo nuevamente.');
            }
        }
        $budgets = $this->Currencies->Budgets->find('list', ['limit' => 200]);
        $this->set(compact('currency', 'budgets'));
        $this->set('_serialize', ['currency']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Currency id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $currency = $this->Currencies->get($id, [
            'contain' => ['Budgets']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $currency = $this->Currencies->patchEntity($currency, $this->request->data);
            if ($this->Currencies->save($currency)) {
                $this->Flash->success('La divisa ha sido actualizada.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('La divisa no ha sido actualizada, intentalo nuevamente.');
            }
        }
        $budgets = $this->Currencies->Budgets->find('list', ['limit' => 200]);
        $this->set(compact('currency', 'budgets'));
        $this->set('_serialize', ['currency']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Currency id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $currency = $this->Currencies->get($id);
        if ($this->Currencies->delete($currency)) {
            $this->Flash->success('La divisa ha sido eliminada.');
        } else {
            $this->Flash->error('La divisa no ha sido eliminada, intentalo nuevamente.');
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Obtiene el valor mas actualizado en base al id de la moneda
     * @param  [type] $id [description]
     * @return [type]     [description]
     * @author Hugo Inda <hugo.inda@ideauno.cl>
     */
    function getLastValueById($id = null)
    {
        if($id != null)
        {
            if($this->request->is('ajax')) 
            {     
                $this->viewBuilder()->layout('ajax');

                $this->loadModel('Currencies');
                $this->Currencies->updateCurrencies();

                $this->loadModel('Valoresmonedas');
                $currency = $this->Valoresmonedas->find('all', ['contain' => ['Currencies'], 'conditions' => ['Valoresmonedas.currency_id' => $id, 'Valoresmonedas.currency_date' => date('Y-m-d')], 'limit' => 1])->first();

                if($currency && $currency->currency->variable_value == 1)
                {
                    $this->set('value', str_replace('.', ',', $currency->currency_value));
                }
                else
                {
                    $this->set('value', 1);
                }
            }
        }
     }
}
