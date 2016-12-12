<?php if (!empty($bi['children'])) : ?>
    <?php if(is_null($bi['parent_id'])): ?>
        <div class="panel panel-<?= $panel_type ?>">
            <div class="panel-heading" role="tab" id="<?= $bi['id'].'_2' ?>">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="<?= $bi['parent_id'] ?>" href="#<?= $bi['id'] ?>" aria-expanded="false" aria-controls="<?= $bi['id'] ?>">
                        <?php $pSum = (isset($parent_sum[$bi['item']]['budget_total'])) ? $parent_sum[$bi['item']]['budget_total'] : 0; ?>
                        <?php //echo ($panel_type == "default") ? '<s>' . $bi['item'] . ' ' . $bi['description'] . ' (' . moneda($pSum) . ') </s> Deshabilitado' : $bi['item'] . ' ' . $bi['description'] . ' (' . moneda($pSum) . ')'; ?>

                        <?php
                          if($panel_type == "default")
                          {
                              echo $this->Html->tag('span', '<s>'.$bi['item'].' '.$bi['description'].'</s> Deshabilitado', ['class' => 'text-left']);
                          }
                          else
                          {
                              echo $this->Html->tag('span', $bi['item'] . ' ' . $bi['description'], ['class' => 'text-left']);
                          }
                        ?>  
                    </a>
                </h4>
            </div>
            <div id="<?= $bi['id'] ?>" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="<?= $bi['id'].'_2' ?>">
                <div class="panel-body">
                    <div class="panel-group <?php echo ($panel_type == "default") ? "text-muted" : ""; ?>" id="<?= $bi['id'] ?>" role="tablist" aria-multiselectable="true">
                        <table class="table table-hover edp">
                            <thead>
                                <tr>
                                    <th>Ítem</th>
                                    <th>Descripción</th>
                                    <th class="text-right">Total<?php if(strtolower($unidad_moneda) != 'peso'): ?><br><a class="btn btn-link btn-xs uber-small" data-toggle="currency-total" href="javascript:void(0)"><?= $unidad_moneda; ?></a><?php endif; ?></th>
                                    <th class="text-right">A. de obras [%]</th>
                                    <th class="text-right">A. a la Fecha [%]</th>
                                    <th class="text-right">
                                      <?php if(strtolower($unidad_moneda) != 'peso'): ?>
                                        Monto [<?= $unidad_moneda; ?>]
                                        <br>
                                        A. a la Fecha
                                      <?php else:?>
                                        Monto
                                        <br>
                                        A. a la Fecha
                                      <?php endif; ?>
                                    </th>
                                    <th class="text-right">A. E.P Anterior [%]</th>
                                    <th class="text-right">
                                      <?php if(strtolower($unidad_moneda) != 'peso'): ?>
                                        Monto [<?= $unidad_moneda; ?>]
                                        <br>
                                        A. EP. Anterior
                                      <?php else:?>
                                        Monto
                                        <br>
                                        A. EP. Anterior
                                      <?php endif; ?>
                                    </th>
                                    <th class="text-right">A. presente EP. [%]</th>
                                    <th class="text-right">
                                      <?php if(strtolower($unidad_moneda) != 'peso'): ?>
                                        Monto [<?= $unidad_moneda; ?>]
                                        <br>
                                        A. EP. Presente
                                      <?php else:?>
                                        Monto
                                        <br>
                                        A. EP. Presente
                                      <?php endif; ?>
                                    </th>
                                    
                                </tr>
                            </thead>
                            <?php
                                foreach ($bi['children'] as $children) :
                                    $panelType = ($children['disabled'] == 0) ? 'material-blue-grey-400': 'default';
                                    echo $this->element('budget_items_edp_add',['bi' => $children, 'panel_type' => $panelType, 'unidad_moneda' => $unidad_moneda ]);
                             endforeach; ?>
                        </table>
                    </div>
              </div>
            </div>
        </div>
    <?php else : ?>
        <tr class="father active">
           <td><strong><?= $bi['item']; ?></strong></td>
           <td><strong><?= $bi['description']; ?></strong></td>
           <td></td>
           <td></td>
           <td></td>
           <td></td>
           <td></td>
           <td></td>
           <td></td>
           <td></td>        
        </tr>
        <?php
        foreach ($bi['children'] as $children) :
            echo $this->element('budget_items_edp_add',['bi' => $children, 'budget' => $budget, 'unidad_moneda' => $unidad_moneda]);
        endforeach;
    endif;
