<?php

namespace Dashboard\Controller;

use Business\Abstraction\ControllerCRUD;
use Zend\Form\Form;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class UserController extends ControllerCRUD
{
    public $sucursal;

    public function __construct($table, $sucursal)
    {
        parent::__construct($table);
        $describeColumnas = [
            'id' => [
                'PK' => 1,
                'AI' => 1
            ],
            'username' => [
                'PK' => 0,
                'AI' => 0,
                'TIPO' => 'VARCHAR',
                'REQUIRED' => true,
                'MIN_LENGHT' => 1,
                'LENGHT' => 12
            ],
           'password' => [
                'PK' => 0,
                'AI' => 0,
                'TIPO' => 'VARCHAR',
                'REQUIRED' => true,
                'MIN_LENGHT' => 1,
                'LENGHT' => 40
            ],
            'full_name' => [
                'PK' => 0,
                'AI' => 0,
                'TIPO' => 'VARCHAR',
                'REQUIRED' => true,
                'MIN_LENGHT' => 1,
                'LENGHT' => 300
            ],
            'email' => [
                'PK' => 0,
                'AI' => 0,
                'TIPO' => 'VARCHAR',
                'REQUIRED' => true,
                'MIN_LENGHT' => 1,
                'LENGHT' => 300
            ],
            'active' => [
                'PK' => 0,
                'AI' => 0,
                'REQUIRED' => true,
            ],
            'role_id' => [
                'PK' => 1,
                'AI' => 0,
                'FK' => 1,
                'FUNC' => 'obtenerRoles',
                'REQUIRED' => true,
            ],
            'succod' => [
               'PK' => 1,
               'AI' => 0,
               'FK' => 1,
               'TIPO' => 'VARCHAR',
               'FUNC' => 'obtenerSucursales',
               'multiple' => 'multiple',
               'REQUIRED' => true,
            ],

        ];
            
        $columnasListar = [
            'id' => 'ID',
            'username' => 'Username',
            'full_name' =>'Nombre completo',
            'email' =>'Correo',
            'password' => 'Contraseña',
            'role_id' =>'Rol',
            'active' => 'Active',
            'succod' => 'Sucursales'
        ];
        
        $indexRedirect = 'dashboard-user-listar';
        
        $tableId = ['id'];
        
        $dscEliminar = [
            'id', 'username','full_name','email','role_id','active'
        ];
        
        $this->setTitulo('Usuarios');
        $this->setColumnasListar($columnasListar);
        $this->setIndexRedirect($indexRedirect);
        $this->setDescribeColumnas($describeColumnas);
        $this->setTableIds($tableId);
        $this->setDscEliminar($dscEliminar);
        $this->sucursal = $sucursal;
    }


   public function listarAction()
   {

      $this->setColumnasListar([
         'id' => 'ID',
         'username' => 'Username',
         'full_name' =>'Nombre completo',
         'email' =>'Correo',
         //'password' => 'Contraseña',
         'role_id' =>'Rol',
         'active' => 'Active',
         'succod' => 'Sucursales'
      ]);

      /** @var \Zend\View\Model\ViewModel */
      $viewManager = parent::listarAction();

      return $viewManager;
   }


   public function agregarAction()
   {
      /** @var \Zend\View\Model\ViewModel */
      $viewManager = parent::agregarAction();
      $viewManager->setTemplate('dashboard/user/agregar');

      return $viewManager;
   }

   public function editarAction()
   {
      $describeColumnas = [
         'id' => [
            'PK' => 1,
            'AI' => 1
         ],
         'username' => [
            'PK' => 0,
            'AI' => 0,
            'TIPO' => 'VARCHAR',
            'REQUIRED' => true,
            'MIN_LENGHT' => 1,
            'LENGHT' => 12
         ],
         'password' => [
            'PK' => 0,
            'AI' => 0,
            'TIPO' => 'VARCHAR',
            'REQUIRED' => false,
            'MIN_LENGHT' => 1,
            'LENGHT' => 40
         ],
         'full_name' => [
            'PK' => 0,
            'AI' => 0,
            'TIPO' => 'VARCHAR',
            'REQUIRED' => true,
            'MIN_LENGHT' => 1,
            'LENGHT' => 300
         ],
         'email' => [
            'PK' => 0,
            'AI' => 0,
            'TIPO' => 'VARCHAR',
            'REQUIRED' => true,
            'MIN_LENGHT' => 1,
            'LENGHT' => 300
         ],
         'active' => [
            'PK' => 0,
            'AI' => 0,
            'REQUIRED' => true,
         ],
         'role_id' => [
            'PK' => 1,
            'AI' => 0,
            'FK' => 1,
            'FUNC' => 'obtenerRoles',
            'REQUIRED' => true,
         ],
         'succod' => [
            'PK' => 1,
            'AI' => 0,
            'FK' => 1,
            'TIPO' => 'VARCHAR',
            'FUNC' => 'obtenerSucursales',
            'multiple' => 'multiple',
            'REQUIRED' => false,
         ],

      ];

      $this->setDescribeColumnas($describeColumnas);
      /** @var \Zend\View\Model\ViewModel */
      $viewManager = parent::editarAction();
      $viewManager->setTemplate('dashboard/user/editar');

      return $viewManager;
   }

   public function obtenerSucursalesAction()
   {
      $empresa = $this->params()->fromPost('empcod', NULL);
      $data = !empty($empresa) ? ['empcod' => $empresa] : NULL;
      /** @var \Business\Model\Sucursal */
      $sucursal = $this->sucursal->obtenerSucursales($data);

      return new JsonModel($sucursal);
   }
}
