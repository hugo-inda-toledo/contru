<?php if(!empty($bi['children'])): ?>
  <?php if(is_null($bi['parent_id'])): ?>           
      <tr class="grand-father">
        <td class="text-left"><?= $bi['item']; ?></td>
        <td><?= $bi['description']; ?></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      <?php foreach ($bi['children'] as $children): ?>
             <?php echo $this->element('budget_items_edp_pdf',['bi' => $children, 'paymentStatement' => $paymentStatement]) ?>                          
      <?php endforeach ?>                    
  <?php else: ?>

      <tr class="father">
        <td class="text-left"><?= $bi['item']; ?></td>
        <td><?= $bi['description']; ?></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>        
      </tr>

      <?php foreach ($bi['children'] as $children): ?>
             <?php echo $this->element('budget_items_edp_pdf',['bi' => $children, 'paymentStatement' => $paymentStatement]) ?>                          
      <?php endforeach ?>              

  <?php endif ?>
<?php else: ?>  
<?php //Not childrens ?>
 <?php // Si esta deshabilitado ?>
  <?php if ($bi['disabled']): ?>

      <tr class="item disabled">
        <td class="text-left"><?= $bi['item']; ?></td>
        <td class="tachado"><?= $bi['description']; ?></td>
        <td class="text-center"><?= $bi['unit']['name']; ?></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>        
      </tr>
      
  <?php else: ?>
        <?php // Si no esta deshabilitado  ?>  
        <?php                
            // Tiene progress? 
            $item_on = false;
            if(isset($bi['progress']) && ! empty($bi['progress'])) {                     

                    // Si progress pertenece a EDP 
                    if($bi['progress'][0]['payment_statement_id'] == $paymentStatement->id){
                      $item_on = true;
                      $completado = $bi['progress'][0]['overall_progress_percent'];   
                      // Edp Anterior?
                      if(isset($bi['progress'][1])){                          
                          $edp_anterior = $bi['progress'][1]['overall_progress_percent'];                          
                      }
                      else{
                          $edp_anterior = 0;
                      }
                    }
                    else{         
                        // Primer progress no pertenece a EDP Actual.                                       
                        // Revisar si existe otro Progress es del EDP Anterior.
                        // Si es así, sumo cero
                        // Si no seteo cero pq no existe progress asociados.
                        if(isset($bi['progress'][1])){
                          $completado = $bi['progress'][1]['overall_progress_percent'];
                          $edp_anterior = $bi['progress'][1]['overall_progress_percent'];                          
                        }
                        else{
                          // no existe progress asociado a ningin EDP
                          // no se avanzo nada.
                          $completado = 0;
                          $edp_anterior = 0;
                        }
                                                      
                    }                           
            }
            else {                                                         
                  // No tiene progress entonces es cero el avance.                         
                  $completado = 0;
                  $edp_anterior = 0;                            
            } 

           $edp_presente = $completado - $edp_anterior;
           $moneda = $paymentStatement['contract_value_uf'];
           $monto_item_total_moneda = round($bi['total_price']/$moneda,2);
           $monto_a_cobrar_edp_moneda = round($monto_item_total_moneda * $edp_presente/100,2);

        ?>    
     <tr class="item<?= $item_on ? ' success' : '' ?>">
        <td class="text-left"><?= $bi['item']; ?></td>
        <td><?= $bi['description']; ?></td>
        <td class="text-center"><?= $bi['unit']['name']; ?></td>
        <td><?= $monto_item_total_moneda; ?></td>
        <td><?= $completado; ?>%</td>
        <td><?= $this->Print->decimal($completado * $monto_item_total_moneda); ?></td>
        <td><?= $edp_anterior; ?>%</td>
        <td><?= $this->Print->decimal($edp_anterior * $monto_item_total_moneda); ?></td>
        <td><?= $edp_presente; ?>%</td>
        <td><?= $this->Print->decimal($monto_a_cobrar_edp_moneda); ?></td>              
    </tr>
  <?php endif ?>
<?php endif ?>

