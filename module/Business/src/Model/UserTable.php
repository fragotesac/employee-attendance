<?php

namespace Business\Model;

use Business\Abstraction\Model;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

/**
 * Description of UserTable
 *
 * @author Francis Gonzales <fgonzalestello91@gmail.com>
 */
class UserTable extends Model
{
    
    /**
     * This functions returns a query to get 
     * all the users
     * @return \Zend\Db\Sql\Select
     */
    public function getUsersList()
    {
        $select = new Select();
        $select->from(array(
                    'u' => 'user'
                ))
                ->join(array(
                    'r' => 'role'
                    ), 
                    'u.role_id = r.id'
                 )
                ->order('u.id');

        return $select;
    }
    
    /**
     * This function returns the user by ID
     * @param int $userId
     * @return array
     */
    public function getUser($userId)
    {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql
                  ->select()
                  ->from(array('u' => 'user'))
                  ->join(
                        array('r' => 'role'),
                        'r.id = u.role_id',
                        array('*')
                    )
                  ->where(array('u.id' => $userId))
                  ->order('u.id');
        $stmt = $sql->prepareStatementForSqlObject($select);
        $results = $stmt->execute(); 
        return $results;
    }
    
    /**
     * This function allows to add users
     * @param array $params
     * @return boolean
     */
    public function addUser($params)
    {
        $params['password'] = sha1($params['password']);
        $rs = $this->tableGateway->insert($params);
        return $rs;
    }
    
    /**
     * This function allows to edit Users
     * @param array $set
     * @param array $where
     * @return boolean
     */
    public function editUser($set, $where)
    {
        if (!empty($set['password'])) {
            $set['password'] = sha1($set['password']);
        } else {
            unset($set['password']);
        }
        $rs = $this->tableGateway->update($set, $where);
        return $rs;
    }
    
    public function deleteUser($userId)
    {
        $where = array('id' => $userId);
        $rs = $this->tableGateway->delete($where);
        return $rs;
    }

    public function obtenerRoles()
    {
        $arrRol = [];
        $roles = $this->fkTable['rol']->select()->toArray();
        foreach ($roles as $rol) {
            $arrRol[$rol['id']] = $rol['name'];
        }
        return $arrRol;
    }

    public function obtenerEmpresas()
    {
       $arrRol = [];
       $roles = $this->fkTable['empresa']->select()->toArray();
       foreach ($roles as $rol) {
          $arrRol[$rol['empcod']] = $rol['empnom'];
       }
       return $arrRol;
    }

   public function obtenerSucursales($userId = NULL)
   {
      if (empty($userId)) {
         $sql = $this->getSql();
         $select = $sql
            ->select()
            ->from(['s' => 'sucursal'])
            ->join(['e' => 'admmcias'], 'e.empcod = s.empcod')
            ->order('s.sucnom');
         $sucursales = $this->fetchSql($sql, $select);

         foreach ($sucursales as $sucursal) {
            $arrRol[$sucursal['empcod']]['options'][$sucursal['empcod'] . '-' . $sucursal['succod']] = $sucursal['sucnom'];
            $arrRol[$sucursal['empcod']]['label'] = $sucursal['empnom'];
            $arrRol[$sucursal['empcod']]['id'] = $sucursal['empcod'];
         }
      } else {
         $sql = $this->getSql();
         $select = $sql
            ->select()
            ->from(['us' => 'ususuc'])
            ->join(['u' => 'user'], 'u.username = us.usucod')
            ->join(['s' => 'sucursal'], 's.succod = us.loccod')
            ->join(['e' => 'admmcias'], 'e.empcod = s.empcod')
            ->order('s.sucnom');
         $sucursales = $this->fetchSql($sql, $select);
         $arrRol = $suc = [];
         foreach ($sucursales as $sucursal) {
            $suc[$sucursal['id']][] = ucwords(/*$sucursal['empnom'] . '-' . */$sucursal['sucnom']);
         }

         foreach ($suc as $id => $sucu) {
            $arrRol[$id] = implode(' | ', $sucu);
         }
      }

      return $arrRol;
   }

   public function insertData($data)
   {
      $empcod = [];
      try {
         $data['username'] = strtolower($data['username']);
         $data['password'] = sha1(strtolower($data['password']));
         // removing data
         $this->fkTable['usuemp']->delete([
            'siscod' => '01',
            'usucod' => $data['username'],
         ]);
         $this->fkTable['ususuc']->delete([
            'siscod' => '01',
            'usucod' => $data['username'],
         ]);

         foreach ($data['succod'] as $sucursal) {
            $suc = explode('-', $sucursal);
            $empcod[$suc[0]] = $suc[0];
            $this->fkTable['ususuc']->insert([
               'siscod' => '01',
               'usucod' => $data['username'],
               'empcod' => $suc[0],
               'loccod' => $suc[1],
               'estado' => 'S'
            ]);
         }

         foreach ($empcod as $emp) {
            $this->fkTable['usuemp']->insert([
               'siscod' => '01',
               'usucod' => $data['username'],
               'empcod' => $emp,
               'estado' => 'S'
            ]);
         }

         unset($data['succod']);

         $rs = $this->tableGateway->insert($data);
      } catch (\Exception $e) {
         error_log($e->getMessage());
         $rs = false;
      }

      return [
         'result' => $rs
      ];
   }

   public function getData($dataKey)
   {
      $dataRow = [];
      try {
         $dataUser = (array)$this->tableGateway->select($dataKey)->current();
         $dataPermisos = $this->fkTable['ususuc']->select(['usucod' => $dataUser['username']])->toArray();
         $permisos = [];
         foreach ($dataPermisos as $permiso) {
            $permisos[] = $permiso['empcod'] . '-' . $permiso['loccod'];
         }
         $dataUser['succod'] = $permisos;
      } catch (\Exception $e) {
         error_log($e->getMessage());
         $rs = false;
      }

      return [
         'data' => $dataUser
      ];
   }

   public function updateData($updateKeys, $setData)
   {
      $empcod = [];
      try {
         $data['username'] = strtolower($setData['username']);

         if (!empty($setData['password'])) {
            $setData['password'] = sha1(strtolower($setData['password']));
         } else {
            unset($setData['password']);
         }
         // removing data
         $this->fkTable['usuemp']->delete([
            'siscod' => '01',
            'usucod' => $data['username'],
         ]);
         $this->fkTable['ususuc']->delete([
            'siscod' => '01',
            'usucod' => $data['username'],
         ]);

         foreach ($setData['succod'] as $sucursal) {
            $suc = explode('-', $sucursal);
            $empcod[$suc[0]] = $suc[0];
            $this->fkTable['ususuc']->insert([
               'siscod' => '01',
               'usucod' => $data['username'],
               'empcod' => $suc[0],
               'loccod' => $suc[1],
               'estado' => 'S'
            ]);
         }

         foreach ($empcod as $emp) {
            $this->fkTable['usuemp']->insert([
               'siscod' => '01',
               'usucod' => $data['username'],
               'empcod' => $emp,
               'estado' => 'S'
            ]);
         }

         unset($setData['succod']);
         $rs = $this->tableGateway->update($setData, $updateKeys);
      } catch (\Exception $e) {
         error_log($e->getMessage());
         $rs = false;
      }

      return [
         'result' => $rs
      ];
   }
}