<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo de Importaciones de Iconstruye'));
$this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __(''), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/add'];
$this->set('buttons', $buttons);
?>
<?= $this->Html->script('iconstruyeImports.confirm'); ?>
<div class="panel panel-material-blue-grey-700">
    <div class="panel-heading">
        <h3 class="panel-title">Importación Iconstruye</h3>
    </div>
    <div class="panel-body">
    <!-- Panel content -->
        <h3>Resumen de datos importación IConstruye</h3>
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <?php $number_errors = (!empty($errores[0]['linea'])) ? count($errores) : 0 ?>
                <h4 class="subheader"><?= __('Detalle de Errores, se detectaron un total de ' . $number_errors . ' registros que no se importarán.');?></h4>
                <table class="table table-striped table-hover table-condensed">
                    <col width="30%">
                    <col width="70%">
                    <tr>
                        <th><?= __('Linea(s)') ?></th>
                        <th><?= __('Error') ?></th>
                    </tr>
                <?php
                    $errUnique = array_unique(array_column($errores,'error'));
                    $indice = array_column($errores,'linea');
                    //codigo marciano para calcular rangos de lineas
                    foreach($errUnique as $err) {
                        $temp = array_keys(array_column($errores,'error'),$err);
                        $keysmsg = '';
                        $keys = array();
                        foreach($temp as $t) {
                            $keys[] = $indice[$t];
                        }
                        $previous = null;
                        $result = null;
                        $consecutiveArray = array();
                        foreach($keys as $number) {
                            if ($number == $previous + 1) {
                                $consecutiveArray[] = $number;
                            } else {
                                $result[] = $consecutiveArray;
                                $consecutiveArray = array($number);
                            }
                            $previous = $number;
                        }
                        $result[] = $consecutiveArray;
                        foreach($result as $re) {
                            if(!empty($re)){
                                if(count($re) > 1) {
                                    $keysmsg .= (empty($keysmsg)) ? min($re) . '-' . max($re): ', ' . min($re) . '-' . max($re);
                                } else {
                                    $keysmsg .= (empty($keysmsg)) ? min($re) : ', ' . min($re);
                                }
                            }
                        }
                        // fin codigo marciano
                        if (!empty($errores[0]['linea'])) {
                            if ($errores[0] == 'No se encontraron errores.') :
                                echo "<tr class='success'>";
                            else :
                                echo "<tr class='danger'>";
                            endif;
                        }
                        echo '<td>' . $keysmsg . '.</td> <td>' . $err . ' </td></tr>';
                    }
                    if (!empty($errores[0]['linea'])) {
                        foreach ($errores as $error) :
                            $errRow[] = $error['linea'];
                        endforeach;
                    } else {
                        echo '<tr class="success"><td>0</td><td>' . $errores[0] . ' </td></tr>';
                    }
                    ?>
                </table>
            </div>
        </div>
        <button id="formsubmit" type="button" class="mdConfirm btn btn-material-orange-900">Confirmar</button>
        <button id="formcancel" type="button" class="mdCancel btn btn-flat btn-link">Cancelar</button>
        <div class="related row">
            <div class="col-sm-12 col-md-12">
                <h3>Detalle del Presupuesto</h3>
                <?php if (!empty($excel['registros'])) : ?>
                    <div class="table-scroll">
                        <table id="excel" class="table table-striped table-hover table-condensed">
                            <?php if ($excel['type'] == 'guide_exits') : ?>
                                <tr>
                                    <th><?= __('Línea') ?></th>
                                    <th><?= __('Obra') ?></th>
                                    <th><?= __('Partida') ?></th>
                                    <th><?= __('Documento') ?></th>
                                    <th><?= __('Fecha de Sistema') ?></th>
                                    <th><?= __('Código') ?></th>
                                    <th><?= __('Descripción') ?></th>
                                    <th><?= __('Unidad') ?></th>
                                    <th><?= __('Cantidad') ?></th>
                                    <th><?= __('PPP') ?></th>
                                    <th><?= __('Total') ?></th>
                                </tr>
                                <?php
                                foreach ($excel['registros'] as $k => $row) :
                                    $msg = '';
                                    $keys = (array_keys(array_column($excel['errores'],'linea'), $k));
                                    if (!empty($keys)) :
                                        foreach($keys as $err) :
                                            $msg .= $excel['errores'][$err]['error'] . ' ';
                                        endforeach; ?>
                                        <tr class="danger" txt="<?php echo (!empty($msg)) ? ucfirst($msg): ''; ?>">
                                    <?php else : ?>
                                        <tr>
                                    <?php endif; ?>
                                    <td><?= h($k) ?></td>
                                    <td><?= h($row['building_id']) ?></td>
                                    <td><?= h($row['budget_item']) ?></td>
                                    <td><?= h($row['voucher']) ?></td>
                                    <td><?= h($row['date_system']) ?></td>
                                    <td><?= h($row['product_code']) ?></td>
                                    <td><?= h($row['product_name']) ?></td>
                                    <td><?= h($row['unit_type']) ?></td>
                                    <td><?= h($row['amount']) ?></td>
                                    <td><?= moneda($row['unit_price']) ?></td>
                                    <td><?= moneda($row['product_total']) ?></td>
                                </tr>
                                <?php
                                endforeach;
                            elseif ($excel['type'] == 'subcontracts') : ?>
                                <tr>
                                    <th><?= __('Línea') ?></th>
                                    <th><?= __('Obra') ?></th>
                                    <th><?= __('Partida') ?></th>
                                    <th><?= __('Estado Partida') ?></th>
                                    <th><?= __('N° Subcontrato') ?></th>
                                    <th><?= __('Nombre Obra') ?></th>
                                    <th><?= __('Rut') ?></th>
                                    <th><?= __('Nombre') ?></th>
                                    <th><?= __('Descripción') ?></th>
                                    <th><?= __('Moneda') ?></th>
                                    <th><?= __('Tasa de Cambio') ?></th>
                                    <th><?= __('Unidad') ?></th>
                                    <th><?= __('Cantidad') ?></th>
                                    <th><?= __('Precio') ?></th>
                                    <th><?= __('Total') ?></th>
                                    <th><?= __('Descripción Trabajo') ?></th>
                                    <th><?= __('Cantidad Trabajo') ?></th>
                                    <th><?= __('Total Trabajo') ?></th>
                                    <th><?= __('Saldo') ?></th>
                                    <th><?= __('Monto EEPP') ?></th>
                                    <th><?= __('Fecha') ?></th>
                                </tr>
                                <?php
                                foreach ($excel['registros'] as $k => $row) :
                                    $msg = '';
                                    $keys = (array_keys(array_column($excel['errores'],'linea'), $k));
                                    if (!empty($keys)) :
                                        foreach($keys as $err) :
                                            $msg .= $excel['errores'][$err]['error'] . ' ';
                                        endforeach; ?>
                                        <tr class="danger" txt="<?php echo (!empty($msg)) ? ucfirst($msg): ''; ?>">
                                    <?php else : ?>
                                        <tr>
                                    <?php endif; ?>
                                    <td><?= h($k) ?></td>
                                    <td><?= h($row['building_id']) ?></td>
                                    <td><?= h($row['budget_item']) ?></td>
                                    <td><?= is_null($row['budget_item_id']) ? 'Partida no existe en el presupuesto' : 'Partida ok'?></td>
                                    <td><?= h($row['subcontract_work_number']) ?></td>
                                    <td><?= h($row['building_name']) ?></td>
                                    <td><?= h($row['rut']) ?></td>
                                    <td><?= h($row['name']) ?></td>
                                    <td><?= h($row['description']) ?></td>
                                    <td><?= h($row['currency']) ?></td>
                                    <td><?= h($row['currency_rate']) ?></td>
                                    <td><?= h($row['unit_type']) ?></td>
                                    <td><?= h($row['amount']) ?></td>
                                    <td><?= h($row['price']) ?></td>
                                    <td><?= h($row['total']) ?></td>
                                    <td><?= h($row['partial_description']) ?></td>
                                    <td><?= h($row['partial_amount']) ?></td>
                                    <td><?= h($row['partial_total']) ?></td>
                                    <td><?= h($row['balance_due']) ?></td>
                                    <td><?= h($row['payment_statement_total']) ?></td>
                                    <td nowrap><?= h($row['date']) ?></td>
                                </tr>
                                <?php
                                endforeach;
                            endif; ?>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <button id="formsubmit2" type="button" class="mdConfirm btn btn-material-orange-900">Confirmar</button>
        <button id="formcancel2" type="button" class="mdCancel btn btn-flat btn-link">Cancelar</button>
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
                <p>¿Está seguro que desea realizar la Importación?</p>
            </div>
            <div class="modal-footer">
                <?= $this->Form->create($iconstruyeImport); ?>
                <?php echo $this->Form->input('confirm', ['type' => 'hidden', 'value' => 1]); ?>
                <?= $this->Form->button(__('Confirmar')) ?>
                <button type="button" class="btn btn-flat btn-link" data-dismiss="modal">Cancelar</button>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>


<!-- modal confirmación -->
<div id="cancelDiag" class="modal">
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
                <button type="button" class="btn btn-flat btn-link" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
