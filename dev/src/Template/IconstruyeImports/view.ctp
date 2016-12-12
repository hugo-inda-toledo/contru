<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo de Importaciones Iconstruye'));
$this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __(''), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/add'];
$this->set('buttons', $buttons);
?>
<div class="panel panel-material-blue-grey-700">
    <div class="panel-heading">
        <h3 class="panel-title">Importación Iconstruye</h3>
    </div>
    <div class="panel-body">
    <!-- Panel content -->
        <h3>Resumen de datos importación IConstruye</h3>
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <?php $total_records = ($iconstruyeImport->type == 'guide_exits') ? count($iconstruyeImport->subcontracts) : count($iconstruyeImport->subcontracts) ?>
                <h4 class="subheader"><?= __('Total de ' . $total_records . ' registros.');?></h4>
            </div>
        </div>
        <div class="related row">
            <div class="col-sm-12 col-md-12">
                 <h3>Detalle</h3>
                     <div class="table-scroll">
                        <table id="excel" class="table table-striped table-hover table-condensed">
                            <?php if ($iconstruyeImport->type == 'guide_exits') : ?>
                                <?php if (!empty($guideExit)) : ?>
                                <tr>
                                    <th><?= __('Id') ?></th>
                                    <th><?= __('Obra') ?></th>
                                    <th><?= __('Partida') ?></th>
                                    <th><?= __('Documento') ?></th>
                                    <th><?= __('Fecha de Sistema') ?></th>
                                    <th><?= __('Codigo') ?></th>
                                    <th><?= __('Descripción') ?></th>
                                    <th><?= __('Unidad') ?></th>
                                    <th><?= __('Cantidad') ?></th>
                                    <th><?= __('PPP') ?></th>
                                    <th><?= __('Total') ?></th>
                                </tr>
                                <?php foreach ($guideExit as $row): ?>
                                <tr>
                                    <td><?= h($row->id) ?></td>
                                    <td><?= h($row->budget_item->budget->building->softland_id) ?></td>
                                    <td><?= h($row->budget_item->item) ?></td>
                                    <td><?= h($row->voucher) ?></td>
                                    <td><?= h($row->date_system) ?></td>
                                    <td><?= h($row->product_code) ?></td>
                                    <td><?= h($row->product_name) ?></td>
                                    <td><?= h($row->unit_type) ?></td>
                                    <td><?= h($row->amount) ?></td>
                                    <td><?= moneda($row->unit_price) ?></td>
                                    <td><?= moneda($row->product_total) ?></td>
                                </tr>
                                <?php endforeach;
                                endif;
                            elseif ($iconstruyeImport->type == 'subcontracts') :
                                if (!empty($subcontracts)) : ?>
                                    <tr>
                                        <th><?= __('Obra') ?></th>
                                        <th><?= __('Partida') ?></th>
                                        <th><?= __('N° Subcontrato') ?></th>
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
                                    <?php foreach ($subcontracts as $subcontract) : ?>
                                    <tr>
                                        <td><?= $subcontract->budget_item->budget->building->softland_id ?></td>
                                        <td><?= $subcontract->budget_item->item . ' ' . $subcontract->budget_item->description ?></td>
                                        <td><?= h($subcontract->subcontract_work_number) ?></td>
                                        <td nowrap><?= h($subcontract->rut) ?></td>
                                        <td><?= h($subcontract->name) ?></td>
                                        <td><?= h($subcontract->description) ?></td>
                                        <td><?= h($subcontract->currency) ?></td>
                                        <td><?= h($subcontract->currency_rate) ?></td>
                                        <td><?= h($subcontract->unit_type) ?></td>
                                        <td><?= h($subcontract->amount) ?></td>
                                        <td nowrap><?= moneda($subcontract->price) ?></td>
                                        <td nowrap><?= moneda($subcontract->total) ?></td>
                                        <td><?= h($subcontract->partial_description) ?></td>
                                        <td nowrap><?= h($subcontract->partial_amount) ?></td>
                                        <td nowrap><?= moneda($subcontract->partial_total) ?></td>
                                        <td nowrap><?= moneda($subcontract->balance_due) ?></td>
                                        <td nowrap><?= moneda($subcontract->payment_statement_total) ?></td>
                                        <td nowrap><?= h($subcontract->date->format('d-m-Y')) ?></td>
                                    </tr>
                                    <?php endforeach;
                                endif;
                            endif; ?>
                        </table>
                    </div>
                    <?= $this->Element('paginador'); ?>
                    <?= $this->Html->link('Volver atras', $this->request->referer(), ['id' => 'confirmcancel', 'class' => "btn btn-material-orange-900"]); ?>
            </div>
        </div>
    </div>
</div>
