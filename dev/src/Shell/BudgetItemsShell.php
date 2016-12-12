<?php

namespace App\Shell;

use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;
use Cake\Log\Log;
use Psy\Shell as PsyShell;
use App\Shell\TableRegistry;
use Cake\I18n\Date;
use Cake\I18n\Time;
use Cake\Cache\Cache;

/**
 * Simple console wrapper around Psy\Shell.
 */
class BudgetItemsShell extends Shell
{

    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Budgets');
	    $this->loadModel('BudgetItems');
	    $this->loadModel('IcPartida');
	    $this->loadModel('IcOrgc');
	    $this->loadModel('IcOrdenCompraDistribucion');
	    $this->loadModel('IcOrdenCompra');
	    $this->loadModel('IcOrdenCompraItem');
	    $this->loadModel('Currencies');
	    $this->loadModel('IcMaterial');
	    $this->loadModel('IcSubcontratoDistribucion');
	    $this->loadModel('IcSubcontratoItem');
        $this->loadModel('IcSubcontratoConsolidado');
	    $this->loadModel('IcEstadoPagoDistribucion');
    }

    public function main()
    {
        $budgets = $this->Budgets->find('all')
        			->contain([
        				'Buildings' => [
		        			'queryBuilder' => function($q){
			                    return $q->where(['Buildings.active' => 1, 'Buildings.omit' => 0]);
			            	}
        				]
        			])
        			->toArray();

        if(count($budgets) > 0)
        {
        	foreach($budgets as $budget)
        	{
        		$this->out('Actualizando valores de la obra');
        		$this->refreshItems($budget->id);
        		$this->out('Se ha finalizado la actualizacion de valores de la obra');
                
                Cache::delete('mascara_vista_'.$budget->id, 'config_cache_mascara');

        		$this->out('**************************************************');
        		$this->out('**************************************************');
        		$this->out('**************************************************');
        	}

            
        	$this->out('Actualizacion completa');
        }
        else
        {
        	$this->out('No hay presupuestos para actualizar');
        }
    }

    function refreshItems($budget_id = null)
	{
	    $badge_currency_keyword = '';
	    $currency = array();

	    $budget = $this->Budgets
	        ->find('all', [
	            'conditions' => ['Budgets.id' => $budget_id]
	            ])
	        ->contain([
	            'Currency',
	            'BudgetItems' =>[
	                'queryBuilder' => function($q){
	                    return $q->where(['BudgetItems.disabled' => 0, 'BudgetItems.parent_id IS NOT' => null]);
	                },
	                'ChildBudgetItems' => [
	                    'queryBuilder' => function($q){
	                        return $q->where(['ChildBudgetItems.id IS NOT' => null]);
	                    }
	                ]
	            ],
	        ])
	        ->first();

	    switch ($budget->currency->sbif_api_keyword) {
	        case 'peso':
	            $badge_currency_keyword = $budget->currency->sbif_api_keyword;
	            break;
	        
	        default:
	            $badge_currency_keyword = $budget->currency->sbif_api_keyword;

	            $this->loadModel('Currencies');
	            $currency = $this->Currencies->find('all', [
	                'conditions' => ['Currencies.sbif_api_keyword' => 'peso'],
	                'fields' => ['Currencies.sbif_api_keyword', 'Currencies.initials']
	            ])->first();

	            break;
	    }

	    foreach($budget->budget_items as $bi)
	    {
            $starts = array();

            if(count($bi->child_budget_items) > 0)
            {
                $starts[] = array('Partida', 'Descripcion', 'Materiales Comprom.','Materiales Gastados', 'Materiales Facturados', 'Subcontratos Comprom.','Subcontratos Gastados', 'Subcontratos Facturados'); 
            }

	        foreach($bi->child_budget_items as $item)
	        {
	            $commitedMaterials = $this->commitedMaterials($item->id, $currency, $badge_currency_keyword);
	            $spentMaterials = $this->spentMaterials($item->id, $currency, $badge_currency_keyword);
	            $invoicedMaterials = $this->invoicedMaterials($item->id, $currency, $badge_currency_keyword);

	            $commitedSubcontracts = $this->commitedSubcontracts($item->id, $currency, $badge_currency_keyword);
	            $spentSubcontracts = $this->spentSubcontracts($item->id, $currency, $badge_currency_keyword);
	            $invoicedSubcontracts = $this->invoicedSubcontracts($item->id, $currency, $badge_currency_keyword);

	            $this->out('Insertando datos de la partida '.$item->item.' ('.$item->description.')...');
                //$this->out('Insertando datos de la partida '.$item->item.' ('.$item->description.')... materiales c: '.$commitedMaterials.' sp: '.$spentMaterials.' in: '.$invoicedMaterials.' subcon c: '.$commitedSubcontracts.' sp: '.$spentSubcontracts.' in: '.$invoicedSubcontracts);

	            $this->BudgetItems->injectGlobalQuantification($item, $commitedMaterials, $spentMaterials, $invoicedMaterials, $commitedSubcontracts, $spentSubcontracts, $invoicedSubcontracts);

                $starts[] = array($item->item, $item->description, $commitedMaterials, $spentMaterials, $invoicedMaterials, $commitedSubcontracts, $spentSubcontracts, $invoicedSubcontracts); 

                Cache::delete('materiales_comprometidos_vista_'.$item->id, 'config_cache_mascara');
                Cache::delete('materiales_gastados_vista_'.$item->id, 'config_cache_mascara');
                Cache::delete('materiales_facturados_vista_'.$item->id, 'config_cache_mascara');

                Cache::delete('subcontratos_comprometidos_vista_'.$item->id, 'config_cache_mascara');
                Cache::delete('subcontratos_gastados_vista_'.$item->id, 'config_cache_mascara');
                Cache::delete('subcontratos_facturados_vista_'.$item->id, 'config_cache_mascara');
	        }

            if(count($bi->child_budget_items) > 0)
            {
                $this->helper('Table')->output($starts);
            }
	    }

	    unset($budget);
	    unset($currency);

	    $budget_items = $this->BudgetItems->find('all')
	                    ->where(['BudgetItems.budget_id' => $budget_id, 'BudgetItems.disabled' => 0, 'BudgetItems.parent_id IS NOT' => NULL])
	                    ->select([
	                        'BudgetItems.id',
                            'BudgetItems.item',
                            'BudgetItems.description',
	                        'BudgetItems.commited',
	                        'BudgetItems.spent',
	                        'BudgetItems.invoiced',
	                        'BudgetItems.commited_materials',
	                        'BudgetItems.spent_materials',
	                        'BudgetItems.invoiced_materials',
	                        'BudgetItems.commited_subcontracts',
	                        'BudgetItems.spent_subcontracts',
	                        'BudgetItems.invoiced_subcontracts'
	                    ])
	                    ->contain([
	                        'ParentBudgetItems',
	                        'ChildBudgetItems'
	                    ])
	                    ->order(['BudgetItems.item' => 'DESC'])
	                    ->toArray();

	    foreach($budget_items as $bi)
	    {
	        if(count($bi->child_budget_items) > 0)
	        {
	        	$this->out('Insertando la suma de total de la partida subpadre '.$bi->item.' ('.$bi->description.')...');

	            $this->BudgetItems->injectQuantificationDetails($bi);
	        }
	    }

	    $budget_items = $this->BudgetItems->find('all')
	                    ->where(['BudgetItems.budget_id' => $budget_id, 'BudgetItems.parent_id IS' => NULL])
	                    ->select([
	                        'BudgetItems.id',
	                        'BudgetItems.parent_id',
                            'BudgetItems.item',
                            'BudgetItems.description',
	                        'BudgetItems.commited',
	                        'BudgetItems.spent',
	                        'BudgetItems.invoiced',
	                        'BudgetItems.commited_materials',
	                        'BudgetItems.spent_materials',
	                        'BudgetItems.invoiced_materials',
	                        'BudgetItems.commited_subcontracts',
	                        'BudgetItems.spent_subcontracts',
	                        'BudgetItems.invoiced_subcontracts'
	                    ])
	                    ->contain([
	                        'ParentBudgetItems',
	                        'ChildBudgetItems'
	                    ])
	                    ->order(['BudgetItems.item' => 'DESC'])
	                    ->toArray();

	    foreach($budget_items as $bi)
	    {
	    	$this->out('Insertando la suma total de la partida padre '.$bi->item.' ('.$bi->description.')...');
	        $this->BudgetItems->injectQuantificationDetails($bi);
	    }

	    unset($budget_items);
	}

	public function commitedMaterials($item_id = null, $currency = array(), $currency_keyword = null)
    {
        $total = 0;

        if($item_id != null)
        {
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
                    foreach($ids_ocs as $oc)
                    {
                        $oc_data = $this->IcOrdenCompra
                                    ->find('all')
                                    ->contain(['IcOrdenCompraConsolidado'])
                                    ->where(['IcOrdenCompra.IDOC' => $oc->IDOC])
                                    ->select(['IcOrdenCompra.IDOC', 'IcOrdenCompra.FECHACREACION'])
                                    ->first();

                        if(count($oc_data) > 0)
                        {
                            $oc_items = $this->IcOrdenCompraItem
                                        ->find('all')
                                        ->contain(['IcUom', 'IcOrdenCompraItemDescuento'])
                                        ->where(['IcOrdenCompraItem.IDOC' => $oc_data->IDOC])
                                        ->select(['IDOCLINEA', 'MONTOUNITARIO', 'CANTIDAD'])
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
                                                    ->select(['VALOR', 'TIPODISTRIB'])
                                                    ->toArray();

                                    if(count($item_distr) > 0)
                                    {
                                        $quantity = 0;

                                        foreach($item_distr as $distr)
                                        {
                                            if($distr->TIPODISTRIB == 1)
                                            {
                                                $quantity = ($distr->VALOR / 100) * $item->CANTIDAD;
                                            }
                                            else
                                            {
                                                $quantity = $distr->VALOR;
                                            }

                                            switch ($currency_keyword) {
                                                case 'peso':
                                                    $total += $item->MONTOUNITARIO * $quantity;
                                                    break;
                                                
                                                default:
                                                    $total += $this->Currencies->transformValue($item->MONTOUNITARIO * $quantity, $currency_keyword, $currency->sbif_api_keyword, $oc_data->FECHACREACION->year.'-'.$oc_data->FECHACREACION->month.'-'.$oc_data->FECHACREACION->day);
                                                    break;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $total;
    }

    function spentMaterials($item_id = null, $currency = array(), $currency_keyword = null)
    {
        $total = 0;

        if($item_id != null)
        {
            // obtiene id de la obra de iconstruye dandole el id del sistema
           $iContruyeOrgcId = $this->getCustomColumnFromIconstruye($item_id, 'IDORGC');
            // obtiene el codigo de la partida de iconstruye dandole el id del sistema
            $iContruyeItemId = $this->getCustomColumnFromIconstruye($item_id, 'IDPARTIDA');

            // Si encuentra el Id en Iconstruye
            if($iContruyeItemId != false && $iContruyeOrgcId != false)
            {
                //Encapsular en array los materiales de la partida
                $materials = $this->IcMaterial
                            ->find('all')
                            ->contain(['IcConsumo' => function($q) {
                                return $q
                                    ->select(['IcConsumo.FECHACREACION']);
                            }])
                            ->where([
                                'IcMaterial.IDPARTIDA' => $iContruyeItemId,
                                'IcMaterial.IDORGC' => $iContruyeOrgcId
                            ])
                            ->select('IcMaterial.COSTOLINEA')
                            ->toArray();

                if(count($materials) > 0)
                {
                    foreach($materials as $material)
                    {
                        switch ($currency_keyword) {
                            case 'peso':
                                $total += $material->COSTOLINEA;
                                break;
                            
                            default:
                                $total += $this->Currencies->transformValue($material->COSTOLINEA, $currency_keyword, $currency->sbif_api_keyword, $material->ic_consumo->FECHACREACION->year.'-'.$material->ic_consumo->FECHACREACION->month.'-'.$material->ic_consumo->FECHACREACION->day);
                                break;
                        }
                    }
                }
            }
        }
        return $total;
    }

    function invoicedMaterials($item_id = null, $currency = array(), $currency_keyword = null)
    {
        $total = 0;

        if($item_id != null)
        {
            // obtiene id de la obra de iconstruye dandole el id del sistema
            $iContruyeOrgcId = $this->getCustomColumnFromIconstruye($item_id, 'IDORGC');
            // obtiene el codigo de la partida de iconstruye dandole el id del sistema
            $iContruyeCodigoId = $this->getCustomColumnFromIconstruye($item_id, 'CODIGO');

            // Si se obtienen los datos anteriores
            if($iContruyeOrgcId != false && $iContruyeCodigoId != false)
            {
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
                                        'IcOrdenCompraItem' => ['IcUom']
                                    ])
                                    ->where([
                                        'IcOrdenCompraDistribucion.IDORGC' => $iContruyeOrgcId,
                                        'IcOrdenCompraDistribucion.IDCENTCOSTO' => $iContruyeCodigoId

                                    ])
                                    ->order(['IcOrdenCompraDistribucion.IDOC' => 'ASC'])
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
                }

                foreach($real_data as $oc)
                {
                    foreach($oc['OrdenCompraItem'] as $item)
                    {

                        switch ($currency_keyword) {
                            case 'peso':
                                $total += $item['Distribucion']['PRECIOVALOR'];
                                break;
                            
                            default:
                                $total += $this->Currencies->transformValue($item['Distribucion']['PRECIOVALOR'], $currency_keyword, $currency->sbif_api_keyword, $oc['Factura'][0]->FECHAEMISION->year.'-'.$oc['Factura'][0]->FECHAEMISION->month.'-'.$oc['Factura'][0]->FECHAEMISION->day);
                                break;
                        }
                    }
                }

                //debug($real_data);
            }
        }

        return $total;
    }

    function commitedSubcontracts($item_id = null, $currency = array(), $currency_keyword = null)
    {
        $total = 0;

        if($item_id != null)
        {
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
                    foreach($ids_sbcts as $sub)
                    {
                        $subcontrato_data = $this->IcSubcontratoConsolidado
                                    ->find('all')
                                    ->where(['IcSubcontratoConsolidado.IDDOC' => $sub->IDSUBCONT])
                                    ->contain(['IcSubcontrato' => ['IcSubcontratoAprobacion']])
                                    ->first();

                        if(count($subcontrato_data) > 0)
                        {
                            $approved = true;

                            foreach($subcontrato_data->ic_subcontrato->ic_subcontrato_aprobacion as $aprob)
                            {
                                if($aprob->APROBADA != 1)
                                {
                                    $approved = false;
                                }
                            }

                            if($approved == true)
                            {
                                $subcontrato_items = $this->IcSubcontratoItem
                                            ->find('all')
                                            ->where(['IcSubcontratoItem.IDDOC' => $subcontrato_data->IDDOC])
                                            ->select(['IDLINEA', 'MONTOUNITARIO', 'CANTIDAD', 'MONTOAVANCE'])
                                            ->toArray();

                                if(count($subcontrato_items) > 0)
                                {
                                    foreach($subcontrato_items as $item)
                                    {
                                        $item_distr = $this->IcSubcontratoDistribucion
                                                        ->find('all')
                                                        ->where([
                                                            'IcSubcontratoDistribucion.IDSUBCONTLINEA' => $item->IDLINEA,
                                                            'IcSubcontratoDistribucion.IDCENTCOSTO' => $iContruyeCodigoId
                                                        ])
                                                        ->select(['VALOR'])
                                                        ->toArray();

                                        if(count($item_distr) > 0)
                                        {
                                            $porc = 0;

                                            foreach($item_distr as $dist)
                                            {
                                                $porc = ($dist->VALOR / 100) * $item->CANTIDAD;

                                                $this->loadModel('Currencies');

                                                switch ($currency_keyword) {
                                                    case 'peso':
                                                        $total += $item->MONTOUNITARIO * $porc;
                                                        break;
                                                    
                                                    default:
                                                        $total += $this->Currencies->transformValue($item->MONTOUNITARIO * $porc, $currency_keyword, $currency->sbif_api_keyword, $subcontrato_data->ic_subcontrato->FECHACREACION->year.'-'.$subcontrato_data->ic_subcontrato->FECHACREACION->month.'-'.$subcontrato_data->ic_subcontrato->FECHACREACION->day);
                                                        break;
                                                }
                                            } 
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $total;
    }

    function spentSubcontracts($item_id = null, $currency = array(), $currency_keyword = null)
    {
        $total = 0;
        $total_uf = 0;

        if($item_id != null)
        {
            // obtiene id de la obra de iconstruye dandole el id del sistema
            $iContruyeOrgcId = $this->getCustomColumnFromIconstruye($item_id, 'IDORGC');
            // obtiene el codigo de la partida de iconstruye dandole el id del sistema
            $iContruyeCodigoId = $this->getCustomColumnFromIconstruye($item_id, 'CODIGO');

            $estado_pagos = $this->IcEstadoPagoDistribucion
                            ->find('all')
                            ->contain([
                                'IcEstadoPago' =>[
                                    'IcEstadoPagoAprobacion'
                                ]
                            ])
                            ->where([
                                'IcEstadoPagoDistribucion.IDORGC' => $iContruyeOrgcId,
                                'IcEstadoPagoDistribucion.IDCENTCOSTO' => $iContruyeCodigoId
                            ])
                            ->order(['IcEstadoPagoDistribucion.IDSUBCONTRATO' => 'DESC'])
                            ->toArray();

            if(count($estado_pagos) > 0)
            {
                foreach($estado_pagos as $ep)
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
                            switch ($currency_keyword) {
                                case 'peso':
                                    $total += $ep->PRECIOVALOR;
                                    break;
                                
                                default:
                                    $total += $this->Currencies->transformValue($ep->PRECIOVALOR, $currency_keyword, $currency->sbif_api_keyword, $ep->ic_estado_pago->FECHACREACION->year.'-'.$ep->ic_estado_pago->FECHACREACION->month.'-'.$ep->ic_estado_pago->FECHACREACION->day);
                                    break;
                            }
                        }
                    }
                }

            }
        }

        return $total; 
    }

    function invoicedSubcontracts($item_id = null, $currency = array(), $currency_keyword = null)
    {
        $total = 0;

        if($item_id != null)
        {
            // obtiene id de la obra de iconstruye dandole el id del sistema
            $iContruyeOrgcId = $this->getCustomColumnFromIconstruye($item_id, 'IDORGC');
            // obtiene el codigo de la partida de iconstruye dandole el id del sistema
            $iContruyeCodigoId = $this->getCustomColumnFromIconstruye($item_id, 'CODIGO');

            $estado_pagos = $this->IcEstadoPagoDistribucion
                            ->find('all')
                            ->contain([
                                'IcEstadoPago' =>[
                                    'IcFactura'
                                ],
                            ])
                            ->where([
                                'IcEstadoPagoDistribucion.IDORGC' => $iContruyeOrgcId,
                                'IcEstadoPagoDistribucion.IDCENTCOSTO' => $iContruyeCodigoId
                            ])
                            ->order(['IcEstadoPagoDistribucion.IDESTADOPAGO' => 'ASC'])
                            ->toArray();

            if(count($estado_pagos) > 0)
            {           
                foreach($estado_pagos as $ep)
                {
                    switch ($currency_keyword) {
                        case 'peso':
                            $total += $ep->PRECIOVALOR;
                            break;
                        
                        default:
                            $total += $this->Currencies->transformValue($ep->PRECIOVALOR, $currency_keyword, $currency->sbif_api_keyword, $ep->ic_estado_pago->ic_factura->FECHAEMISION->year.'-'.$ep->ic_estado_pago->ic_factura->FECHAEMISION->month.'-'.$ep->ic_estado_pago->ic_factura->FECHAEMISION->day);
                            break;
                    }
                }
            }
        }

        return $total;
    }

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
}











