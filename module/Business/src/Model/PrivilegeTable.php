<?php

/**
 * Privilege Table
 * @autor Francis Gonzales <fgonzalestello91@gmail.com>
 */

namespace Business\Model;

use Business\Plugin\DbSelect;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Business\Abstraction\Model;
use Zend\Paginator\Paginator;

class PrivilegeTable  extends Model
{
   public function fetchAll($paginated = false, $where = [], $orderBy = [])
   {
      if ($paginated) {
         $select = new Select($this->tableGateway->getTable());
         $select->columns(['menu_id' => new Expression('group_concat(m.label)')])
            ->join(
            ['m' => 'menu'],
            'm.id = privilege.menu_id',
            []
         )
            ->join(
               ['r' => 'role'],
               'r.id = privilege.role_id',
               ['id', 'role_id' => 'name']
            )
         ->where(['m.mostrar' => 1])
         ->group(['r.id']);

         $paginatorAdapter = new DbSelect(
            $select,
            $this->tableGateway->getAdapter()
         );

         $paginator = new Paginator($paginatorAdapter);

         return $paginator;
      }

      return $this->tableGateway->select();
   }

   public function getAclMenu($roleId)
   {
      $sql = new Sql($this->tableGateway->getAdapter());
      $select1 = $sql
         ->select()
         ->from(['p' => 'privilege'])
         ->join(
            ['m' => 'menu'],
            'm.id = p.menu_id',
            []
         )
         ->join(
            ['ms' => 'menu'],
            'm.id = ms.parent and ms.permiso >= p.permiso',
            ['*']
         )
         ->where(['m.mostrar' => 1, 'ms.mostrar' => 0, 'p.role_id' => $roleId])
      ;

      $select2 = $sql
         ->select()
         ->from(['p' => 'privilege'])
         ->join(
            ['m' => 'menu'],
            'm.id = p.menu_id and m.permiso >= p.permiso',
            ['*']
         )
         ->where(['m.mostrar' => 1, 'p.role_id' => $roleId])
      ;

      $select1->combine($select2);

      $stmt = $sql->prepareStatementForSqlObject($select1);
      $results = $stmt ->execute();
      foreach ($results as $result) {
         $routes[] = $result['route'];
      }

      return [$roleId => array_unique($routes)];
   }

   public function getMenuByUser($userId)
   {
      $sql = new Sql($this->tableGateway->getAdapter());
      $select = $sql
         ->select()
         ->from(array('p' => 'privilege'))
         ->join(
            array('m' => 'menu'),
            'm.id = p.menu_id',
            array('*')
         )
         ->join(
            array('r' => 'role'),
            'r.id = p.role_id',
            array('*')
         )
         ->join(
            array('u' => 'user'),
            'u.role_id = r.id',
            array('full_name')
         )
         ->where(['m.mostrar' => 1, 'u.id' => $userId])
         ->order('m.id');
      $stmt = $sql->prepareStatementForSqlObject($select);

      $results = $stmt ->execute();

      return $results;
   }

    public function obtenerRoles()
    {
        $arrRol = [];
        $roles = $this->fkTable['role']->select()->toArray();
        foreach ($roles as $rol) {
            $arrRol[$rol['id']] = $rol['name'];
        }
        return $arrRol;
    }

   /**
    *
    * TOMAR EN CUENTA QUE AQUI SOBREESCRIBIMOS EL ROLE_ID
    * @return array
    */
   public function getData($dataKey)
   {
      $data = ['role_id' => $dataKey['id']];
      try {
         $dataRow = (array)$this->tableGateway->select($data)->toArray();
      } catch (\Exception $e) {
         error_log($e->getMessage());
         $rs = false;
      }

      return [
         'data' => $dataRow
      ];
   }

   public function obtenerMenus()
   {
      $arrSib = [];
      $arrMenu = [];
      $menus = $this->fkTable['menu']->select(['mostrar' => 1])->toArray();
      foreach ($menus as $key => $menu) {
         if (!empty($menu['parent'])) {
            $arrSib[$menu['parent']][] = $menu;
            unset($menu[$key]);
         } else {
            $arrMen[$menu['id']] = $menu['label'];
         }
      }

      foreach ($arrMen as $key => $men) {
         if (empty($arrSib[$key])) {
            $arrMenu[$key]['options'][$key] = strip_tags($men);
            $arrMenu[$key]['label'] = 'Opts';
            $arrMenu[$key]['id'] = $key;

         } else {
            foreach ($arrSib[$key] as $siblings) {
               $arrMenu[$key]['options'][$siblings['id']] = strip_tags($siblings['label']);
               $arrMenu[$key]['label'] = $men;
               $arrMenu[$key]['id'] = $key;
            }
         }
      }

      return $arrMenu;
   }

   public function obtenerAccesos()
   {
      return [
         MenuTable::PER_FULL => 'Acceso Total',
         MenuTable::PER_DELETE => 'Lectura, Escritura, Eliminacion',
         MenuTable::PER_WRITE => 'Lectura, Escritura',
         MenuTable::PER_READ => 'Lectura',
      ];
   }

   public function updateData($updateKeys, $setData)
   {
      try {
         $menuId = [];
         foreach ($setData['menu_id'] as $menu) {
            $menuPer = explode('-', $menu);
            $menuId[] = $menuPer[0];
         }
         $menuRow = $this->fkTable['menu']->select(['id' => $menuId, 'parent IS NOT NULL'])->toArray();
         foreach ($menuRow as $menHead) {
            $parents[] = $menHead['parent'];
         }
         $parents = array_unique($parents);
         foreach ($parents as $par) {
            $setData['menu_id'][] = $par . '-4';
         }

         $this->tableGateway->delete(['role_id' => $setData['role_id']]);
         foreach ($setData['menu_id'] as $menu) {
            $menuPer = explode('-', $menu);
            $rs = $this->tableGateway->insert([
               'role_id' => $setData['role_id'],
               'menu_id' => $menuPer[0],
               'permiso' => $menuPer[1],
            ]);
         }
      } catch (\Exception $e) {
         error_log($e->getMessage());
         $rs = false;
      }

      return [
         'result' => $rs
      ];
   }

   public function insertData($data)
   {
      try {
         $menuId = [];
         foreach ($data['menu_id'] as $menu) {
            $menuPer = explode('-', $menu);
            $menuId[] = $menuPer[0];
         }
         $menuRow = $this->fkTable['menu']->select(['id' => $menuId, 'parent IS NOT NULL'])->toArray();
         foreach ($menuRow as $menHead) {
            $parents[] = $menHead['parent'];
         }
         $parents = array_unique($parents);
         foreach ($parents as $par) {
            $data['menu_id'][] = $par . '-4';
         }

         $this->tableGateway->delete(['role_id' => $data['role_id']]);

         foreach ($data['menu_id'] as $menu) {
            $menuPer = explode('-', $menu);
            $rs = $this->tableGateway->insert([
               'role_id' => $data['role_id'],
               'menu_id' => $menuPer[0],
               'permiso' => $menuPer[1],
            ]);
         }
      } catch (\Exception $e) {
         error_log($e->getMessage());
         $rs = false;
      }

      return [
         'result' => $rs
      ];
   }
}
