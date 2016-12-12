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
 * @since         0.10.8
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Configure paths required to find CakePHP + general filepath
 * constants
 */
require __DIR__ . '/paths.php';

// Use composer to load the autoloader.
require ROOT . DS . 'vendor' . DS . 'autoload.php';

// spl_autoload_unregister(array('App', 'load'));
// spl_autoload_register(array('App', 'load'), true, true);


// require_once(ROOT . DS . 'vendor' . DS . 'ghunti' . DS . 'highcharts-php' . DS . 'src' . DS . 'Highchart.php');
// require_once(ROOT . DS . 'vendor' . DS . 'ghunti' . DS . 'highcharts-php' . DS . 'src' . DS . 'HighchartJsExpr.php');
// require_once(ROOT . DS . 'vendor' . DS . 'ghunti' . DS . 'highcharts-php' . DS . 'src' . DS . 'HighchartOption.php');

/**
 * Bootstrap CakePHP.
 *
 * Does the various bits of setup that CakePHP needs to do.
 * This includes:
 *
 * - Registering the CakePHP autoloader.
 * - Setting the default application paths.
 */
require CORE_PATH . 'config' . DS . 'bootstrap.php';
// You can remove this if you are confident you have intl installed.
if (!extension_loaded('intl')) {
    trigger_error('You must enable the intl extension to use CakePHP.', E_USER_ERROR);
}

use Cake\Cache\Cache;
use Cake\Console\ConsoleErrorHandler;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Core\Plugin;
use Cake\Database\Type;
use Cake\Datasource\ConnectionManager;
use Cake\Error\ErrorHandler;
use Cake\Log\Log;
use Cake\Network\Email\Email;
use Cake\Network\Request;
use Cake\Routing\DispatcherFactory;
use Cake\Utility\Inflector;
use Cake\Utility\Security;
use Ghunti\HighchartsPHP\Highchart;
use Ghunti\HighchartsPHP\HighchartJsExpr;
use Ghunti\HighchartsPHP\HighchartOption;

/**
 * Read configuration file and inject configuration into various
 * CakePHP classes.
 *
 * By default there is only one configuration file. It is often a good
 * idea to create multiple configuration files, and separate the configuration
 * that changes from configuration that does not. This makes deployment simpler.
 */
try {
    Configure::config('default', new PhpConfig());
    Configure::load('app', 'default', false);
} catch (\Exception $e) {
    die($e->getMessage() . "\n");
}

// Load an environment local configuration file.
// You can use a file like app_local.php to provide local overrides to your
// shared configuration.
//Configure::load('app_local', 'default');

// When debug = false the metadata cache should last
// for a very very long time, as we don't want
// to refresh the cache while users are doing requests.
if (!Configure::read('debug')) {
    Configure::write('Cache._cake_model_.duration', '+1 years');
    Configure::write('Cache._cake_core_.duration', '+1 years');
}

/**
 * Set server timezone to UTC. You can change it to another timezone of your
 * choice but using UTC makes time calculations / conversions easier.
 */
date_default_timezone_set('America/Santiago');

/**
 * Configure the mbstring extension to use the correct encoding.
 */
mb_internal_encoding(Configure::read('App.encoding'));

/**
 * Set the default locale. This controls how dates, number and currency is
 * formatted and sets the default language to use for translations.
 */
ini_set('intl.default_locale', 'en_US');

/**
 * Register application error and exception handlers.
 */
$isCli = php_sapi_name() === 'cli';
if ($isCli) {
    (new ConsoleErrorHandler(Configure::read('Error')))->register();
} else {
    (new ErrorHandler(Configure::read('Error')))->register();
}

// Include the CLI bootstrap overrides.
if ($isCli) {
    require __DIR__ . '/bootstrap_cli.php';
}

/**
 * Set the full base URL.
 * This URL is used as the base of all absolute links.
 *
 * If you define fullBaseUrl in your config file you can remove this.
 */
if (!Configure::read('App.fullBaseUrl')) {
    $s = null;
    if (env('HTTPS')) {
        $s = 's';
    }

    $httpHost = env('HTTP_HOST');
    if (isset($httpHost)) {
        Configure::write('App.fullBaseUrl', 'http' . $s . '://' . $httpHost);
    }
    unset($httpHost, $s);
}

