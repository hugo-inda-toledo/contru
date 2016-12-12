<ul class="pagination">
	<?= $this->Paginator->first(__('Primeros registros')) ?>
	<?= $this->Paginator->prev(__('« Anterior')) ?>
	<?= $this->Paginator->numbers() ?>
	<?= $this->Paginator->next(__('Siguiente »')) ?>
	<?= $this->Paginator->last(__(' Últimos registros')) ?>
</ul>
<p><?= $this->Paginator->counter() ?></p>