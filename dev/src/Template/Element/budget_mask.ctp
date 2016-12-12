<?php
$crv = 1; // Conversion Reference Value
if(isset($the_budget_currency_value)){
        $crv = $the_budget_currency_value;
}

if (!empty($bi['children'])) :
  	if (is_null($bi['parent_id'])) : ?>
    	<!-- padre  -->
        <tr class="info">
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
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <?php
    	foreach ($bi['children'] as $children) :
       		echo $this->element('budget_mask', ['bi' => $children, 'the_budget_currency_value' => $crv]);
     	endforeach;
	 else: ?>
        <tr>
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
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <?php
        foreach ($bi['children'] as $children):
      		echo $this->element('budget_mask', ['bi' => $children, 'the_budget_currency_value' => $crv]);
      	endforeach;
    endif;
 else:
  	/*
     <th><?= __('Unidad') ?></th>
    <th><?= __('Cantidad') ?></th>
    <th><?= __('Precio Unitario') ?></th>
    <th><?= __('Precio Total') ?></th>
    <th><?= __('Precio Moneda') ?></th> 
     */
    //ITEMS
    $done = false;
    if (isset($bi['progress']) && ! empty($bi['progress'])) :
        if ($bi['progress'][0]['overall_progress_percent'] < 100) :
        else:
            // item en 100%
            // No es posible seleccionar en la planificacion
            $done = true;
        endif;
    else :
        
    endif;
    if ($bi['disabled']): ?>
      	<tr class="disabled hidden">
        	<td class="tachado"><span class="label label-warning">Ítem deshabilitado</span><?= ' ' . $bi['item'].' '.$bi['description']; ?></td>
        	<td class="text-center"><?= $bi['unit']['name']; ?></td>
            <td class="text-right"><?= moneda($bi['quantity']); ?></td>
            <td class="text-right"><?= moneda($bi['unity_price']); ?></td>
            <td class="text-right"><?= moneda($bi['total_price']); ?></td>
            <td><?= $bi['comments']; ?></td>
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
	<?php elseif ($done) : ?>
      	<tr class="done hidden">
          	<td><span class="label label-success">Completado</span><?= ' ' . $bi['item'].' '.$bi['description']; ?></td>
          	<td class="text-center"><?= $bi['unit']['name']; ?></td>
          	<td class="text-right"><?= moneda($bi['quantity']); ?></td>
            <td class="text-right"><?= moneda($bi['unity_price']); ?></td>
            <td class="text-right"><?= moneda($bi['total_price']); ?></td>
            <td><?= $bi['comments']; ?></td>
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
    <?php else: ?>
      	<tr class="incomplete">
            <td><?= $bi['item'].' '.$bi['description']; ?></td>
            <td class="text-center"><?= $bi['unit']['name']; ?></td>
            <td class="text-right"><?= moneda($bi['quantity']); ?></td>
            <td class="text-right"><?= moneda($bi['unity_price']); ?></td>
            <td class="text-right"><?= moneda($bi['total_price']); ?></td>
            <td><?= $bi['comments']; ?></td>
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
	endif;
endif; ?>