<ul class="breadcrumb" style="margin-bottom: 5px;">
	<li><?= $this->Html->link(__('Inicio'), ['controller' => 'users', 'action' => 'home'], []); ?></li>
	<li><?= $this->Html->link(__($second_breadcrumb['text']), ['controller' => $second_breadcrumb['controller'], 'action' => $second_breadcrumb['action']], ['class' => '']); ?></li>
	<li class="active"><?= __($third_breadcrumb); ?></li>
</ul>