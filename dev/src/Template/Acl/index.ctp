<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo Perfiles de Usuarios'));
$this->assign('title_icon', 'users');
$buttons = array();
$this->set('buttons', $buttons);
?>


<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">
            Administración Permisos de Perfiles de Usuarios
        </h3>
    </div>
    <div class="panel-body">
        <?= $this->Form->create(null, ['id' => 'formAddPerm','action' => 'AddPermissions']); ?>
        <?= $this->Form->hidden('group_id',['id' => 'group']);?>
        <?= $this->Form->hidden('aco_id',['id' => 'aco']); ?>
        <?= $this->Form->hidden('permission',['id' => 'val']); ?>
        <?= $this->Form->end() ?>        
        

        <table class="table table-hover text-center">
            <thead>
                <tr>
                    <th><?= __('Función') ?></th>
                    <?php foreach ($groups as $key => $group): ?>
                        <th><?= $group->name ?></th>
                    <?php endforeach ?>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($acos as $aco): ?>
                <?php if (!($aco->alias == 'App' || $aco->parent_id == 2)): ?>            
                            <?php if ($aco->parent_id == 1 || $aco->id == 1): ?>
                                <tr parent="<?php echo $aco->id ?>" class="info">
                                    <td>
                                    -><?= h($aco->alias) ?>/
                                <?php else: ?>
                                    <tr child="<?php echo $aco->parent_id ?>" style="display: none;">
                                        <td>
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= h($aco->alias) ?>/
                            <?php endif ?>
                            </td>
                        <?php foreach ($groups as $group): ?>                    
                            <?php $flag = 0; ?>
                                <?php foreach ($aco->acos_groups as $ag): ?>
                                    <?php if ($ag->group_id == $group->id): ?>                           
                                        <?php if ($ag->permission): ?>                                        
                                            <td>
                                                <i data-group="<?php echo $group->id; ?>" data-aco="<?php echo $aco->id; ?>" data-val="<?php echo $ag->permission; ?>" name="sel_group" class="tiny mdi-action-done mdi-success swa"></i>
                                            </td>    
                                        <?php else: ?>
                                            <td>
                                                <i data-group="<?php echo $group->id; ?>" data-aco="<?php echo $aco->id; ?>" data-val=0 name="sel_group" class="tiny mdi-action-highlight-remove mdi-danger swa"></i>
                                            </td>
                                        <?php endif ?>
                                        <?php $flag = 1;?>
                                        <?php break; ?>
                                    <?php endif ?>
                                <?php endforeach ?>
                                <?php if($flag == 0): ?>
                                    <td>
                                        <i data-group="<?php echo $group->id; ?>" data-aco="<?php echo $aco->id; ?>" data-val="" name="sel_group" class="tiny mdi-action-help mdi-material-grey-400 swa"></i>
                                    </td>
                                <?php endif ?>
                        <?php endforeach ?>            
                    </tr>
                <?php endif ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
      $(document).on('click','tr td:first-child',function(){
        if($(this).parent().attr('parent') ) {
            if($("tr[child='" + $(this).parent().attr('parent') + "'").is(':hidden')) {
                $("tr[child='" + $(this).parent().attr('parent') + "'").fadeIn();    
            } else {
                $("tr[child='" + $(this).parent().attr('parent') + "'").fadeOut();    
            }
        }
      });
      $(document).on('click','.swa',function(){
            var sel = $(this);
            var tag = 0;
            if(sel.attr('class').search('help') != -1 && tag == 0) {
                sel.attr('class',sel.attr('class').replace('help mdi-material-grey-400','done mdi-success'));
                sel.attr('data-val','1');
                tag = 1;
            }
            
            if(sel.attr('class').search('highlight-remove') != -1 && tag == 0) {
                sel.attr('class',sel.attr('class').replace('highlight-remove mdi-danger','help mdi-material-grey-400'));
                sel.attr('data-val','2');
                tag = 1;
            }
            
            if(sel.attr('class').search('done') != -1 && tag == 0) {
                sel.attr('class',sel.attr('class').replace('done mdi-success','highlight-remove mdi-danger'));
                sel.attr('data-val','0');
                tag = 1;
            }
            
            var val = sel.attr('data-val');
            var group = sel.attr('data-group');
            var aco = sel.attr('data-aco');

            $.ajax({
                   url: 'Acl/addPermissions',
                   type: 'POST',
                   data: {  group_id : group,
                            aco_id : aco,
                            permission : val
                        },
                    success: function(response) {
                        console.log(response);
                        },
                    error: function(response) {
                        console.log(response);
                        }
               });
            if(sel.closest('tr').attr('parent')) {
                var parent = sel.closest('tr').attr('parent');
                var tdIndex = sel.closest('td').index();
                $("tr[child='" + parent + "']").each(function() {
                    $(this).find('td:eq(' + tdIndex + ') .swa').trigger("click");
                });
            }      
        });
    });
</script>
