<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo de Trabajadores'));
$this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __(''), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/add'];
$this->set('buttons', $buttons);
?>

<div class="panel panel-material-blue-grey-700">
    <!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Lista de Trabajadores (<?=count($sf_workers)?> encontrados)</h3>
    </div>
    <div class="panel-body">
        <!-- Panel content -->
        <!-- Filtro -->
        <?php /*echo $this->Form->create('Workers', ['class' => 'form-horizontal']); ?>
        <div class="col-lg-6">
            <?php echo $this->Form->input('SfWorkerBuildings.codArn', ['label' => 'Área de Negocio', 'options' => $sf_buildings]); ?>
            <?php echo $this->Form->button('Buscar', ['type' => 'submit']); ?>
        </div>
        <?= $this->Form->end(); */?>
        <?php if( count($sf_workers) == 0 ): ?>
            <div class="well">La obra no tiene trabajadores asignados</div>
        <?php else: ?>

            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <!-- <th><?= $this->Paginator->sort('ficha', __('Ficha')) ?></th> -->
                        <th>Nombre Completo<br/>RUT</th>
                        <th>Cargo<br/>Fecha Ingreso</th>
                        <th>Dirección<br/>Teléfono</th>
                        <!-- <th>Sueldo</th> -->
                        <!-- <th><?= $this->Paginator->sort('telefono1', __('Teléfono 1')) ?></th> -->
                        <th class="actions"></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $c=0;
                foreach ($sf_workers as $key=>$worker):
                    $c++;
                ?>
                    <tr>
                        <!-- <td><?= h($worker['ficha']) ?></td> -->
                        <td><?= h($worker['nombres']) ?><br/><?= h($worker['rut']) ?></td>
                        <td><?= h($worker['Cargo']['nombre_cargo']) ?><br/><?= date('d-m-Y', strtotime($worker['fechaIngreso'])) ?></td>
                        <!-- <td><?= h($worker['rut']) ?></td> -->
                        <td>
                            <?= h($worker['direccion']) ?><br/>
                            <p><i class="glyphicon glyphicon-earphone"></i> <?= h($worker['telefono1']) ?></p>
                        </td>
                        <td class="actions">
                            <div class="btn-group">
                                <?= $this->Form->postLink(__('Ver'),
                                    ['controller' => 'workers', 'action' => 'view', ],
                                    ['data' => array('worker' => array('ficha' => $worker['ficha']), 'SfWorkerBuildings' => array('codArn' => $last_search)), 'class' => 'btn btn-default btn-sm']); ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <!-- <?= $this->Element('paginador'); ?>  -->
    </div>
</div>
<?= $this->Html->script('workers.index'); ?>