else :
    if ($bi['disabled']) : ?>
        <tr class="item disable">
            <td class="tachado"><?= $bi['item']; ?>  </td>
            <td class="tachado"><?= $bi['description']; ?></td>
            <td class="text-right" nowrap><span class="total_price" data-value="<?=$bi['total_price'] ?>"><?= $bi['total_price']; ?></span></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>             
        </tr>
<?php elseif (!empty($paymentStatement_budgetItems_completed[$bi['id']]) && empty($paymentStatement_budgetItems_ordered[$bi['id']])) : ?>
      <tr class="success">
          <?= $this->Form->hidden('budget_item.' . $bi['id'] . '.id', ['value' => $bi['id']]); ?>
          <td><?= $bi['item']; ?></td>
          <td><?= $bi['description']; ?></td>
          <td class="text-right" nowrap>
              <span class="total_price" data-original="<?= $bi['total_price']; ?>" data-value="<?=$bi['total_price'] ?>"><?= $bi['total_price']; ?></span>
          </td>
          <td></td>
          <td class="text-right">
            <?= $paymentStatement_budgetItems_completed[$bi['id']]['progress'] ?>%

            <?= $this->Form->hidden('budget_item.' . $bi['id'] . '.progress', ['value' => $paymentStatement_budgetItems_completed[$bi['id']]['progress']]); ?>
          </td>
          <td class="text-right">
            <?= $paymentStatement_budgetItems_completed[$bi['id']]['progress_value'] ?>

            <?= $this->Form->hidden('budget_item.' . $bi['id'] . '.progress_value', ['value' => $paymentStatement_budgetItems_completed[$bi['id']]['progress_value']]); ?>
          </td>
          <td class="text-right">
            <?= $paymentStatement_budgetItems_completed[$bi['id']]['progress'] ?>%

            <?= $this->Form->hidden('budget_item.' . $bi['id'] . '.previous_progress', ['value' => $paymentStatement_budgetItems_completed[$bi['id']]['progress']]); ?>
          </td>
          <td class="text-right">
            <?= $paymentStatement_budgetItems_completed[$bi['id']]['progress_value'] ?>
            
            <?= $this->Form->hidden('budget_item.' . $bi['id'] . '.previous_progress_value', ['value' => $paymentStatement_budgetItems_completed[$bi['id']]['progress_value']]); ?>    
          </td>
          <td class="text-right">
            <?= moneda(0);?>%

            <?= $this->Form->hidden('budget_item.' . $bi['id'] . '.overall_progress', ['value' => 0]); ?> 
          </td>  
          <td class="text-right">
            <?= moneda(0);?>

            <?= $this->Form->hidden('budget_item.' . $bi['id'] . '.overall_progress_value', ['value' => 0]); ?>    
          </td> 
      </tr>
  <?php else :
        if (!empty($last_paymentStatement_budgetItems_ordered[$bi['id']])) :
            

            if ($last_paymentStatement_budgetItems_ordered[$bi['id']]['progress'] == 100) : ?>
                <tr class="success">
                    <td><?= $bi['item']; ?></td>
                    <td><?= $bi['description']; ?></td>
                    <td class="text-right" nowrap>
                        <span class="total_price" data-original="<?= $bi['total_price']; ?>" data-value="<?=$bi['total_price'] ?>"><?= $bi['total_price']; ?></span>
                    </td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                    <td class="text-right"><?= moneda($last_paymentStatement_budgetItems_ordered[$bi['id']]['progress']); ?>%</td>
                    <td class="text-right"><?= moneda($last_paymentStatement_budgetItems_ordered[$bi['id']]['progress_value']); ?></td>   
                    <td class="text-right"></td>
                    <td class="text-right"></td>                 
                </tr>
            <?php else : ?>
                <tr>
                    <?= $this->element('budget_items_edp_item_detail_add_from_previous', [
                        'bi' => $bi,
                        'mode' => 'add',
                        'unidad_moneda' => $unidad_moneda,
                        'payment_statement_item' => $last_paymentStatement_budgetItems_ordered
                    ]); ?>
                </tr>
            <?php endif; ?>
        <?php else :
            echo $this->element('budget_items_edp_item_detail_add_from_zero', ['bi' => $bi, 'panel_type' => isset($panel_type) ? $panel_type : 'default', 'unidad_moneda' => $unidad_moneda] );
        endif;
    endif;
endif; ?>
