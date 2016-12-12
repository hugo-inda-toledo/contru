<?php
if (!empty($bi['children'])) :
	if (is_null($bi['parent_id'])) : //Padre ?>
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
			echo $this->element('acordion_schedule_edit', ['bi' => $children, 'schedule_id' => $schedule_id]);
		endforeach;
	else : ?>
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
		foreach ($bi['children'] as $children) :
			echo $this->element('acordion_schedule_edit', ['bi' => $children, 'schedule_id' => $schedule_id]);
		endforeach;
	endif;
else :
  	//ITEMS
	$disabled = $bi['disabled'];
	$done = false;
	$disabled_op = true;
	$checked = false;
	$has_progress_schedule = false;
    $avance_real_cantidad = 0;
    $avance_real_monto = 0;
    $avance_proyectado_monto = 0;
  	// Tiene algun progress?
	if (isset($bi['progress']) && !empty($bi['progress'])) :
  		// Si el progress 0 es el de la planificación, seteo valores
		if ($bi['progress'][0]['schedule_id'] == $schedule_id) :
			$value_per = $bi['progress'][0]['proyected_progress_percent'];
			$value_hrs = $bi['progress'][0]['overall_progress_percent'];
			$disabled_op = false;
			$checked = true;
			$progress_check = true;
			$has_progress_schedule = true;
			$progress_id = $bi['progress'][0]['id'];
			if (isset($bi['progress'][1])) :
				$value_per_ant = $bi['progress'][1]['proyected_progress_percent'];
				$value_hrs_ant = $bi['progress'][1]['overall_progress_percent'];
                //revisar si progress anterior es 100%
				$done = ($value_hrs_ant == 100) ? true : false;
			else :
                // No tiene progress anterior => parte en cero
				$value_per_ant = 0;
				$value_hrs_ant = 0;
			endif;
		else :
            // progress en [0] es el progress anterior
			$value_per = $bi['progress'][0]['proyected_progress_percent'];
			$value_hrs = $bi['progress'][0]['overall_progress_percent'];
			$value_per_ant = $bi['progress'][0]['proyected_progress_percent'];
			$value_hrs_ant = $bi['progress'][0]['overall_progress_percent'];
			$done = ($value_hrs_ant == 100) ? true : false;
		endif;
        $avance_real_cantidad = ($bi['progress'][0]['overall_progress_percent'] * $bi['quantity'])/100;
        $avance_real_monto = ($bi['progress'][0]['overall_progress_percent'] * $bi['total_price'])/100;
        $avance_proyectado_monto = ($bi['progress'][0]['proyected_progress_percent'] * $bi['total_price'])/100;
	else :
   		// No tengo progress asociado, parten en cero
		$value_per = 0;
		$value_hrs = 0;
		$value_per_ant = 0;
		$value_hrs_ant = 0;
	endif;
	$value_unit_max = $bi['quantity'];
	$value_unit_min = $value_hrs_ant / 100 * $bi['quantity'];
	if ($disabled) : ?>
		<tr class="disabled hidden">
			<td></td>
        	<td class="tachado"><span class="label label-warning">Ítem deshabilitado</span><?= ' ' .  $bi['item'].' '.$bi['description']/* ." | ". $bi['quantity'] .' | '.strtoupper($bi['unit']['name'])*/; ?></td>
            <td><?=strtoupper($bi['unit']['name']);?></td>
            <td class="text-right"><?=moneda($bi['quantity']);?></td>
            <td class="text-right"><?= moneda($bi['unity_price']); ?></td>
            <td class="text-right"><?= moneda($bi['total_price']); ?></td>
        	<td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
		</tr>
	<?php
	else:
		if ($done) : ?>
			<tr class="done hidden">
				<td></td>
	          	<td><span class="label label-success">Completado</span><?= ' ' . $bi['item'].' '.$bi['description']/* ." | ". $bi['quantity'] .' | '.strtoupper($bi['unit']['name'])*/; ?></td>
	            <td><?=strtoupper($bi['unit']['name']);?></td>
	            <td class="text-right"><?=moneda($bi['quantity']);?></td>
                <td class="text-right"><?= moneda($bi['unity_price']); ?></td>
                <td class="text-right"><?= moneda($bi['total_price']); ?></td>
	          	<td>
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
	              	<?php // Si viene del post
	              	if (isset($this->request->data['BudgetItems'])) :
	              		$checked = ($this->request->data['BudgetItems'][$bi['id']]['id'] == 1) ? true : false;
	              	endif;
	              	echo $this->Form->checkbox('BudgetItems.' . $bi['id'] . '.id' , ['hiddenField' => true, 'data-disabled' => '_' . $bi['id'], 'value' => 1, 'checked' => $checked]);
	              	//Tiene progress en ésta planificación
	               	if ($has_progress_schedule) :
	              		echo $this->Form->hidden('BudgetItems.'.$bi['id'].'.progress_id', ['value' => $progress_id]);
	            	endif; ?>
          		</td>
          		<td><?php echo $bi['item'] . ' ' . $bi['description']/*. " | " . $bi['quantity'] . ' | ' . strtoupper($bi['unit']['name'])*/; ?></td>
	            <td><?=strtoupper($bi['unit']['name']);?></td>
	            <td class="text-right"><?=moneda($bi['quantity']);?></td>
                <td class="text-right"><?= moneda($bi['unity_price']); ?></td>
                <td class="text-right"><?= moneda($bi['total_price']); ?></td>
          		<td class="text-right">
          			<!-- Muestra el ulitmo avance proyectado para poder hacer la proxima proyeccion -->
	          		<?= $value_per_ant?>%
	              	<div class="progress">
	                  <?php if ($value_per_ant == 100) : ?>
	                      <div class="progress-bar progress-bar-success" style="width: <?= $value_per_ant ?>%"></div>
	                  <?php else : ?>
	                      <div class="progress-bar progress-bar-material-orange-<?= substr($value_per_ant, 0, 1) ?>00" style="width: <?= $value_per_ant ?>%"></div>
	                  <?php endif; ?>
	         	 	</div>
          		</td>
                <td class="percent input-inline text-right">
                    <div class="units text-right">
                        <div class="form-group">
                            <?php // Si viene del POST, vuelvo a setiar ultimos valores ingresados por POST
                            if (isset($this->request->data['BudgetItems']) && isset($this->request->data['BudgetItems'][$bi['id']]['proyected_progress_percent'])) :
                                $valor_percent = $this->request->data['BudgetItems'][$bi['id']]['proyected_progress_percent'];
                                $disabled_op = false;
                            else :
                                //Parte deshabilitado con valor base
                                // Nose porque mostraba en base al $value_hrs
                                $valor_percent = $value_per;
                            endif;
                            echo $this->Form->input('BudgetItems.'.$bi['id'].'.proyected_progress_percent', [
                                'value' => $valor_percent, 'id' => 'per_'.$bi['id'] , 'label' => false, 'type' => 'text', 'data-v-min' => '0.00', 'data-v-max' => 100,
                                'disabled' => $disabled_op, 'templates' => [
                                    'input' => '<input class="form-control ldz_numeric_no_sign text-right" type="{{type}}" name="{{name}}" {{attrs}}>',
                                    'inputContainer' => '{{content}} <span>%%</span>'
                                ]
                            ]); ?>
                            <span class="help-block"></span>
                        </div>
                    </div>
                </td>
                <td class="unit input-inline">
                    <div class="units text-right">
                        <div class="form-group">
                            <?php // Si viene del POST, vuelvo a setiar ultimos valores ingresados por POST
                            if (isset($this->request->data['BudgetItems']) && isset($this->request->data['BudgetItems'][$bi['id']]['proyected_progress_unit'])) :
                                $valor_unit = $this->request->data['BudgetItems'][$bi['id']]['proyected_progress_unit'];
                                $disabled_op = false;
                            else :
                                //Parte deshabilitado con valor base
                                // Nose porque mostraba en base al $value_unit_min
                                $valor_unit = ($valor_percent * $bi['quantity'])/100;
                                // $valor_unit = $value_unit_min;
                            endif;
                            echo $this->Form->input('BudgetItems.'.$bi['id'].'.proyected_progress_unit', [
                                'value' => $valor_unit, 'id' => 'unit_'.$bi['id'] , 'label' => false, 'type' => 'text', 'data-v-min' => '0.00','data-v-max' => $value_unit_max, 'style' => 'width: 100px;', 'disabled' => $disabled_op,'data-input' => 'avance_proyectado', 'templates' => [
                                    'input' => '<input class="form-control ldz_numeric_no_sign text-right" type="{{type}}" name="{{name}}" {{attrs}}>',
                                    'inputContainer' => '{{content}} <span></span>'
                                ]
                            ]); ?>
                            <span class="help-block"></span>
                        </div>
                    </div>
                </td>
                <td class="text-right monto_proyectado ldz_numeric_no_sign" data-total-price="<?=$bi['total_price'];?>"><?= $avance_proyectado_monto; ?></td>
          		<td>
          			<?= $value_hrs_ant?>%
	                <div class="progress">
	                    <?php if ($value_hrs_ant == 100) : ?>
	                        <div class="progress-bar progress-bar-success" style="width: <?= $value_hrs_ant ?>%"></div>
	                    <?php else : ?>
	                        <div class="progress-bar progress-bar-material-orange-<?= substr($value_hrs_ant, 0, 1) ?>00" style="width: <?= $value_hrs_ant ?>%"></div>
	                    <?php endif; ?>
	                </div>
          			<?= $this->Form->input('BudgetItems.'.$bi['id'].'.overall_progress_percent',['type'=>'hidden','value'=>$value_hrs_ant])  ?>
          		</td>
                <td class="text-right ldz_numeric_no_sign" data-avance-real-cantidad="<?=$avance_real_cantidad;?>"><?php echo $avance_real_cantidad; ?></td>
                <td class="text-right ldz_numeric_no_sign"><?php echo $avance_real_monto; ?></td>
          	</tr>
      	<?php endif;
  	endif;
endif; ?>