Cache::config(Configure::consume('Cache'));
ConnectionManager::config(Configure::consume('Datasources'));
Email::configTransport(Configure::consume('EmailTransport'));
Email::config(Configure::consume('Email'));
Log::config(Configure::consume('Log'));
Security::salt(Configure::consume('Security.salt'));

/**
 * The default crypto extension in 3.0 is OpenSSL.
 * If you are migrating from 2.x uncomment this code to
 * use a more compatible Mcrypt based implementation
 */
// Security::engine(new \Cake\Utility\Crypto\Mcrypt());

/**
 * Setup detectors for mobile and tablet.
 */
Request::addDetector('mobile', function ($request) {
    $detector = new \Detection\MobileDetect();
    return $detector->isMobile();
});
Request::addDetector('tablet', function ($request) {
    $detector = new \Detection\MobileDetect();
    return $detector->isTablet();
});

/**
 * Custom Inflector rules, can be set to correctly pluralize or singularize
 * table, model, controller names or whatever other string is passed to the
 * inflection functions.
 *
 * Inflector::rules('plural', ['/^(inflect)or$/i' => '\1ables']);
 * Inflector::rules('irregular', ['red' => 'redlings']);
 * Inflector::rules('uninflected', ['dontinflectme']);
 * Inflector::rules('transliteration', ['/å/' => 'aa']);
 */

/**
 * Plugins need to be loaded manually, you can either load them one by one or all of them in a single call
 * Uncomment one of the lines below, as you need. make sure you read the documentation on Plugin to use more
 * advanced ways of loading plugins
 *
 * Plugin::loadAll(); // Loads all plugins at once
 * Plugin::load('Migrations'); //Loads a single plugin named Migrations
 *
 */

Plugin::load('Migrations');
//Plugin::load('Acl', ['bootstrap' => true]);
Plugin::load('Search');
// Plugin::load('Proffer', ['bootstrap' => true]);
// Plugin::load('Bootstrap3');
//Plugin::load('cake-excel', ['bootstrap' => true, 'routes' => true]);


// Only try to load DebugKit in development mode
// Debug Kit should not be installed on a production system
if (Configure::read('debug')) {
    Plugin::load('DebugKit', ['bootstrap' => true]);
}


/**
 * Connect middleware/dispatcher filters.
 */
DispatcherFactory::add('Asset');
DispatcherFactory::add('Routing');
DispatcherFactory::add('ControllerFactory');

/**
 * Enable default locale format parsing.
 * This is needed for matching the auto-localized string output of Time() class when parsing dates.
 */
Type::build('datetime')->useLocaleParser();

// Proffer plugin para upload de archivos
//Plugin::load('Proffer', ['bootstrap' => true]);

/**
 * Funcion url absoluta servidor
 */
function get_server_url($internal = false, $full = false) {
   $s = empty($_SERVER['HTTPS']) ? '' : ($_SERVER['HTTPS'] == 'on') ? 's' : '';
   $p = strtolower($_SERVER['SERVER_PROTOCOL']);
   $protocol = substr($p, 0, strpos($p, '/')) . $s;
   //$name_addr = $internal ? $_SERVER['SERVER_ADDR'] : $_SERVER['SERVER_NAME'];
   $name_addr = $internal ? $_SERVER['SERVER_ADDR'] : $_SERVER['HTTP_HOST'];
   $port = ($_SERVER['SERVER_PORT'] == '80') ? '' : (':'.$_SERVER['SERVER_PORT']);
   $uri = $_SERVER['REQUEST_URI'];
   return $protocol . '://' . $name_addr . $port . ($full ? $uri : '');
}

/**
 * Acá se definen las variables globales del sistema
 * Se crean variables globales estilo USR_GRP_ADMIN
 * @var [type]
 */
