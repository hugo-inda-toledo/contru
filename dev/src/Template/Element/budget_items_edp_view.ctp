<?php if (!empty($bi['children'])) : ?>
    <?php if(is_null($bi['parent_id'])): ?>
        <div class="panel panel-<?= $panel_type ?>">
            <div class="panel-heading" role="tab" id="<?= $bi['id'].'_2' ?>">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="<?= $bi['parent_id'] ?>" href="#<?= $bi['id'] ?>" aria-expanded="false" aria-controls="<?= $bi['id'] ?>">
                        <?php //$pSum = (isset($parent_sum[$bi['item']]['budget_total'])) ? $parent_sum[$bi['item']]['budget_total'] : 0; ?>
                        <?php $pSum = $bi['total_price']; ?>
                        <?php //echo ($panel_type == "default") ? '<s>' . $bi['item'] . ' ' . $bi['description'] . ' (' . moneda($pSum) . ') </s> Deshabilitado' : $bi['item'] . ' ' . $bi['description'] . ' (' . moneda($pSum) . ')'; ?>
                        <?php
                          if($panel_type == "default")
                          {
                              echo $this->Html->tag('span', '<s>'.$bi['item'].' '.$bi['description'].'</s> Deshabilitado', ['class' => 'text-left']);//.' '.$this->Html->tag('span', moneda($pSum).' '.$unidad_moneda, ['class' => 'pull-right']);
                          }
                          else
                          {
                              echo $this->Html->tag('span', $bi['item'] . ' ' . $bi['description'], ['class' => 'text-left']);//. $this->Html->tag('span', moneda($pSum).' '.$unidad_moneda, ['class' => 'pull-right']);
                          }
                        ?>  
                    </a>
                </h4>
            </div>
            <div id="<?= $bi['id'] ?>" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="<?= $bi['id'].'_2' ?>">
                <div class="panel-body">
                    <div class="panel-group <?php echo ($panel_type == "default") ? "text-muted" : ""; ?>" id="<?= $bi['id'] ?>" role="tablist" aria-multiselectable="true">
                        <table class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th width="74">Ítem</th>
                                    <th width="380">Designación</th>
                                    <th width="74">Unidad</th>
                                    <th width="74">Cantidad</th>
                                    <th width="74">Precio Unitario</th>
                                    <th width="79" class="text-right">Total<br><a class="btn btn-link btn-xs uber-small" data-toggle="currency-total" href="javascript:void(0)"><?= $unidad_moneda; ?></a></th>
                                    <th width="215" class="text-right" colspan="2">
                                        Avance a la Fecha<br>
                                        <span>[%]</span> | <span>[Monto <?= $budget->currencies_values[0]['currency']['name'] ?>]</span>
                                    </th>
                                    <th width="215" class="text-right" colspan="2">
                                        Avance EP anterior<br>
                                        <span>[%]</span> | <span>[Monto <?= $budget->currencies_values[0]['currency']['name'] ?>]</span>
                                    </th>
                                    <th width="215" class="text-right" colspan="2">
                                        Avance presente EP<br>
                                        <span>[%]</span> | <span>[Monto <?= $budget->currencies_values[0]['currency']['name'] ?>]</span>
                                    </th>
                                </tr>
                            </thead>
                            <?php
                                foreach ($bi['children'] as $children) :
                                    $panelType = ($children['disabled'] == 0) ? 'material-blue-grey-400': 'default';
                                    echo $this->element('budget_items_edp_view',['bi' => $children, 'panel_type' => $panelType, 'unidad_moneda' => $unidad_moneda, 'progress_totals' => $progress_totals]);
                             endforeach; ?>
                        </table>
                    </div>
              </div>
            </div>
        </div>
    <?php else : ?>

        <?php

          /*echo 'element<pre>';
          print_r($progress_totals);
          echo '</pre>';*/

          $total_progress = 0;
          $total_last_progress = 0;
          $total_present_progress = 0;

          if($progress_totals != null)
          {
            foreach($progress_totals as $key => $value)
            {
              if($key == $bi['id'])
              {
                if($value != null)
                {
                  foreach($value as $key2 => $value2)
                  {
                    $total_progress += $value2['progress_value'];
                    $total_last_progress += $value2['previous_progress_value'];
                    $total_present_progress += $value2['overall_progress_value'];
                  }
                }
              }
            }
          }

          
        ?>

        <tr class="father active info">
           <td><strong><?= $bi['item']; ?></strong></td>
           <td><strong><?= $bi['description']; ?></strong></td>
           <td></td>
           <td></td>
           <td></td>
           <td class="text-right"><strong><?= moneda($bi['total_price']); ?></strong></td>
           <td></td>
           <td class="text-right"><?= moneda($total_progress);?></td>
           <td></td>
           <td class="text-right"><?= moneda($total_last_progress);?></td>
           <td></td>
           <td class="text-right"><?= moneda($total_present_progress);?></td>
        </tr>
        <?php
        foreach ($bi['children'] as $children) :
            echo $this->element('budget_items_edp_view',['bi' => $children,'budget' => $budget, 'unidad_moneda' => $unidad_moneda, 'progress_totals' => $progress_totals]);
        endforeach;
    endif;
