<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo De Gastos'));
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

<?php echo $this->Html->link(__('Volver'), ['controller' => 'buildings', 'action' => 'dashboard', $sf_building->CodArn], array('class' => 'btn btn-sm btn-primary text-right'));?>

<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Partidas</h3>
    </div>
    <div class="panel-body">
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
                    <th class="text-left"></th>
                </tr>
            </thead>
            <tbody>
                <?php
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
                    }
                    echo $this->element('budget_review_with_materials', ['bi' => $bi, 'the_budget_currency_value' => $budget->currencies_values[0]['value'], 'includeTotal' => true]);
                ?>
                <?php endforeach; ?>
            </tbody>
         </table>
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