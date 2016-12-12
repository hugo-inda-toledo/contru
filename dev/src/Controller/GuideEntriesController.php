<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * GuideEntries Controller
 *
 * @property \App\Model\Table\GuideEntriesTable $GuideEntries */
class GuideEntriesController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['IconstruyeImports', 'BudgetItems']
        ];
        $this->set('guideEntries', $this->paginate($this->GuideEntries));
        $this->set('_serialize', ['guideEntries']);
    }

    /**
     * View method
     *
     * @param string|null $id Guide Entry id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $guideEntry = $this->GuideEntries->get($id, [
            'contain' => ['IconstruyeImports', 'BudgetItems']
        ]);
        $this->set('guideEntry', $guideEntry);
        $this->set('_serialize', ['guideEntry']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $guideEntry = $this->GuideEntries->newEntity();
        if ($this->request->is('post')) {
            $guideEntry = $this->GuideEntries->patchEntity($guideEntry, $this->request->data);
            if ($this->GuideEntries->save($guideEntry)) {
                $this->Flash->success('The guide entry has been saved.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('The guide entry could not be saved. Please, try again.');
            }
        }
        $iconstruyeImports = $this->GuideEntries->IconstruyeImports->find('list', ['limit' => 200]);
        $budgetItems = $this->GuideEntries->BudgetItems->find('list', ['limit' => 200]);
        $this->set(compact('guideEntry', 'iconstruyeImports', 'budgetItems'));
        $this->set('_serialize', ['guideEntry']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Guide Entry id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $guideEntry = $this->GuideEntries->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $guideEntry = $this->GuideEntries->patchEntity($guideEntry, $this->request->data);
            if ($this->GuideEntries->save($guideEntry)) {
                $this->Flash->success('The guide entry has been saved.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('The guide entry could not be saved. Please, try again.');
            }
        }
        $iconstruyeImports = $this->GuideEntries->IconstruyeImports->find('list', ['limit' => 200]);
        $budgetItems = $this->GuideEntries->BudgetItems->find('list', ['limit' => 200]);
        $this->set(compact('guideEntry', 'iconstruyeImports', 'budgetItems'));
        $this->set('_serialize', ['guideEntry']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Guide Entry id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $guideEntry = $this->GuideEntries->get($id);
        if ($this->GuideEntries->delete($guideEntry)) {
            $this->Flash->success('The guide entry has been deleted.');
        } else {
            $this->Flash->error('The guide entry could not be deleted. Please, try again.');
        }
        return $this->redirect(['action' => 'index']);
    }
}