else :
    if ($bi['disabled']) : ?>
      <tr class="item disable">
           <td class="tachado"><?= $bi['item']; ?>  </td>
           <td class="tachado"><?= $bi['description']; ?></td>
           <td class="tachado"><?= $bi->unit->name; ?></td>
           <td class="tachado"><?= moneda($bi['quantity']); ?></td>
           <td class="tachado"><?= moneda($bi['unity_price']); ?></td>
           <td nowrap data><span class="total_price" data-original="<?= moneda($bi['total_price']); ?>" data-value="<?=$bi['total_price'] ?>"><?= moneda($bi['total_price']); ?></span></td>
           <td nowrap class="text-right"><?= (!empty($paymentStatement_budgetItems_ordered[$bi['id']])) ? $paymentStatement_budgetItems_ordered[$bi['id']]['overall_progress'] : 0 ?>%</td>
           <td nowrap class="text-right"><?= (!empty($paymentStatement_budgetItems_ordered[$bi['id']])) ? $paymentStatement_budgetItems_ordered[$bi['id']]['overall_progress_value'] : 0 ?></td>
           <td nowrap class="text-right"><?= (!empty($paymentStatement_budgetItems_ordered[$bi['id']])) ? $paymentStatement_budgetItems_ordered[$bi['id']]['previous_progress'] : 0 ?>%</td>
           <td nowrap class="text-right"><?= (!empty($paymentStatement_budgetItems_ordered[$bi['id']])) ? $paymentStatement_budgetItems_ordered[$bi['id']]['previous_progress_value'] : 0 ?></td>
           <td nowrap class="text-right"><?= (!empty($paymentStatement_budgetItems_ordered[$bi['id']])) ? $paymentStatement_budgetItems_ordered[$bi['id']]['progress'] : 0 ?>%</td>
           <td nowrap class="text-right"><?= (!empty($paymentStatement_budgetItems_ordered[$bi['id']])) ? $paymentStatement_budgetItems_ordered[$bi['id']]['progress_value'] : 0 ?></td>
      </tr>
