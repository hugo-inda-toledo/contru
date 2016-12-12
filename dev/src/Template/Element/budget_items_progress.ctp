<?php

          // SET PROGRESS
          $value_per = $bi['progress'][0]['proyected_progress_percent'];
          $value_hrs = $bi['progress'][0]['overall_progress_percent'];
          $progress_id = $bi['progress'][0]['id'];

          //valores maximo y minimos
          $value_unit_max = $bi['quantity'];
          $value_unit_min = 0;
          //$value_unit_min = $value_hrs/100 * $bi['quantity'];

        $avance_real_monto = ($bi['progress'][0]['overall_progress_percent'] * $bi['total_price'])/100;
        $avance_proyectado_monto = ($bi['progress'][0]['proyected_progress_percent'] * $bi['total_price'])/100;

?>
<?php // Si viene del POST, vuelvo a setiar ultimos valores ingresados por POST
    if(isset($this->request->data['BudgetItems']) && isset($this->request->data['BudgetItems'][$bi['id']]['proyected_progress_percent'])) :
        $valor_percent = $this->request->data['BudgetItems'][$bi['id']]['overall_progress_percent'];
    else :
        //Valor base
        $valor_percent = $value_hrs;
    endif;
  ?>

<tr class="incomplete">
    <td>
        <?= $bi['item']; ?>
        <?= $this->Form->input('BudgetItems.'.$bi['id'].'.progress_id',['type'=>'hidden','value'=>$progress_id]); ?>
        <?= $this->Form->input('BudgetItems.'.$bi['id'].'.proyected_progress_percent',['type'=>'hidden','value'=>$value_per]); ?>
    </td>
    <td class="text-left"><?= $bi['description'] ?></td>
    <td class="text-left"><?= (!empty($units[$bi['unit_id']])) ? h($units[$bi['unit_id']]) : ''  ?></td>
    <td class="text-right"><?= moneda($bi['quantity']) ?></td>
    <td class="text-right"><?= moneda($bi['unity_price']); ?></td>
    <td class="text-right ldz_numeric_no_sign"><?= $bi['total_price']; ?></td>
    <td class="text-right">
        <?= moneda($valor_percent)?>%
        <div class="progress">
            <?php if ($value_per == 100) : ?>
                <div class="progress-bar progress-bar-success" style="width: <?= $valor_percent ?>%"></div>
            <?php else : ?>
                <div class="progress-bar progress-bar-material-orange-<?= substr($valor_percent, 0, 1) ?>00" style="width: <?= $valor_percent ?>%"></div>
            <?php endif; ?>
        </div>
    </td>
    <td class="text-right">
        <?= moneda($value_per)?>%
        <div class="progress">
            <?php if ($value_per == 100) : ?>
                <div class="progress-bar progress-bar-success" style="width: <?= $value_per ?>%"></div>
            <?php else : ?>
                <div class="progress-bar progress-bar-material-orange-<?= substr($value_per, 0, 1) ?>00" style="width: <?= $value_per ?>%"></div>
            <?php endif; ?>
        </div>
    </td>
    <td class="text-right avance_proyectado ldz_numeric_no_sign"><?= $avance_proyectado_monto; ?></td>
    <td class="percent input-inline text-right">
        <div class="units text-right">
            <?= $this->Form->input('BudgetItems.'.$bi['id'].'.overall_progress_percent', [
            'value' => $valor_percent, 'id' => 'per_'.$bi['id'] , 'label' => false, 'type' => 'text', 'data-v-min' =>'0.00','data-v-max' => 100,
            'templates' => [
                'input' => '<input class="form-control ldz_numeric_no_sign text-right" type="{{type}}" name="{{name}}" {{attrs}}>',
                'inputContainer' => '{{content}} <span>%%</span>',
            ]
          ]); ?>
        </div>
    </td>
    <td class="unit input-inline">
        <div class="units text-right">
        <?php // Si viene del POST, vuelvo a setiar ultimos valores ingresados por POST
            if(isset($this->request->data['BudgetItems']) && isset($this->request->data['BudgetItems'][$bi['id']]['proyected_progress_unit'])) :
                $valor_unit = $this->request->data['BudgetItems'][$bi['id']]['overall_progress_unit'];
            else :
                //Valor base
                $valor_unit = $value_unit_min;
            endif;
            // $valor_unit = number_format($valor_unit, 2, ".", ",");
            // $value_unit_min = number_format($value_unit_min, 2, ".", ",");
        ?>
        <?= $this->Form->input('BudgetItems.'.$bi['id'].'.overall_progress_unit', [
            'value' => $valor_unit, 'id' => 'unit_'.$bi['id'] , 'label' => false, 'type' => 'text', 'data-v-min' => '0.00','data-v-max' => $value_unit_max,
            'data-input'=>"avance_real",
            'templates' => [
                'input' => '<input class="form-control ldz_numeric_no_sign text-right" type="{{type}}" name="{{name}}" {{attrs}}>',
                'inputContainer' => '{{content}} <span>'. $bi['unit']['name'] .'</span>',
            ]
        ]); ?>
        </div>
    </td>
    <td class="text-right monto_proyectado ldz_numeric_no_sign" data-total-price="<?=$bi['total_price'];?>"><?php echo $avance_real_monto; ?></td>
</tr>
