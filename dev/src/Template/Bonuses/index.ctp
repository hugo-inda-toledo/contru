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
        <h3 class="panel-title">Lista de Bonos</h3>
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
                <?= $this->Html->link(__('Agregar Nuevo Bono'), ['action' => 'add', $budget->id], ['class' => 'btn btn-material-orange-900 pull-right btn-md']) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
            <?php if (count($bonuses) > 0) : ?>
                <table cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th><?= h('Trabajadores') ?></th>
                            <th><?= $this->Paginator->sort('budget_id', 'Descripción') ?></th>
                            <th><?= $this->Paginator->sort('state', 'Estado') ?></th>
                            <th><?= $this->Paginator->sort('created', 'Fecha Bono') ?></th>
                            <th><?= $this->Paginator->sort('created', 'Fecha Creación') ?></th>
                            <th class="actions"><?= __('Acciones') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($bonuses as $bonus) : ?>
                        <tr>
                            <td>
                                <?php
                                foreach($bonuses_unique as $bo)  :
                                    if($bonus->budget_id ==$bo->budget_id && $bonus->created == $bo->created) :
                                        echo "<p>" . $bo->total_workers . "</p>";
                                    endif;
                                endforeach;
                                ?>
                            </td>
                            <td>
                                <?= h($bonus->description);?>
                            </td>
                            <td><?= h($bonus->state) ?></td>
                            <td><?= h($bonus->start_date->format('d-m-Y')) ?></td>
                            <td><?= h($bonus->created->format('d-m-Y H:i:s')) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('Editar'), ['action' => 'edit', $bonus->id], ['class' => 'btn btn-xs btn-material-orange-900 dropdown-toggle']) ?>
                                <?= $this->Html->link(__('Ver'), ['action' => 'view', $bonus->id], ['class' => 'btn btn-xs btn-material-orange-900 dropdown-toggle']) ?>
                                <?php
                                $group = $this->request->session()->read('Auth.User.group_id');
                                //flujo estados
                                if ($group == USR_GRP_ADMIN_OBRA) :
                                    if($bonus->state == $states[0]) :
                                        echo $this->Form->postLink(__('Aprobar'), ['action' => 'change_state', $bonus->id], ['class' => 'btn btn-xs btn-material-orange-900 approve', 'data' => ['state' => $states[1]]]);
                                        echo $this->Form->postLink(__('Rechazar'), ['action' => 'change_state', $bonus->id], ['class' => 'btn btn-xs btn-danger confirm', 'data' => ['state' => $states[4]]]);
                                    endif;
                                endif;
                                if ($group == USR_GRP_VISITADOR) :
                                    if ($bonus->state == $states[1]) :
                                        echo $this->Form->postLink(__('Aprobar'), ['action' => 'change_state', $bonus->id], ['class' => 'btn btn-xs btn-material-orange-900 approve', 'data' => ['state' => $states[2]]]);
                                        echo $this->Form->postLink(__('Rechazar'), ['action' => 'change_state', $bonus->id], ['class' => 'btn btn-xs btn-danger confirm', 'data' => ['state' => $states[4]]]);
                                    endif;
                                endif;
                                if (in_array($group, array(USR_GRP_GE_FINAN, USR_GRP_JEFE_RRHH, USR_GRP_GE_GRAL)) && in_array($bonus->state ,array($states[0], $states[1], $states[2]))) :
                                    echo $this->Form->postLink(__('Aprobar'), ['action' => 'change_state', $bonus->id], ['class' => 'btn btn-xs btn-material-orange-900 approve', 'data' => ['state' => $states[3]]]);
                                    echo $this->Form->postLink(__('Rechazar'), ['action' => 'change_state', $bonus->id], ['class' => 'btn btn-xs btn-danger confirm', 'data' => ['state' => $states[4]]]);
                                endif;
                                if (in_array($group, array(USR_GRP_GE_FINAN, USR_GRP_JEFE_RRHH, USR_GRP_ADMIN_OBRA, USR_GRP_GE_GRAL)) && in_array($bonus->state ,array($states[3]))) :
                                    echo $this->Form->postLink(__('Finalizar'), ['action' => 'change_state', $bonus->id], ['class' => 'btn btn-xs btn-material-orange-900 confirm', 'data' => ['state' => $states[5]]]);
                                endif;
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?= $this->Element('paginador'); ?>
            <?php else : ?>
                <h4>No hay bonos disponibles</h4>
            <?php endif; ?>
            </div>
        </div>
        <?= $this->Html->link(__('Atras'), ['controller' => 'buildings', 'action' => 'dashboard', $sf_building->CodArn], ['class' => 'btn btn-flat btn-link']) ?>
    </div>
</div>