$variables_globales = [
    'USR_GRP' => [
        'ADMIN'      => 1,  // administrador - users/index
        'COORD_PROY' => 2,  // coordinador de proyectos - buildings/index
        'GE_GRAL'    => 3,  // gerente general - buildings/index
        'GE_FINAN'   => 4,  // gerente finanzas - buildings/index
        'JEFE_RRHH'  => 5,  // jefe recursos humanos - assists/index
        'VISITADOR'  => 6,  // visitador de obra - progress/index
        'ADMIN_OBRA' => 7,  // administrador de obra - schedules/index
        'ASIS_RRHH'  => 8,  // asistente de recursos humanos - assists/index
        'OFI_TEC'    => 9,  // oficina técina - schedules/index
    ],
    'ASSIST_TYPES_GRP' => [
        'ASISTENCIA'        => 1,   // * asistencia
        'FALLA'             => 2,   // * ausencia
        'PERMISO'           => 3,   // * ausencia con permiso
        'LICEN_COMPIN'      => 4,   // * licencia compin
        'LICEN_ACHS'        => 5,   // * licencia achs
        'CESACION'          => 6,   // * cesación contrato
        'MOV_PERSONAL'      => 7,   // * movimiento trabajador de obra
        'INCORP_TRABAJADOR' => 8,   // * incorporación nuevo trabajador
    ]
];
foreach ($variables_globales as $group_key => $group) {
    foreach ($group as $key => $var) {
        if (! defined($group_key . '_' . $key)) {
            define($group_key . '_' . $key, $var);
        }
    }
}
/**
 * Menu de los usuarios
 */
