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
        <h3 class="panel-title">Agregar Comentario al Presupuesto</h3>
    </div>
    <div class="panel-body">
        <div class="budgets index large-10 medium-9 columns">
            <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th class="col-md-1"><?= $this->Paginator->sort('id','mensaje') ?></th>
                    <th class="col-md-6"><?= $this->Paginator->sort('comments','Observacion') ?></th>
                    <th class="col-md-3"><?= h('Usuario') ?></th>
                    <th class="col-md-1"><?= $this->Paginator->sort('created','Creado') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 0; ?>
                <?php if(count($observations)): ?>
                    <?php foreach ($observations as $o): ?>
                    <tr>
                        <td><?= $this->Number->format(++$i) ?></td>
                        <td><?= h($o->observation) ?></td>
                        <td><?= h($users[$o->user_id]) ?></td>
                        <td><?= h($o->created) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            </table>
            <!-- Panel content -->
            <?= $this->Form->create($observation); ?>
            <div class="col-md-6 col-sm-6">
                <fieldset>
                    <?php
                        echo $this->Form->input('model',[ 'type' => 'hidden']);
                        echo $this->Form->input('action',[ 'type' => 'hidden']);
                        echo $this->Form->input('model_id',[ 'type' => 'hidden']);
                        echo $this->Form->input('user_id', ['type' => 'hidden' , 'options' => $users]);
                        echo $this->Form->input('observation', ['label' => 'Comentario', 'type' => 'textarea', 'escape' => false]);
                    ?>
                </fieldset>
                <?= $this->Form->button(__('Guardar')) ?>
                 <?= $this->Html->link(
                        'Cancelar',
                        ['controller' => 'budgets', 'action' => 'review', $o->model_id],
                        ['class' => 'btn btn-flat btn-link']
                    ); ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
