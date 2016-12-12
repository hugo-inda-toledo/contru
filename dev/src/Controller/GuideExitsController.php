<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * GuideExits Controller
 *
 * @property \App\Model\Table\GuideExitsTable $GuideExits */
class GuideExitsController extends AppController
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
        $this->set('guideExits', $this->paginate($this->GuideExits));
        $this->set('_serialize', ['guideExits']);
    }

    /**
     * View method
     *
     * @param string|null $id Guide Exit id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $guideExit = $this->GuideExits->get($id, [
            'contain' => ['IconstruyeImports', 'BudgetItems']
        ]);
        $this->set('guideExit', $guideExit);
        $this->set('_serialize', ['guideExit']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $guideExit = $this->GuideExits->newEntity();
        if ($this->request->is('post')) {
            $guideExit = $this->GuideExits->patchEntity($guideExit, $this->request->data);
            if ($this->GuideExits->save($guideExit)) {
                $this->Flash->success('The guide exit has been saved.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('The guide exit could not be saved. Please, try again.');
            }
        }
        $iconstruyeImports = $this->GuideExits->IconstruyeImports->find('list', ['limit' => 200]);
        $budgetItems = $this->GuideExits->BudgetItems->find('list', ['limit' => 200]);
        $this->set(compact('guideExit', 'iconstruyeImports', 'budgetItems'));
        $this->set('_serialize', ['guideExit']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Guide Exit id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $guideExit = $this->GuideExits->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $guideExit = $this->GuideExits->patchEntity($guideExit, $this->request->data);
            if ($this->GuideExits->save($guideExit)) {
                $this->Flash->success('The guide exit has been saved.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('The guide exit could not be saved. Please, try again.');
            }
        }
        $iconstruyeImports = $this->GuideExits->IconstruyeImports->find('list', ['limit' => 200]);
        $budgetItems = $this->GuideExits->BudgetItems->find('list', ['limit' => 200]);
        $this->set(compact('guideExit', 'iconstruyeImports', 'budgetItems'));
        $this->set('_serialize', ['guideExit']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Guide Exit id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $guideExit = $this->GuideExits->get($id);
        if ($this->GuideExits->delete($guideExit)) {
            $this->Flash->success('The guide exit has been deleted.');
        } else {
            $this->Flash->error('The guide exit could not be deleted. Please, try again.');
        }
        return $this->redirect(['action' => 'index']);
    }
}
