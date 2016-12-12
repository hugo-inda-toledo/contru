<?php if(!empty($budget)) { ?>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-7">
                    <h4><strong>Información General Presupuesto Obra</strong></h4>
                </div>
                <div class="col-sm-5 text-right">
                    <?= $this->Html->link( sprintf("%s/%s", $sf_building['DesArn'], $sf_building['CodArn']),
                        ['controller' => 'buildings', 'action' => 'dashboard', $sf_building['CodArn']],
                        ['class' => 'btn btn-sm btn-flat']);?>                    
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-lg-6">
                    <dl class="dl-horizontal">
                        <dt data-toggle="tooltip" data-placement="right" data-original-title="Estado Actual del Presupuesto"><?= __('Estado') ?>:</dt>
                        <dd><?= h(end($budget->budget_approvals)->budget_state->name) ?></dd>
                        <dt class="bg-success"  data-toggle="tooltip" data-placement="right" data-original-title="Fecha de creación del Presupuesto"><?= __('Fecha inicio Obra') ?>:</dt>
                        <dd class="bg-success" ><?= h($budget->created->format('d/m/Y H:m')) ?></dd>
                        <dt data-toggle="tooltip" data-placement="right" data-original-title="Duración del Presupuesto en Meses"><?= __('Duración') ?>:</dt>
                        <dd><?= $this->Number->format($budget->duration) . ' (Meses)' ?></dd>

                        <?php foreach ($budget->building['buildings_users'] as $building_user) :
                                if ($building_user['user_id'] == USR_GRP_ADMIN_OBRA) : ?>
                                    <dt class="bg-success" data-toggle="tooltip" data-placement="right" data-original-title="Usuario Perfil Administrador de Obra"><?= __('Administrador Obra') ?>:</dt>
                                    <dd class="bg-success"><?= $building_user['user']['first_name'] . ' ' . $building_user['user']['lastname_f']; ?></dd>
                            <?php
                                elseif ($building_user['user_id'] == USR_GRP_VISITADOR) : ?>
                                    <dt class="bg-success" data-toggle="tooltip" data-placement="right" data-original-title="Usuario Perfil Visitador de la Obra"><?= __('Visitador Obra') ?>:</dt>
                                    <dd class="bg-success"><?= $building_user['user']['first_name'] . ' ' . $building_user['user']['lastname_f']; ?></dd>
                            <?php
                                endif;
                        endforeach;?>
                    </dl>
                </div>
                <div class="col-xs-12 col-lg-6">
                    <dl class="dl-horizontal">
                        <dt data-toggle="tooltip" data-placement="right" data-original-title="Total Neto del Contrato"><?= __('Total Contrato') ?>:</dt>
                        <dd>
                            <?php 
                                $utilities = ((($budget->total_cost+$budget->general_costs) * $budget->utilities)/100);
                                $net_total = $budget->total_cost + $budget->general_costs + $utilities;
                                echo moneda($net_total).' '.$budget->currencies[0]->initials; 
                            ?>
                        </dd>
                        <dt class="bg-success" data-toggle="tooltip" data-placement="right" data-original-title="Monto Anticipado"><?= __('Anticipo') ?>:</dt>
                        <dd class="bg-success" >
                            <?= moneda($budget->advances).'% ['.moneda(($net_total * $budget->advances)/100).' '.$budget->currencies[0]->initials.']'; ?>   
                        </dd>
                        <dt data-toggle="tooltip" data-placement="right" data-original-title="Monto Retenido"><?= __('Retenciones') ?>:</dt>
                        <dd>
                            <?= moneda($budget->retentions).'% ['.moneda(($net_total * $budget->retentions)/100).' '.$budget->currencies[0]->initials.']'; ?>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
<?php }else{ ?>
    <p>No se encontraron presupuestos relacionados a la obra.</p>
<?php } ?>