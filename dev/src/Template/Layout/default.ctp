<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = 'LDZ - Control Presupuesto Obra';
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="text/javascript">
        var JSCFG_DOMAIN = '<?php echo get_server_url();?>';
        var JSCFG_BASE = '<?php echo $this->request->base;?>';
        var JSCFG_URL = JSCFG_DOMAIN + JSCFG_BASE;
    </script>
    <title>
        <?= $cakeDescription ?>
    </title>
    <?= $this->Html->meta('favicon.ico', '/favicon.ico', ['type' => 'icon']); ?>
    <?= $this->Html->css('/bootstrap/3.3.5/css/bootstrap.min.css') ?>
    <?= $this->Html->css('/material-design/css/roboto.min.css') ?>
    <?= $this->Html->css('/material-design/css/material.min.css') ?>
    <?= $this->Html->css('/material-design/css/ripples.min.css') ?>
    <?= $this->Html->css('/material-design/css/material-fullpalette.css') ?>
    <?= $this->Html->css('/assets/data-tables-1.10.10/datatables.min.css') ?>
    <?= $this->Html->css('/assets/select2-3.5.4/select2.css') ?>
    <?= $this->Html->css('/assets/select2-3.5.4/select2-bootstrap.css') ?>
    <?= $this->Html->css('bootstrap-material-datetimepicker.css') ?>
    <?= $this->Html->css('/material-design/css/material-icons.css') ?>
    <?= $this->Html->css('/assets/collapse-cards-material/paper-collapse.min.css') ?>
    <?= $this->Html->css('/assets/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') ?>
    <?= $this->Html->css('cpo.css') ?>
    <!-- Scripts -->
    <?= $this->Html->script('jquery-1.11.3.min.js') ?>
    <?= $this->Html->script('/assets/jquery-validation-1.14.0/jquery.validate.min.js') ?>
    <?= $this->Html->script('/assets/jquery-validation-1.14.0/localization/messages_es.js') ?>
    <?= $this->Html->script('/assets/data-tables-1.10.10/datatables.min.js') ?>
    <?= $this->Html->script('/assets/moment-js/moment-with-locales.min.js') ?>
    <?= $this->Html->script('/bootstrap/3.3.5/js/transition.js') ?>
    <?= $this->Html->script('/bootstrap/3.3.5/js/collapse.js') ?>
    <?= $this->Html->script('/bootstrap/3.3.5/js/bootstrap.min.js') ?>
    <?= $this->Html->script('/material-design/js/ripples.min.js') ?>
    <?= $this->Html->script('/material-design/js/material.min.js') ?>
    <?= $this->Html->script('/assets/select2-3.5.4/select2.min.js') ?>
    <?= $this->Html->script('/assets/select2-3.5.4/select2_locale_es.js') ?>
    <?= $this->Html->script('bootstrap-material-datetimepicker.js') ?>
    <?= $this->Html->script('/assets/collapse-cards-material/paper-collapse.min.js') ?>
    <?= $this->Html->script('/assets/bootbox.4.4.0/bootbox.min.js') ?>
    <?= $this->Html->script('/assets/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') ?>
    <?= $this->Html->script('/assets/highcharts-4.2.1/highcharts.js') ?>
    <?= $this->Html->script('/assets/autoNumeric/autoNumeric-min.js') ?>
    <?= $this->Html->script('ldz.js') ?>
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('script') ?>
    <?= $this->fetch('css') ?>
</head>
<body>
    <?php /*echo ($this->request->session()->check('Auth.User')) ? $this->Element('menu') : ''  ;// si está conectado*/ ?>
    <?php if ($this->request->params['action']!= 'login' && $this->request->params['action']!= 'forgottenPassword' && $this->request->params['action']!= 'restorePassword') : //login distinto ?>
        <?= $this->Element('sidebar'); // coloca un menu ?>
        <div class="container-fluid">
           <!-- Sidebar menu TODO: lógica de perfiles de usuario del menú superior-->
            <div class="row">
                <div class="col-sm-12 col-md-12">
                    <?= $this->Flash->render() ?>

                    <h3 class="page-header">
                        <?= $this->fetch('title_text', __('Colocar titulo')) ?>
                    </h3>
                    <!-- <h6>Breadcrumb / TODO</h6> -->
                    <?= $this->Element('breadcrumb'); // coloca un menu ?>
                    <?= $this->fetch('content') ?>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="login">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 col-md-12">
                        <?= $this->Flash->render() ?>
                        <?= $this->fetch('content') ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <footer>
        <div class="navbar navbar-material-blue-grey-700" style="margin-bottom:0px;">
            <?= $this->Html->image('logo_login.png', ['alt' => 'LDZ', 'class' => 'img-responsive']); ?>
            <!-- <h4 class="list-group-item-heading">LDZ CONSTRUCTORA</h4> -->
        </div>
    </footer>

    <script>
        $(document).ready(function() {
            // This command is used to initialize some elements and make them work properly
            $.material.init();
        });
    </script>
</body>
</html>
