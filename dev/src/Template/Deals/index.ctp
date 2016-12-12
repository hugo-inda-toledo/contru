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
        <h3 class="panel-title">Lista de Tratos</h3>
    </div>
    <div class="panel-body">
        <?= $this->Element('info_budget_building'); ?>
        <div class="row">
            <div class="col-lg-6">
              <?php if ($this->request->session()->read('Auth.User.group_id') != USR_GRP_ADMIN_OBRA && $this->request->session()->read('Auth.User.group_id') != USR_GRP_ASIS_RRHH &&
                  $this->request->session()->read('Auth.User.group_id') != USR_GRP_OFI_TEC) :
                  echo $this->Element('building_filter'); // coloca un menu
               endif; ?>
            </div>
            <div class="col-lg-6">
                <?= $this->Html->link(__('Agregar Nuevo Trato'), ['action' => 'add', $budget->id], ['class' => 'btn btn-material-orange-900 pull-right btn-md']) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
            <?php if (count($deals) > 0) : ?>
                <table cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th><?= h('Trabajadores') ?></th>
                            <th><?= $this->Paginator->sort('description', 'Descripción') ?></th>
                            <th><?= $this->Paginator->sort('state', 'Estado') ?></th>
                            <th><?= $this->Paginator->sort('start_date', 'Fecha Trato') ?></th>
                            <th><?= $this->Paginator->sort('created', 'Fecha Creación') ?></th>
                            <th class="actions"><?= __('Acciones') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($deals as $deal): ?>
                        <tr>
                            <td>
                                <?php foreach($deals_unique as $du) :
                                        if($deal->budget_id ==$du->budget_id && $deal->start_date == $du->start_date && $deal->created == $du->created) :
                                            echo "<p>" . $du->total_workers . "</p>";
                                        endif;
                                    endforeach;?>
                            <?php //$deal->has('worker') ? $this->Html->link($deal->worker->id, ['controller' => 'Workers', 'action' => 'view', $deal->worker->id]) : '' ?>
                            </td>
                            <td>
                                <?= (strlen($deal->description) > 30) ? substr($deal->description, 0, 24) . '...' : $deal->description; ?>
                            </td>
                            <td><?= h($deal->state) ?></td>
                            <td><?= h($deal->start_date->format('d-m-Y')) ?></td>
                            <td><?= h($deal->created->format('d-m-Y')) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('Editar'), ['action' => 'edit', $deal->id], ['class' => 'btn btn-xs btn-material-orange-900 dropdown-toggle']) ?>
                                <?= $this->Html->link(__('Ver'), ['action' => 'view', $deal->id], ['class' => 'btn btn-xs btn-material-orange-900 dropdown-toggle']) ?>
                                <?php
                                $group = $this->request->session()->read('Auth.User.group_id');
                                //flujo stados
                                if($group == USR_GRP_ADMIN_OBRA) :
                                    if($deal->state == $states[0]) :
                                        echo $this->Form->postLink(__('Aprobar'), ['action' => 'change_state', $deal->id], ['class' => 'btn btn-xs btn-success approve', 'data' => ['state' => $states[1]]]);
                                        echo $this->Form->postLink(__('Rechazar'), ['action' => 'change_state', $deal->id], ['class' => 'btn btn-xs btn-danger confirm_reason', 'data' => ['state' => $states[4]]]);
                                    endif;
                                endif;
                                if($group == USR_GRP_VISITADOR) :
                                    if($deal->state == $states[1]) :
                                        echo $this->Form->postLink(__('Aprobar'), ['action' => 'change_state', $deal->id], ['class' => 'btn btn-xs btn-success approve', 'data' => ['state' => $states[2]]]);
                                        echo $this->Form->postLink(__('Rechazar'), ['action' => 'change_state', $deal->id], ['class' => 'btn btn-xs btn-danger confirm_reason', 'data' => ['state' => $states[4]]]);
                                    endif;
                                endif;
                                if(in_array($group, array(USR_GRP_GE_FINAN, USR_GRP_JEFE_RRHH, USR_GRP_JEFE_RRHH, USR_GRP_GE_GRAL)) && in_array($deal->state ,array($states[0], $states[1], $states[2]))) :
                                    echo $this->Form->postLink(__('Aprobar'), ['action' => 'change_state', $deal->id], ['class' => 'btn btn-xs btn-success approve', 'data' => ['state' => $states[3]]]);
                                    echo $this->Form->postLink(__('Rechazar'), ['action' => 'change_state', $deal->id], ['class' => 'btn btn-xs btn-danger confirm_reason', 'data' => ['state' => $states[4]]]);
                                endif;
                                if(in_array($group, array(USR_GRP_GE_FINAN, USR_GRP_JEFE_RRHH, USR_GRP_ADMIN_OBRA, USR_GRP_GE_GRAL)) && in_array($deal->state ,array($states[3]))) :
                                    echo $this->Form->postLink(__('Finalizar'), ['action' => 'change_state', $deal->id], ['class' => 'btn btn-xs btn-success confirm', 'data' => ['state' => $states[5]]]);
                                endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?= $this->Element('paginador'); ?>
            <?php else : ?>
                <h4>No hay Tratos disponibles</h4>
            <?php endif; ?>
            </div>
        </div>
        <?= $this->Html->link(__('Atras'), ['controller' => 'buildings', 'action' => 'dashboard', $sf_building->CodArn], ['class' => 'btn btn-flat btn-link']) ?>
    </div>
</div>
<?= $this->Html->script('deals.index.js') ?>
