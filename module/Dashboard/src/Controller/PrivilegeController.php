<?php

namespace Dashboard\Controller;

use Business\Abstraction\ControllerCRUD;
use Business\Model\Menu;
use Business\Model\MenuTable;
use Zend\Form\Form;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class PrivilegeController extends ControllerCRUD
{
    public $table;

    public function __construct($table)
    {
        $this->table = $table;
        parent::__construct($table);
        $describeColumnas = [
            'id' => [
                'PK' => 1,
                'AI' => 1
            ],
           'role_id' => [
               'PK' => 0,
               'AI' => 0,
               'FK' => 1,
               'FUNC' => 'obtenerRoles'
           ],
           'menu_id' => [
                'PK' => 0,
                'AI' => 0,
                'FK' => 1,
                'FUNC' => 'obtenerMenus',
                'multiple' => 'multiple'
           ],

        ];
            
        $columnasListar = [
            'id' => 'ID',
            'role_id' => 'Perfil',
            'menu_id' =>'Menu',
        ];
        
        $indexRedirect = 'dashboard-privilege-listar';
        
        $tableId = ['id'];
        
        $dscEliminar = [
            'id'
        ];
        
        $this->setTitulo('Privilegios');
        $this->setColumnasListar($columnasListar);
        $this->setIndexRedirect($indexRedirect);
        $this->setDescribeColumnas($describeColumnas);
        $this->setTableIds($tableId);
        $this->setDscEliminar($dscEliminar);
    }

   public function agregarAction()
   {
      /** @var \Zend\View\Model\ViewModel */
      $viewManager = parent::agregarAction();
      $viewManager->setTemplate('dashboard/privilegio/agregar');
      $viewManager->setVariable('permiso', $this->table->obtenerAccesos());

      return $viewManager;
   }

   public function listarAction()
   {
      $describeColumnas = [
         'id' => [
            'PK' => 1,
            'AI' => 1
         ],
         'role_id' => [
            'PK' => 0,
            'AI' => 0,
         ],
         'menu_id' => [
            'PK' => 0,
            'AI' => 0,
         ],
      ];
      $this->setDescribeColumnas($describeColumnas);
      $viewManager = parent::listarAction();

      return $viewManager;
   }

   public function editarAction()
   {
      $this->setColumnasListar([
         'role_id' => 'Perfil',
         'menu_id' =>'Menu',
      ]);

      $this->setDescribeColumnas([
         'role_id' => [
            'PK' => 0,
            'AI' => 0,
            'FK' => 1,
            'FUNC' => 'obtenerRoles'
         ],
         'menu_id' => [
            'PK' => 0,
            'AI' => 0,
            'FK' => 1,
            'FUNC' => 'obtenerMenus',
            'multiple' => 'multiple'
         ],
      ]);
      /** @var \Zend\View\Model\ViewModel */
      $viewManager = parent::editarAction();
      $viewManager->setTemplate('dashboard/privilegio/editar');
      $viewManager->setVariable('permiso', $this->table->obtenerAccesos());
      $viewManager->setVariable('data_update', $viewManager->defaultValue);

      return $viewManager;
   }
}
