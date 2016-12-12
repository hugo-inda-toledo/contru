<div id="<?php echo $schedule_id ?>" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title"><?= $title_modal ?></h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(null, [
                    'url' => ['controller' => $controller, 'action' => $action]
                ]); ?>
                <fieldset>
                    <?php
                        echo $this->Form->hidden('schedule_id', ['value' => $schedule_id]);
                        echo $this->Form->input('Approval.comment', ['type' => 'textarea', 'escape' => false, 'label' => 'Comentario del rechazo']);
                    ?>
                </fieldset>
            </div>
            <div class="modal-footer">
                <?= $this->Form->button(__('Guardar'), ['class' => 'btn btn-material-orange-900']) ?>
                <?= $this->Form->end() ?>
                <button type="button" class="btn btn-flat btn-link" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>