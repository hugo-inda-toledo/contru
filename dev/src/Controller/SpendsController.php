<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\I18n\Date;
use Cake\I18n\Time;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Controller\Component;
use Cake\Cache\Cache;

/**
 * Spends Controller
 *
 * @property \App\Model\Table\IcMaterials */
class SpendsController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->loadModel('BudgetItems');
        $this->loadModel('Budgets');
        $this->loadModel('Currencies');
        $this->loadModel('IcOrgc');
        $this->loadModel('IcOrdenCompraItem');
        $this->loadModel('IcOrdenCompra');
        $this->loadModel('IcPartida');
        $this->loadModel('IcMaterial');
        $this->loadModel('IcSubcontrato');
        $this->loadModel('IcSubcontratoItem');
        $this->loadModel('IcSubcontratoDistribucion');
        $this->loadModel('IcOrdenCompraDistribucion');
        $this->loadModel('IcEstadoPagoDistribucion');
        $this->loadModel('IcEstadoPago');
        $this->loadModel('IcRespaldoOcDistribucion');
        $this->loadModel('IcRespaldo');
    }

    /**
     * Obtiene el valor de la columna determinada de la partida que se almacena en iconstruye
     * @param  [type]  [description]
     * @return [type]  [description]
     * @author Hugo Inda <hugo.inda@ideauno.cl>
     */
    function getCustomColumnFromIconstruye($item_id = null, $column = null)
    {
        // Si viene id y columna a devolver
        if($item_id != null && $column != null)
        {
            //Obtener el id de la obra a partir de la partida
            $budgetItem = $this->BudgetItems->get($item_id, [
                'contain' => ['Budgets' => ['Buildings']],
                'select' => ['budget.building_id']
            ]);


            //Si existe la partida
            if($budgetItem != null)
            {
                //Obtengo todas las obras de iConstruye
                $orgcs = $this->IcOrgc->find('all')->toArray();

                //iteracion de obras
                foreach($orgcs as $orgc)
                {
                    //El codigo en iConstruye estar en varchar, se debe pasar a integer
                    $orgc->CODIGO = intval($orgc->CODIGO);

                    switch($orgc->CODIGO)
                    {
                        //En el caso que el codigo de obra de mySql coincida con el codigo pasado a int de iConstruye
                        case $budgetItem->budget->building->softland_id :

                            //Busqueda de partida en iConstruye
                            $partida = $this->IcPartida->find()->where(['IcPartida.IDORGC' => $orgc->IDORGC, 'IcPartida.CODIGO' => $budgetItem->item])->first();

                            //Si existe la partida
                            if($partida != null)
                            {
                                //Retornar el id de la partida que se encuentra en iconstruye
                                if($partida->$column)
                                {
                                    return $partida->$column;
                                }

                                return false;
                            }
                            else
                            {
                                return false;
                            }

                            break;
                    }
                }

                return false;
            }

            return false;
        }

        return false;
    }

    /**
     * Máscara con gastos de un presupuesto
     * @param  [type] $budget_id [description]
     * @return [type]            [description]
     * @author Hugo Inda <hugo.inda@ideauno.cl>
     */
    function overview($budget_id = null)
    {
        // cucho: si no hay un cache de la vista
        if (($vista_mascara = Cache::read('mascara_vista_' . $budget_id, 'config_cache_mascara')) === false) {
            // cucho: fin

            $this->loadModel('Budgets');
            $this->loadModel('BudgetItems');
            $this->loadModel('BudgetItemsSchedules');

            $budget = array();

            $group_id = $this->request->session()->read('Auth.User.group_id');

            if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
                $user_buildings = $this->Budgets->UserCreateds->getUserBuildings($this->request->session()->read('Auth.User.id'));
                if (count($user_buildings) > 0) {
                    $budget = $this->Schedules->Budgets->find('all', [
                        'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates'],
                        'conditions' => ['Budgets.building_id' => $user_buildings[0]]
                    ])->first();
                    if (empty($budget_id) && $budget_id == null) {
                        $budget_id = $budget->id;
                    } else {
                        if ($budget->id != $budget_id) {
                            $this->Flash->info('El usuario no está asociado a ninguna obra o esta no corresponde a la asistencia. Por favor, edite la información de usuario.');
                            return $this->redirect(['controller' => 'users', 'action' => 'index']);
                        }
                    }
                } else {
                    $this->Flash->info('El usuario no está asociado a ninguna obra. Por favor, edite la información de usuario.');
                    return $this->redirect(['controller' => 'users', 'action' => 'index']);
                }
            } else {
                if (empty($budget_id) && $budget_id == null) {
                    $buildings = $this->Schedules->Budgets->Buildings->getActiveBuildingsWithSoftlandInfo();
                    $budget = $this->Schedules->Budgets->find('all', [
                        'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates'],
                        'conditions' => ['Budgets.building_id' => $buildings[0]]
                    ])->first();
                    $budget_id = $budget->id;
                } else {
                    $budget = $this->Budgets->get($budget_id, [
                        'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'Currencies']
                    ]);
                }
            }

            //información general
            $this->loadModel('SfBuildings');
            $sf_building = $this->SfBuildings->find('all', [
                 'conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]
            ])->first();

            // Load budgetItems
            $bi = $this->BudgetItems
            ->find('all', [
                'conditions' => ['budget_id' => $budget_id, 'parent_id IS' => null, 'BudgetItems.disabled' => 0]
                ])
            ->contain([
                'Units',
                'Progress' => function ($q) {
                    return $q
                        ->order(['Progress.proyected_progress_percent' => 'DESC']);
                    }
            ]);
            $budget_items = array();
            foreach ($bi as $value) {
                $children = $this->BudgetItems
                ->find('children', ['for' => $value->id])
                ->find('threaded')
                // Progress ordenados descendente por avance proyectado
                ->contain([
                    'Units',
                    'Progress' => function ($q) {
                        return $q
                            ->order(['Progress.proyected_progress_percent' => 'DESC']);
                        },
                    'Materials'
                ])
                ->toArray();
                $budget_items[$value->id] = $value->toArray();
                $budget_items[$value->id]['children'] = $children;
            }  
            
            $this->set(compact('schedule', 'budget_items', 'budget', 'sf_building'));
            $this->set('_serialize', ['schedule']);
            $this->set('budget_id', $budget_id);

            // cucho: escribe el render en un cache
            Cache::write('mascara_vista_' . $budget_id, $this->render(), 'config_cache_mascara');

        }
        else
        { // cucho: hay un cache

            // cucho: lee el cache y le hace un render, parece que ultrajo los estandares)
            echo Cache::read('mascara_vista_' . $budget_id, 'config_cache_mascara');

            // cucho: no hace render
            $this->autoRender = false;
        }
        // cucho: fin
    }

    /**
     * Limpia el cache de la máscara
     * @param  [type] $budget_id [description]
     * @return [type]            [description]
     * @author Hugo Inda <hugo.inda@ideauno.cl>
     */
    function cleanCachedMask($budget_id = null)
    {
        if($budget_id != null)
        {
            Cache::delete('mascara_vista_'.$budget_id, 'config_cache_mascara');
        }
    }

    /**
     * Verifica si existe cache de la máscara
     * @param  [type] $budget_id [description]
     * @return [type]            [description]
     * @author Hugo Inda <hugo.inda@ideauno.cl>
     */
    function verifiedIfExistCache($budget_id = null)
    {
        $exist_cache = 'no_exist';

        if($budget_id != null)
        {
            $this->viewBuilder()->layout('ajax');
            
            if(($vista_mascara = Cache::read('mascara_vista_' . $budget_id, 'config_cache_mascara')) === false)
            {
                $exist_cache = 'no_exist';
            }
            else
            {
                $exist_cache = 'exist';
            }
        }
        
        $this->set('exist_cache', $exist_cache);
    }

    /**
     * Retorna los materiales comprados para una partida
     * @param  [type] $item_id [description]
     * @return [type]          [description]
     * @author Hugo Inda <hugo.inda@ideauno.cl>
     */
    function purchasedMaterialsDetails($item_id = null)
    {
        if($item_id != null)
        {
            if(($vista_mascara = Cache::read('materiales_comprometidos_vista_' . $item_id, 'config_cache_mascara')) === false)
            {
                $budget_item = $this->BudgetItems->find('all')->contain(['Budgets' => ['Currencies']])->where(['BudgetItems.id' => $item_id])->first();

                $iContruyeOrgcId = $this->getCustomColumnFromIconstruye($item_id, 'IDORGC');
                // obtiene el codigo de la partida de iconstruye dandole el id del sistema
                $iContruyeCodigoId = $this->getCustomColumnFromIconstruye($item_id, 'CODIGO');

                if($iContruyeOrgcId != false && $iContruyeCodigoId != false)
                {
                    $ids_ocs = $this->IcOrdenCompraDistribucion
                                ->find('all')
                                //->contain(['IcOrdenCompra', 'IcOrdenCompraItem'])
                                ->where([
                                    'IcOrdenCompraDistribucion.IDORGC' => $iContruyeOrgcId,
                                    'IcOrdenCompraDistribucion.IDCENTCOSTO' => $iContruyeCodigoId
                                ])
                                ->order(['IcOrdenCompraDistribucion.IDOC' => 'DESC'])
                                ->select(['IDOC'])
                                ->group(['IDOC'])
                                ->toArray();

                    if(count($ids_ocs) > 0)
                    {
                        $real_data = array();
                        $x = 0;
                        
                        foreach($ids_ocs as $oc)
                        {
                            $oc_data = $this->IcOrdenCompra
                                        ->find('all')
                                        ->contain(['IcOrdenCompraConsolidado', 'IcOrdenCompraCargo'])
                                        ->where(['IcOrdenCompra.IDOC' => $oc->IDOC])
                                        ->first();

                            if(count($oc_data) > 0)
                            {
                                $real_data[$x]['OrdenCompra'] = $oc_data;

                                $oc_items = $this->IcOrdenCompraItem
                                            ->find('all')
                                            ->contain(['IcUom', 'IcOrdenCompraItemDescuento'])
                                            ->where(['IcOrdenCompraItem.IDOC' => $oc_data->IDOC])
                                            ->toArray();

                                if(count($oc_items) > 0)
                                {
                                    $y = 0;

                                    foreach($oc_items as $item)
                                    {
                                        $item_distr = $this->IcOrdenCompraDistribucion
                                                        ->find('all')
                                                        ->where([
                                                            'IcOrdenCompraDistribucion.IDOCLINEA' => $item->IDOCLINEA,
                                                            'IcOrdenCompraDistribucion.IDCENTCOSTO' => $iContruyeCodigoId
                                                        ])
                                                        ->toArray();

                                        if(count($item_distr) > 0)
                                        {
                                            $real_data[$x]['OrdenCompraItem'][$y]['Data'] = $item;
                                            $real_data[$x]['OrdenCompraItem'][$y]['Distribucion'] = $item_distr;

                                            $real_data[$x]['OrdenCompraItem'][$y]['Data']['currency_day_value'] = $this->Currencies->getDayValue($oc_data->FECHACREACION->year.'-'.$oc_data->FECHACREACION->month.'-'.$oc_data->FECHACREACION->day, $budget_item->budget->currencies[0]->sbif_api_keyword);
                                        }

                                        $y++;
                                    }
                                }

                                $x++;
                            }
                        }

                        $this->set('budget_item', $budget_item);
                        $this->set('ordenes_compra', $real_data);

                        Cache::write('materiales_comprometidos_vista_' . $item_id, $this->render(), 'config_cache_mascara');                    }
                    else
                    {
                        $this->Flash->error(__('No existen ordenes de compra asociadas a esta partida'));
                        $this->redirect($this->referer());
                    }
                }
                else
                {
                    // Lanza flash de error y redirige a la pagina actual
                    $this->Flash->error(__('No se encontro identificadores de la partida y/o obra en iConstruye.'));
                    $this->redirect($this->referer());
                }
            }
            else
            {
                echo Cache::read('materiales_comprometidos_vista_' . $item_id, 'config_cache_mascara');
                $this->autoRender = false;
            }
        }
        else
        {
            // Si no lanza flash de error y redirige a la pagina actual
            $this->Flash->error(__('No se pueden mostrar materiales sin identificador de partida'));
            $this->redirect($this->referer());
        }
    }

    /**
     * Retorna materiales consumidos de una partida
     * @param  [type]  [description]
     * @return [type]  [description]
     * @author Hugo Inda <hugo.inda@ideauno.cl>
     */
    public function usedMaterialsDetails($item_id = null)
    {
        // si viene un id
        if($item_id != null)
        {
            if(($vista_mascara = Cache::read('materiales_gastados_vista_' . $item_id, 'config_cache_mascara')) === false)
            {
                //Data básica de la partida
                $budget_item = $this->BudgetItems->find('all')->contain(['Budgets' => ['Currencies']])->where(['BudgetItems.id' => $item_id])->first();

                // obtiene id de la partida de iconstruye dandole el id del sistema
                $iContruyeItemId = $this->getCustomColumnFromIconstruye($item_id, 'IDPARTIDA');

                // Si encuentra el Id en Iconstruye
                if($iContruyeItemId != false)
                {
                    //Encapsular en array los materiales de la partida
                    $materials = $this->IcMaterial
                                ->find('all')
                                ->contain(['IcConsumo'])
                                ->where(['IcMaterial.IDPARTIDA' => $iContruyeItemId])
                                ->toArray();

                    foreach($materials as $material)
                    {
                        $material->ic_consumo['currency_day_value'] = $this->Currencies->getDayValue($material->ic_consumo->FECHACREACION->year.'-'.$material->ic_consumo->FECHACREACION->month.'-'.$material->ic_consumo->FECHACREACION->day, $budget_item->budget->currencies[0]->sbif_api_keyword);
                    }

                    if(count($materials) > 0)
                    {
                        //Envio arrays al template
                        $this->set('budget_item', $budget_item);
                        $this->set('materials', $materials);
                        $this->set('_serialize', ['materials']);

                        Cache::write('materiales_gastados_vista_' . $item_id, $this->render(), 'config_cache_mascara');
                    }
                    else
                    {
                        // Lanza flash de error y redirige a la pagina actual
                        $this->Flash->warning(__('La partida no tiene materiales asociados.'));
                        $this->redirect($this->referer());
                    }
                }
                else
                {
                    // Lanza flash de error y redirige a la pagina actual
                    $this->Flash->error(__('No se encontro el identificador de la partida en iConstruye.'));
                    $this->redirect($this->referer());
                }
            }
            else
            {
                echo Cache::read('materiales_gastados_vista_' . $item_id, 'config_cache_mascara');
                $this->autoRender = false;
            }
            

        }
        else
        {
            // Si no lanza flash de error y redirige a la pagina actual
            $this->Flash->error(__('No se pueden mostrar materiales sin identificador de partida'));
            $this->redirect($this->referer());
        }

    }

    /**
     * Detalle de materiales facturados para una partida especifica
     * @param  [type] $item_id [description]
     * @return [type]          [description]
     * @author Hugo Inda <hugo.inda@ideauno.cl>
     */
    function factMaterialsDetails($item_id = null)
    {
        if($item_id != null)
        {
            if(($vista_mascara = Cache::read('materiales_facturados_vista_' . $item_id, 'config_cache_mascara')) === false)
            {
                $budget_item = $this->BudgetItems->find('all')->contain(['Budgets' => ['Currencies']])->where(['BudgetItems.id' => $item_id])->first();

                // obtiene id de la obra de iconstruye dandole el id del sistema
                $iContruyeOrgcId = $this->getCustomColumnFromIconstruye($item_id, 'IDORGC');
                // obtiene el codigo de la partida de iconstruye dandole el id del sistema
                $iContruyeCodigoId = $this->getCustomColumnFromIconstruye($item_id, 'CODIGO');

                //Obtengo array con ordenes de compra y sus items en base a el ID de la obra en iConstruye
                //y el codigo de la partida en iConstruye
                $ordenes_compra_data = $this->IcOrdenCompraDistribucion
                                    ->find('all')
                                    ->contain([
                                        'IcOrdenCompra' => [
                                            'IcRespaldoOcDistribucion' =>[
                                                'queryBuilder' => function($q) use ($iContruyeCodigoId){
                                                    return $q->where(['IcRespaldoOcDistribucion.IDCENTCOSTO'=>$iContruyeCodigoId]);
                                                },
                                                'IcRespaldo' => [
                                                    'IcFactura'
                                                ]
                                            ],
                                            'IcOrdenCompraConsolidado'
                                        ],
                                        'IcOrdenCompraItem' => ['IcUom'],
                                        'IcTipoDoc'
                                    ])
                                    ->where([
                                        'IcOrdenCompraDistribucion.IDORGC' => $iContruyeOrgcId,
                                        'IcOrdenCompraDistribucion.IDCENTCOSTO' => $iContruyeCodigoId

                                    ])
                                    ->order(['IcOrdenCompraDistribucion.IDOC' => 'DESC'])
                                    ->toArray();

                $real_data = array();

                if(count($ordenes_compra_data) > 0)
                {
                    $x=0;
                    $y=0;
                    $z=0;

                    foreach($ordenes_compra_data as $purchase_order)
                    {
                        if($x == 0)
                        {
                            $lastId = $purchase_order->ic_orden_compra->IDOC;


                            if($purchase_order->ic_orden_compra->ic_orden_compra_consolidado != null && $purchase_order->ic_orden_compra->ic_respaldo_oc_distribucion->ic_respaldo->ic_factura != null)
                            {

                                $lastFactId = $purchase_order->ic_orden_compra->ic_respaldo_oc_distribucion->ic_respaldo->ic_factura->IDDOC;

                                $lastItemId = $purchase_order->ic_orden_compra_item->IDOCLINEA;

                                $real_data[$x]['OrdenCompra'] = $purchase_order->ic_orden_compra;
                                $real_data[$x]['OrdenCompraTipo'] = $purchase_order->ic_tipo_doc;
                                $real_data[$x]['OrdenCompraItem'][$y]['Data'] = $purchase_order->ic_orden_compra_item;
                                $real_data[$x]['OrdenCompraItem'][$y]['Distribucion']['VALOR'] = $purchase_order->VALOR;
                                $real_data[$x]['OrdenCompraItem'][$y]['Distribucion']['PRECIOVALOR'] = $purchase_order->PRECIOVALOR;
                                $real_data[$x]['OrdenCompraConsolidado'] = $purchase_order->ic_orden_compra->ic_orden_compra_consolidado;
                                $real_data[$x]['Factura'][$z] = $purchase_order->ic_orden_compra->ic_respaldo_oc_distribucion->ic_respaldo->ic_factura;

                                $real_data[$x]['Factura'][$z]['currency_day_value'] = $this->Currencies->getDayValue($purchase_order->ic_orden_compra->ic_respaldo_oc_distribucion->ic_respaldo->ic_factura->FECHAEMISION->year.'-'.$purchase_order->ic_orden_compra->ic_respaldo_oc_distribucion->ic_respaldo->ic_factura->FECHAEMISION->month.'-'.$purchase_order->ic_orden_compra->ic_respaldo_oc_distribucion->ic_respaldo->ic_factura->FECHAEMISION->day, $budget_item->budget->currencies[0]->sbif_api_keyword);

                                $y++;
                                $x++;
                                $z++;
                            }
                        }
                        else
                        {
                            if($lastId != $purchase_order->ic_orden_compra->IDOC)
                            {
                                
                                $lastId = $purchase_order->ic_orden_compra->IDOC;

                                if($purchase_order->ic_orden_compra->ic_orden_compra_consolidado != null && $purchase_order->ic_orden_compra->ic_respaldo_oc_distribucion->ic_respaldo->ic_factura != null)
                                {
                                    $x++;
                                    $y=0;
                                    $z=0;

                                    $lastFactId = $purchase_order->ic_orden_compra->ic_respaldo_oc_distribucion->ic_respaldo->ic_factura->IDDOC;

                                    $lastItemId = $purchase_order->ic_orden_compra_item->IDOCLINEA;

                                    $real_data[$x-1]['OrdenCompra'] = $purchase_order->ic_orden_compra;
                                    $real_data[$x-1]['OrdenCompraTipo'] = $purchase_order->ic_tipo_doc;
                                    $real_data[$x-1]['OrdenCompraItem'][$y]['Data'] = $purchase_order->ic_orden_compra_item;
                                    $real_data[$x-1]['OrdenCompraItem'][$y]['Distribucion']['VALOR'] = $purchase_order->VALOR;
                                    $real_data[$x-1]['OrdenCompraItem'][$y]['Distribucion']['PRECIOVALOR'] = $purchase_order->PRECIOVALOR;
                                    $real_data[$x-1]['OrdenCompraConsolidado'] = $purchase_order->ic_orden_compra->ic_orden_compra_consolidado;
                                    $real_data[$x-1]['Factura'][$z] = $purchase_order->ic_orden_compra->ic_respaldo_oc_distribucion->ic_respaldo->ic_factura;

                                    $real_data[$x-1]['Factura'][$z]['currency_day_value'] = $this->Currencies->getDayValue($purchase_order->ic_orden_compra->ic_respaldo_oc_distribucion->ic_respaldo->ic_factura->FECHAEMISION->year.'-'.$purchase_order->ic_orden_compra->ic_respaldo_oc_distribucion->ic_respaldo->ic_factura->FECHAEMISION->month.'-'.$purchase_order->ic_orden_compra->ic_respaldo_oc_distribucion->ic_respaldo->ic_factura->FECHAEMISION->day, $budget_item->budget->currencies[0]->sbif_api_keyword);

                                    $y++;
                                    $z++;
                                }
                                
                            }
                            else
                            {
                                if($purchase_order->ic_orden_compra->ic_orden_compra_consolidado != null && $purchase_order->ic_orden_compra->ic_respaldo_oc_distribucion->ic_respaldo->ic_factura != null)
                                {
                                    //$real_data[$x-1]['OrdenCompraItem'][$y] = $purchase_order->ic_orden_compra_item;

                                    if($lastItemId != $purchase_order->ic_orden_compra_item->IDOCLINEA)
                                    {
                                        $real_data[$x-1]['OrdenCompraItem'][$y]['Data'] = $purchase_order->ic_orden_compra_item;
                                        $real_data[$x-1]['OrdenCompraItem'][$y]['Distribucion']['VALOR'] = $purchase_order->VALOR;
                                        $real_data[$x-1]['OrdenCompraItem'][$y]['Distribucion']['PRECIOVALOR'] = $purchase_order->PRECIOVALOR;

                                        $lastItemId = $purchase_order->ic_orden_compra_item->IDOCLINEA;
                                    
                                        $y++;
                                    }

                                    if($lastFactId != $purchase_order->ic_orden_compra->ic_respaldo_oc_distribucion->ic_respaldo->ic_factura->IDDOC)
                                    {
                                        $real_data[$x-1]['Factura'][$z] = $purchase_order->ic_orden_compra->ic_respaldo_oc_distribucion->ic_respaldo->ic_factura;

                                        $real_data[$x-1]['Factura'][$z]['currency_day_value'] = $this->Currencies->getDayValue($purchase_order->ic_orden_compra->ic_respaldo_oc_distribucion->ic_respaldo->ic_factura->FECHAEMISION->year.'-'.$purchase_order->ic_orden_compra->ic_respaldo_oc_distribucion->ic_respaldo->ic_factura->FECHAEMISION->month.'-'.$purchase_order->ic_orden_compra->ic_respaldo_oc_distribucion->ic_respaldo->ic_factura->FECHAEMISION->day, $budget_item->budget->currencies[0]->sbif_api_keyword);

                                        $lastFactId = $purchase_order->ic_orden_compra->ic_respaldo_oc_distribucion->ic_respaldo->ic_factura->IDDOC;

                                        $z++;
                                    }
                                }
                            }
                        }                        
                    }
                }
                
                for($x=0; $x < count($real_data); $x++)
                {
                    $real_data[$x]['OrdenCompraItem'] = array_unique($real_data[$x]['OrdenCompraItem'], SORT_REGULAR);
                    $real_data[$x]['Factura'] = array_unique($real_data[$x]['Factura'], SORT_REGULAR);
                }

                $this->set('budget_item', $budget_item);
                $this->set('real_data', $real_data);

                Cache::write('materiales_facturados_vista_' . $item_id, $this->render(), 'config_cache_mascara');
            }
            else
            {
                echo Cache::read('materiales_facturados_vista_' . $item_id, 'config_cache_mascara');
                $this->autoRender = false;
            }

            
        }
    }

    /**
     * Retorna los subcontratos de una partida
     * @param  [type] $item_id [description]
     * @return [type]          [description]
     * @author Hugo Inda <hugo.inda@ideauno.cl>
     */
    function subcontractsDetails($item_id = null)
    {
        if($item_id != null)
        {
            if(($vista_mascara = Cache::read('subcontratos_comprometidos_vista_' . $item_id, 'config_cache_mascara')) === false)
            {
                $budget_item = $this->BudgetItems->find('all')->contain(['Budgets' => ['Currencies']])->where(['BudgetItems.id' => $item_id])->first();

                // obtiene id de la partida de iconstruye dandole el id del sistema
                $iContruyeOrgcId = $this->getCustomColumnFromIconstruye($item_id, 'IDORGC');
                // obtiene el codigo de la partida de iconstruye dandole el id del sistema
                $iContruyeCodigoId = $this->getCustomColumnFromIconstruye($item_id, 'CODIGO');

                // Si se obtienen los datos anteriores
                if($iContruyeOrgcId != false && $iContruyeCodigoId != false)
                {
                    //Buscar subcontratos en iConstruye a partir del id de la obra y el codigo de la partida
                    $ids_sbcts = $this->IcSubcontratoDistribucion
                                ->find('all')
                                ->where([
                                    'IcSubcontratoDistribucion.IDORGC' => $iContruyeOrgcId,
                                    'IcSubcontratoDistribucion.IDCENTCOSTO' => $iContruyeCodigoId
                                ])
                                ->order(['IcSubcontratoDistribucion.IDSUBCONT' => 'DESC'])
                                ->select(['IDSUBCONT'])
                                ->group(['IDSUBCONT'])
                                ->toArray();

                    if(count($ids_sbcts) > 0)
                    {
                        $real_data = array();
                        $x = 0;
                        
                        foreach($ids_sbcts as $sub)
                        {
                            $subcontrato_data = $this->IcSubcontrato
                                        ->find('all')
                                        ->contain(['IcSubcontratoConsolidado', 'IcSubcontratoTipo', 'IcSubcontratoAprobacion'])
                                        ->where(['IcSubcontrato.IDDOC' => $sub->IDSUBCONT])
                                        ->first();

                            //debug($subcontrato_data);

                            if(count($subcontrato_data) > 0)
                            {
                                if($subcontrato_data->ic_subcontrato_consolidado != null)
                                {
                                    $approved = true;

                                    foreach($subcontrato_data->ic_subcontrato_aprobacion as $aprob)
                                    {
                                        if($aprob->APROBADA != 1)
                                        {
                                            $approved = false;
                                        }
                                    }

                                    if($approved == true)
                                    {
                                        $real_data[$x]['Subcontrato'] = $subcontrato_data;

                                        $subcontrato_items = $this->IcSubcontratoItem
                                                    ->find('all')
                                                    ->contain(['IcUom'])
                                                    ->where(['IcSubcontratoItem.IDDOC' => $subcontrato_data->IDDOC])
                                                    ->toArray();

                                        if(count($subcontrato_items) > 0)
                                        {
                                            $y = 0;

                                            foreach($subcontrato_items as $item)
                                            {
                                                $item_distr = $this->IcSubcontratoDistribucion
                                                                ->find('all')
                                                                ->where([
                                                                    'IcSubcontratoDistribucion.IDSUBCONTLINEA' => $item->IDLINEA,
                                                                    'IcSubcontratoDistribucion.IDCENTCOSTO' => $iContruyeCodigoId
                                                                ])
                                                                ->contain(['IcTipoDoc'])
                                                                ->toArray();

                                                if(count($item_distr) > 0)
                                                {
                                                    $real_data[$x]['SubcontratoItem'][$y]['Data'] = $item;
                                                    $real_data[$x]['SubcontratoItem'][$y]['Distribucion'] = $item_distr;

                                                    $real_data[$x]['Subcontrato']['currency_day_value'] = $this->Currencies->getDayValue($subcontrato_data->FECHACREACION->year.'-'.$subcontrato_data->FECHACREACION->month.'-'.$subcontrato_data->FECHACREACION->day, $budget_item->budget->currencies[0]->sbif_api_keyword);
                                                }

                                                $y++;
                                            }
                                        }

                                        $x++;
                                    }
                                }
                            }
                        }

                        
                        $this->set('budget_item', $budget_item);
                        $this->set('subcontratos', $real_data);
                        $this->set('iContruyeCodigoId', $iContruyeCodigoId);
                        $this->set('iContruyeOrgcId', $iContruyeOrgcId);

                        Cache::write('subcontratos_comprometidos_vista_' . $item_id, $this->render(), 'config_cache_mascara');
                    }
                    else
                    {
                        // Si no lanza flash de error y redirige a la pagina actual
                        $this->Flash->warning(__('La partida no tiene subcontratos asociados'));
                        $this->redirect($this->referer());
                    }
                }
                else
                {
                    // Si no lanza flash de error y redirige a la pagina actual
                    $this->Flash->error(__('No se encontraron los identificadores para la busqueda de subcontratos'));
                    $this->redirect($this->referer());
                }
            }
            else
            {
                echo Cache::read('subcontratos_comprometidos_vista_' . $item_id, 'config_cache_mascara');
                $this->autoRender = false;
            }
        }
        else
        {
            // Si no lanza flash de error y redirige a la pagina actual
            $this->Flash->error(__('No se pueden mostrar subcontratos sin identificador de partida'));
            $this->redirect($this->referer());
        }
    }

    function usedSubcontractsDetails($item_id = null)
    {
        if($item_id != null)
        {
            if(($vista_mascara = Cache::read('subcontratos_gastados_vista_' . $item_id, 'config_cache_mascara')) === false)
            {
                $budget_item = $this->BudgetItems->find('all')->contain(['Budgets' => ['Currencies']])->where(['BudgetItems.id' => $item_id])->first();

                // obtiene id de la obra de iconstruye dandole el id del sistema
                $iContruyeOrgcId = $this->getCustomColumnFromIconstruye($item_id, 'IDORGC');
                // obtiene el codigo de la partida de iconstruye dandole el id del sistema
                $iContruyeCodigoId = $this->getCustomColumnFromIconstruye($item_id, 'CODIGO');

                $estado_pagos = $this->IcEstadoPagoDistribucion
                                ->find('all')
                                ->contain([
                                    'IcSubcontrato', 
                                    'IcEstadoPago' =>[
                                        'IcEstadoDoc',
                                        'IcTipoDoc',
                                        'IcEstadoPagoAprobacion'
                                    ],
                                    'IcEstadoPagoItem'
                                ])
                                ->where([
                                    'IcEstadoPagoDistribucion.IDORGC' => $iContruyeOrgcId,
                                    'IcEstadoPagoDistribucion.IDCENTCOSTO' => $iContruyeCodigoId
                                ])
                                ->order(['IcEstadoPagoDistribucion.IDSUBCONTRATO' => 'DESC'])
                                ->toArray();

                $real_data = array();

                if(count($estado_pagos) > 0)
                {
                    $x=0;
                    $y=0;
                    $z=0;

                    foreach($estado_pagos as $ep)
                    {
                        if($x == 0)
                        {

                            if(count($ep->ic_estado_pago->ic_estado_pago_aprobacion) > 0)
                            {
                                $approved = true;

                                foreach($ep->ic_estado_pago->ic_estado_pago_aprobacion as $aprob)
                                {
                                    if($aprob->APROBADA != 1)
                                    {
                                        $approved = false;
                                    }
                                }

                                if($approved == true)
                                {
                                    $lastSubId = $ep->ic_subcontrato->IDDOC;
                                    $lastEpId = $ep->ic_estado_pago->IDDOC;




                                    $real_data[$x]['Subcontrato'] = $ep->ic_subcontrato;
                                    $real_data[$x]['EstadoPago'][$y]['Data'] = $ep->ic_estado_pago;
                                    $real_data[$x]['EstadoPago'][$y]['Items'][$z]['Data'] = $ep->ic_estado_pago_item;
                                    $real_data[$x]['EstadoPago'][$y]['Items'][$z]['Distribucion']['VALOR'] = $ep->VALOR;
                                    $real_data[$x]['EstadoPago'][$y]['Items'][$z]['Distribucion']['PRECIOVALOR'] = $ep->PRECIOVALOR;

                                    $real_data[$x]['EstadoPago'][$y]['Data']['currency_day_value'] = $this->Currencies->getDayValue($ep->ic_estado_pago->FECHACREACION->year.'-'.$ep->ic_estado_pago->FECHACREACION->month.'-'.$ep->ic_estado_pago->FECHACREACION->day, $budget_item->budget->currencies[0]->sbif_api_keyword);


                                    $y++;
                                    $x++;
                                    $z++;
                                }
                            }
                        }
                        else
                        {
                            if($ep->ic_subcontrato->IDDOC == $lastSubId)
                            {
                                $lastSubId = $ep->ic_subcontrato->IDDOC;
                                

                                if($lastEpId == $ep->ic_estado_pago->IDDOC)
                                {
                                    //$lastEpId = $ep->ic_estado_pago->IDDOC;

                                    $real_data[$x-1]['EstadoPago'][$y-1]['Items'][$z]['Data'] = $ep->ic_estado_pago_item;
                                    $real_data[$x-1]['EstadoPago'][$y-1]['Items'][$z]['Distribucion']['VALOR'] = $ep->VALOR;
                                    $real_data[$x-1]['EstadoPago'][$y-1]['Items'][$z]['Distribucion']['PRECIOVALOR'] = $ep->PRECIOVALOR;
                                    //$y++;
                                    $z++;
                                }
                                else
                                {
                                    if(count($ep->ic_estado_pago->ic_estado_pago_aprobacion) > 0)
                                    {
                                        $approved = true;

                                        foreach($ep->ic_estado_pago->ic_estado_pago_aprobacion as $aprob)
                                        {
                                            if($aprob->APROBADA != 1)
                                            {
                                                $approved = false;
                                            }
                                        }

                                        if($approved == true)
                                        {
                                            
                                            $z=0;
                                            
                                            $lastEpId = $ep->ic_estado_pago->IDDOC;

                                            $real_data[$x-1]['EstadoPago'][$y]['Data'] = $ep->ic_estado_pago;
                                            $real_data[$x-1]['EstadoPago'][$y]['Items'][$z]['Data'] = $ep->ic_estado_pago_item;
                                            $real_data[$x-1]['EstadoPago'][$y]['Items'][$z]['Distribucion']['VALOR'] = $ep->VALOR;
                                            $real_data[$x-1]['EstadoPago'][$y]['Items'][$z]['Distribucion']['PRECIOVALOR'] = $ep->PRECIOVALOR;

                                            $real_data[$x-1]['EstadoPago'][$y]['Data']['currency_day_value'] = $this->Currencies->getDayValue($ep->ic_estado_pago->FECHACREACION->year.'-'.$ep->ic_estado_pago->FECHACREACION->month.'-'.$ep->ic_estado_pago->FECHACREACION->day, $budget_item->budget->currencies[0]->sbif_api_keyword);

                                            $y++;
                                            $z++;
                                        }
                                    }
                                }

                                
                            }
                            else
                            {
                                $lastSubId = $ep->ic_subcontrato->IDDOC;
                                

                                if(count($ep->ic_estado_pago->ic_estado_pago_aprobacion) > 0)
                                {
                                    $approved = true;

                                    foreach($ep->ic_estado_pago->ic_estado_pago_aprobacion as $aprob)
                                    {
                                        if($aprob->APROBADA != 1)
                                        {
                                            $approved = false;
                                        }
                                    }

                                    if($approved == true)
                                    {

                                        $lastEpId = $ep->ic_estado_pago->IDDOC;

                                        $x++;
                                        $y=0;
                                        $z=0;


                                        $real_data[$x-1]['Subcontrato'] = $ep->ic_subcontrato;
                                        $real_data[$x-1]['EstadoPago'][$y]['Data'] = $ep->ic_estado_pago;
                                        $real_data[$x-1]['EstadoPago'][$y]['Items'][$z]['Data'] = $ep->ic_estado_pago_item;
                                        $real_data[$x-1]['EstadoPago'][$y]['Items'][$z]['Distribucion']['VALOR'] = $ep->VALOR;
                                        $real_data[$x-1]['EstadoPago'][$y]['Items'][$z]['Distribucion']['PRECIOVALOR'] = $ep->PRECIOVALOR;

                                        $real_data[$x-1]['EstadoPago'][$y]['Data']['currency_day_value'] = $this->Currencies->getDayValue($ep->ic_estado_pago->FECHACREACION->year.'-'.$ep->ic_estado_pago->FECHACREACION->month.'-'.$ep->ic_estado_pago->FECHACREACION->day, $budget_item->budget->currencies[0]->sbif_api_keyword);

                                        $y++;
                                        $z++;
                                    }
                                }
                            }
                        }                        
                    }
                }

                $this->set('budget_item', $budget_item);
                $this->set('real_data', $real_data);

                Cache::write('subcontratos_gastados_vista_' . $item_id, $this->render(), 'config_cache_mascara');
            }
            else
            {
                echo Cache::read('subcontratos_gastados_vista_' . $item_id, 'config_cache_mascara');
                $this->autoRender = false;
            }
            

        }
    }

    function factSubcontractsDetails($item_id = null)
    {
        if($item_id != null)
        {
            if(($vista_mascara = Cache::read('subcontratos_facturados_vista_' . $item_id, 'config_cache_mascara')) === false)
            {
                $budget_item = $this->BudgetItems->find('all')->contain(['Budgets' => ['Currencies']])->where(['BudgetItems.id' => $item_id])->first();
                // obtiene id de la obra de iconstruye dandole el id del sistema
                $iContruyeOrgcId = $this->getCustomColumnFromIconstruye($item_id, 'IDORGC');
                // obtiene el codigo de la partida de iconstruye dandole el id del sistema
                $iContruyeCodigoId = $this->getCustomColumnFromIconstruye($item_id, 'CODIGO');

                $estado_pagos = $this->IcEstadoPagoDistribucion
                                ->find('all')
                                ->contain([
                                    'IcSubcontrato', 
                                    'IcEstadoPago' =>[
                                        'IcEstadoDoc',
                                        'IcTipoDoc',
                                        'IcFactura'
                                    ],
                                    'IcEstadoPagoItem'
                                ])
                                ->where([
                                    'IcEstadoPagoDistribucion.IDORGC' => $iContruyeOrgcId,
                                    'IcEstadoPagoDistribucion.IDCENTCOSTO' => $iContruyeCodigoId
                                ])
                                ->order(['IcEstadoPagoDistribucion.IDSUBCONTRATO' => 'DESC'])
                                ->toArray();

                //debug($estado_pagos);

                if(count($estado_pagos) > 0)
                {
                    $real_data = array();
                    $x = 0;
                    $y=0;
                    
                    foreach($estado_pagos as $ep)
                    {
                        if($x == 0)
                        {
                            $lastSubId = $ep->ic_subcontrato->IDDOC;

                            $real_data[$x]['Subcontrato'] = $ep->ic_subcontrato;
                            $real_data[$x]['EstadoPago'][$y]['Data'] = $ep->ic_estado_pago;
                            $real_data[$x]['EstadoPago'][$y]['Item'] = $ep->ic_estado_pago_item;
                            $real_data[$x]['EstadoPago'][$y]['Distribucion']['VALOR'] = $ep->VALOR;
                            $real_data[$x]['EstadoPago'][$y]['Distribucion']['PRECIOVALOR'] = $ep->PRECIOVALOR;
                            $real_data[$x]['EstadoPago'][$y]['Data']['currency_day_value'] = $this->Currencies->getDayValue($ep->ic_estado_pago->ic_factura->FECHAEMISION->year.'-'.$ep->ic_estado_pago->ic_factura->FECHAEMISION->month.'-'.$ep->ic_estado_pago->ic_factura->FECHAEMISION->day, $budget_item->budget->currencies[0]->sbif_api_keyword);
                            
                            $x++;
                            $y++;
                        }
                        else
                        {
                            if($lastSubId != $ep->ic_subcontrato->IDDOC)
                            {
                                
                                $lastSubId = $ep->ic_subcontrato->IDDOC;
                                $x++;
                                $y=0;

                                $real_data[$x-1]['Subcontrato'] = $ep->ic_subcontrato;
                                $real_data[$x-1]['EstadoPago'][$y]['Data'] = $ep->ic_estado_pago;
                                $real_data[$x-1]['EstadoPago'][$y]['Item'] = $ep->ic_estado_pago_item;
                                $real_data[$x-1]['EstadoPago'][$y]['Distribucion']['VALOR'] = $ep->VALOR;
                                $real_data[$x-1]['EstadoPago'][$y]['Distribucion']['PRECIOVALOR'] = $ep->PRECIOVALOR;
                                $real_data[$x-1]['EstadoPago'][$y]['Data']['currency_day_value'] = $this->Currencies->getDayValue($ep->ic_estado_pago->ic_factura->FECHAEMISION->year.'-'.$ep->ic_estado_pago->ic_factura->FECHAEMISION->month.'-'.$ep->ic_estado_pago->ic_factura->FECHAEMISION->day, $budget_item->budget->currencies[0]->sbif_api_keyword);

                                $y++;
                            }
                            else
                            {
                                $real_data[$x-1]['EstadoPago'][$y]['Data'] = $ep->ic_estado_pago;
                                $real_data[$x-1]['EstadoPago'][$y]['Item'] = $ep->ic_estado_pago_item;
                                $real_data[$x-1]['EstadoPago'][$y]['Distribucion']['VALOR'] = $ep->VALOR;
                                $real_data[$x-1]['EstadoPago'][$y]['Distribucion']['PRECIOVALOR'] = $ep->PRECIOVALOR;
                                $real_data[$x-1]['EstadoPago'][$y]['Data']['currency_day_value'] = $this->Currencies->getDayValue($ep->ic_estado_pago->ic_factura->FECHAEMISION->year.'-'.$ep->ic_estado_pago->ic_factura->FECHAEMISION->month.'-'.$ep->ic_estado_pago->ic_factura->FECHAEMISION->day, $budget_item->budget->currencies[0]->sbif_api_keyword);

                                $y++;
                            }
                        }
                    }

                    //debug($real_data);
                    $this->set('budget_item', $budget_item);

                    $this->set('subcontratos', $real_data);
                    Cache::write('subcontratos_facturados_vista_' . $item_id, $this->render(), 'config_cache_mascara');
                }
            }
            else
            {
                echo Cache::read('subcontratos_facturados_vista_' . $item_id, 'config_cache_mascara');
                $this->autoRender = false;
            }
        }
    }
}