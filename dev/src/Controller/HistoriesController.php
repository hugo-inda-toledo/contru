<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * RenditionItems Controller
 *
 * @property \App\Model\Table\RenditionItemsTable $RenditionItems */
class HistoriesController extends AppController
{

    /**
     * Listado del log
     * @return [type] [description]
     * @author Carlos Vásquez <carlos.vasquez@ideauno.cl>
     */
    public function index()
    {
        $this->paginate = [
            'order' => ['Histories.created' => 'desc'],
            'contain' => ['Users', 'Groups'],
        ];
        $this->set('histories', $this->paginate($this->Histories));
        $this->set('_serialize', ['histories']);
    }

}
