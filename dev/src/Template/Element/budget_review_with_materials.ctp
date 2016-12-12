<?php

$crv = 1; // Conversion Reference Value
if(isset($the_budget_currency_value)){
        $crv = $the_budget_currency_value;
}

if (!empty($bi['children'])) :
    $total = 0;
  	if (is_null($bi['parent_id'])) : ?>
    	<!-- padre  -->
        <tr class="info" data-type="parent">
            <td></td>
            <td><strong><?= $bi['item'].' '.$bi['description']; ?></strong></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-right"><?php
                if(isset($includeTotal) && $includeTotal){
                    echo moneda($bi['total_price']);
                }
            ?></td>
            <td class="text-right"><?php
                if(isset($includeTotal) && $includeTotal){
                    echo moneda($bi['target_value']);
                }
            ?></td>
        </tr>
        <?php
    	foreach ($bi['children'] as $children) :
            $options = ['bi' => $children, 'the_budget_currency_value' => $crv];
            if(isset($includeTotal) && $includeTotal) $options['includeTotal'] = true;
       		echo $this->element('budget_review_with_materials', $options);
     	endforeach;
        /*if(isset($includeTotal) && $includeTotal):
        ?>
            <tr class="warning">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right">TOTAL <?=$bi['description']?>: </td>
                <td class="text-right"><?=moneda($bi['total_price']);?></td>
                <td></td>
                <td></td>
            </tr>
        <?php
        endif;*/
	 else: ?>
        <tr>
            <td></td>
            <td><strong><?= $bi['item'].' '.$bi['description']; ?></strong></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-right"><?php
                if(isset($includeTotal) && $includeTotal){
                    echo moneda($bi['total_price']);
                }
            ?></td>
            <td class="text-right"><?php
                if(isset($includeTotal) && $includeTotal){
                    echo moneda($bi['target_value']);
                }
            ?></td>

            <td class="text-center">
                <?php echo $this->cell('Spend::verifiedIfHaveUsedMaterials', [$bi['id']]);?>
            </td>
            <td class="text-center">
                <?php echo $this->cell('Spend::verifiedIfHavePurchasedMaterials', [$bi['id']]);?>
            </td>
            <td class="text-center">
                <?php echo $this->cell('Spend::verifiedIfHaveSubcontracts', [$bi['id']]);?>
            </td>
        </tr>
        <?php
        foreach ($bi['children'] as $children):
            $total += $children['total_price'];
            $options = ['bi' => $children, 'the_budget_currency_value' => $crv];
            if(isset($includeTotal) && $includeTotal) $options['includeTotal'] = true;
            echo $this->element('budget_review_with_materials', $options);
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
            <td></td>
        	<td class="tachado"><span class="label label-warning">Ítem deshabilitado</span><?= ' ' . $bi['item'].' '.$bi['description']; ?></td>
        	<td><?= $bi['unit']['name']; ?></td>
            <td class="text-right"><?= moneda($bi['quantity']); ?></td>
            <td class="text-right"><?= moneda($bi['unity_price']); ?></td>
            <td class="text-right"><?= moneda($bi['total_price']); ?></td>
            <td class="text-right"><?= moneda($bi['target_value']); ?></td>

      	</tr>
	<?php elseif ($done) : ?>
      	<tr class="done hidden">
          	<td></td>
          	<td><span class="label label-success">Completado</span><?= ' ' . $bi['item'].' '.$bi['description']; ?></td>
          	<td><?= $bi['unit']['name']; ?></td>
          	<td class="text-right"><?= moneda($bi['quantity']); ?></td>
            <td class="text-right"><?= moneda($bi['unity_price']); ?></td>
            <td class="text-right"><?= moneda($bi['total_price']); ?></td>
            <td class="text-right"><?= moneda($bi['target_value']); ?></td>


      	</tr>
    <?php else: ?>
        <?php
            $extraClass="";
            if($bi['parent_id'] == null && ($bi['extra'] == 1 || $bi['extra'] == 2)){
                $extraClass=" info";
            }
        ?>
      	<tr class="incomplete<?=$extraClass?>">
        	<td>

            </td>
            <td>
                <?php if($extraClass!=""){ ?>
                    <strong><?= $bi['item'].' '.$bi['description']; ?></strong>
                <?php }else{ ?>
                    <?= $bi['item'].' '.$bi['description']; ?>
                <?php } ?>
            </td>
            <td><?= isset($bi['unit']['name']) ? $bi['unit']['name'] : ''; ?></td>
            <td class="text-right"><?= moneda($bi['quantity']); ?></td>
            <td class="text-right"><?= moneda($bi['unity_price']); ?></td>
            <td class="text-right"><?= moneda($bi['total_price']); ?></td>
            <td class="text-right"><?= moneda($bi['target_value']); ?></td>

            <td class="text-center">
                <?php echo $this->cell('Spend::verifiedIfHaveUsedMaterials', [$bi['id']]);?>
            </td>
            <td class="text-center">
                <?php echo $this->cell('Spend::verifiedIfHavePurchasedMaterials', [$bi['id']]);?>
            </td>
            <td class="text-center">
                <?php echo $this->cell('Spend::verifiedIfHaveSubcontracts', [$bi['id']]);?>
            </td>
        </tr>
	<?php
	endif;
endif; ?>