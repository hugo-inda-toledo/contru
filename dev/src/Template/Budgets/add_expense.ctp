<?= $this->Html->script('/handtomeexcel/pikaday/pikaday.js') ?>
<?= $this->Html->css('/handtomeexcel/pikaday/pikaday.css') ?>

<?= $this->Html->css('/handtomeexcel/handsontable.css') ?>
<?= $this->Html->css('/handtomeexcel/handsontable-custom.css') ?>

<?= $this->Html->script('/handtomeexcel/moment/moment.js') ?>
<?= $this->Html->script('/handtomeexcel/zeroclipboard/ZeroClipboard.js') ?>

<?= $this->Html->script('/handtomeexcel/handsontable.js') ?>

<?= $this->Html->script('/handtomeexcel/formula/handsontable.formula.js') ?>
<?= $this->Html->script('/handtomeexcel/ruleJS.all.full.min.js') ?>
<?= $this->Html->script('budgets.add_extra.js') ?>
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
        <h3 class="panel-title">Agregar Gastos Presupuesto</h3>
    </div>
    <div class="panel-body">
        <div class="col-sm-12 col-md-12">
            <?php if (count($datos_excel['errores']) > 0) : ?>
                <h4 class="subheader"><?= __('Validaciones de Errores') ?></h4>
                <table cellpadding="0" cellspacing="0">
                    <?php foreach ($datos_excel['errores'] as $error): ?>
                    <tr>
                        <?php if($error == 'No se encontraron errores.') :
                            echo "<td class='success'>";
                        else :
                            echo "<td class='danger'>";
                        endif; ?>
                        <?= h($error) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
            <?php if(!empty($info)): ?>
                <h4 class="subheader"><?= __('Validaciones') ?></h4>
                <table cellpadding="0" cellspacing="0">
                <?php
                    foreach ($info as $inf) :
                        echo "<td class='info'>".h($inf) . '</td></tr>';
                    endforeach; ?>
                </table>
                <?php echo $this->Form->hidden('confirm_info', ['value' => '1']); ?>
            <?php endif; ?>
        </div>
    <div class="col-sm-12 col-md-12">
        <?php echo 'El Item recomendado para añadir como siguiente gasto no contemplado al presupuesto es: ' . $new_parent; ?>
    </div>

     <?php if (!empty($budget_items)) : ?>
        <h3>Detalle Partidas de Presupuesto <button type="button" class="btn btn-right btn-default btn-raised btn-sm mb-10" id="show-all">Mostrar todos</button></h3>
        <table class="table table-condensed table-hover table-item table-striped ">
            <col width="4%">
            <col width="30%">
            <col width="8%">
            <col width="10%">
            <col width="12%">
            <col width="12%">
            <col width="12%">
            <thead>
                <tr>
                    <th></th>
                    <th>Descripción</th>
                    <th class="text-left"><?= __('Unidad') ?></th>
                    <th class="text-right"><?= __('Cantidad') ?></th>
                    <th class="text-right"><?= __('Precio Unitario') ?></th>
                    <th class="text-right"><?= __('Precio Total') ?> </th>
                    <th class="text-right"><?= __('Valor Objetivo') ?></th>
                    <th class="text-left"><?= __('Comentario') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($budget_items as $bi) :
                    echo $this->element('budget_review', ['bi' => $bi]);
                endforeach; ?>
            </tbody>
         </table>
    <?php endif; ?>

    <div class="col-sm-12 col-md-12">
      <p> Es posible agregar gastos no comteplados a partidas y/o sub-partidas ya existentes dentro de el capitulo corespondiente.</p>
      <p> Por ejemplo, el capitulo 10 corresponde a gastos no conteplados y dentro de el ya existen los registros 10.1 y 10.2, para ingresar un registro dentro del item 10.1, solo basta con ingresar una item como 10.1.1 y el sistema automaticamente lo registra como un item dentro de la partida 10.1.</p>
    </div>
    <div class="col-sm-12 col-md-12">
        <div id="example1"></div>
    </div>
    <div class="col-sm-12 col-md-12">
        <?= $this->Form->create($budget, ['id' => 'excelform']); ?>
        <?php echo $this->Form->input('excel', ['type' => 'hidden', 'value' => '']);   ?>
        <?php if(!empty($info)): ?>
            <?php echo $this->Form->hidden('confirm_info', ['value' => '1']); ?>
        <?php endif; ?>
        <?= $this->Form->button('Guardar', ['id' => 'btnHand', 'class' => 'btn btn-sm btn-material-orange-900']) ?>
        <?= $this->Html->link('Volver', ['controller' => 'Buildings', 'action' => 'dashboard', $sf_building->CodArn], ['id' => 'formState', 'class' => 'btn btn-flat btn-link']); ?>
        <?= $this->Form->end() ?>

    </div>
  </div>
  <div id="confirmDiag" class="modal">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                  <h4 class="modal-title">Confirmación</h4>
              </div>
              <div class="modal-body">
                  <p>Esta seguro que desea agregar estos items al presupuesto?</p>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-flat btn-link" data-dismiss="modal">Cancelar</button>
                  <button id="excelsubmit" type="button" class="btn btn-material-orange-900">Guardar</button>
              </div>
          </div>
      </div>
  </div>


      <script>
      $(document).ready(function(){

            var units = <?php echo (!empty($units)) ? json_encode(array_values($units)) : "[" .'n/a' . "]"; ?>;
            var data1 = <?php echo (!empty($datos_excel['excel'])) ? json_encode(array_values($datos_excel['excel'])) : "[{A: '" . $new_parent . "'}]"; ?>,
              container1 = document.getElementById('example1'),
              settings1 = {
                data: data1,
                //minRows: 5,
                minCols: 7,
                startRows: 7,
                startCols: 7,
                colHeaders: ['Item (A)', 'Descripcion (B)', 'Unidad (C)', 'Cantidad (D)', 'P.Unitario (E)', 'Total (F)', 'Comentario (G)', 'Valor Objetivo (H)'],
                colWidths: [65, 300, 65, 65, 100, 120, 400, 120],
                rowHeaders: true,
                className: "htCenter",
                minSpareRows: 1,
                contextMenu: true,
                formulas:true,
                contextMenuCopyPaste: {
                  swfPath: '../../handtomeexcel/zeroclipboard/ZeroClipboard.swf'
                },
                columns: [
                {
                  data: 'A'
                  // 1nd column is simple text, no special options here
                },
                {
                  data: 'B',
                },
                {
                  data: 'C',
                  type: 'autocomplete',
                  source: units,
                  strict: false
                },
                {
                  data: 'D',
                  //type: 'numeric',
                  format: '0.0'
                },
                {
                  data: 'E',
                  //type: 'numeric',
                  format: '0.0'
                },
                {
                  data: 'F',
                  //type: 'numeric',
                  format: '0.0'
                },
                {
                  data: 'G'
                },
                {
                  data: 'H',
                  //type: 'numeric',
                  format: '0.0'
                }
              ],
               afterSetCellMeta: function (row, col, key, val) {
                    console.log("cell meta changed", row, col, key, val);
                },
              },
              hot1;

            hot1 = new Handsontable(container1, settings1);
            hot1.render();
        $( "#excelform" ).submit(function( event ) {
          $('#confirmDiag').modal('show');
          event.preventDefault();
        });
        $("#excelsubmit").click(function(e){
          document.forms['excelform'].excel.value = JSON.stringify({data: hot1.getData()});
          document.forms['excelform'].submit();
          $('#confirmDiag').modal('hide');

        });
      });
      function toPost(){
          document.forms['excelform'].excel.value = JSON.stringify({data: hot1.getData()});
      }
      </script>