$menu_usuarios = [
    USR_GRP_ADMIN => [ // menú del administrador
        '0'  => ['title' => __('Usuarios'), 'icon' => 'mdi-action-account-circle', 'items' => [
                '0' => [ 'title' => __('Usuarios'), 'controller' => 'users', 'action' => 'index'],
                '1' => [ 'title' => __('Agregar Usuario'), 'controller' => 'users', 'action' => 'add']
            ]
        ],
        '1'  => ['title' => __('Perfiles de Usuario'), 'icon' => 'mdi-action-account-child', 'items' => [
                '0' => [ 'title' => __('Perfiles'), 'controller' => 'groups', 'action' => 'index'],
                '1' => [ 'title' => __('Agregar Perfil'), 'controller' => 'groups', 'action' => 'add']
            ]
        ],
        '2'  => ['title' => __('Permisos de Usuarios'), 'icon' => 'mdi-action-lock-outline', 'controller' => 'permissions', 'action' => 'index'],
        '3'  => ['title' => __('LOGS'), 'icon' => 'mdi-action-list', 'controller' => 'histories', 'action' => 'index']
    ],
    USR_GRP_COORD_PROY => [ // menú del coordinador de proyectos
        '0'  => ['title' => __('Obras'), 'icon' => 'mdi-social-location-city', 'controller' => 'buildings', 'action' => 'index'],
        '1'  => ['title' => __('Avance'), 'icon' => 'mdi-action-assessment', 'items' => [
                '0' => [ 'title' => __('Planificaciones'), 'controller' => 'schedules', 'action' => 'index'],
                // '1' => [ 'title' => __('Avances de Obra'), 'controller' => 'progress', 'action' => 'index'],
                '1' => [ 'title' => __('Estados de Pago'), 'controller' => 'payment_statements', 'action' => 'index']
            ]
        ],
        '2'  => ['title' => __('RRHH'), 'icon' => 'mdi-av-recent-actors', 'items' => [
                '0' => [ 'title' => __('Asistencias'), 'controller' => 'assists', 'action' => 'index'],
                '1' => [ 'title' => __('Trabajo Realizado'), 'controller' => 'completed_tasks', 'action' => 'index'],
                '2' => [ 'title' => __('Tratos'), 'controller' => 'deals', 'action' => 'index'],
                '3' => [ 'title' => __('Bonos'), 'controller' => 'bonuses', 'action' => 'index'],
                '4' => [ 'title' => __('Asistencia Mensual'), 'controller' => 'assists', 'action' => 'assist_month_detail'],
                '5' => [ 'title' => __('Reportes Remuneraciones'), 'controller' => 'salary_reports', 'action' => 'index']
            ]
        ]
    ],
    USR_GRP_GE_GRAL => [ // menú del gerente general
        '0'  => ['title' => __('Obras'), 'icon' => 'mdi-social-location-city', 'controller' => 'buildings', 'action' => 'index'],
        '1'  => ['title' => __('Avance'), 'icon' => 'mdi-action-assessment', 'items' => [
                '0' => [ 'title' => __('Planificaciones'), 'controller' => 'schedules', 'action' => 'index'],
                // '1' => [ 'title' => __('Avances de Obra'), 'controller' => 'progress', 'action' => 'index'],
                '1' => [ 'title' => __('Estados de Pago'), 'controller' => 'payment_statements', 'action' => 'index']
            ]
        ],
        '2'  => ['title' => __('RRHH'), 'icon' => 'mdi-av-recent-actors', 'items' => [
                '0' => [ 'title' => __('Asistencias'), 'controller' => 'assists', 'action' => 'index'],
                '1' => [ 'title' => __('Trabajo Realizado'), 'controller' => 'completed_tasks', 'action' => 'index'],
                '2' => [ 'title' => __('Tratos'), 'controller' => 'deals', 'action' => 'index'],
                '3' => [ 'title' => __('Bonos'), 'controller' => 'bonuses', 'action' => 'index'],
                '4' => [ 'title' => __('Asistencia Mensual'), 'controller' => 'assists', 'action' => 'assist_month_detail'],
                '5' => [ 'title' => __('Reportes Remuneraciones'), 'controller' => 'remunerations_reports', 'action' => 'index']
            ]
        ],
        '3'  => ['title' => __('LOGS'), 'icon' => 'mdi-action-list', 'controller' => 'histories', 'action' => 'index']
    ],
    USR_GRP_GE_FINAN => [ // menú del gerente finanzas
        '0'  => ['title' => __('Obras'), 'icon' => 'mdi-social-location-city', 'controller' => 'buildings', 'action' => 'index'],
        '1'  => ['title' => __('Avance'), 'icon' => 'mdi-action-assessment', 'items' => [
                '0' => [ 'title' => __('Planificaciones'), 'controller' => 'schedules', 'action' => 'index'],
                // '1' => [ 'title' => __('Avances de Obra'), 'controller' => 'progress', 'action' => 'index'],
                '1' => [ 'title' => __('Estados de Pago'), 'controller' => 'payment_statements', 'action' => 'index']
            ]
        ],
        '2'  => ['title' => __('RRHH'), 'icon' => 'mdi-av-recent-actors', 'items' => [
                '0' => [ 'title' => __('Asistencias'), 'controller' => 'assists', 'action' => 'index'],
                '1' => [ 'title' => __('Trabajo Realizado'), 'controller' => 'completed_tasks', 'action' => 'index'],
                '2' => [ 'title' => __('Tratos'), 'controller' => 'deals', 'action' => 'index'],
                '3' => [ 'title' => __('Bonos'), 'controller' => 'bonuses', 'action' => 'index'],
                '4' => [ 'title' => __('Asistencia Mensual'), 'controller' => 'assists', 'action' => 'assist_month_detail'],
                '5' => [ 'title' => __('Reportes Remuneraciones'), 'controller' => 'salary_reports', 'action' => 'index']
            ]
        ],
        '3'  => ['title' => __('LOGS'), 'icon' => 'mdi-action-list', 'controller' => 'histories', 'action' => 'index']
    ],
    USR_GRP_JEFE_RRHH => [ // menú del jefe recursos humanos
        '0'  => ['title' => __('Obras'), 'icon' => 'mdi-social-location-city', 'controller' => 'buildings', 'action' => 'index'],
        '1'  => ['title' => __('RRHH'), 'icon' => 'mdi-av-recent-actors', 'items' => [
                '0' => [ 'title' => __('Asistencias'), 'controller' => 'assists', 'action' => 'index'],
                '1' => [ 'title' => __('Tratos'), 'controller' => 'deals', 'action' => 'index'],
                '2' => [ 'title' => __('Bonos'), 'controller' => 'bonuses', 'action' => 'index'],
                '3' => [ 'title' => __('Trabajo Realizado'), 'controller' => 'completed_tasks', 'action' => 'index'],
                '4' => [ 'title' => __('Asistencia Mensual'), 'controller' => 'assists', 'action' => 'assist_month_detail'],
                '5' => [ 'title' => __('Reportes Remuneraciones'), 'controller' => 'salary_reports', 'action' => 'index']
            ]
        ],
        '2'  => ['title' => __('Avance'), 'icon' => 'mdi-action-assessment', 'items' => [
                '0' => [ 'title' => __('Planificaciones'), 'controller' => 'schedules', 'action' => 'index'],
                // '1' => [ 'title' => __('Avances de Obra'), 'controller' => 'progress', 'action' => 'index']
                '1' => [ 'title' => __('Estados de Pago'), 'controller' => 'payment_statements', 'action' => 'index'],
            ]
        ],
    ],
    USR_GRP_VISITADOR => [ // menú del visitador
        '0'  => ['title' => __('Obras'), 'icon' => 'mdi-social-location-city', 'controller' => 'buildings', 'action' => 'index'],
        '1'  => ['title' => __('RRHH'), 'icon' => 'mdi-av-recent-actors', 'items' => [
                '0' => [ 'title' => __('Asistencias'), 'controller' => 'assists', 'action' => 'index'],
                '1' => [ 'title' => __('Tratos'), 'controller' => 'deals', 'action' => 'index'],
                '2' => [ 'title' => __('Bonos'), 'controller' => 'bonuses', 'action' => 'index'],
                '3' => [ 'title' => __('Trabajo Realizado'), 'controller' => 'completed_tasks', 'action' => 'index']
            ]
        ],
        '2'  => ['title' => __('Avance'), 'icon' => 'mdi-action-assessment', 'items' => [
                '0' => [ 'title' => __('Planificaciones'), 'controller' => 'schedules', 'action' => 'index'],
                // '1' => [ 'title' => __('Avances de Obra'), 'controller' => 'progress', 'action' => 'index'],
                '1' => [ 'title' => __('Estados de Pago'), 'controller' => 'payment_statements', 'action' => 'index']
            ]
        ],
    ],
    USR_GRP_ADMIN_OBRA => [ // menú del administrador de la obra
        '0'  => ['title' => __('Ver Presupuesto Obra'), 'icon' => 'mdi-social-location-city', 'controller' => 'budgets', 'action' => 'review'],
        '1'  => ['title' => __('RRHH'), 'icon' => 'mdi-av-recent-actors', 'items' => [
                '0' => [ 'title' => __('Asistencias'), 'controller' => 'assists', 'action' => 'index'],
                '1' => [ 'title' => __('Trabajo Realizado'), 'controller' => 'completed_tasks', 'action' => 'index'],
                '2' => [ 'title' => __('Tratos'), 'controller' => 'deals', 'action' => 'index'],
                '3' => [ 'title' => __('Agregar Trato'), 'controller' => 'deals', 'action' => 'add'],
                '4' => [ 'title' => __('Bonos'), 'controller' => 'bonuses', 'action' => 'index'],
                '5' => [ 'title' => __('Agregar Bono'), 'controller' => 'bonuses', 'action' => 'add'],
                '6' => [ 'title' => __('Asistencia Mensual'), 'controller' => 'assists', 'action' => 'assist_month_detail']
            ]
        ],
        '2'  => ['title' => __('Avance'), 'icon' => 'mdi-action-assessment', 'items' => [
                '0' => [ 'title' => __('Planificaciones'), 'controller' => 'schedules', 'action' => 'index'],
                '1' => [ 'title' => __('Agregar Planificación'), 'controller' => 'schedules', 'action' => 'add'],
                // '2' => [ 'title' => __('Avances de Obra'), 'controller' => 'progress', 'action' => 'index'],
                // '2' => [ 'title' => __('Agregar Avance de Obra'), 'controller' => 'progress', 'action' => 'add'],
                '2' => [ 'title' => __('Estados de Pago'), 'controller' => 'payment_statements', 'action' => 'index']
            ]
        ],
    ],
    USR_GRP_ASIS_RRHH => [ // menú del asistente recursos humanos
        '0'  => ['title' => __('RRHH'), 'icon' => 'mdi-av-recent-actors', 'items' => [
                '0' => [ 'title' => __('Asistencias'), 'controller' => 'assists', 'action' => 'index'],
                '1' => [ 'title' => __('Ingresar Asistencia'), 'controller' => 'assists', 'action' => 'add'],
                '2' => [ 'title' => __('Tratos'), 'controller' => 'deals', 'action' => 'index'],
                '3' => [ 'title' => __('Agregar Trato'), 'controller' => 'deals', 'action' => 'add'],
                '4' => [ 'title' => __('Bonos'), 'controller' => 'bonuses', 'action' => 'index'],
                '5' => [ 'title' => __('Agregar Bono'), 'controller' => 'bonuses', 'action' => 'add']
            ]
        ],
    ],
    USR_GRP_OFI_TEC => [ // menú del oficina técnica
        '0'  => ['title' => __('Ver Presupuesto Obra'), 'icon' => 'mdi-social-location-city', 'controller' => 'budgets', 'action' => 'review'],
        '1'  => ['title' => __('RRHH'), 'icon' => 'mdi-av-recent-actors', 'items' => [
                '0' => [ 'title' => __('Asistencias'), 'controller' => 'assists', 'action' => 'index'],
                '1' => [ 'title' => __('Trabajo Realizado'), 'controller' => 'completed_tasks', 'action' => 'index']
            ]
        ],
        '2'  => ['title' => __('Avance'), 'icon' => 'mdi-action-assessment', 'items' => [
                '0' => [ 'title' => __('Planificaciones'), 'controller' => 'schedules', 'action' => 'index'],
                '1' => [ 'title' => __('Agregar Planificación'), 'controller' => 'schedules', 'action' => 'add'],
                // '2' => [ 'title' => __('Avances de Obra'), 'controller' => 'progress', 'action' => 'index'],
                '2' => [ 'title' => __('Estados de Pago'), 'controller' => 'payment_statements', 'action' => 'index']
            ]
        ],
    ]
] ;

