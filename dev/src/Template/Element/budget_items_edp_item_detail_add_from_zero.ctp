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
                              echo $this->Html->tag('span', '<s>'.$bi['item'].' '.$bi['description'].'</s> Deshabilitado', ['class' => 'text-left']).' '.$this->Html->tag('span', moneda($pSum).' '.$unidad_moneda, ['class' => 'pull-right']);
                            }
                            else
                            {
                              echo $this->Html->tag('span', $bi['item'] . ' ' . $bi['description'], ['class' => 'text-left']). $this->Html->tag('span', moneda($pSum).' '.$unidad_moneda, ['class' => 'pull-right']);
                            }
                        ?>  
                    </a>
                </h4>
            </div>
            <div id="<?= $bi['id'] ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="<?= $bi['id'].'_2' ?>">
                <div class="panel-body">
                    <div class="panel-group <?php echo ($panel_type == "default") ? "text-muted" : ""; ?>" id="<?= $bi['id'] ?>" role="tablist" aria-multiselectable="true">
                        <table class="table table-hover edp">
                            <thead>
                                <tr>
                                    <th>Ítem</th>
                                    <th>Descripción</th>
                                    <th class="text-right">Total<br><a class="btn btn-link btn-xs uber-small" data-toggle="currency-total" href="javascript:void(0)"><?= $unidad_moneda; ?></a></th>
                                    <th class="text-right">A. de obras [%]</th>
                                    <th class="text-right">A. a la Fecha [%]</th>
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
                                    <th class="text-right">A. E.D.P anterior [%]</th>
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
                                    <th class="text-right">A. presente E.D.P [%]</th>
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
                        </table>
                    </div>
              </div>
            </div>
        </div>
<?php else : ?>
    <tr>
        <input class="form-control" type="hidden" name="budget_item[<?= $bi['id'] ?>][id]" value="<?= $bi['id'] ?>">
        <td><?= $bi['item']; ?></td>
        <td><?= $bi['description']; ?></td>
        <td class="text-right" nowrap>
            <span class="total_price" data-original="<?= $bi['total_price']; ?>" data-value="<?=$bi['total_price'] ?>"><?= $bi['total_price']; ?></span>
        </td>        
        <td class="percentage_overall_progress text-right">
            <a href="javascript:void(0);" onclick='sugerido("#budget-item-<?= $bi['id'] ?>-progress", <?= $bi['percentage_overall_progress'] ?>);' class="btn btn-xs btn-default" type="button" data-toggle="tooltip" data-placement="left" title="Usar como porcentaje de pago (A. presente E.D.P.)"><span class="ldz_numeric_no_sign"><?= $bi['percentage_overall_progress'] ?></span></a>
        </td>
        
        <td class="progress_present text-right">
            <div class="form-group">
                <input class="form-control text-right" type="number" name="budget_item[<?= $bi['id'] ?>][progress]" data-type="progress" min="0.01" max="100" id="budget-item-<?= $bi['id'] ?>-progress" step="0.01">
            </div>
        </td>
        <td class="progress_value text-right">
            <input class="form-control text-right" type="hidden" name="budget_item[<?= $bi['id'] ?>][progress_value]" readonly="readonly" data-total_price="<?= $bi['total_price'] ?>"
              data-type="progress_value" id="budget-item-<?= $bi['id'] ?>-progress-value">
            <span class="ldz_numeric_no_sign"></span>
       </td>
        <td class="text-right previous_progress">
           <input class="form-control" type="hidden" name="budget_item[<?= $bi['id'] ?>][previous_progress]" data-type="previous_progress" value="0">
           <!--<input class="form-control" type="hidden" name="budget_item[<?= $bi['id'] ?>][previous_progress_value]" value="0">-->
            0%
        </td>
        <td class="text-right previous_progress_value">
           <!--<input class="form-control" type="hidden" name="budget_item[<?= $bi['id'] ?>][previous_progress]" data-type="previous_progress" value="0">-->
           <input class="form-control" type="hidden" name="budget_item[<?= $bi['id'] ?>][previous_progress_value]" value="0">
            <?= moneda(0);?>
        </td>
        <td class="overall_progress text-right">
            <!--<input class="form-control" type="hidden" name="budget_item[<?= $bi['id'] ?>][overall_progress_value]" data-type="overall_progress_value" data-value="0">-->
            <div class="form-group">
              <input class="form-control text-right" type="hidden" name="budget_item[<?= $bi['id'] ?>][overall_progress]" readonly="readonly" data-type="overall_progress"
                data-value="0" step="any" id="budget-item-<?= $bi['id'] ?>-overall-progress" value="0">
                <span></span>
            </div>
        </td>
        <td class="overall_progress_value text-right">
            <input class="form-control" type="hidden" name="budget_item[<?= $bi['id'] ?>][overall_progress_value]" data-type="overall_progress_value" data-value="0">
            <!--<div class="form-group">
              <input class="form-control text-right" type="hidden" name="budget_item[<?= $bi['id'] ?>][overall_progress]" readonly="readonly" data-type="overall_progress"
                data-value="0" step="any" id="budget-item-<?= $bi['id'] ?>-overall-progress" value="0">
                <span></span>
            </div>-->
            <span></span>
        </td>
        
    </tr>
<?php endif; ?>