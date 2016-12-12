<!--<pre>
<?php //print_r($payment_statement_item[$bi['id']]);?>
</pre>-->

<?php //echo 'overall_progress_value: '.$payment_statement_item[$bi['id']]['overall_progress_value']?>

<?= $this->Form->hidden('budget_item.' . $bi['id'] . '.id', ['value' => $bi['id']]); ?>
<td><?= $bi['item']; ?></td>
<td><?= $bi['description']; ?></td>
<td class="text-right" nowrap>
    <span class="total_price" data-original="<?= $bi['total_price']; ?>" data-value="<?=$bi['total_price'] ?>"><?= $bi['total_price'] ?></span>
</td>
<td class="percentage_overall_progress text-right">
    <a href="javascript:void(0);" onclick='sugerido("#budget-item-<?= $bi['id'] ?>-progress", <?= $bi['percentage_overall_progress'] ?>);' class="btn btn-xs btn-default" type="button" data-toggle="tooltip" data-placement="left" title="Usar como porcentaje de pago (A. presente E.D.P.)"><span class="ldz_numeric_no_sign"><?= $bi['percentage_overall_progress'] ?></span></a>
</td>

<td class="text-right progress_present">
    <?php if($this->request->params['action'] == 'edit'):?>
        <?= $this->Form->input('budget_item.' . $bi['id'] . '.progress', [
            'templates' => [
                'input' => '<input class="form-control text-right" type="{{type}}" name="{{name}}" {{attrs}}>',
            ],
            'type' => 'number',
            'data-type' => 'progress',
            'label' => false,
            'min' => $payment_statement_item[$bi['id']]['previous_progress'],
            'max' => 100,
            'step' => '0.01',
            'default' => $payment_statement_item[$bi['id']]['progress']
        ]); ?>
    <?php else:?>
        <?= $this->Form->input('budget_item.' . $bi['id'] . '.progress', [
            'templates' => [
                'input' => '<input class="form-control text-right" type="{{type}}" name="{{name}}" {{attrs}}>',
            ],
            'type' => 'number',
            'data-type' => 'progress',
            'label' => false,
            'min' => $payment_statement_item[$bi['id']]['previous_progress'],
            'max' => 100,
            'step' => '0.01'
        ]); ?>
    <?php endif;?>
    
</td>
<td class="text-right progress_value">
    <?= $this->Form->hidden('budget_item.' . $bi['id'] . '.progress_value', [
        'data-total_price' => $bi['total_price'], 'data-type' => 'progress_value',
        'value' => $payment_statement_item[$bi['id']]['progress_value']
    ]); ?>
    <span class="" data-original="<?=  $payment_statement_item[$bi['id']]['progress_value']; ?>"><?php  //$payment_statement_item[$bi['id']]['progress_value']; ?>
        <?php 
            if($payment_statement_item[$bi['id']]['progress_value'] != 0 && $this->request->params['action'] == 'edit')
            {
                echo moneda($payment_statement_item[$bi['id']]['progress_value']);
            }
        ?>        
    </span>
</td>
<td class="text-right previous_progress">
    <?php if($payment_statement_item[$bi['id']]['previous_progress'] == 0 && $this->request->params['action'] == 'add'):?>

        <?= $this->Form->hidden('budget_item.' . $bi['id'] . '.previous_progress', [
            'data-type' => 'previous_progress',
            'value' => $payment_statement_item[$bi['id']]['progress']
        ]); ?>
        <span><?= moneda($payment_statement_item[$bi['id']]['progress']); ?>%</span>

    <?php else:?>

        <?= $this->Form->hidden('budget_item.' . $bi['id'] . '.previous_progress', [
            'data-type' => 'previous_progress',
            'value' => $payment_statement_item[$bi['id']]['previous_progress']
        ]); ?>
        <span><?= moneda($payment_statement_item[$bi['id']]['previous_progress']); ?>%</span>

    <?php endif;?>
</td>

<td class="text-right previous_progress_value">
    <?php if($payment_statement_item[$bi['id']]['previous_progress_value'] == 0 && $this->request->params['action'] == 'add'):?>

        <?= $this->Form->hidden('budget_item.' . $bi['id'] . '.previous_progress_value', [
                'data-type' => 'previous_progress_value',
                'value' => $payment_statement_item[$bi['id']]['progress_value']
            ]); ?>
        <span><?= moneda($payment_statement_item[$bi['id']]['progress_value']); ?></span>

    <?php else:?>

        <?= $this->Form->hidden('budget_item.' . $bi['id'] . '.previous_progress_value', [
                'data-type' => 'previous_progress_value',
                'value' => $payment_statement_item[$bi['id']]['previous_progress_value']
            ]); ?>
        <span><?= moneda($payment_statement_item[$bi['id']]['previous_progress_value']); ?></span>

    <?php endif;?>
</td>

<td class="text-right overall_progress">
    <div class="form-group">
        <?= $this->Form->hidden('budget_item.' . $bi['id'] . '.overall_progress', [
            'data-type' => 'overall_progress',
            'data-value' => $payment_statement_item[$bi['id']]['overall_progress'],
            'value' => $payment_statement_item[$bi['id']]['overall_progress']
        ]); ?>
        <?php /*$this->Form->hidden('budget_item.' . $bi['id'] . '.overall_progress_value', [
            'data-type' => 'overall_progress_value',
            'data-value' => $payment_statement_item[$bi['id']]['overall_progress_value'],
            'value' => $payment_statement_item[$bi['id']]['overall_progress_value']
        ]);*/ ?>
        
        <?php if($this->request->params['action'] == 'edit'):?>
            <span><?= moneda($payment_statement_item[$bi['id']]['overall_progress']); ?>%</span>
        <?php else:?>
            <span></span>
        <?php endif;?>
    </div>
</td>

<td class="text-right overall_progress_value">
    <div class="form-group">
        <?php /*$this->Form->hidden('budget_item.' . $bi['id'] . '.overall_progress', [
            'data-type' => 'overall_progress',
            'data-value' => $payment_statement_item[$bi['id']]['overall_progress'],
            'value' => $payment_statement_item[$bi['id']]['overall_progress']
        ]);*/ ?>
        <?= $this->Form->hidden('budget_item.' . $bi['id'] . '.overall_progress_value', [
            'data-type' => 'overall_progress_value',
            'data-value' => $payment_statement_item[$bi['id']]['overall_progress_value'],
            'value' => $payment_statement_item[$bi['id']]['overall_progress_value']
        ]); ?>

        <?php if($this->request->params['action'] == 'edit'):?>
            <span><?= moneda($payment_statement_item[$bi['id']]['overall_progress_value']); ?></span>
        <?php else:?>
            <span></span>
        <?php endif;?>
    </div>
</td>

<?php //debug($payment_statement_item);?>