Configure::write('menu_usuarios', $menu_usuarios);

/* Variables para calculo de remuneraciones */
$remuneraciones = [
    'dias_contables' => 30,
    'gratificacion' => 98958,
    'colacion' => 60000,
    'movilizacion' => 54400,
    'porcentaje_salud' => 7,
    'porcentaje_afp' => 10,
    'porcentaje_afc' => 2,
    'seguro_cesantia' => 0.006,
    'tope_maximo_imponible' => 74.3,  // en UF sirve solo para isapre y afp
    'tope_maximo_imponible_afc' => 111.4  // en UF
];
Configure::write('remuneraciones', $remuneraciones);


/**
 * Hace un tag para los activos
 * @param  [type] $valor [description]
 * @return string cuadrado rojo cuando entra false
 * @return string cuadrado verde cuando entra true
 * @author Carlos Vásquez <carlos.vasquez@ideauno.cl>
 */
function activo($valor = null)
{
    if ($valor) {
        return '<span class="mif-stop fg-darkGreen" title="' . __('Activo') . '"></span>';
    } else {
        return '<span class="mif-stop fg-darkRed" title="' . __('Desactivado, no se encuentra publicado') . '"></span>';
    }
}


/**
 * Coloca una estrella
 * @param  [type] $valor [description]
 * @return string estrella amarilla y llena cuando viene true
 * @return string estrella gris y Vacía cuando viene false
 * @author Carlos Vásquez <carlos.vasquez@ideauno.cl>
 */
