<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * CurrenciesValues Controller
 *
 * @property \App\Model\Table\CurrenciesValuesTable $CurrenciesValues */
class CurrenciesValuesController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Budgets', 'Currencies']
        ];
        $this->set('currenciesValues', $this->paginate($this->CurrenciesValues));
        $this->set('_serialize', ['currenciesValues']);
    }

    /**
     * View method
     *
     * @param string|null $id Currencies Value id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $currenciesValue = $this->CurrenciesValues->get($id, [
            'contain' => ['Budgets', 'Currencies']
        ]);
        $this->set('currenciesValue', $currenciesValue);
        $this->set('_serialize', ['currenciesValue']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $currenciesValue = $this->CurrenciesValues->newEntity();
        if ($this->request->is('post')) {
            $currenciesValue = $this->CurrenciesValues->patchEntity($currenciesValue, $this->request->data);
            if ($this->CurrenciesValues->save($currenciesValue)) {
                $this->Flash->success('El valor de la divisa ha sido guardado.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('El valor de la divisa no ha sido guardado, intentalo nuevamente.');
            }
        }
        $budgets = $this->CurrenciesValues->Budgets->find('list', ['limit' => 200]);
        $currencies = $this->CurrenciesValues->Currencies->find('list', ['limit' => 200]);
        $this->set(compact('currenciesValue', 'budgets', 'currencies'));
        $this->set('_serialize', ['currenciesValue']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Currencies Value id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $currenciesValue = $this->CurrenciesValues->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $currenciesValue = $this->CurrenciesValues->patchEntity($currenciesValue, $this->request->data);
            if ($this->CurrenciesValues->save($currenciesValue)) {
                $this->Flash->success('El valor de la divisa ha sido actualizado.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('El valor de la divisa no ha sido actualizado, intentalo nuevamente.');
            }
        }
        $budgets = $this->CurrenciesValues->Budgets->find('list', ['limit' => 200]);
        $currencies = $this->CurrenciesValues->Currencies->find('list', ['limit' => 200]);
        $this->set(compact('currenciesValue', 'budgets', 'currencies'));
        $this->set('_serialize', ['currenciesValue']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Currencies Value id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $currenciesValue = $this->CurrenciesValues->get($id);
        if ($this->CurrenciesValues->delete($currenciesValue)) {
            $this->Flash->success('El valor de la divisa ha sido eliminado.');
        } else {
            $this->Flash->error('El valor de la divisa no ha sido eliminado, intentalo nuevamente.');
        }
        return $this->redirect(['action' => 'index']);
    }
}
