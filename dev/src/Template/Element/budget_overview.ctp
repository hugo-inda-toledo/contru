<?php

$crv = 1; // Conversion Reference Value
if(isset($the_budget_currency_value)){
        $crv = $the_budget_currency_value;
}

if (!empty($bi['children'])) :
    $total = 0;
  	if (is_null($bi['parent_id'])) : ?>
    	<!-- padre  -->
        <tr class="success" data-type="parent">
            <td>   
                <strong>
                    <?php 
                        if(strlen($bi['description']) > 31)
                        {
                            echo $this->Html->tag('span', $bi['item'].' '.substr($bi['description'], 0, 30).'...', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => $bi['description']));
                        }
                        else
                        {
                            echo $bi['item'].' '.$bi['description']; 
                        }
                    ?>
                </strong>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-right"><?php
                if(isset($includeTotal) && $includeTotal)
                {
                    echo $this->Html->tag('span', moneda($bi['total_price']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Precio total: {0} ({1})', $bi['item'], $bi['description'])));
                }
            ?></td>
            <td class="text-right"><?php
                if(isset($includeTotal) && $includeTotal)
                {
                    echo $this->Html->tag('span', moneda($bi['target_value']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Presupuesto objetivo: {0} ({1})', $bi['item'], $bi['description'])));
                }
            ?></td>

            <td class="text-right">
                <?= $this->Html->tag('span', moneda($bi['commited']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Total comprometido: {0} ({1})', $bi['item'], $bi['description']))); ?>
            </td>
            <td class="text-right">
                <?= $this->Html->tag('span', moneda($bi['spent']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Total gastado: {0} ({1})', $bi['item'], $bi['description']))); ?>
            </td>
            <td class="text-right">
                <?= $this->Html->tag('span', moneda($bi['invoiced']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Total facturado: {0} ({1})', $bi['item'], $bi['description']))); ?>   
            </td>
            <td class="text-right">
                <?php 
                    if(($bi['target_value'] - $bi['commited']) < 0)
                    {
                        echo $this->Html->tag('span', moneda($bi['target_value'] - $bi['commited']), array('class' => 'text-danger')); 
                    }
                    else
                    {
                        echo moneda($bi['target_value'] - $bi['commited']); 
                    }
                ?>
            </td>
            <td class="text-right">
                <?php 
                    if(($bi['target_value'] - $bi['spent']) < 0)
                    {
                        echo $this->Html->tag('span', moneda($bi['target_value'] - $bi['spent']), array('class' => 'text-danger')); 
                    }
                    else
                    {
                        echo moneda($bi['target_value'] - $bi['spent']); 
                    }
                ?>
            </td>
            <td class="text-right">
                <?php 
                    if($bi['total_price'] - $bi['spent'] < 0)
                    {
                        echo $this->Html->tag('span', moneda($bi['total_price'] - $bi['spent']), array('class' => 'text-danger')); 
                    }
                    else
                    {
                        echo moneda($bi['total_price'] - $bi['spent']); 
                    }
                ?>
            </td>
            
            <td class="text-right">
                <?php
                    if($bi['percentage_proyected_progress'] != null)
                    {
                        //echo $bi['percentage_proyected_progress'].'%';
                        echo $this->Html->tag('span', $bi['percentage_proyected_progress'].'%', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('% de avance proyectado: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                    else
                    {
                        //echo '0%';
                        echo $this->Html->tag('span', '0%', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('% de avance proyectado: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                ?>
            </td>
            <td class="text-right">
                <?php
                    if($bi['percentage_overall_progress'] != null)
                    {
                        //echo $bi['percentage_overall_progress'].'%';
                        echo $this->Html->tag('span', $bi['percentage_overall_progress'].'%', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('% de avance real: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                    else
                    {
                        //echo '0%';
                        echo $this->Html->tag('span', '0%', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('% de avance real: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                ?>
            </td>
            <td class="text-right">
                <?php
                    if($bi['percentage_paid'] != null)
                    {
                        //echo $bi['percentage_paid'].'%';
                        echo $this->Html->tag('span', $bi['percentage_paid'].'%', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('% de avance financiero: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                    else
                    {
                        //echo '0%';
                        echo $this->Html->tag('span', '0%', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('% de avance financiero: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                ?>
            </td>
        </tr>
        <?php
    	foreach ($bi['children'] as $children) :
            $options = ['bi' => $children, 'the_budget_currency_value' => $crv];
            if(isset($includeTotal) && $includeTotal) $options['includeTotal'] = true;
       		echo $this->element('budget_overview', $options);
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
        <tr class="info">
            <td>   
                <strong>
                    <?php 
                        if(strlen($bi['description']) > 31)
                        {
                            echo $this->Html->tag('span', $bi['item'].' '.substr($bi['description'], 0, 30).'...', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => $bi['description']));
                        }
                        else
                        {
                            echo $bi['item'].' '.$bi['description']; 
                        }
                    ?>
                </strong>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-right"><?php
                if(isset($includeTotal) && $includeTotal){
                    //echo moneda($bi['total_price']);
                    echo $this->Html->tag('span', moneda($bi['total_price']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Precio total: {0} ({1})', $bi['item'], $bi['description'])));
                }
            ?></td>
            <td class="text-right"><?php
                if(isset($includeTotal) && $includeTotal){
                    //echo moneda($bi['target_value']);
                    echo $this->Html->tag('span', moneda($bi['target_value']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Presupuesto objetivo: {0} ({1})', $bi['item'], $bi['description'])));
                }
            ?></td>
           
            <td class="text-right">
                <?= $this->Html->tag('span', moneda($bi['commited']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Total comprometido: {0} ({1})', $bi['item'], $bi['description']))); ?>
            </td>
            <td class="text-right">
                <?= $this->Html->tag('span', moneda($bi['spent']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Total gastado: {0} ({1})', $bi['item'], $bi['description']))); ?>
                <br>
            </td>
            <td class="text-right">
                <?= $this->Html->tag('span', moneda($bi['invoiced']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Total facturado: {0} ({1})', $bi['item'], $bi['description']))); ?>   
            </td>
            <td class="text-right">
                <?php 
                    if(($bi['target_value'] - $bi['commited']) < 0)
                    {
                        echo $this->Html->tag('span', moneda($bi['target_value'] - $bi['commited']), array('class' => 'text-danger')); 
                    }
                    else
                    {
                        echo moneda($bi['target_value'] - $bi['commited']); 
                    }
                ?>
            </td>
            <td class="text-right">
                <?php 
                    if(($bi['target_value'] - $bi['spent']) < 0)
                    {
                        echo $this->Html->tag('span', moneda($bi['target_value'] - $bi['spent']), array('class' => 'text-danger')); 
                    }
                    else
                    {
                        echo moneda($bi['target_value'] - $bi['spent']); 
                    }
                ?>
            </td>
            <td class="text-right">
                <?php 
                    if($bi['total_price'] - $bi['spent'] < 0)
                    {
                        echo $this->Html->tag('span', moneda($bi['total_price'] - $bi['spent']), array('class' => 'text-danger')); 
                    }
                    else
                    {
                        echo moneda($bi['total_price'] - $bi['spent']); 
                    }
                ?>
            </td>
            <td class="text-right">
                <?php
                    if($bi['percentage_proyected_progress'] != null)
                    {
                        //echo $bi['percentage_proyected_progress'].'%';
                        echo $this->Html->tag('span', $bi['percentage_proyected_progress'].'%', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('% de avance proyectado: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                    else
                    {
                        //echo '0%';
                        echo $this->Html->tag('span', '0%', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('% de avance proyectado: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                ?>
            </td>
            <td class="text-right">
                <?php
                    if($bi['percentage_overall_progress'] != null)
                    {
                        //echo $bi['percentage_overall_progress'].'%';
                        echo $this->Html->tag('span', $bi['percentage_overall_progress'].'%', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('% de avance real: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                    else
                    {
                        //echo '0%';
                        echo $this->Html->tag('span', '0%', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('% de avance real: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                ?>
            </td>
            <td class="text-right">
                <?php
                    if($bi['percentage_paid'] != null)
                    {
                        //echo $bi['percentage_paid'].'%';
                        echo $this->Html->tag('span', $bi['percentage_paid'].'%', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('% de avance financiero: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                    else
                    {
                        //echo '0%';
                        echo $this->Html->tag('span', '0%', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('% de avance financiero: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                ?>
            </td>
        </tr>
        <?php
        foreach ($bi['children'] as $children):
            $total += $children['total_price'];
            $options = ['bi' => $children, 'the_budget_currency_value' => $crv];
            if(isset($includeTotal) && $includeTotal) $options['includeTotal'] = true;
            echo $this->element('budget_overview', $options);
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
        	<td class="tachado">
                <span class="label label-warning">Ítem deshabilitado</span>
                <?php 
                    if(strlen($bi['description']) > 31)
                    {
                        echo $this->Html->tag('span', $bi['item'].' '.substr($bi['description'], 0, 30).'...', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => $bi['description']));
                    }
                    else
                    {
                        echo $bi['item'].' '.$bi['description']; 
                    }
                ?>
            </td>
        	<td>
                <?php echo $this->Html->tag('span', $bi['unit']['name'], array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Unidad: {0} ({1})', $bi['item'], $bi['description'])));?>   
            </td>
            <td class="text-right">
                <?php echo $this->Html->tag('span', moneda($bi['quantity']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Cantidad: {0} ({1})', $bi['item'], $bi['description'])));?>          
            </td> 
            <td class="text-right">
                <?php echo $this->Html->tag('span', moneda($bi['unity_price']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Precio unitario: {0} ({1})', $bi['item'], $bi['description'])));?>   
            </td>
            <td class="text-right">
                <?php echo $this->Html->tag('span', moneda($bi['total_price']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Precio total: {0} ({1})', $bi['item'], $bi['description'])));?>   
            </td>
            <td class="text-right">
                <?php 
                    echo $this->Html->tag('span', moneda($bi['target_value']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Presupuesto objetivo: {0} ({1})', $bi['item'], $bi['description'])));
                ?>  
            </td>
            
            <td class="text-right">
                <?= $this->Html->tag('span', moneda($bi['commited']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Total comprometido: {0} ({1})', $bi['item'], $bi['description']))); ?>
            </td>
            <td class="text-right">
                <?= $this->Html->tag('span', moneda($bi['spent']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Total gastado: {0} ({1})', $bi['item'], $bi['description']))); ?>
            </td>
            <td class="text-right">
                <?= $this->Html->tag('span', moneda($bi['invoiced']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Total facturado: {0} ({1})', $bi['item'], $bi['description']))); ?>
            </td>
            <td class="text-right">
                <?php 
                    if(($bi['target_value'] - $bi['commited']) < 0)
                    {
                        echo $this->Html->tag('span', moneda($bi['target_value'] - $bi['commited']), array('class' => 'text-danger')); 
                    }
                    else
                    {
                        echo moneda($bi['target_value'] - $bi['commited']); 
                    }
                ?>
            </td>
            <td class="text-right">
                <?php 
                    if(($bi['target_value'] - $bi['spent']) < 0)
                    {
                        echo $this->Html->tag('span', moneda($bi['target_value'] - $bi['spent']), array('class' => 'text-danger')); 
                    }
                    else
                    {
                        echo moneda($bi['target_value'] - $bi['spent']); 
                    }
                ?>
            </td>
            <td class="text-right">
                <?php 
                    if($bi['total_price'] - $bi['spent'] < 0)
                    {
                        echo $this->Html->tag('span', moneda($bi['total_price'] - $bi['spent']), array('class' => 'text-danger')); 
                    }
                    else
                    {
                        echo moneda($bi['total_price'] - $bi['spent']); 
                    }
                ?>
            </td>

            <td class="text-right">
                <?php
                    if($bi['percentage_proyected_progress'] != null)
                    {
                        //echo $bi['percentage_proyected_progress'].'%';
                        echo $this->Html->tag('span', $bi['percentage_proyected_progress'].'%', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('% de avance proyectado: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                    else
                    {
                        //echo '0%';
                        echo $this->Html->tag('span', '0%', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('% de avance proyectado: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                ?>
            </td>
            <td class="text-right">
                <?php
                    if($bi['percentage_overall_progress'] != null)
                    {
                        //echo $bi['percentage_overall_progress'].'%';
                        echo $this->Html->tag('span', $bi['percentage_overall_progress'].'%', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('% de avance real: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                    else
                    {
                        //echo '0%';
                        echo $this->Html->tag('span', '0%', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('% de avance real: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                ?>
            </td>
            <td class="text-right">
                <?php
                    if($bi['percentage_paid'] != null)
                    {
                        //echo $bi['percentage_paid'].'%';
                        echo $this->Html->tag('span', $bi['percentage_paid'].'%', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('% de avance financiero: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                    else
                    {
                        //echo '0%';
                        echo $this->Html->tag('span', '0%', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('% de avance financiero: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                ?>
            </td>
      	</tr>
	<?php elseif ($done) : ?>
      	<tr class="done hidden">
          	<td>
                <span class="label label-success">Completado</span>
                <?php 
                    if(strlen($bi['description']) > 31)
                    {
                        echo $this->Html->tag('span', $bi['item'].' '.substr($bi['description'], 0, 30).'...', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => $bi['description']));
                    }
                    else
                    {
                        echo $bi['item'].' '.$bi['description']; 
                    }
                ?>
            </td>
          	<td>
                <?php echo $this->Html->tag('span', $bi['unit']['name'], array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Unidad: {0} ({1})', $bi['item'], $bi['description'])));?>   
            </td>
            <td class="text-right">
                <?php echo $this->Html->tag('span', moneda($bi['quantity']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Cantidad: {0} ({1})', $bi['item'], $bi['description'])));?>          
            </td> 
            <td class="text-right">
                <?php echo $this->Html->tag('span', moneda($bi['unity_price']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Precio unitario: {0} ({1})', $bi['item'], $bi['description'])));?>   
            </td>
            <td class="text-right">
                <?php echo $this->Html->tag('span', moneda($bi['total_price']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Precio total: {0} ({1})', $bi['item'], $bi['description'])));?>   
            </td>
            <td class="text-right">
                <?php 
                    echo $this->Html->tag('span', moneda($bi['target_value']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Presupuesto objetivo: {0} ({1})', $bi['item'], $bi['description'])));
                ?>  
            </td>
            
            <td class="text-right">
                <?= $this->Html->tag('span', moneda($bi['commited']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Total comprometido: {0} ({1})', $bi['item'], $bi['description']))); ?>
            </td>
            <td class="text-right">
                <?= $this->Html->tag('span', moneda($bi['spent']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Total gastado: {0} ({1})', $bi['item'], $bi['description']))); ?>
            </td>
            <td class="text-right">
                <?= $this->Html->tag('span', moneda($bi['invoiced']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Total facturado: {0} ({1})', $bi['item'], $bi['description']))); ?>   
            </td>
            <td class="text-right">
                <?php 
                    if(($bi['target_value'] - $bi['commited']) < 0)
                    {
                        echo $this->Html->tag('span', moneda($bi['target_value'] - $bi['commited']), array('class' => 'text-danger')); 
                    }
                    else
                    {
                        echo moneda($bi['target_value'] - $bi['commited']); 
                    }
                ?>
            </td>
            <td class="text-right">
                <?php 
                    if(($bi['target_value'] - $bi['spent']) < 0)
                    {
                        echo $this->Html->tag('span', moneda($bi['target_value'] - $bi['spent']), array('class' => 'text-danger')); 
                    }
                    else
                    {
                        echo moneda($bi['target_value'] - $bi['spent']); 
                    }
                ?>
            </td>
            <td class="text-right">
                <?php 
                    if($bi['total_price'] - $bi['spent'] < 0)
                    {
                        echo $this->Html->tag('span', moneda($bi['total_price'] - $bi['spent']), array('class' => 'text-danger')); 
                    }
                    else
                    {
                        echo moneda($bi['total_price'] - $bi['spent']); 
                    }
                ?>
            </td>

            <td class="text-right">
                <?php
                    if($bi['percentage_proyected_progress'] != null)
                    {
                        //echo $bi['percentage_proyected_progress'].'%';
                        echo $this->Html->tag('span', $bi['percentage_proyected_progress'].'%', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('% de avance proyectado: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                    else
                    {
                        //echo '0%';
                        echo $this->Html->tag('span', '0%', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('% de avance proyectado: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                ?>
            </td>
            <td class="text-right">
                <?php
                    if($bi['percentage_overall_progress'] != null)
                    {
                        //echo $bi['percentage_overall_progress'].'%';
                        echo $this->Html->tag('span', $bi['percentage_overall_progress'].'%', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('% de avance real: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                    else
                    {
                        //echo '0%';
                        echo $this->Html->tag('span', '0%', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('% de avance real: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                ?>
            </td>
            <td class="text-right">
                <?php
                    if($bi['percentage_paid'] != null)
                    {
                        //echo $bi['percentage_paid'].'%';
                        echo $this->Html->tag('span', $bi['percentage_paid'].'%', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('% de avance financiero: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                    else
                    {
                        //echo '0%';
                        echo $this->Html->tag('span', '0%', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('% de avance financiero: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                ?>
            </td>
      	</tr>
    <?php else: ?>
        <?php
            $extraClass="";
            if($bi['parent_id'] == null && ($bi['extra'] == 1 || $bi['extra'] == 2)){
                $extraClass=" info";
            }
        ?>
      	<tr class="incomplete<?=$extraClass?> active">
            <td>
                <?php if($extraClass!=""):?>

                    <strong>
                        <?php 
                            if(strlen($bi['description']) > 31)
                            {
                                echo $this->Html->tag('span', $bi['item'].' '.substr($bi['description'], 0, 30).'...', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => $bi['description']));
                            }
                            else
                            {
                                echo $bi['item'].' '.$bi['description']; 
                            }
                        ?>
                    </strong>

                <?php else: ?>

                    <?php 
                        if(strlen($bi['description']) > 31)
                        {
                            echo $this->Html->tag('span', $bi['item'].' '.substr($bi['description'], 0, 30).'...', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => $bi['description']));
                        }
                        else
                        {
                            echo $bi['item'].' '.$bi['description']; 
                        }
                    ?>

                <?php endif; ?>
            </td>
            
            <td>
                <?php 
                    if(isset($bi['unit']['name']))
                    {
                        echo $this->Html->tag('span', $bi['unit']['name'], array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Unidad: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                ?>   
            </td>
            <td class="text-right">
                <?php echo $this->Html->tag('span', moneda($bi['quantity']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Cantidad: {0} ({1})', $bi['item'], $bi['description'])));?>          
            </td> 
            <td class="text-right">
                <?php echo $this->Html->tag('span', moneda($bi['unity_price']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Precio unitario: {0} ({1})', $bi['item'], $bi['description'])));?>   
            </td>
            <td class="text-right">
                <?php echo $this->Html->tag('span', moneda($bi['total_price']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Precio total: {0} ({1})', $bi['item'], $bi['description'])));?>   
            </td>
            <td class="text-right">
                <?php 
                    echo $this->Html->tag('span', moneda($bi['target_value']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Presupuesto objetivo: {0} ({1})', $bi['item'], $bi['description'])));
                ?>  
            </td>
            <td class="text-right">
                <?= $this->Html->tag('span', moneda($bi['commited']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Total comprometido: {0} ({1})', $bi['item'], $bi['description']))); ?>
            </td>
            <td class="text-right">
                <?= $this->Html->tag('span', moneda($bi['spent']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Total gastado: {0} ({1})', $bi['item'], $bi['description']))); ?>
            </td>
            <td class="text-right">
                <?= $this->Html->tag('span', moneda($bi['invoiced']), array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Total facturado: {0} ({1})', $bi['item'], $bi['description']))); ?>   
            </td>
            <td class="text-right">
                <?php 
                    if(($bi['target_value'] - $bi['commited']) < 0)
                    {
                        echo $this->Html->tag('span', moneda($bi['target_value'] - $bi['commited']), array('class' => 'text-danger')); 
                    }
                    else
                    {
                        echo moneda($bi['target_value'] - $bi['commited']); 
                    }
                ?>
            </td>
            <td class="text-right">
                <?php 
                    if(($bi['target_value'] - $bi['spent']) < 0)
                    {
                        echo $this->Html->tag('span', moneda($bi['target_value'] - $bi['spent']), array('class' => 'text-danger')); 
                    }
                    else
                    {
                        echo moneda($bi['target_value'] - $bi['spent']); 
                    }
                ?>
            </td>
            <td class="text-right">
                <?php 
                    if($bi['total_price'] - $bi['spent'] < 0)
                    {
                        echo $this->Html->tag('span', moneda($bi['total_price'] - $bi['spent']), array('class' => 'text-danger')); 
                    }
                    else
                    {
                        echo moneda($bi['total_price'] - $bi['spent']); 
                    }
                ?>
            </td>
            <td class="text-right">
                <?php
                    if($bi['percentage_proyected_progress'] != null)
                    {
                        echo $this->Html->tag('span', $bi['percentage_proyected_progress'].'%', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('% de avance proyectado: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                    else
                    {
                        echo $this->Html->tag('span', '0%', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('% de avance proyectado: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                ?>
            </td>
            <td class="text-right">
                <?php
                    if($bi['percentage_overall_progress'] != null)
                    {
                        echo $this->Html->tag('span', $bi['percentage_overall_progress'].'%', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('% de avance real: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                    else
                    {
                        echo $this->Html->tag('span', '0%', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('% de avance real: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                ?>
            </td>
            <td class="text-right">
                <?php
                    if($bi['percentage_paid'] != null)
                    {
                        echo $this->Html->tag('span', $bi['percentage_paid'].'%', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('% de avance financiero: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                    else
                    {
                        echo $this->Html->tag('span', '0%', array('class' => 'pointer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('% de avance financiero: {0} ({1})', $bi['item'], $bi['description'])));
                    }
                ?>
            </td>
        </tr>
        <tr>
            <td>Materiales</td>
            <td colspan="5"></td>
            <td class="text-right">
                <?php
                    if($bi['commited_materials']  > 0)
                    {
                        echo $this->Html->link($this->Html->tag('strong', moneda($bi['commited_materials'])), '/spends/purchasedMaterialsDetails/'.$bi['id'] , array('escape' => false,  'target' => '_blank', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Materiales comprometidos: {0} ({1})', $bi['item'], $bi['description']), 'style' => 'color:#009688;'));
                    }
                    else
                    {
                        echo moneda($bi['commited_materials']);
                    }
                ?> 
            </td>
            <td class="text-right">
                <?php
                    if($bi['spent_materials'] > 0)
                    {
                        echo $this->Html->link($this->Html->tag('strong', moneda($bi['spent_materials'])), '/spends/usedMaterialsDetails/'.$bi['id'] , array('escape' => false,  'target' => '_blank', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Materiales gastados: {0} ({1})', $bi['item'], $bi['description']), 'style' => 'color:#009688;'));
                    }
                    else
                    {
                        echo moneda($bi['spent_materials']);
                    }
                ?>
            </td>
            <td class="text-right">
                <?php
                    if($bi['invoiced_materials'] > 0)
                    {
                        echo $this->Html->link($this->Html->tag('strong', moneda($bi['invoiced_materials'])), '/spends/factMaterialsDetails/'.$bi['id'] , array('escape' => false,  'target' => '_blank', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Materiales facturados: {0} ({1})', $bi['item'], $bi['description']), 'style' => 'color:#009688;'));
                    }
                    else
                    {
                        echo moneda($bi['invoiced_materials']);
                    }
                ?> 
            </td>
            <td colspan="6"></td>
        </tr>
        <tr>
            <td>Subcontratos</td>
            <td colspan="5"></td>
            <td class="text-right">
                <?php
                    if($bi['commited_subcontracts'] > 0)
                    {
                        echo $this->Html->link($this->Html->tag('strong', moneda($bi['commited_subcontracts'])), '/spends/subcontractsDetails/'.$bi['id'] , array('escape' => false,  'target' => '_blank', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Subcontratos comprometidos: {0} ({1})', $bi['item'], $bi['description']), 'style' => 'color:#009688;'));
                    }
                    else
                    {
                        echo moneda($bi['commited_subcontracts']);
                    }
                ?>
            </td>
            <td class="text-right">
                <?php
                    if($bi['spent_subcontracts'] > 0)
                    {
                        echo $this->Html->link($this->Html->tag('strong', moneda($bi['spent_subcontracts'])), '/spends/usedSubcontractsDetails/'.$bi['id'] , array('escape' => false,  'target' => '_blank', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Subcontratos gastados: {0} ({1})', $bi['item'], $bi['description']), 'style' => 'color:#009688;'));
                    }
                    else
                    {
                        echo moneda($bi['spent_subcontracts']);
                    }
                ?>
            </td>
            <td class="text-right">
                <?php
                    if($bi['invoiced_subcontracts'] > 0)
                    {
                        echo $this->Html->link($this->Html->tag('strong', moneda($bi['invoiced_subcontracts'])), '/spends/factSubcontractsDetails/'.$bi['id'] , array('escape' => false,  'target' => '_blank', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => __('Subcontratos facturados: {0} ({1})', $bi['item'], $bi['description']), 'style' => 'color:#009688;'));
                    }
                    else
                    {
                        echo moneda($bi['invoiced_subcontracts']);
                    }
                ?>
            </td>
            <td colspan="6"></td>
        </tr>
        <tr>
            <td>Mano de Obra</td>
            <td colspan="5"></td>
            <td><!--sueldos--></td>
            <td><!--sueldos pagados--></td>
            <td><!--n/a--></td>
            <td colspan="6"></td>
        </tr>
	<?php
	endif;
endif; ?>