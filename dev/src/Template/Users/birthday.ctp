<?php
    $arrayMeses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
?>
<div class="grid">
    <div class="row cells3">
        <div class="cell">
            <div class="grid">
                <div class="row cells2">
                <?php if ( $users->count() == 1 ) : ?>
                    <?php foreach ($users as $user): ?>
                        <div class="row cells1">
                                <div class="cell" style="text-align:center">
                                    <?php
                                        // si no existe la foto, coloca una de reemplazo
                                        $ruta_imagen = WWW_ROOT . 'files' . DS . 'users' . DS . 'photo' . DS . $user->photo_dir . DS . '40x40_' . $user->photo;
                                        if (! file_exists($ruta_imagen)) 
                                            $ruta_imagen = DS . 'img' . DS . 'default_user' . DS . '40x40.png'; 
                                        else 
                                            $ruta_imagen = $this->Url->build('/', true) . 'files' . DS . 'users' . DS . 'photo' . DS . $user->photo_dir . DS . '40x40_' . $user->photo;
                                        ?>
                                    <?= $this->Html->image($ruta_imagen, ['alt' => $user->photo, 'class' => 'place-center']); ?> &nbsp;
                                </div>
                                <br><br><br><br>
                                <div class="cell" style="text-align:center">
                                    <?= h($user->name) ?> <?= h($user->last_name) ?>
                                    <?= h($user->birth_date->format('d/m')) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <?php 
                        foreach ($users as $val => $user): ?>
                        <?php if($val == 2) break; ?>
                            <div class="cell" style="text-align:center">
                                <?php
                                    // si no existe la foto, coloca una de reemplazo
                                    $ruta_imagen = WWW_ROOT . 'files' . DS . 'users' . DS . 'photo' . DS . $user->photo_dir . DS . '40x40_' . $user->photo;
                                    if (! file_exists($ruta_imagen)) 
                                        $ruta_imagen = DS . 'img' . DS . 'default_user' . DS . '40x40.png'; 
                                    else 
                                        $ruta_imagen = $this->Url->build('/', true) . 'files' . DS . 'users' . DS . 'photo' . DS . $user->photo_dir . DS . '40x40_' . $user->photo;
                                    ?>
                                <?= $this->Html->image($ruta_imagen, ['alt' => $user->photo, 'class' => 'place-center']); ?> &nbsp;
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div style="text-align:center"><strong><?= $arrayMeses[date('m')-1]; ?></strong></div>
                    <br>
                    <?php foreach ($users as $val => $user): ?>
                        <?php if($val == 2) break; ?>
                        <div class="row cells2">
                            <div class="cell" >
                                <?= h($user->name) ?> <?= h($user->last_name) ?>
                            </div>
                            <div class="cell">
                                <?= h($user->birth_date->format('d/m')) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php
                    $count = 0;
                    foreach ($users as $user) : ?>
                        <?php if( $user->birth_date->format('d') == date('d')) : ?>
                            <?php if( !$count == 1 ) : ?>
                                <br>
                                <strong>Cumpleaños de hoy</strong><br><br>
                            <?php 
                            $count++ ;
                            endif; ?>
                            <div class="row cells1">
                                <div class="cell">
                                    <?= h($user->name) ?> <?= h($user->last_name) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>