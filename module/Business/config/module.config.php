<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Business;

use Dashboard\Controller as Dcontroller;
use Integration\Controller as Icontroller;
use Zend\I18n\Translator\Translator;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

$routes = [
   'dashboard-login' => [
      'route' => '/',
      'controller' => Dcontroller\LoginController::class,
      'action'     => 'index',
   ],
   'dashboard-logout' => [
      'route' => '/dashboard/logout',
      'controller' => Dcontroller\LoginController::class,
      'action'     => 'logout',
   ],
   'dashboard-index' => [
      'route' => '/dashboard',
      'controller' => Dcontroller\IndexController::class,
      'action'     => 'index',
   ],
   'dashboard-actualizar-empresa-sesion' => [
      'route' => '/dashboard/actualizar-empresa-sesion',
      'controller' => Dcontroller\IndexController::class,
      'action'     => 'actualizarEmpresaSession',
   ],
   'dashboard-index-boleta-electronica' => [
      'route' => '/dashboard/boleta-electronica',
      'controller' => Dcontroller\IndexController::class,
      'action'     => 'boleta-electronica',
   ],
   'dashboard-index-enviar-correo' => [
      'route' => '/dashboard/enviar-correo',
      'controller' => Dcontroller\IndexController::class,
      'action'     => 'enviar-correo',
   ],
   'dashboard-rol-listar' => [
      'route' => '/dashboard/mantenimiento/rol',
      'controller' => Dcontroller\RolController::class,
      'action'     => 'listar',
   ],
   'dashboard-rol-agregar' => [
      'route' => '/dashboard/mantenimiento/rol/agregar',
      'controller' => Dcontroller\RolController::class,
      'action'     => 'agregar',
   ],
   'dashboard-rol-editar' => [
      'route' => '/dashboard/mantenimiento/rol/editar/id/:id',
      'controller' => Dcontroller\RolController::class,
      'action'     => 'editar',
      'constraints' => [
         'id'     => '[0-9]+',
      ],
   ],
   'dashboard-rol-eliminar' => [
      'route' => '/dashboard/mantenimiento/rol/eliminar/id/:id',
      'controller' => Dcontroller\RolController::class,
      'action'     => 'eliminar',
      'constraints' => [
         'id'     => '[0-9]+',
      ],
   ],
    'dashboard-modulo-listar' => [
        'route' => '/dashboard/mantenimiento/modulo',
        'controller' => Dcontroller\ModuloController::class,
        'action'     => 'listar',
    ],
    'dashboard-modulo-agregar' => [
        'route' => '/dashboard/mantenimiento/modulo/agregar',
        'controller' => Dcontroller\ModuloController::class,
        'action'     => 'agregar',
    ],
    'dashboard-modulo-editar' => [
        'route' => '/dashboard/mantenimiento/modulo/editar/id/:id',
        'controller' => Dcontroller\ModuloController::class,
        'action'     => 'editar',
        'constraints' => [
            'id'     => '[0-9]+',
        ],
    ],
    'dashboard-modulo-eliminar' => [
        'route' => '/dashboard/mantenimiento/modulo/eliminar/id/:id',
        'controller' => Dcontroller\ModuloController::class,
        'action'     => 'eliminar',
        'constraints' => [
            'id'     => '[0-9]+',
        ],
    ],
    'dashboard-user-listar' => [
        'route' => '/dashboard/mantenimiento/user',
        'controller' => Dcontroller\UserController::class,
        'action'     => 'listar',
    ],
    'dashboard-user-agregar' => [
        'route' => '/dashboard/mantenimiento/user/agregar',
        'controller' => Dcontroller\UserController::class,
        'action'     => 'agregar',
    ],
    'dashboard-user-editar' => [
        'route' => '/dashboard/mantenimiento/user/editar/id/:id',
        'controller' => Dcontroller\UserController::class,
        'action'     => 'editar',
        'constraints' => [
            'id'     => '[0-9]+',
        ],
    ],
    'dashboard-user-eliminar' => [
        'route' => '/dashboard/mantenimiento/user/eliminar/id/:id',
        'controller' => Dcontroller\UserController::class,
        'action'     => 'eliminar',
        'constraints' => [
            'id'     => '[0-9]+',
        ],
    ],
    'dashboard-menu-listar' => [
        'route' => '/dashboard/mantenimiento/menu',
        'controller' => Dcontroller\MenuController::class,
        'action'     => 'listar',
    ],
    'dashboard-menu-agregar' => [
        'route' => '/dashboard/mantenimiento/menu/agregar',
        'controller' => Dcontroller\MenuController::class,
        'action'     => 'agregar',
    ],
    'dashboard-menu-editar' => [
        'route' => '/dashboard/mantenimiento/menu/editar/id/:id',
        'controller' => Dcontroller\MenuController::class,
        'action'     => 'editar',
        'constraints' => [
            'id'     => '[0-9]+',
        ],
    ],
    'dashboard-menu-eliminar' => [
        'route' => '/dashboard/mantenimiento/menu/eliminar/id/:id',
        'controller' => Dcontroller\MenuController::class,
        'action'     => 'eliminar',
        'constraints' => [
            'id'     => '[0-9]+',
        ],
    ],
    'dashboard-privilege-listar' => [
        'route' => '/dashboard/mantenimiento/privilege',
        'controller' => Dcontroller\PrivilegeController::class,
        'action'     => 'listar',
    ],
    'dashboard-privilege-agregar' => [
        'route' => '/dashboard/mantenimiento/privilege/agregar',
        'controller' => Dcontroller\PrivilegeController::class,
        'action'     => 'agregar',
    ],
    'dashboard-privilege-editar' => [
        'route' => '/dashboard/mantenimiento/privilege/editar/id/:id',
        'controller' => Dcontroller\PrivilegeController::class,
        'action'     => 'editar',
        'constraints' => [
            'id'     => '[0-9]+',
        ],
    ],
    'dashboard-privilege-eliminar' => [
        'route' => '/dashboard/mantenimiento/privilege/eliminar/id/:id',
        'controller' => Dcontroller\PrivilegeController::class,
        'action'     => 'eliminar',
        'constraints' => [
            'id'     => '[0-9]+',
        ],
    ],
   'dashboard-empresa-listar' => [
      'route' => '/dashboard/mantenimiento/empresa',
      'controller' => Dcontroller\EmpresaController::class,
      'action'     => 'listar',
   ],
   'dashboard-empresa-agregar' => [
      'route' => '/dashboard/mantenimiento/empresa/agregar',
      'controller' => Dcontroller\EmpresaController::class,
      'action'     => 'agregar',
   ],
   'dashboard-empresa-editar' => [
      'route' => '/dashboard/mantenimiento/empresa/editar/empcod/:empcod',
      'controller' => Dcontroller\EmpresaController::class,
      'action'     => 'editar',
   ],
   'dashboard-empresa-eliminar' => [
      'route' => '/dashboard/mantenimiento/empresa/eliminar/empcod/:empcod',
      'controller' => Dcontroller\EmpresaController::class,
      'action'     => 'eliminar',
   ],
   'dashboard-obtener-periodo' => [
      'route' => '/dashboard/obtener-periodo',
      'controller' => Dcontroller\IndexController::class,
      'action'     => 'obtener-periodo',
   ],
   'dashboard-agendar-correos' => [
      'route' => '/dashboard/agendar-correos',
      'controller' => Dcontroller\IndexController::class,
      'action'     => 'agendar-correos',
   ],
   'dashboard-email-agendar-listar' => [
      'route' => '/dashboard/reporte/correos',
      'controller' => Dcontroller\EmailAgendarController::class,
      'action'     => 'listar',
   ],
   'dashboard-datos-importar' => [
       'route' => '/dashboard/datos-importar',
       'controller' => Dcontroller\DatosImportarController::class,
       'action'     => 'index',
   ],
    'dashboard-convertidor-crt' => [
        'route' => '/dashboard/convertidor-crt',
        'controller' => Dcontroller\FirmaDigitalExportarController::class,
        'action'     => 'index',
    ],
    'dashboard-archivo-importar' => [
        'route' => '/dashboard/archivo-importar',
        'controller' => Dcontroller\DatosImportarController::class,
        'action'     => 'importarArchivo',
    ],
    'dashboard-monitoreo' => [
        'route' => '/dashboard/monitoreo',
        'controller' => Dcontroller\MonitoreoController::class,
        'action'     => 'index',
    ],
    'dashboard-email-detalle' => [
        'route' => '/dashboard/email-detalle',
        'controller' => Dcontroller\MonitoreoController::class,
        'action'     => 'email-detalle',
    ],
    'dashboard-obtener-email' => [
        'route' => '/dashboard/obtener-email',
        'controller' => Dcontroller\MonitoreoController::class,
        'action'     => 'obtener-email',
    ],
    'dashboard-generar-avisos' => [
        'route' => '/dashboard/generar-avisos',
        'controller' => Dcontroller\GenerarAvisosController::class,
        'action'     => 'index',
    ],
    'dashboard-enviar-avisos' => [
        'route' => '/dashboard/enviar-avisos',
        'controller' => Dcontroller\GenerarAvisosController::class,
        'action'     => 'enviarAviso',
    ],
    'dashboard-generar-backups' => [
        'route' => '/dashboard/generar-backups',
        'controller' => Dcontroller\GenerarBackupsController::class,
        'action'     => 'index',
    ],
	'dashboard-valores-listar' => [
		'route' => '/dashboard/valores',
		'controller' => Dcontroller\ValoresController::class,
		'action'     => 'listar',
	],
	'dashboard-valores-agregar' => [
		'route' => '/dashboard/valores/agregar',
		'controller' => Dcontroller\ValoresController::class,
		'action'     => 'agregar',
	],
	'dashboard-valores-editar' => [
		'route' => '/dashboard/valores/editar/:empcod/:loccod/:calcod/:turcod/:percod',
		'controller' => Dcontroller\ValoresController::class,
		'action'     => 'editar',
	],
	'dashboard-valores-eliminar' => [
		'route' => '/dashboard/valores/eliminar/:empcod/:loccod/:calcod/:turcod/:percod',
		'controller' => Dcontroller\ValoresController::class,
		'action'     => 'eliminar',
	],
    'dashboard-notifica-agregar' => [
        'route' => '/dashboard/notifica/agregar',
        'controller' => Dcontroller\NotificaController::class,
        'action'     => 'agregar',
    ],
    'dashboard-asistencias' => [
        'route' => '/dashboard/asistencias',
        'controller' => Dcontroller\AsistenciasController::class,
        'action'     => 'index',
    ],
   // WebServices
   'integration-webservice-sincronizar' => [
      'route' => '/webservices/integracion',
      'controller' => Icontroller\IndexController::class,
      'action'     => 'index',
   ],
   'integration-email-agenda' => [
      'route' => '/integracion/email-agenda',
      'controller' => Icontroller\IndexController::class,
      'action'     => 'email-agenda',
   ],
   'integration-email-leido' => [
      'route' => '/integracion/email-leido/:empcod/:percod/:pcacod/:calcod/:permail[/:item]',
      'controller' => Icontroller\IndexController::class,
      'action'     => 'email-leido',
   ],
   'integration-boleta-haberes' => [
      'route' => '/b/:p',
      'controller' => Icontroller\IndexController::class,
      'action'     => 'boleta-haberes',
   ],
];
$arrRoutes = [];

foreach ($routes as $key => $route) {
   $arrRoutes[$key] = [
      'type' => Segment::class,
      'options' => [
         'route'    => $route['route'],
         'defaults' => [
            'controller' => $route['controller'],
            'action'     => $route['action'],
         ],
      ],
   ];

   if (!empty($route['constraints'])) {
      $arrRoutes[$key]['options']['constraints'] = $route['constraints'];
   }
}

return [
   'router' => [
      'routes' => $arrRoutes,
   ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
           'ViewJsonStrategy'
        ],
    ],
   'view_helpers' => [
      'aliases' => [
         'Base64Image' => View\Helper\Base64Image::class,
         'PKs' => View\Helper\PKs::class,
         'acl' => View\Helper\Acl::class,
      ],
      'factories' => [
         View\Helper\Base64Image::class => InvokableFactory::class,
         View\Helper\PKs::class => InvokableFactory::class,
         View\Helper\Acl::class => InvokableFactory::class
      ]
   ],
	'service_manager' => [
		'aliases' => [
			Translator::class => 'MvcTranslator'
		]
	]
];
