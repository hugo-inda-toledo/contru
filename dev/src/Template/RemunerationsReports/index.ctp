<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo de Recursos humanos'));
// $this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __(''), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/add'];
$this->set('buttons', $buttons);
?>
<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Reportes de remuneraciones solicitados</h3>
    </div>
    <div class="panel-body">
        <?= $this->Element('info_budget_building'); ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <h4><strong>Generar Reporte</strong></h4>
                    </div>
                    <div class="col-lg-9">
                        <?php echo $this->Form->create('RemunerationsReports'); ?>
                        <div class="col-sm-12 col-md-12">
                            <div class="col-sm-12 col-md-2"><?php
                                echo $this->Form->input('RemunerationsReports.month', [
                                'templates' => [
                                    'input' => '<input class="form-control text-left ldz_numeric_no_sign" type="{{type}}" name="{{name}}" {{attrs}}>',
                                ],
                                'type'=>'number', 'label' => 'Mes', 'max' => '12', 'min' => '1']);
                            ?></div>
                            <div class="col-sm-12 col-md-2"><?php
                                echo $this->Form->label('RemunerationsReports.year', 'Año');
                                echo $this->Form->year('RemunerationsReports', [
                                    'minYear' => date('Y'), 'maxYear' => date('Y')+1, 'value' => date('Y'), 'empty' => false
                                ]);
                            ?></div>
                            <div class="col-sm-12 col-md-2"><?php
                                echo $this->Form->input('RemunerationsReports.day_cut', [
                                    'templates' => [
                                        'input' => '<input class="form-control text-left ldz_numeric_no_sign" type="{{type}}" name="{{name}}" {{attrs}}>',
                                    ],
                                    'type'=>'number', 'label' => 'Día de corte', 'max' => '30', 'min' => '1']);
                            ?></div>
                            <div class="col-sm-12 col-md-4"><?php
                                echo $this->Form->input('RemunerationsReports.day_cut_prev', [
                                    'templates' => [
                                        'input' => '<input class="form-control text-left ldz_numeric_no_sign" type="{{type}}" name="{{name}}" {{attrs}}>',
                                    ],
                                    'type'=>'number', 'label' => 'Día de corte mes anterior', 'max' => '30', 'min' => '1']);
                            ?></div>
                            <div class="col-sm-12 col-md-2"><?php
                                echo $this->Form->button('Generar', ['class' => 'btn btn-fab btn-fab-mini generate_report', 'div' => false]);
                            ?></div>
                        </div>
                        <?php echo $this->Form->end();
                    ?></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <?php if (count($remunerationsReports) > 0) : ?>
                    <table cellpadding="0" cellspacing="0">
                        <thead>
                            <tr>
                                <th><?= $this->Paginator->sort('id', 'ID Reporte') ?></th>
                                <th><?= $this->Paginator->sort('status', 'Estado') ?></th>
                                <th><?= $this->Paginator->sort('building_id', 'Obra') ?></th>
                                <th><?= $this->Paginator->sort('month', 'Mes') ?></th>
                                <th><?= $this->Paginator->sort('day_cut', 'Día de Corte') ?></th>
                                <th><?= $this->Paginator->sort('day_cut_prev', 'Día de Corte Mes Anterior') ?></th>
                                <th><?= $this->Paginator->sort('progress', 'Progreso') ?></th>
                                <th><?= $this->Paginator->sort('created', 'Fecha solicitud') ?></th>
                                <th class="actions"><?= 'Acciones'; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($remunerationsReports as $remunerationsReport): ?>
                            <tr>
                                <td><?= $this->Number->format($remunerationsReport->id) ?></td>
                                <td><?= $status[$remunerationsReport->status]; ?></td>
                                <td><?= h($sf_building->DesArn) ?></td>
                                <td><?= $this->Number->format($remunerationsReport->month) ?></td>
                                <td><?= $this->Number->format($remunerationsReport->day_cut) ?></td>
                                <td><?= $this->Number->format($remunerationsReport->day_cut_prev) ?></td>
                                <td class="text-center">
                                    <?= $this->Number->format($remunerationsReport->progress) ?>%
                                    <div class="progress">
                                        <div class="progress-bar" style="width: <?=$remunerationsReport->progress;?>%;"></div>
                                    </div>
                                </td>
                                <td><?= h($remunerationsReport->created) ?></td>
                                <td class="actions">
                                    <?php //echo $this->Html->ink('Ver', ['action' => 'view', $remunerationsReport->id], ['class' => 'btn btn-xs btn-material-orange-900 dropdown-toggle']) ?>
                                    <?php if($remunerationsReport->progress>=100){
                                        echo $this->Html->link('Descargar archivo', ['action' => 'download_file', $remunerationsReport->id], ['class' => 'btn btn-xs btn-material-orange-900 dropdown-toggle']);
                                    } ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?= $this->Element('paginador'); ?>
                <?php else : ?>
                    <h4>No hay reportes disponibles</h4>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
