<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo Avance de Obra'));
$this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __('Volver'), 'class' => 'primary', 'icon' => 'plus', 'link' => '/schedules/index/'+$schedule->budget_id];
$this->set('buttons', $buttons);
$theSign = trim(getSignByCurrencyId($budget->currencies_values{0}->currency->id));
?>

<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Detalle de la Planificación Semanal y Avance de Obra</h3>
    </div>
    <div class="panel-body">
        <?= $this->Element('info_budget_building'); ?>


        <?php
            if($this->Access->verifyAction('schedules', 'progress') == true)
            {
                $editable = false;
                $added = false;
                foreach($schedule->progress as $progress)
                {
                    if($progress->overall_progress_percent != 0)
                    {
                        $editable = true;
                        $added = false;
                    }
                    else
                    {
                        $added = true;
                    }
                }

                if($schedule->progress_approved == false)
                {
                    if($editable == true)
                    {
                        echo $this->Html->link(__('Editar Avance de Obra'), ['controller' => 'schedules', 'action' => 'progress', $schedule->id], ['class' => 'btn btn-sm btn-material-orange-900']);
                    }
                    elseif($added == true)
                    {
                        echo $this->Html->link(__('Ingresar Avance de Obra'), ['controller' => 'schedules', 'action' => 'progress', $schedule->id], ['class' => 'btn btn-sm btn-material-orange-900']);
                    }
                }

            }
        ?>

        <?php //debug($schedule);?>

        <?php $group_id = $this->request->session()->read('Auth.User.group_id');
        if (!$approvals->isEmpty()) :
            $approvals_gerente = 0;
            $rejects_gerente = 0;
            echo '<div class="well well-sm">';
            foreach ($approvals as $approval) :
                $approvals_gerente = ($approval['group_id'] == USR_GRP_GE_GRAL || $approval['group_id'] == USR_GRP_GE_FINAN) ? 1 : 0;
                echo '<p class="text-success">' . $approval['comment'] . ' | Fecha: ' . $approval['created']->nice() . '</p>';
            endforeach;
            echo '</div>';
            if (!$rejects->isEmpty()) :
                echo '<div class="shadow-z-1">';
                foreach ($rejects as $reject) :
                    $rejects_gerente = ($reject['group_id'] == USR_GRP_GE_GRAL || $reject['group_id'] == USR_GRP_GE_FINAN) ? 1 : 0;
                    echo '<p class="text-danger">' . $reject['comment'] . ' | Fecha: ' . $reject['created']->nice() . '</p>';
                endforeach;
                echo '</div>';
            endif;
            if ($group_id == USR_GRP_GE_GRAL || $group_id == USR_GRP_GE_FINAN) :
                if ($approvals_gerente == 0) :
                    echo ($approvals_gerente > 0) ? '' : $this->Form->postLink(__('Aprobar Avance de Obra'), ['action' => 'approve_progress'], ['data' => ['schedule_id' => $schedule['id']], 'class' => 'btn btn-sm btn-success approve', 'confirm' => __('¿Está seguro que desea aprobar la Avance de Obra?')]);

                    // cucho: se esconde el rechazar avance de obra, se edita si no se está de acuerdo

                    // echo ($rejects_gerente > 0) ? '' : $this->Html->link(__('Rechazar Avance de Obra'), '#', ['data-toggle' => 'modal', 'data-target' => '#' . $schedule['id'], 'class' => 'btn btn-sm btn-danger']);
                    // echo $this->Element('reject_modal', ['schedule_id' => $schedule['id'], 'modal_title' => 'Rechazar Avance de Obra', 'controller' => 'schedules', 'action' => 'reject_progress']);
                endif;
            endif;
        elseif (!$rejects->isEmpty()) :
            echo '<div class="well well-sm">';
            foreach ($rejects as $reject) :
                echo '<p class="text-danger">' . $reject['comment'] . ' | Fecha: ' . $reject['created']->nice() . '</p>';
            endforeach;
            echo '</div>';
            if ($group_id == USR_GRP_VISITADOR || $group_id == USR_GRP_GE_GRAL || $group_id == USR_GRP_GE_FINAN) :
                echo $this->Form->postLink(__('Aprobar Avance de Obra'),
                 ['action' => 'approve_progress'],
                 ['data' => ['schedule_id' => $schedule['id']],
                  'class' => 'btn btn-sm btn-success approve', 'confirm' => __('¿Está seguro que desea aprobar el trabajo realizado?')]);
            endif;
        else :
            if ($group_id == USR_GRP_VISITADOR || $group_id == USR_GRP_GE_GRAL || $group_id == USR_GRP_GE_FINAN) :

                if($editable == true)
                {
                    echo $this->Form->postLink(__('Aprobar Avance de Obra'),
                         ['action' => 'approve_progress'],
                         ['data' => ['schedule_id' => $schedule['id']],
                          'class' => 'btn btn-sm btn-success approve', 'confirm' => __('¿Está seguro que desea aprobar la Avance de Obra?')]);

                }

                // cucho: se esconde el rechazar avance de obra, se edita si no se está de acuerdo

                // echo $this->Html->link(__('Rechazar Avance de Obra'), '#', ['data-toggle' => 'modal', 'data-target' => '#' . $schedule['id'], 'class' => 'btn btn-sm btn-danger']);
                // echo $this->Element('reject_modal', ['schedule_id' => $schedule['id'], 'modal_title' => 'Rechazar Avance de Obra', 'controller' => 'schedules', 'action' => 'reject_progress']);
            endif;
        endif;?>
        <h4>Partidas de la Planificación</h4>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th class="text-left">Descripción</th>
                    <th class="text-left">Unidad</th>
                    <th class="text-right">Cantidad</th>
                    <th class="text-right">Precio Unitario</th>
                    <th class="text-right">Precio Total</th>
                    <th class="text-right">Avance Proyectado (%)</th>
                    <th class="text-right">Avance Proyectado (<?= $budget->currencies[0]->initials;?>)</th>
                    <th class="text-right">Avance Real (%)</th>
                    <th class="text-right">Avance Real (UNIDAD)</th>
                    <th class="text-right">Avance Real (<?= $budget->currencies[0]->initials;?>)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $total_avance_uf = 0;
                    $total_real_uf = 0;
                    foreach ($schedule->progress as $key => $prog) :
                        $total_avance_uf += ($prog->proyected_progress_percent / 100) * $prog->budget_item['total_price'];
                        $total_real_uf += ($prog->overall_progress_percent / 100) * $prog->budget_item['total_price'];
                ?>
                    <tr>
                        <td class="text-left"><?= $prog->budget_item->item.' '.$prog->budget_item->description; ?></td>
                        <td class="text-left"><?= (!empty($units[$prog->budget_item['unit_id']])) ? h($units[$prog->budget_item['unit_id']]) : '' ?></td>
                        <td class="text-right"><?= moneda( $prog->budget_item['quantity']) ?></td>
                        <td class="text-right"><?= moneda( $prog->budget_item['unity_price']) ?></td>
                        <td class="text-right"><?= moneda( $prog->budget_item['total_price']) ?></td>
                        <td class="text-right"><?= moneda($prog->proyected_progress_percent); ?>%</td>
                        <td class="text-right"><?= moneda(($prog->proyected_progress_percent / 100) * $prog->budget_item['total_price'])?></td>
                        <td class="text-right"><?= moneda($prog->overall_progress_percent); ?>%</td>
                        <td class="text-right"><?= moneda(($prog->overall_progress_percent * $prog->budget_item['quantity'])/100); ?></td>
                        <td class="text-right"><?= moneda(($prog->overall_progress_percent / 100) * $prog->budget_item['total_price'])?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td class="text-left"><b>Total</b></td>
                    <td class="text-left"></td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                    <td class="text-right"><?=moneda($total_avance_uf); ?></td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                    <td class="text-right"><?=moneda($total_real_uf); ?></td>
                </tr>
            </tbody>
        </table>
        <?= $this->Html->link(__('Volver'), ['controller' => 'schedules', 'action' => 'index', $schedule->budget_id], ['class' => 'btn btn-flat btn-link']) ?>
    </div>
</div>