function estrella($valor = null)
{
    if ($valor) {
        return '<span class="mif-star-full fg-yellow" title="' . __('Destacado') . '"></span>';
    } else {
        return '<span class="mif-star-empty fg-grayLight" title="' . __('No Destacado') . '"></span>';
    }
}

Configure::write('ruta_archivos', WWW_ROOT . 'files');

Configure::write('ruta_imagenes', WWW_ROOT . 'img' . DS . 'files');

/**
 * Devuelve el valor $numero formateado
 * según la función number_format
 *
 * Si el parámetro $conversion viene definido, y es distinto de cero,
 * se usa como valor de referencia y se convierte.
 *
 * @param  [type]  $numero     [description]
 * @param  integer $conversion  valor de referencia para convertir $numero. Es
 *                              es el valor en pesos del dolar, UF, etc
 *
 * @return [type]              [description]
 * @author Julio Quinteros <julio.quinteros@ideauno.cl>
 */
function moneda($numero, $conversion = 1)
{
    if($conversion == 0) $conversion = 1;
    if(!is_finite($conversion)) $conversion = 1;
    return number_format( ($numero/$conversion), '2', ',', '.');
}

/**
 * Función para mostrar texto de error de un input en el formato que lo hace cake
 * @param  [String] $error_msg  Texto que tendrá el error
 * @return [String]             Html del error con el mensaje en su interior
 * @author Matías Pardo <matias.pardo@ideauno.cl>
 */
