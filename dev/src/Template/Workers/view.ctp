<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo de Trabajadores'));
$this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __(''), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/add'];
$this->set('buttons', $buttons);
?>
<style>
.dl-horizontal > dt:nth-child(4n), .dl-horizontal > dt:nth-child(4n-1),.dl-horizontal > dd:nth-child(4n), .dl-horizontal > dd:nth-child(4n-1)  {
    background: #dff0d8;
}
.dl-horizontal > dt:nth-child(4n-2), .dl-horizontal > dt:nth-child(4n-3),.dl-horizontal > dt:nth-child(4n-2), .dl-horizontal > dd:nth-child(4n-3)  {
    background: #fff;
}
.dl-horizontal.remuneracion dd{
    text-align: right;
}
</style>
<div class="panel panel-default">
    <div class="panel-body">
        <h4><strong>Información Trabajador: </strong><?= $worker_info['ficha']; ?> </h4>
        <div class="col-md-6 col-sm-6">
            <dl class="dl-horizontal">
                <dt data-toggle="tooltip" data-placement="right" data-original-title="Nombres trabajador"><?= __('Nombres') ?>:</dt>
                <dd><?= h($worker_info['nombres']); ?></dd>
                <dt data-toggle="tooltip" data-placement="right" data-original-title="Apellido paterno y materno"><?= __('Apellidos') ?>:</dt>
                <dd><?= h($worker_info['appaterno'] . ' ' . $worker_info['apmaterno']); ?></dd>
                <dt data-toggle="tooltip" data-placement="right" data-original-title="Rol Unico Nacional"><?= __('Run') ?>:</dt>
                <dd><?= h($worker_info['rut']); ?></dd>
                <dt data-toggle="tooltip" data-placement="right" data-original-title="Correo Electronico"><?= __('E-mail') ?>:</dt>
                <dd><?= h($worker_info['Email']); ?></dd>
                <dt data-toggle="tooltip" data-placement="right" data-original-title="Direccion de domicilio"><?= __('Direccion') ?>:</dt>
                <dd><?= h($worker_info['direccion']); ?></dd>
                <dt data-toggle="tooltip" data-placement="right" data-original-title="Telefono de contacto"><?= __('Telefono') ?>:</dt>
                <dd><?= h($worker_info['telefono1']); ?></dd>
            </dl>
        </div>
        <div class="col-md-6 col-sm-6">
            <dl class="dl-horizontal">
                <dt data-toggle="tooltip" data-placement="right" data-original-title="Fecha de Nacimiento"><?= __('Fecha de Nac.') ?>:</dt>
                <dd><?= h($worker_info['fechaNacimient']->format('d-m-Y')) ?></dd>
                <dt data-toggle="tooltip" data-placement="right" data-original-title="Fecha de ingreso"><?= __('Fecha de ingreso') ?>:</dt>
                <dd><?= h($worker_info['fechaIngreso']->format('d-m-Y')) ?></dd>
                <dt data-toggle="tooltip" data-placement="right" data-original-title="Cargo (codigo / cargo)"><?= __('Cargo') ?>:</dt>
                <dd><?= h($worker_info['Cargo']['cod_cargo'] . ' / ' . $worker_info['Cargo']['nombre_cargo']) ?></dd>
            </dl>
        </div>
    </div>



    <? /* Actual */?>
    <div class="panel-body">
        <h4>
            <strong>Remuneración: </strong>
        </h4>
        <div class="col-md-12 col-sm-12"><?php
            echo $this->Form->create('Worker', ['class' => 'form-inline']);
                /*echo $this->Form->input('months', ['label' => 'Mes', 'empty' => 'Seleccione un mes', 'options' => $months, 'value' => (!empty($this->request->query['months']) ? $this->request->query['months'] : '')]); */
                echo $this->Form->input('month', [
                    'templates' => [
                        'input' => '<input class="form-control text-left ldz_numeric_no_sign" type="{{type}}" name="{{name}}" {{attrs}}>',
                    ],
                    'type'=>'number', 'label' => 'Mes', 'max' => '12', 'min' => '1']);
                echo $this->Form->input('day_cut', [
                    'templates' => [
                        'input' => '<input class="form-control text-left ldz_numeric_no_sign" type="{{type}}" name="{{name}}" {{attrs}}>',
                    ],
                    'type'=>'number', 'label' => 'Día de corte', 'max' => '30', 'min' => '1']);
                echo $this->Form->input('day_cut_prev', [
                    'templates' => [
                        'input' => '<input class="form-control text-left ldz_numeric_no_sign" type="{{type}}" name="{{name}}" {{attrs}}>',
                    ],
                    'type'=>'number', 'label' => 'Día de corte mes anterior', 'max' => '30', 'min' => '1']);
                echo $this->Form->hidden('worker.ficha');
                echo $this->Form->hidden('SfWorkerBuildings.codArn');
                echo $this->Form->button('Buscar', ['class' => 'btn btn-fab btn-fab-mini', 'div' => false]);
            echo $this->Form->end();
            if(isset($search_results) && !empty($search_results)){ ?>
                <div class="col-md-6 col-sm-6">
                    <dl class="dl-horizontal remuneracion">
                        <?php foreach($search_results AS $sr){ ?>
                            <dt data-toggle="tooltip" data-placement="right" data-original-title="<?=$sr['description'];?>"><?=$sr['title'];?>:</dt>
                            <dd><?=$sr['value'];?></dd>
                        <?php } ?>
                    </dl>
                </div>
            <?php }
            if(isset($search_results_prev) && !empty($search_results_prev)){ ?>
                <div class="col-md-6 col-sm-6">
                    <dl class="dl-horizontal remuneracion">
                        <?php foreach($search_results_prev AS $sr){ ?>
                            <dt data-toggle="tooltip" data-placement="right" data-original-title="<?=$sr['description'];?>"><?=$sr['title'];?>:</dt>
                            <dd><?=$sr['value'];?></dd>
                        <?php } ?>
                    </dl>
                </div>
            <?php } ?>
        </div>
    </div>

    <?php
    if(!empty($worker_payments)):
        foreach($worker_payments as $wp) : ?>
            <div class="panel-body">
                <h4><strong>Remuneracion: </strong><?= $wp['P090']['valor']; ?> </h4>
                <div class="col-md-4 col-sm-4">
                    <dl class="dl-horizontal remuneracion">
                    <?php $limit_items = round(count($wp) / 3); ?>
                    <?php $i = 1; ?>
                        <?php foreach($wp as $wp_item): ?>
                            <dt data-toggle="tooltip" data-placement="right" data-original-title="<?= $wp_item['descripcion'] ?>"><?= __($wp_item['descripcion']) ?>:</dt>
                            <dd><?= (strlen($wp_item['valor']) > 2 && !strpos($wp_item['valor'],'-'))? moneda($wp_item['valor']) : h($wp_item['valor']); ?></dd>
                            <?php if($i == $limit_items): ?>
                                </dl>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                <dl class="dl-horizontal remuneracion">
                                <?php $i = 1; ?>
                            <?php endif; ?>
                            <?php $i++; ?>
                        <?php endforeach; ?>
                    <dl>
                </div>
            </div>
    <?php
        endforeach;
    else: ?>
        <div class="panel-body">
            <h4>No se encontraron registros</h4>
        </div>
    <?php endif; ?>
    <?= $this->Form->postLink(__('Volver atras'),
        ['controller' => 'workers', 'action' => 'index'],
        ['data' => array('SfWorkerBuildings' => array('codArn' => $last_search)), 'class' => 'btn btn-default btn-sm']); ?>
</div>