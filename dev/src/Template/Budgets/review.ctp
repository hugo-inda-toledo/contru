<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo Presupuesto'));
$this->assign('title_icon', 'groups');
$buttons = array();
$this->set('buttons', $buttons);
$theSign = trim(getSignByCurrencyId($budget->currencies_values{0}->currency->id));
?>
<style>
table.dataTable.table-condensed>thead>tr>th {
    padding-right: 0;
}
</style>
<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Vista Resumida Presupuesto</h3>
    </div>
    <div class="panel-body">
        <!-- Panel content -->
        <?= $this->Element('info_budget_building'); ?>
        <!-- <h3>Detalle Partidas de Presupuesto <button type="button" class="btn btn-right btn-default btn-raised btn-sm mb-10" id="show-all">Mostrar todos</button></h3> -->
        <table class="table table-condensed table-hover table-item table-striped " id="budget_review">
            <col width="4%">
            <col width="30%">
            <col width="2%">
            <col width="9%">
            <col width="10%">
            <col width="10%">
            <col width="10%">
            <col width="30%">
            <thead id="header_budget">
                <tr>
                    <th></th>
                    <th>Descripción</th>
                    <th class="text-left"><?= __('Unidad') ?></th>
                    <th class="text-right"><?= __('Cantidad') ?></th>
                    <th class="text-right"><?= __('Precio Unitario') ." ($theSign)"; ?></th>
                    <th class="text-right"><?= __('Precio Total')." ($theSign)"; ?> </th>
                    <th class="text-right"><?= __('Valor objetivo')." ($theSign)"; ?> </th>
                    <th class="text-left"><?= __('Comentario') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $ggtv = 0;
                foreach ($budget_items as $bi) :
                    if(isset($this->request->query['extra'])){
                        if($this->request->query['extra']=="0"){
                            if($bi['parent_id'] == null && ($bi['extra'] == 1 || $bi['extra'] == 2)){
                                continue;
                            }
                        }else{
                            if($bi['extra'] != $this->request->query['extra']){
                                continue;
                            }
                        }
                        $budget->total_cost = $bi['total_price'];
                        $budget->total_target_value = $bi['target_value'];
                    }
                    if($bi['parent_id'] == null && ($bi['extra'] == 3)){
                        $ggtv = $bi['target_value'];
                    }
                    echo $this->element('budget_review', ['bi' => $bi, 'the_budget_currency_value' => $budget->currencies_values[0]['value'], 'includeTotal' => true]);
                ?>
                <?php endforeach; ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right">
                            <strong><span data-toggle="tooltip" data-placement="left" data-original-title="Suma de Partidas">Total <?=!isset($this->request->query['extra'])?"Costo Directo":"";?></span></strong>
                        </td>
                        <td class="text-right"><strong><?=moneda($budget->total_cost);?></strong></td>
                        <td class="text-right"><strong><?=moneda($budget->total_target_value);?></strong></td>
                        <td></td>
                    </tr>
                <?php if(!isset($this->request->query['extra'])){ ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right">
                            <strong><span data-toggle="tooltip" data-placement="left" data-original-title="Suma de Gastos Generales">Gastos Generales</span></strong>
                        </td>
                        <td class="text-right"><strong><?=moneda($budget->general_costs);?></strong></td>
                        <td class="text-right"><strong><?=moneda($ggtv);?></strong></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right">
                            <strong><span data-toggle="tooltip" data-placement="left" data-original-title="UT=(CD+GG)*<?=$budget->utilities;?>%">Total Utilidades</span></strong>
                        </td>
                        <td class="text-right"><strong><?= moneda((($budget->total_cost+$budget->general_costs)*$budget->utilities)/100) ?></strong></td>
                        <td class="text-right"><strong><?= moneda((($budget->total_cost+$budget->general_costs)*$budget->utilities)/100) ?></strong></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right">
                            <strong><span data-toggle="tooltip" data-placement="left" data-original-title="Total Neto=CD+GG+UT">Total Neto</span></strong>
                        </td>
                        <td class="text-right"><strong><?php
                            $utilities_contract = ($budget->total_cost + $budget->general_costs) * ($budget->utilities / 100);
                            $total_contract_currency = $budget->total_cost + $budget->general_costs + $utilities_contract;
                            echo moneda($total_contract_currency);
                        ?></strong></td>
                        <td class="text-right"><strong><?php
                            $utilities_contract = ($budget->total_target_value + $ggtv) * ($budget->utilities / 100);
                            $total_contract_currency = $budget->total_target_value + $ggtv + $utilities_contract;
                            echo moneda($total_contract_currency);
                        ?></strong></td>
                        <td class="text-right"></td>
                        <td></td>
                    </tr>
                <?php } ?>

            </tbody>
         </table>

         <?php if(count($observations) > 0):?>
            <br/><hr/><br/>
            <?php echo $this->Html->tag('h4', __('Comentarios'));?>
            <table>
                <thead>
                    <tr>
                        <th>Fecha de Comentario</th>
                        <th>Comentario</th>
                        <th>Comentado por</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($observations as $observation):?>
                        <tr>
                            <td><?= $observation->created;?></td>
                            <td><?= $observation->observation;?></td>
                            <td><?= $observation->user->first_name.' '.$observation->user->lastname_f.' '.$observation->user->lastname_m;?></td>
                        </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
         <?php endif;?>
         <?php //= $this->Element('info_budget_detail'); ?>
        <?= $this->Html->link(__('Volver Atras'), ['controller' => 'buildings', 'action' => 'dashboard', $sf_building->CodArn], ['class' => 'btn btn-flat btn-link']) ?>
    </div>
</div>
<?= $this->Html->script('schedules.add'); ?>
<script>
$(document).ready(function(){
    $(window).scroll(function(){
        var aTop = $('.dataTable').position().top;
        if($(this).scrollTop()>=aTop){
            $('#header_budget').css({
                'position': 'fixed',
                'top': '0',
                'background-color': '#fff'
            });
        }else{
            $('#header_budget').css({
                'position': 'relative'
            });
        }
    });
});
</script>