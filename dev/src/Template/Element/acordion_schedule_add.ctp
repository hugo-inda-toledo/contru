<?php
if (!empty($bi['children'])) :
  	if (is_null($bi['parent_id'])) : ?>
    	<!-- padre  -->
        <tr class="info">
            <td></td>
            <td><strong><?= $bi['item'].' '.$bi['description']; ?></strong></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <?php
    	foreach ($bi['children'] as $children) :
       		echo $this->element('acordion_schedule_add', ['bi' => $children]);
     	endforeach;
	 else: ?>
        <tr>
            <td></td>
            <td><strong><?= $bi['item'].' '.$bi['description']; ?></strong></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <?php
        foreach ($bi['children'] as $children):
      		echo $this->element('acordion_schedule_add', ['bi' => $children]);
      	endforeach;
    endif;
 else:
  	//ITEMS
  	$disabled = $bi['disabled'];
  	$done = false;
    $avance_real_cantidad=0;
    $avance_real_monto = 0;
    $avance_proyectado_monto = 0;
  	if (isset($bi['progress']) && ! empty($bi['progress'])) :
      	if ($bi['progress'][0]['overall_progress_percent'] < 100) :
            //echo $this->Form->checkbox('BudgetItems.'.$bi['id'].'.id' , ['checked' => true, 'data-disabled' => '_'.$bi['id'], 'hiddenField' => false,'value' => $bi['id']]);
            $value_per = $bi['progress'][0]['proyected_progress_percent'];
            $value_hrs = $bi['progress'][0]['overall_progress_percent'];
           	// echo $this->Form->hidden('BudgetItems.'.$bi['id'].'.progress_id',['value' =>  $bi['progress'][0]['id']]);
      	else:
            // item en 100%
            // No es posible seleccionar en la planificacion
          	$done = true;
          	$value_per = $bi['progress'][0]['proyected_progress_percent'];
          	$value_hrs = $bi['progress'][0]['overall_progress_percent'];
         	// echo '<i class="mdi-action-done"></i>';
      	endif;
        $avance_real_cantidad = ($bi['progress'][0]['overall_progress_percent'] * $bi['quantity'])/100;
        $avance_real_monto = ($bi['progress'][0]['overall_progress_percent'] * $bi['total_price'])/100;
        $avance_proyectado_monto = ($bi['progress'][0]['proyected_progress_percent'] * $bi['total_price'])/100;
  	else :
       	// echo $this->Form->checkbox('BudgetItems.'.$bi['id'].'.id' , ['hiddenField' => false,'data-disabled' => '_'.$bi['id'],'value' => $bi['id']]);
        $value_per = 0;
        $value_hrs = 0;
  	endif;
  	// Min y Máx. Min no puede ser menor al proyectado anterior.
  	$value_unit_max = $bi['quantity'];
  	$value_unit_min = $value_hrs / 100 * $bi['quantity'];
  	if ($disabled): ?>
      	<tr class="disabled hidden">
            <td></td>
        	<td class="tachado"><span class="label label-warning">Ítem deshabilitado</span><?= ' ' . $bi['item'].' '.$bi['description'] /*." | ". $bi['quantity'] .' | '.strtoupper($bi['unit']['name'])*/; ?></td>
            <td><?=strtoupper($bi['unit']['name']);?></td>
            <td class="text-right"><?=moneda($bi['quantity']);?></td>
            <td class="text-right"><?= moneda($bi['unity_price']); ?></td>
            <td class="text-right"><?= moneda($bi['total_price']); ?></td>
        	<td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
      	</tr>
	<?php elseif ($done) : ?>
      	<tr class="done hidden">
          	<td></td>
          	<td><span class="label label-success">Completado</span><?= ' ' . $bi['item'].' '.$bi['description']/* ." | ". $bi['quantity'] .' | '.strtoupper($bi['unit']['name'])*/; ?></td>
            <td><?=strtoupper($bi['unit']['name']);?></td>
            <td class="text-right"><?=moneda($bi['quantity']);?></td>
            <td class="text-right"><?= moneda($bi['unity_price']); ?></td>
            <td class="text-right"><?= moneda($bi['total_price']); ?></td>
          	<td class="text-right">
             	<!-- Muestra el ulitmo avance proyectado para poder hacer la proxima proyeccion -->
          		<?= $value_per?>%
              	<div class="progress">
                  <?php if ($value_per == 100) : ?>
                      <div class="progress-bar progress-bar-success" style="width: <?= $value_per ?>%"></div>
                  <?php else : ?>
                      <div class="progress-bar progress-bar-material-orange-<?= substr($value_per, 0, 1) ?>00" style="width: <?= $value_per ?>%"></div>
                  <?php endif; ?>
         	 	</div>
 	 	    </td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-right">
                <!-- Muestra el ulitmo avance proyectado para poder hacer la proxima proyeccion -->
                <div>
                    <?= $value_hrs?>%
                    <div class="progress">
                      <?php if ($value_hrs == 100) : ?>
                          <div class="progress-bar progress-bar-success" style="width: <?= $value_hrs ?>%"></div>
                      <?php else : ?>
                          <div class="progress-bar progress-bar-material-orange-<?= substr($value_hrs, 0, 1) ?>00" style="width: <?= $value_hrs ?>%"></div>
                      <?php endif; ?>
                    </div>
                </div>
            </td>
            <td></td>
            <td></td>
      	</tr>
    <?php else: ?>
      	<tr class="incomplete">
        	<td>
          		<?= $this->Form->checkbox('BudgetItems.'.$bi['id'].'.id' , ['hiddenField' => true,'data-disabled' => '_'.$bi['id'],'value' => 1]); ?>
            </td>
            <td><?= $bi['item'].' '.$bi['description']/* ." | ". $bi['quantity'] .' | '.strtoupper($bi['unit']['name'])*/; ?></td>
            <td><?=strtoupper($bi['unit']['name']);?></td>
            <td class="text-right"><?=moneda($bi['quantity']);?></td>
            <td class="text-right"><?= moneda($bi['unity_price']); ?></td>
            <td class="text-right"><?= moneda($bi['total_price']); ?></td>
            <td class="text-right">
                <?= $value_per?>%
                <div class="progress">
                    <?php if ($value_per == 100) : ?>
                        <div class="progress-bar progress-bar-success" style="width: <?= $value_per ?>%"></div>
                    <?php else : ?>
                        <div class="progress-bar progress-bar-material-orange-<?= substr($value_per, 0, 1) ?>00" style="width: <?= $value_per ?>%"></div>
                    <?php endif; ?>
                </div>
            </td>
            <td class="percent input-inline text-right">
                <div class="units text-right">
                    <div class="form-group">
                        <?php if (isset($this->request->data['BudgetItems']) && isset($this->request->data['BudgetItems'][$bi['id']]['proyected_progress_percent'])) :
                            $valor_percent = $this->request->data['BudgetItems'][$bi['id']]['proyected_progress_percent'];
                            $disabled_op = false;
                        else :
                            //Parte deshabilitado
                            $valor_percent = $value_per;
                            $disabled_op = true;
                        endif;
                        echo $this->Form->input('BudgetItems.'.$bi['id'].'.proyected_progress_percent', [
                            'value' => $valor_percent, 'id' => 'per_'.$bi['id'] , 'label' => false, 'type' => 'text', 'data-v-min' => '0.00', 'data-v-max' => 100,
                            'disabled' => $disabled_op,
                                'templates' => [
                                'input' => '<input class="form-control ldz_numeric_no_sign text-right" type="{{type}}" name="{{name}}" {{attrs}}>',
                                'inputContainer' => '{{content}} <span>%%</span>'
                            ]]); ?>
                        <span class="help-block"></span>
                    </div>
                </div>
            </td>
            <td class="unit input-inline">
                <div class="units text-right">
                <!-- Si viene del POST, vuelvo a setiar ultimos valores ingresados por POST -->
                    <div class="form-group">
                    <?php
                        if (isset($this->request->data['BudgetItems']) && isset($this->request->data['BudgetItems'][$bi['id']]['proyected_progress_unit'])) :
                            $valor_unit = $this->request->data['BudgetItems'][$bi['id']]['proyected_progress_unit'];
                            $disabled_op = false;
                        else :
                            //Parte deshabilitado
                            $valor_unit = $value_unit_min;
                            $disabled_op = true;
                        endif;
                        // pr($value_unit_max);
                        // $valor_unit = number_format((int)$valor_unit, 2, ".", ",");
                        // $value_unit_min = number_format($value_unit_min, 2, ".", ",");
                        echo $this->Form->input('BudgetItems.'.$bi['id'].'.proyected_progress_unit', [
                            'value' => $valor_unit, 'id' => 'unit_'.$bi['id'] , 'label' => false, 'type' => 'text', 'data-v-min' => '0.00','data-v-max' => $value_unit_max,
                            'disabled' => $disabled_op,
                            'style' => 'width: 100px;',
                            'data-input' => 'avance_proyectado',
                            'templates' => [
                                'input' => '<input class="form-control ldz_numeric_no_sign text-right" type="{{type}}" name="{{name}}" {{attrs}}>',
                                'inputContainer' => '{{content}} <span></span>',
                            ]
                        ]); ?>
                        <span class="help-block"></span>
                    </div>
                </div>
            </td>
            <td class="text-right monto_proyectado ldz_numeric_no_sign" data-total-price="<?=$bi['total_price'];?>"><?= $avance_proyectado_monto; ?></td>
            <td>
                <?= $value_hrs?>%
                <div class="progress">
                    <?php if ($value_hrs == 100) : ?>
                        <div class="progress-bar progress-bar-success" style="width: <?= $value_hrs ?>%"></div>
                    <?php else : ?>
                        <div class="progress-bar progress-bar-material-orange-<?= substr($value_hrs, 0, 1) ?>00" style="width: <?= $value_hrs ?>%"></div>
                    <?php endif; ?>
                </div>
                <?= $this->Form->input('BudgetItems.'.$bi['id'].'.overall_progress_percent',['type'=>'hidden','value'=>$value_hrs])  ?>
            </td>
            <td class="text-right ldz_numeric_no_sign" data-avance-real-cantidad="<?=$avance_real_cantidad;?>"><?php echo $avance_real_cantidad; ?></td>
            <td class="text-right ldz_numeric_no_sign" data-avance-real-monto="<?=$avance_real_monto?>"><?php echo $avance_real_monto; ?></td>
        </tr>
	<?php
	endif;
endif; ?>