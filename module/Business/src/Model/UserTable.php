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

   public function insertData($data)
   {
      $empcod = [];
      try {
         $data['username'] = strtolower($data['username']);
         $data['password'] = sha1(strtolower($data['password']));

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
	   $dataUser = [];
      try {
         $dataUser = (array)$this->tableGateway->select($dataKey)->current();
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