function input_error_html($error_msg)
{
    return '<div class="error-message">' . $error_msg .'</div>';
}

/**
 * Función para usar en las vistas, para mostrar los mensajes de error provenientes de Table UNA SOLA VEZ
 * Es un parche al problema de que salga doble.
 *
 * se debe marcar el campo en el formulario como ['error' => false] para que no se muestren lso errores por defecto
 * Ejemplo de uso en Vacations/add
 * @param   [String]  $fieldName  Nombre del campo a verificar
 * @param   [Entity]  $entity     Entidad a la que se le verán los errors
 * @return  [String]              Html con los errores.
 * @author Matías Pardo <matias.pardo@ideauno.cl>
 */
function output_one_time_error($fieldName, $entity)
{
    if (empty($fieldName) || empty($entity) || empty($entity->errors()) || empty($entity->errors()[$fieldName])) {
        return '';
    }

    $html_output = '';
    foreach ($entity->errors()[$fieldName] as $key => $value) {
        $html_output.= input_error_html($value);
    }
    return $html_output;
}

/**
 * Retorna el signo según el tipo de moneda especificado en la base de datos, lo ideal sería guardar el signo directo en db
 * @param  string $id ID del tipo de moneda
 * @return string     Retorna el signo
 * @author Gabriel Rebolledo <gabriel.rebolledo@ideauno.cl>
 */
function getSignByCurrencyId($id){
    $theSign = "$ ";
    if( $id == "2" ){
        $theSign = "USD ";
    }else if( $id == "3" ){
        $theSign = "UF ";
    }
    return $theSign;
}

/**
 * Retorna la fecha en español (fue hecha a la rápida xd)
 * @param  String $date Fecha a cambiar
 * @return String       Fecha
 * @author Piccolo Daimaku <piocoro.daimaku@ideauno.cl>
 */
function convertMonthToSpanish($date, $formatReturn="d-m-Y"){
    $months=["January"=>"Enero","Febraury"=>"Febrero","March"=>"Marzo","April"=>"Abril","May"=>"Mayo","June"=>"Junio","July"=>"Julio","August"=>"Agosto","September"=>"Septiembre","October"=>"Octubre","November"=>"Noviembre","December"=>"Diciembre"];
    $days=["Sunday" => "Domingo", "Monday"=>"Lunes","Tuesday"=>"Martes","Wednesday"=>"Miércoles","Thursday"=>"Jueves","Friday"=>"Viernes","Saturday"=>"Sábado"];
    foreach($months AS $eng_m => $m){
        if(substr_count($date, $eng_m) > 0){
            $date = str_replace($eng_m, $m, $date);
        }
    }
    foreach($days AS $eng_d => $d){
        if(substr_count($date, $eng_d) > 0){
            $date = str_replace($eng_d, $d, $date);
        }
    }
    return $date;
}

/**
 * Función temporal para los feriados, es conversable con el cliente para realizar
 * @param  [type] $date [description]
 * @return [type]       [description]
 * @author Piccolo Daimaku <piocoro.daimaku@ideauno.cl>
 */
function esFeriado($date){
    // mes - dia
    $feriados = [
        '01-01',
        '01-05',
        '21-05',
        '19-06',
        '27-06',
        '16-07',
        '15-08',
        '09-18',
        '09-19',
        '10-10',
        '10-23',
        '10-31',
        '11-01',
        '12-08',
        '12-25',
    ];
    $fecha = date('m-d', strtotime($date));
    return in_array($fecha, $feriados);
}