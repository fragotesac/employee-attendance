<?php

namespace Business\Abstraction;

use PHPUnit\Framework\Exception;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Business\Plugin\DbSelect;
use Zend\Db\ResultSet\ResultSet;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\InputFilter\InputFilter;
use DomainException;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\ToInt;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\StringLength;

/**
 * Description of Abstraction/Model
 *
 * @author Francis Gonzales <fgonzalestello91@gmail.com>
 */
abstract class Model
{

	protected $tableGateway;
	protected $fkTable;
	protected $library;

	public function __construct(TableGateway $tableGateway, $fkTable = [], $library = [])
	{
		$this->tableGateway = $tableGateway;
		$this->fkTable = $fkTable;
		$this->library = $library;
	}

	public function getLibrary()
	{
		return $this->library;
	}

	public function setLibrary($library)
	{
		$this->library = $library;
	}

	public function getFkTable()
	{
		return $this->fkTable;
	}

	public function setFkTable($fkTable)
	{
		$this->fkTable = $fkTable;
	}

	public function getClassName()
	{
		return get_called_class();
	}

	public function getPrototypeClass()
	{
		return str_replace('Table', '', $this->getClassName());
	}

	public function getTableGateway()
	{
		return $this->tableGateway;
	}

	public function fetchAll($paginated = false, $where = [], $orderBy = [])
	{
		if ($paginated) {
			return $this->fetchPaginatedResults($where, $orderBy);
		}

		return $this->tableGateway->select();
	}

	private function fetchPaginatedResults($where = [], $orderBy = [])
	{
		$prototypeClass = $this->getPrototypeClass();
		$select = new Select($this->tableGateway->getTable());
		if (!empty($where)) {
			$select = $select->where($where);
		}

		if (!empty($orderBy)) {
			$select = $select->order($orderBy);
		}

		$resultSetPrototype = new ResultSet();
		$resultSetPrototype->setArrayObjectPrototype(new $prototypeClass);

		$paginatorAdapter = new DbSelect(
			$select,
			$this->tableGateway->getAdapter(),
			$resultSetPrototype
		);

		$paginator = new Paginator($paginatorAdapter);

		return $paginator;
	}

	public function getData($dataKey)
	{
		try {
			$dataRow = (array)$this->tableGateway->select($dataKey)->current();
		} catch (\Exception $e) {
			error_log($e->getMessage());
			throw new \Exception($e);
			$rs = false;
		}

		return [
			'data' => $dataRow
		];
	}

	public function insertData($data)
	{
		try {
			$rs = $this->tableGateway->insert($data);
		} catch (\Exception $e) {
			error_log($e->getMessage());
			throw new \Exception($e);
			$rs = false;
		}

		return [
			'result' => $rs,
			'last_id' => $this->tableGateway->lastInsertValue
		];
	}

	public function updateData($updateKeys, $setData)
	{
		try {
			$rs = $this->tableGateway->update($setData, $updateKeys);
		} catch (\Exception $e) {
			error_log($e->getMessage());
			throw new \Exception($e);
			$rs = false;
		}

		return [
			'result' => $rs
		];
	}

	public function deleteData($keyInfo)
	{
		try {
			$rs = $this->tableGateway->delete($keyInfo);
		} catch (\Exception $e) {
			error_log($e->getMessage());
			throw new \Exception($e);
			$rs = false;
		}

		return $rs;
	}

	public function getInputFilter($fields)
	{
		if (empty($fields)) {
			return [];
		}

		$inputFilter = new InputFilter();

		foreach ($fields as $name => $field) {
			if (empty($field['AI']) && empty($field['FK']) && empty($field['multiple'])) {
				$inputFilter->add([
					'name' => $name,
					'required' => isset($field['REQUIRED']) ? $field['REQUIRED'] : '',
					'filters' => [
						['name' => StripTags::class],
						['name' => StringTrim::class],
					],
					'validators' => [
						[
							'name' => StringLength::class,
							'options' => [
								'encoding' => 'UTF-8',
								'min' => !empty($field['MIN_LENGHT']) ? $field['MIN_LENGHT'] : NULL,
								'max' => !empty($field['LENGHT']) ? $field['LENGHT']: NULL,
							],
						],
					],
				]);
			}

			if (!empty($field['FK']) && empty($field['multiple'])) {
				$inputFilter->add([
					'name' => $name,
					'required' => isset($field['REQUIRED']) ? $field['REQUIRED'] : NULL,
					'filters' => [
						['name' => StripTags::class],
						['name' => StringTrim::class],
					],
					'validators' => [
						[
							'name' => StringLength::class,
							'options' => [
								'encoding' => 'UTF-8',
							],
						],
					],
				]);
			}

			if (!empty($field['multiple'])) {
				$inputFilter->add([
					'name' => $name,
					'required' => isset($field['REQUIRED']) ? $field['REQUIRED'] : NULL,
					'options' => [
						'disable_inarray_validator' => true,
					],
				]);
			}
		}


		return $inputFilter;
	}

	public function executeSql($sql, $parameters = [])
	{
		$sqlStatement = $this->getTableGateway()
			->getAdapter()->getDriver()
			->createStatement($sql);

		$sqlStatement->prepare();
		$data = $sqlStatement->execute($parameters);

		$resultSetPrototype = new ResultSet();
		$resultSetPrototype->initialize($data);

		return $resultSetPrototype->toArray();
	}

	public function getSql()
	{
		$sql = new Sql($this->tableGateway->getAdapter());
		return $sql;
	}

	public function fetchSql($sql, $select)
	{
		try {
			$stmt = $sql->prepareStatementForSqlObject($select);
			$results = $stmt->execute();
		} catch (\Exception $e) {
			throw new \Exception($e);
			error_log($e->getMessage());
		}
		return $results;
	}

	public function getAllData($dataKey)
	{
		try {
			$data = $this->tableGateway->select($dataKey);
		} catch (\Exception $e) {
			throw new \Exception($e);
			error_log($e->getMessage());
		}

		return $data;
	}

	public function convertResult($result)
	{
		$resultSet = new ResultSet();
		return $resultSet->initialize($result);
	}
}
