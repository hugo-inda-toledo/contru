<?php use Cake\Core\Configure; ?>
<?php $menu = Configure::read('menu_usuarios.'.$this->request->session()->read('Auth.User.group_id')); ?>
<?php if (!empty($menu)) : ?>
    <nav id="topbar" class="navbar navbar-material-blue-grey-700 shadow-z-2">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <?= $this->Html->image('logo_light.png', ['alt' => 'LDZ', 'class' => 'img-responsive topbar_logo', 'url' => '/']); ?>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <?php foreach ($menu as $menu_items) :
                        if (!empty($menu_items['items'])) : ?>
                            <li class="dropdown">
                                <a class="ripple-effect dropdown-toggle" href="#" data-toggle="dropdown" data-placement="right"
                                 data-original-title="<?= (!empty($menu_items['title'])) ? 'Módulo ' . $menu_items['title'] : '' ?>" aria-expanded="false">
                                    <i class="<?= $menu_items['icon'] ?>"></i><?= ' ' . $menu_items['title'] ?>
                                    <b class="caret"></b>
                                </a>
                                <ul class="dropdown-menu btn-material-blue-grey-500">
                                    <?php foreach ($menu_items['items'] as $menu_item) : ?>
                                        <li>
                                           <?= $this->Html->link(' ' . $menu_item['title'],
                                                ['controller' => $menu_item['controller'], 'action' => $menu_item['action']],
                                                ['escape' => false, 'tabindex' => '-1']); ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        <?php else : ?>
                            <li class="dropdown">
                                <?= $this->Html->link('<i class="' . $menu_items['icon'] . '"></i>' . ' ' . $menu_items['title'],
                                    ['controller' => $menu_items['controller'], 'action' => $menu_items['action']],
                                    ['escape' => false, 'class' => 'btn-material-blue-grey-700',
                                     'data-toggle' => 'tooltip', 'data-placement' => 'right', 'data-original-title' => $menu_items['title']]); ?>
                                    <!-- <span class="sidebar-badge badge-circle">i</span> -->
                            </li>
                        <?php endif;
                    endforeach;
                    if(!empty($last_building_info)):
                        $last_building_info_url_dashboard = $this->Url->build(['controller' => 'buildings', 'action' => 'dashboard', $this->request->session()->read('Config.last_building_sf_id')]);
                        $last_building_info_url_close = $this->Url->build(['controller' => 'buildings', 'action' => 'current', 'none']);
                    ?>
                        <li>
                            <div id="last_building_selected" style="margin-top: 15px;">
                                <!-- <button class="btn btn-default"> -->
                                    <span href="#" class="label label-default" data-toggle="tooltip" data-placement="bottom" data-original-title="Ir al Dashboard" data-url="<?=$last_building_info_url_dashboard?>" style="padding: 7px;cursor: pointer;"><?= $last_building_info; ?></span>
                                    <span href="#" class="label label-default" data-toggle="tooltip" data-placement="bottom" data-original-title="Cerrar Sesión obra" data-url="<?=$last_building_info_url_close?>" style="padding: 7px;
    margin-left: -3px;cursor: pointer;">×</span>
                                <!-- </button> -->
                            </div>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="nav navbar-nav navbar-right topbar_user">
                    <li>
                        <p class="navbar-text text-right">
                            <?php $full_name = sprintf("%s %s %s", $this->request->session()->read('Auth.User.first_name'), $this->request->session()->read('Auth.User.lastname_f'), $this->request->session()->read('Auth.User.lastname_m')); ?>
                            <?= $this->Html->link((strlen($full_name) > 24) ? substr($full_name, 0, 20) + '...' : $full_name,
                                ['controller' => 'users', 'action' => 'view', $this->request->session()->read('Auth.User.id')] ); ?>
                            <br/>
                            <em><?= $this->Access->getGroupsForLayout();?></em>
                        </p>
                    </li>
                    <li class="dropdown">
                        <a href="#" data-target="#" class="dropdown-toggle" data-toggle="dropdown" >
                            <?= $this->Html->image('user_icon.png', ['alt' => 'Usuario']); ?>
                            <?php //echo $this->Html->image('account_icon.png', ['alt' => 'Cuenta de Usuario']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><?= $this->Html->link(__('Cambiar Contraseña'), ['controller' => 'users', 'action' => 'updatePassword']) ?></li>
                            <li><?= $this->Html->link(__('Salir'), ['controller' => 'users', 'action' => 'logout'], ['escape' => false]); ?></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
<?php endif; ?>
<?= $this->Html->script('sidebar'); ?>