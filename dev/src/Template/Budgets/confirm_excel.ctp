<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo de Presupuesto'));
$this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __(''), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/add'];
$theSign = trim(getSignByCurrencyId($budget->currencies_values{0}->currency->id));
$this->set('buttons', $buttons);
?>
<div class="panel panel-material-blue-grey-700">
    <!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Confirmar Importación de Presupuesto</h3>
    </div>
    <div class="panel-body">
        <!-- Panel content -->
        <div class="panel panel-default">
            <div class="panel-body">
                <h4>Obra: <?= h($sf_building->DesArn) ?></h4>
                <div class="col-md-4 col-sm-4">
                    <h5><strong><?= __('Mandante') ?>: </strong><?= $budget->building['client'] ?></h5>
                </div>
                <div class="col-md-4 col-sm-4">
                    <h5><strong><?= __('Dirección') ?>: </strong><?= $budget->building['address'] ?></h5>
                </div>
            </div>
        </div>
        <?php //echo $this->Element('info_budget_detail'); ?>
        <?php /*<div class="panel panel-default">
            <div class="panel-body">
                <h4><strong>Información Específica y Totales Presupuesto Obra</h4>
                <div class="col-md-4 col-sm-4">
                    <h5><strong><?= __('Gastos Generales') ?>: </strong><?= moneda($budget->general_costs) ?></h5>
                    <h5><strong><?= __('Utilidades') ?>: </strong><?= moneda($budget->utilities) ?></h5>
                    <h5><strong><?= __('Moneda Presupuesto') ?>: </strong><?= $budget->currencies_values[0]['currency']['name'] . '(' . $budget->currencies_values[0]['currency']['description'] . ')'; ?></h5>
                    <h5><strong><?= __('Valor Referencial Moneda Presupuesto') ?>: </strong><?= moneda($budget->currencies_values[0]['value']); ?></h5>
                </div>
                <div class="col-md-4 col-sm-4">
                    <h5><strong><?= __('Anticipos (%)') ?>: </strong><?= h($budget->advances) ?></h5>
                    <h5><strong><?= __('Retenciones (%)') ?>: </strong><?= h($budget->retentions) ?></h5>
                </div>
            </div>
        </div>*/ ?>
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <h4 class="subheader"><?= __('Detección de Errores') ?></h4>
                <table cellpadding="0" cellspacing="0">
                <?php
                    $hay_errores = false;
                    foreach ($errores as $error) :
                        preg_match('!\d+!', $error, $matches);
                        $errRow[] = reset($matches);
                        echo '<tr>';
                        if($error == 'No se encontraron errores.'):
                            echo "<td class='success'>";
                        else:
                            $hay_errores = true;
                            echo "<td class='danger'>";
                        endif;
                        echo h($error) . '</td></tr>';
                    endforeach; ?>
                </table>
                <?php if(!empty($info)){ ?>
                    <table cellpadding="0" cellspacing="0">
                    <?php
                        foreach ($info as $inf) :
                            echo "<td class='info'>".h($inf) . '</td></tr>';
                        endforeach; ?>
                    </table>
                <?php } ?>
            </div>
        </div>

        <div class="related row">
            <div class="col-sm-12 col-md-12">
                 <h3>Detalle del Presupuesto </h3>
                <?php if (!empty($excels)) : ?>
                    <table id="excel" cellpadding="0" cellspacing="0" class="table table-striped table-hover table-condensed">
                        <col width="4%">
                        <col width="30%">
                        <col width="2%">
                        <col width="9%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                        <col width="30%">
                        <tr>
                            <th></th>
                            <th><?= __('Descripcion') ?></th>
                            <th class="text-left"><?= __('Unidad') ?></th>
                            <th class="text-right"><?= __('Cantidad') ?></th>
                            <th class="text-right"><?= __('Precio Unitario') ." ($theSign)"; ?></th>
                            <th class="text-right"><?= __('Precio Total')." ($theSign)"; ?> </th>
                            <th class="text-right"><?= __('Valor Objetivo')." ($theSign)"; ?> </th>
                            <th class="text-left"><?= __('Comentario') ?></th>
                        </tr>
                        <?php
                        $total = 0;
                        $total_target_value=0;
                        $tmp_items = array_column($excels, 'A');
                        $last_parent = explode('.',max($tmp_items));
                        if(!empty(reset($last_parent))) {
                            $last_parent = reset($last_parent);
                        } else {
                            $last_parent = null;
                        }
                        $gg=0;
                        $ggtv=0;
                        foreach ($excels as $k => $row):
                            $parentIdArr = explode('.', $row['A']);
                            $lastItemChars = array_pop($parentIdArr);
                            $parentId = (!empty($parentIdArr)) ? implode(".",$parentIdArr) : false;
                            $extraTr="";
                            $description = $row['A']." ".$row['B'];
                            if($parentId == null){
                                $extraTr=' class="info odd" data-type="parent-0" data-item="'.$row['A'].'"';
                                $description = "<strong>$description</strong>";
                                if(substr($row['A'], 0, 1) ==$last_parent){
                                    $extraTr.=" data-gg='true'";
                                }
                            }else{
                                if(isset($excels[$k+1])){
                                    $nextRowId = $excels[$k+1]['A'];
                                    if(substr($nextRowId, 0, strlen($row['A'])) == $row['A']){
                                        $extraTr=' data-type="parent_with_childrens" data-item="'.$row['A'].'"';
                                        $description = "<strong>$description</strong>";
                                    }else{
                                        if(substr($row['A'], 0, 1) !=$last_parent){
                                            $total += (float) $row['F'];
                                            $total_target_value += (float) $row['H'];
                                        }else{
                                            $gg += (float) $row['F'];
                                            $ggtv += (float) $row['H'];
                                        }
                                        $extraTr=' data-type="childrens" data-item="'.$row['A'].'"';
                                    }
                                }else{
                                    $nextRowId = $excels[$k-1]['A'];
                                    if(substr($nextRowId, 0, strlen($row['A'])) == $row['A']){
                                        $extraTr=' data-type="parent_with_childrens" data-item="'.$row['A'].'"';
                                        $description = "<strong>$description</strong>";
                                    }else{
                                        if(substr($row['A'], 0, 1) ==$last_parent){
                                            $gg += (float) $row['F'];
                                            $ggtv += (float) $row['H'];
                                        }
                                        $extraTr=' data-type="childrens" data-item="'.$row['A'].'"';
                                    }
                                }
                            }
                            if(in_array($k, $errRow)):
                                preg_match('/[\d]+[\s](\D*)/', $errores[array_search($k, $errRow)], $msg);
                        ?>
                                <tr class="danger" txt="<?php echo (isset($msg[1])) ? ucfirst($msg[1]): ''; ?>">
                            <?php else:?>
                                <tr<?=$extraTr;?>>
                            <?php endif ?>
                                <td></td>
                                <td><?= $description ?></td>
                                <td><?= h($row['C']) ?></td>
                                <td class="text-right quantity" data-real-value="<?=$row['D'];?>"><?= is_null($row['D']) ? '' : moneda($row['D']) ?></td>
                                <td class="text-right unity_price" data-real-value="<?=$row['E'];?>"><?= is_null($row['E']) ? '' : moneda($row['E']) ?></td>
                                <td class="text-right total_price" data-real-value="<?=$row['F'];?>"><?= is_null($row['F']) ? '' : moneda($row['F']) ?></td>
                                <td class="text-right target_value" data-real-value="<?=$row['H'];?>"><?= is_null($row['H']) ? '' : moneda($row['H']) ?></td>
                                <td class="text-left"><?= is_null($row['G']) ? '' : $row['G'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-right">
                                <strong><span data-toggle="tooltip" data-placement="left" data-original-title="Suma de Partidas">Total Costo Directo</span></strong>
                            </td>
                            <td class="text-right"><strong><?=moneda($total);?></strong></td>
                            <td class="text-right"><strong><?=moneda($total_target_value);?></strong></td>
                            <td></td>
                        </tr>
                        <tr class="gg">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-right">
                                <strong><span data-toggle="tooltip" data-placement="left" data-original-title="Suma de Gastos Generales">Gastos Generales</span></strong>
                            </td>
                            <td class="text-right"><strong><?=moneda($gg);?></strong></td>
                            <td class="text-right"><strong><?=moneda($ggtv);?></strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-right">
                                <strong><span data-toggle="tooltip" data-placement="left" data-original-title="UT=(CD+GG)*<?=$budget->utilities;?>%">Total Utilidades</span></strong>
                            </td>
                            <td class="text-right"><strong><?= moneda((($total+$gg)*$budget->utilities)/100) ?></strong></td>
                            <td class="text-right"><strong><?= moneda((($total+$gg)*$budget->utilities)/100) ?></strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-right">
                                <strong><span data-toggle="tooltip" data-placement="left" data-original-title="Total Neto=CD+GG+UT">Total Neto</span></strong>
                            </td>
                            <td class="text-right"><strong><?php
                                $utilities_contract = ($total + $gg) * ($budget->utilities / 100);
                                $total_contract_currency = $total + $gg + $utilities_contract;
                                echo moneda($total_contract_currency);
                            ?></strong></td>
                            <td class="text-right"><strong><?php
                                $utilities_contract = ($total_target_value + $ggtv) * ($budget->utilities / 100);
                                $total_contract_currency = $total_target_value + $ggtv + $utilities_contract;
                                echo moneda($total_contract_currency);
                            ?></strong></td>
                            <td></td>
                        </tr>
                    </table>

                    <div class="well well-sm">
                        <?php if( !$hay_errores ): ?>
                        <?= $this->Form->create($budget, ['id' => 'excelform','type' => 'file']); ?>
                        <?= h('¿Son correctos los datos para la Obra: ' . $sf_building->DesArn . ' ?'); ?>
                        <?php echo $this->Form->hidden('id'); ?>
                        <?= $this->Form->button(__('Confirmar')) ?>
                        <button id="formcancel" type="button" class="btn btn-flat btn-link">Cancelar</button>
                        <?= $this->Form->end() ?>
                        <?php else: ?>
                        <?= $this->Form->create($budget, ['id' => 'excelform','type' => 'file']); ?>
                        <?= h('Se encontraron errores para la obra ' . $sf_building->DesArn); ?>
                        <?php echo $this->Form->hidden('id'); ?>
                        <button id="formcancel" type="button" class="btn btn-danger">Corregir</button>
                        <?= $this->Form->end() ?>
                        <?php endif; ?>
                    </div>


                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- modal confirmación -->
<div id="confirmDiag" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Confirmación</h4>
            </div>
            <div class="modal-body">
                <p>¿Está seguro que desea Cancelar la Importación?</p>
            </div>
            <div class="modal-footer">
                <?= $this->Html->link(
                    'Aceptar',
                    $this->request->referer(),
                    ['id' => 'confirmcancel',
                    'class' => "btn btn-material-orange-900"]
                ); ?>
                <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<?= $this->Html->script('budgets.confirm_excel'); ?>
<script>
var errores = <?= json_encode($errores); ?>;
$('#excel').on({
    'mouseenter': function() {
        var $row = $(this);
        if (!$row.data("bs.tooltip")) {
            $row.tooltip({
                    container: 'body',
                    html: true,
                    trigger: 'manual',
                    title: function() {
                        return $(this).attr('txt');
                    }
                });
        }
        $row.tooltip('show');
    },
    'mouseleave': function() {
        $(this).tooltip('hide');
    }
},'tbody > tr.danger');
</script>
