<?php

namespace Business\Model;


use Zend\Db\Adapter\Driver\Pdo\Result;
use Zend\Db\Sql\Sql;
use Business\Abstraction\Model;
/**
 *  Description of AsistenciaTable
 *
 * @author Francis Gonzales <fgonzalestello91@gmail.com>
 */
class AsistenciaTable extends Model
{
	public function listOfAttendance(int $userId, int $startDate, int $endDate) :? Result
	{
		$sql = $this->getSql();
		$select = $sql->select();
		$select->from('asistencia');
		$select->where(['user_id' => $userId]);
		$select->where->between('created_at', $startDate, $endDate);

		return $this->fetchSql($sql, $select);
	}

    public function getAttendance(int $startDate, int $endDate) :? Result
    {
        $sql = $this->getSql();
        $select = $sql->select();
        $select->from(['a' => 'asistencia']);
        $select->join(['u' => 'user'], 'u.id = a.user_id', ['full_name']);
        $select->where->between('created_at', $startDate, $endDate);
        $select->order('a.created_at, u.id ASC');

        return $this->fetchSql($sql, $select);
    }
}
