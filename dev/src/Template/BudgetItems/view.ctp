<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo de Presupuesto'));
$this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __(''), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/add'];
$this->set('buttons', $buttons);
?>
<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Detalle Partida de Presupuesto</h3>
    </div>
    <div class="panel-body">
        <?php $group_id = $this->request->session()->read('Auth.User.group_id'); ?>
        <h4><strong><?= $budgetItem->item . ' ' . $budgetItem->description ?></strong></h4>
        <div class="row">
            <div class="col-md-8 col-sm-8">
                <div class="row no-margin">
                    <div class="col-xs-12 col-material-blue-grey-100">
                        <div class="row bg-success">
                            <div class="col-xs-12 col-sm-6"><strong><?= __('P/U') ?>:</strong></div>
                            <div class="col-xs-12 col-sm-6 text-right"><?= moneda($budgetItem->unity_price) ?></div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-material-blue-grey-100">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6"><strong><?= __('Cantidad') ?>:</strong></div>
                            <div class="col-xs-12 col-sm-6 text-right"><?= h($budgetItem->quantity) ?></div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-material-blue-grey-100">
                        <div class="row bg-success">
                            <div class="col-xs-12 col-sm-6"><strong><?= __('Total') ?>:</strong></div>
                            <div class="col-xs-12 col-sm-6 text-right"><?= moneda($budgetItem->total_price) ?></div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6"><strong><?= __('Unidad') ?>:</strong></div>
                            <div class="col-xs-12 col-sm-6 text-right"><?= (!empty($budgetItem->unit_id)) ? h($budgetItem->unit->name) : '' ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($group_id == USR_GRP_COORD_PROY || $group_id == USR_GRP_GE_GRAL || $group_id == USR_GRP_GE_FINAN) : ?>
            <div class="col-md-4 col-sm-4">
                <div class="row no-margin">
                    <div class="col-xs-12 col-material-blue-grey-100">
                        <div class="row bg-primary">
                            <div class="col-xs-12 col-sm-6"><strong>Valor Objetivo:</strong></div>
                            <div class="col-xs-12 col-sm-6 text-right"><?= moneda($budgetItem->target_value); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php if (!empty($budgetSchedules->toArray())) : ?>
            <h4>Planificaciones Asociadas a la Partida de Presupuesto</h4>
            <div class="panel panel-default">
                <div class="panel-body">
                    <?php foreach($budgetSchedules as $budgetSchedule) : ?>
                        <h4><?= '<strong>Planificación Semana:</strong> ' . date('d-m-Y',strtotime($budgetSchedule->schedule->start_date)); ?></h4>
                        <div class="row">
                            <div class="col-md-6 col-sm-6">
                                <dl class="dl-horizontal">
                                    <dt><strong><?= __('Nombre:') ?></strong></dt>
                                    <dd><?= h($budgetSchedule->schedule->name) ?></dd>
                                    <dt><strong><?= __('Descripción:') ?></strong></dt>
                                    <dd><?= h($budgetSchedule->schedule->description) ?></dd>
                                    <dt><strong><?= __('Fecha Inicio:') ?></strong></dt>
                                    <dd><?= h($budgetSchedule->schedule->start_date) ?></dd>
                                    <dt><strong><?= __('Fecha Término:') ?></strong></dt>
                                    <dd><?= h($budgetSchedule->schedule->finish_date) ?></dd>
                                    <dt><strong><?= __('Días Trabajados:') ?></strong></dt>
                                    <dd><?= h($budgetSchedule->schedule->total_days) ?></dd>
                                    <dt><strong><?= __('Días festivos:') ?></strong></dt>
                                    <dd><?= h($budgetSchedule->schedule->holidays_week_quantity) ?></dd>
                                </dl>
                                <?php foreach($budgetSchedule->schedule->progress as $progress): ?>
                                    <div class="clearfix">
                                        <?php if (!empty($progress)) : ?>
                                            <strong>Avance Proyectado: </strong><?= h($progress->proyected_progress_percent) ?>%
                                            <div class="progress">
                                                <?php if ($progress->proyected_progress_percent == 100) : ?>
                                                    <div class="progress-bar progress-bar-success" style="width: <?= $progress->proyected_progress_percent ?>%"></div>
                                                <?php else : ?>
                                                    <div class="progress-bar progress-bar-material-orange-<?= substr($progress->proyected_progress_percent, 0, 1) ?>00" style="width: <?= $progress->proyected_progress_percent ?>%"></div>
                                                <?php endif; ?>
                                            </div>
                                        <?php else : ?>
                                            <strong>Avance: 0%</strong>
                                        <?php endif; ?>
                                    </div>
                                    <div class="clearfix">
                                        <?php if (!empty($progress)) : ?>
                                            <strong>Avance Real: </strong><?= h($progress->overall_progress_percent) ?>%
                                            <div class="progress">
                                                <?php if ($progress->overall_progress_percent == 100) : ?>
                                                    <div class="progress-bar progress-bar-success" style="width: <?= $progress->overall_progress_percent ?>%"></div>
                                                <?php else : ?>
                                                    <div class="progress-bar progress-bar-material-orange-<?= substr($progress->overall_progress_percent, 0, 1) ?>00" style="width: <?= $progress->overall_progress_percent ?>%"></div>
                                                <?php endif; ?>
                                            </div>
                                        <?php else : ?>
                                            <strong>Avance: 0%</strong>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <h4><strong>Trabajo Realizado</strong></h4>
                                        <?php
                                        foreach ($budgetSchedule->schedule->completed_tasks as $completed_task) :
                                            $worker_hour = 0;
                                            foreach ($trabajadores as $trabajador) :
                                                if ($trabajador->id == $completed_task->worker_id) :
                                                    echo '<h5><strong>Trabajador: </strong>' . $workers[$trabajador->softland_id] . '</h5>';
                                                    $worker_hour = $task_hours[$trabajador->id][$completed_task->schedule_id];
                                                endif;
                                            endforeach; ?>
                                            <dl class="dl-horizontal">
                                                <dt><?= __('Horas Trabajadas: ') ?></dt>
                                                <dd><?= h($worker_hour) ?></dd>
                                                <dt><?= __('Participación partida: ') ?></dt>
                                                <dd><?= h($completed_task->budget_item_percentage . '%') ?></dd>
                                            </dl>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                    <?php endforeach ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($has_deals) : ?>
            <h4>Tratos Asociados a la Partida de Presupuesto</h4>
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <?php foreach ($deals as $deal) : ?>
                            <div class="col-md-6 col-sm-6">
                                <h4><strong>Trato día: </strong><?= date('d-m-Y', strtotime($deal->start_date))?></h4>
                                <dl class="dl-horizontal">
                                    <dt><?= __('Creado: ') ?></dt>
                                    <dd><?= h(date('d-m-Y', strtotime($deal->created))) ?></dd>
                                    <dt><?= __('Descripción: ') ?></dt>
                                    <dd><?= h( $deal->description) ?></dd>
                                    <?php
                                    foreach ($budgetItem->deal_details as $deal_detail) :
                                        if ($deal->id === $deal_detail->deal_id) : ?>
                                            <dt><?= __('Trabajador: ') ?></dt>
                                            <dd><?= $workers[$deal->worker->softland_id] ?></dd>
                                            <dt><?= __('Monto Total Trato: ') ?></dt>
                                            <dd><?= moneda($deal->amount) ?></dd>
                                            <dt><?= __('Porcentaje partida: %') ?></dt>
                                            <dd><?= h($deal_detail->percentage) ?></dd>
                                        <?php endif;
                                     endforeach; ?>
                                </dl>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($has_bonuses) : ?>
            <h4>Bonos Asociados a la Partida de Presupuesto</h4>
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <?php foreach ($bonuses as $bonus): ?>
                            <div class="col-md-6 col-sm-6">
                                <h4><strong>Bono N°: </strong><?= $bonus->id; ?></h4>
                                <dl class="dl-horizontal">
                                    <dt><?= __('Creado: ') ?></dt>
                                    <dd><?= h(date('d-m-Y',strtotime($bonus->created))) ?></dd>
                                    <dt><?= __('Descripción: ') ?></dt>
                                    <dd><?= h( $bonus->description) ?></dd>
                                    <?php
                                    foreach ($budgetItem->bonus_details as $bonus_detail) :
                                        if ($bonus->id === $bonus_detail->bonus_id) : ?>
                                            <dt><?= __('Trabajador: ') ?></dt>
                                            <dd><?= $workers[$bonus->worker->softland_id] ?></dd>
                                            <dt><?= __('Monto Total Trato: ') ?></dt>
                                            <dd><?= moneda($bonus->amount) ?></dd>
                                            <dt><?= __('Porcentaje partida: %') ?></dt>
                                            <dd><?= h($bonus_detail->percentage) ?></dd>
                                        <?php endif;
                                    endforeach; ?>
                                </dl>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
         <?php endif; ?>
    </div>
</div>
