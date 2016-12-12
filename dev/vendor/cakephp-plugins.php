<?php
$baseDir = dirname(dirname(__FILE__));
return [
    'plugins' => [
        'Acl' => $baseDir . '/vendor/cakephp/acl/',
        'Bake' => $baseDir . '/vendor/cakephp/bake/',
        'Bootstrap' => $baseDir . '/vendor/holt59/cakephp3-bootstrap3-helpers/',
        'CakeExcel' => $baseDir . '/vendor/dakota/cake-excel/',
        'DebugKit' => $baseDir . '/vendor/cakephp/debug_kit/',
        'Migrations' => $baseDir . '/vendor/cakephp/migrations/',
        'Proffer' => $baseDir . '/vendor/davidyell/proffer/',
        'Search' => $baseDir . '/vendor/cakedc/search/'
    ]
];