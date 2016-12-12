<div id="<?php echo $assistance_date ?>" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Rechazar Asistencia</h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(null, [
                    'url' => ['controller' => 'assists', 'action' => 'reject']
                ]); ?>
                <fieldset>
                    <?php
                        echo $this->Form->hidden('budget_id', ['value' => $budget_id]);
                        echo $this->Form->hidden('assistance_date', ['value' => $assistance_date]);
                        echo $this->Form->input('Approval.comment', ['type' => 'textarea', 'escape' => false, 'label' => 'Comentario del rechazo']);
                    ?>
                </fieldset>
            </div>
            <div class="modal-footer">
                <?= $this->Form->button(__('Guardar'), ['class' => 'btn btn-material-orange-900']) ?>
                <?= $this->Form->end() ?>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>