<?php elseif (!empty($paymentStatement_budgetItems_completed[$bi['id']]) && empty($paymentStatement_budgetItems_ordered[$bi['id']])) : ?>

    <?php foreach($paymentStatement_budgetItems_completed_all as $bips):?>
      <?php if($bips->budget_item_id == $bi['id']):?>
        
        <?php if($bips->progress == 100):?>
          <tr class="warning">
            <td><?= $bi['item']; ?></td>
            <td><?= $bi['description']; ?></td>
            <td><?= $bi->unit->name; ?></td>
            <td class="text-right"><?= moneda($bi['quantity']); ?></td>
            <td class="text-right"><?= moneda($bi['unity_price']); ?></td>
            <td nowrap class="text-right">
                <span class="total_price" data-original="<?= moneda($bi['total_price']); ?>" data-value="<?=$bi['total_price'] ?>"><?= moneda($bi['total_price']); ?></span>
            </td>
            

            <?php if($bips->previous_progress == 0):?>

              <td class="text-right"><?= moneda($bips->progress); ?>%</td>
              <td class="text-right"><?= moneda($bips->progress_value); ?></td>
              <td class="text-right"><?= moneda($bips->progress); ?>%</td>
              <td class="text-right"><?= moneda($bips->progress_value); ?></td>
              <td class="text-right"><?= moneda(0); ?>%</td>
              <td class="text-right"><?= moneda(0);?></td>

            <?php else:?>

              <td class="text-right"><?= moneda($bips->progress); ?>%</td>
              <td class="text-right"><?= moneda($bips->progress_value); ?></td>
              <td class="text-right"><?= moneda($bips->previous_progress); ?>%</td>
              <td class="text-right"><?= moneda($bips->previous_progress_value); ?></td>
              <td class="text-right"><?= moneda($bips->overall_progress); ?>%</td>
              <td class="text-right"><?= moneda($bips->overall_progress_value);?></td>

            <?php endif;?>

          </tr>
        <?php else:?>

          <tr>
            <td><?= $bi['item']; ?></td>
            <td><?= $bi['description']; ?></td>
            <td><?= $bi->unit->name; ?></td>
            <td class="text-right"><?= moneda($bi['quantity']); ?></td>
            <td class="text-right"><?= moneda($bi['unity_price']); ?></td>
            <td nowrap class="text-right">
                <span class="total_price" data-original="<?= moneda($bi['total_price']); ?>" data-value="<?=$bi['total_price'] ?>"><?= moneda($bi['total_price']); ?></span>
            </td>
            <td class="text-right"><?= moneda($bips->progress); ?>%</td>
            <td class="text-right"><?= moneda($bips->progress_value); ?></td>
            <td class="text-right"><?= moneda($bips->previous_progress); ?>%</td>
            <td class="text-right"><?= moneda($bips->previous_progress_value); ?></td>
            <td class="text-right"><?= moneda($bips->overall_progress); ?>%</td>
            <td class="text-right"><?= moneda($bips->overall_progress_value);?></td>
          </tr>

        <?php endif;?>
          
      <?php endif;?>
    <?php endforeach;?>
  <?php else: ?>
    <tr class="<?= (!empty($paymentStatement_budgetItems_ordered[$bi['id']])) ? 'with_data':'';?>">
      <td><?= $bi['item']; ?></td>
      <td><?= $bi['description']; ?></td>
      <td><?= $bi->unit->name; ?></td>
      <td class="text-right"><?= moneda($bi['quantity']); ?></td>
      <td class="text-right"><?= moneda($bi['unity_price']); ?></td>
      <td class="text-right" nowrap data><span class="total_price" data-original="<?= moneda($bi['total_price']); ?>" data-value="<?=$bi['total_price'] ?>"><?= moneda($bi['total_price']); ?></span></td>
      <td class="text-right" nowrap><?= (!empty($paymentStatement_budgetItems_ordered[$bi['id']])) ? moneda($paymentStatement_budgetItems_ordered[$bi['id']]['progress']) : moneda(0) ?>%</td>
      <td class="text-right" nowrap><?= (!empty($paymentStatement_budgetItems_ordered[$bi['id']])) ? moneda($paymentStatement_budgetItems_ordered[$bi['id']]['progress_value']) : moneda(0) ?></td>
      <td class="text-right" nowrap><?= (!empty($paymentStatement_budgetItems_ordered[$bi['id']])) ? moneda($paymentStatement_budgetItems_ordered[$bi['id']]['previous_progress']) : moneda(0) ?>%</td>
      <td class="text-right" nowrap><?= (!empty($paymentStatement_budgetItems_ordered[$bi['id']])) ? moneda($paymentStatement_budgetItems_ordered[$bi['id']]['previous_progress_value']) : moneda(0) ?></td>
      
      <td class="text-right" nowrap><?= (!empty($paymentStatement_budgetItems_ordered[$bi['id']])) ? moneda($paymentStatement_budgetItems_ordered[$bi['id']]['overall_progress']) : moneda(0) ?>%</td>
      <td class="text-right" nowrap><?= (!empty($paymentStatement_budgetItems_ordered[$bi['id']])) ? moneda($paymentStatement_budgetItems_ordered[$bi['id']]['overall_progress_value']) : moneda(0) ?></td>
    </tr>
    <?php endif;
endif; ?>