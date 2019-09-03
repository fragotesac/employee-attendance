<?php

namespace Business\Abstraction;

use Business\Model\DocumentoCabecera;
use Business\Model\DocumentoCabeceraTable;
use Business\Model\DocumentoDetalle;
use Business\Model\DocumentoDetalleTable;
use Business\Util\Excel;
use Business\Util\Fecha;
use Business\View\Helper\PKs;
use Zend\Http\Headers;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Permissions\Acl\Acl;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Form;

/**
 * Description of Abstraction/Controller
 *
 * @author Francis Gonzales <fgonzalestello91@gmail.com>
 */
abstract class ControllerCRUD extends AbstractActionController
{

	private $table;
	protected $titulo;
	protected $tableIds;
	protected $columnasListar;
	protected $columnasAgregar;
	protected $describeColumnas;
	protected $indexRedirect;
	protected $dscEliminar;
	protected $defaultOrder;
	protected $controlador;


	public function setControlador($defaulController)
	{
		$this->controlador = $defaulController;
	}

	public function getDefaultOrder()
	{
		return $this->defaultOrder;
	}

	public function setDefaultOrder($defaultOrder)
	{
		$this->defaultOrder = $defaultOrder;
	}

	public function getColumnasAgregar()
	{
		return $this->columnasAgregar;
	}

	public function setColumnasAgregar($columnasAgregar)
	{
		$this->columnasAgregar = $columnasAgregar;
	}

	public function getDscEliminar()
	{
		return $this->dscEliminar;
	}

	public function setDscEliminar($dscEliminar)
	{
		$this->dscEliminar = $dscEliminar;
		return $this;
	}

	public function getTableIds()
	{
		return $this->tableIds;
	}

	public function setTableIds($tableIds)
	{
		$this->tableIds = $tableIds;
		return $this;
	}

	public function getTitulo()
	{
		return $this->titulo;
	}

	public function getColumnasListar()
	{
		return $this->columnasListar;
	}

	public function getDescribeColumnas()
	{
		return $this->describeColumnas;
	}

	public function getIndexRedirect()
	{
		return $this->indexRedirect;
	}

	public function setTitulo($titulo)
	{
		$this->titulo = $titulo;
		return $this;
	}

	public function setColumnasListar($columnasListar)
	{
		$this->columnasListar = $columnasListar;
		return $this;
	}

	public function setDescribeColumnas($describeColumnas)
	{
		$this->describeColumnas = $describeColumnas;
		return $this;
	}

	public function setIndexRedirect($indexRedirect)
	{
		$this->indexRedirect = $indexRedirect;
		return $this;
	}

	public function setIdentity($identity)
	{
	   $this->table->identity = $identity;
	}

	public function __construct($table)
	{
		$this->table = $table;
	}

	public function indexAction()
	{
		if (!empty($this->getIndexRedirect())) {
			$this->redirect()->toRoute($this->getIndexRedirect());
		} else {
			$viewModel = new ViewModel();
			$viewModel->setTemplate('business/crud/index.phtml');

			return $viewModel;
		}
	}

	public function listarAction()
	{
		$key = $this->params()->fromQuery('columna', null);
		$report = $this->params()->fromQuery('report', null);
		$queryData = $this->params()->fromQuery();

		if (!empty($key)) {
			$value = $queryData[$key];
		}
		
		$whereCond = [];
		$orderBy = [];

		$this->setIdentity($this->identity());
		if (!empty($this->getDefaultOrder())) {
			$orderBy = $this->getDefaultOrder();
		}

		if (!empty($value)) {
			$whereCond[$key] = $value;
		}

		$paginator = $this->table->fetchAll(true, $whereCond, $orderBy);
		// Report
		if (!empty($report)) {
			$paginator->setItemCountPerPage(-1);
			return $this->report($paginator, $report);
		}

		$page = (int) $this->params()->fromQuery('page', 1);
		$page = ($page < 1) ? 1 : $page;
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage(10);

		$viewModel = new ViewModel();
		$viewModel->tableIds = $this->getTableIds();
		$viewModel->titulo = $this->getTitulo();
		$viewModel->paginator = $paginator;
		$viewModel->urlPrefix = $this->getUrlPrefix();
		$viewModel->columnas = $this->getColumnasListar();
		$viewModel->dscColumn = $this->getDescribeColumnas();
		$viewModel->fk = $this->getDataFk($this->getDescribeColumnas(), true);

		$viewModel->setTemplate('business/crud/listar.phtml');

		return $viewModel;
	}

	private function listarData($paginator)
	{
		$data = [array_values($this->getColumnasListar())];
		$pks = new PKs();
		foreach ($paginator as $fila):
			$dataTmp = [];
			foreach ($this->getColumnasListar() as $id => $cabecera) {
				if (!empty($this->getDescribeColumnas()[$id]['FK']) || !empty($this->getDescribeColumnas()[$id]['FUNC'])) {
					$fn = $this->getDescribeColumnas()[$id]['FUNC'];
					if (empty($this->getDescribeColumnas()[$id]['multiple'])) {
						$dataTmp[] = !empty($this->getDataFk($this->getDescribeColumnas(), true)[$fn][$fila->$id]) ? $this->getDataFk($this->getDescribeColumnas(), true)[$fn][$fila->$id] : 'NULL';
					} else {
						if (!empty($this->getDataFk($this->getDescribeColumnas(), true)[$fn][$pks->__invoke($this->getTableIds(), $fila, true, true)])) {
							$dataTmp[] = !empty($this->getDataFk($this->getDescribeColumnas(), true)[$fn][$pks->__invoke($this->getTableIds(), $fila, true, true)]) ? $this->getDataFk($this->getDescribeColumnas(), true)[$fn][$pks->__invoke($this->getTableIds(), $fila, true, true)]: 'NULL';
						}
					}
				} else {
					$dataTmp[] = $fila->$id;
				}

			}
			$data[] = $dataTmp;
		endforeach;

		return $data;
	}

	private function report($data, $tipo = 'excel')
	{
		if ($tipo == 'excel') {
			$excel = new Excel();
			$detalle = [];
			foreach ($data as $row) {
				$detalle[] = (array)$row;
			}

			$excel->exportar($this->getColumnasListar(), $detalle, 'reporte');

			return ;
		}
	}

	public function agregarAction()
	{
		$form = new Form('agregar');

		$request   = $this->getRequest();

		$columnasDetalle = $this->getDescribeColumnas();

		$viewModel = new ViewModel();
		$viewModel->titulo = $this->getTitulo();
		$viewModel->form = $form;
		$viewModel->campos = $columnasDetalle;
		$viewModel->camposDescripcion = (!empty($this->getColumnasAgregar()) ? $this->getColumnasAgregar() : $this->getColumnasListar());

		$viewModel->setTemplate('business/crud/agregar.phtml');

		if (!$request->isPost()) {
			$viewModel->fk = $this->getDataFk($columnasDetalle);

			return $viewModel;
		}

		$form->setInputFilter($this->table->getInputFilter($this->getDescribeColumnas()));
		$especiales = $this->columnasEspeciales();
		if (!empty($especiales)) {
			foreach ($especiales as $name => $especial) {
				if (!empty($especial['DATE']) || (!empty($especial['TIPO']) && $especial['TIPO'] == 'DATE')) {
					$request->getPost()->$name = Fecha::formatoFechaGuardar($request->getPost()->$name);
				}
			}
		}

		$form->setData($request->getPost());

		if (!$form->isValid()) {
			$mensajeValidacion = '';
			foreach ($form->getInputFilter()->getInvalidInput() as $error) {
				foreach ($error->getMessages() as $keyId => $detalle) {
					$mensajeValidacion .=  $detalle;
				}
			}

			$this->flashMessenger()->addWarningMessage(['El formulario no es válido', str_replace("'", '', $mensajeValidacion)]);

			return $viewModel;
		}

		try {
			$data = (array)$request->getPost();
			unset($data['submit']);
			unset($data['reset']);
			$inserted = $this->table->insertData($data);
			$viewModel->result = $inserted['result'];

			$dscItem = $this->obtenerDatosImportantes($this->getDscEliminar(), [$data]);
			$this->flashMessenger()->addSuccessMessage(['Agregado Correctamente', 'Se agregó correctamente el registro ' . $dscItem]);

			$this->redirect()->toRoute($this->getIndexRedirect());
		} catch (\Exception $ex) {
			$this->flashMessenger()->addErrorMessage([
				'Hemos Detectado Problemas',
				'Detalle del error: ' . strip_tags($ex->getCode())
			]);
			$viewModel->result = 0;
		}

		return $viewModel;
	}

	public function editarAction()
	{
		$viewModel = new ViewModel();
		$request = $this->getRequest();

		$paramsId = $this->getTableIds();

		foreach ($paramsId as $tableId) {
			$id[$tableId] = $this->params($tableId);
		}

		if (empty($id)) {
			return $this->redirect()->toRoute($this->getIndexRedirect());
		}

		$data = $this->table->getData($id);
		$especiales = $this->columnasEspeciales();
		if (!empty($especiales)) {
			foreach ($especiales as $name => $especial) {
				if (!empty($especial['DATE']) || (!empty($especial['TIPO']) && $especial['TIPO'] == 'DATE')) {
					$data['data'][$name] = Fecha::formatoFechaMostrar($data['data'][$name]);
				}
			}
		}

		$form = new Form('agregar');

		$viewModel->titulo = $this->getTitulo();
		$viewModel->form = $form;
		$viewModel->campos = $this->getDescribeColumnas();
		$viewModel->defaultValue = $data['data'];
		$viewModel->camposDescripcion = (!empty($this->getColumnasAgregar()) ? $this->getColumnasAgregar() : $this->getColumnasListar());
		$viewModel->isEdit = true;
		$viewModel->setTemplate('business/crud/agregar.phtml');

		if (!$request->isPost()) {
			$viewModel->fk = $this->getDataFk($this->getDescribeColumnas());

			return $viewModel;
		}

		$form->setInputFilter($this->table->getInputFilter($this->getDescribeColumnas()));

		if (!empty($especiales)) {
			foreach ($especiales as $name => $especial) {
				if (!empty($especial['DATE']) || (!empty($especial['TIPO']) && $especial['TIPO'] == 'DATE')) {
					$request->getPost()->$name = Fecha::formatoFechaGuardar($request->getPost()->$name);
				}
			}
		}
		$form->setData($request->getPost());

		if (! $form->isValid()) {
			$mensajeValidacion = '';
			foreach ($form->getInputFilter()->getInvalidInput() as $error) {
				foreach ($error->getMessages() as $keyId => $detalle) {
					$mensajeValidacion .= $detalle;
				}
			}

			$this->flashMessenger()->addWarningMessage(['El formulario no es válido', $mensajeValidacion]);

			return null;
		}

		$setValues = $request->getPost()->toArray();
		$intersec = array_intersect_key($setValues, $id);

		foreach ($setValues as $key => $value) {
			if (array_key_exists($key, $intersec)) {
				unset($setValues[$key]);
			}
		}

		unset($setValues['submit']);

		$this->table->updateData($id, $setValues);

		if (!empty($this->indexRedirect)) {
			$dscItem = $this->obtenerDatosImportantes($this->getDscEliminar(), [array_merge($setValues, $id)]);
			$this->flashMessenger()->addInfoMessage(['Actualizado Correctamente', 'Se actualizó correctamente el registro ' . $dscItem]);
			$this->redirect()->toRoute($this->getIndexRedirect());
		} else {
			$viewModel->setTemplate('business/crud/index.phtml');

			return $viewModel;
		}
	}

	public function eliminarAction()
	{
		$viewModel = new ViewModel();
		$paramsId = $this->getTableIds();

		foreach ($paramsId as $tableId) {
			$id[$tableId] = $this->params($tableId);
		}

		if (empty($id)) {
			return $this->redirect()->toRoute($this->getIndexRedirect());
		}

		$request = $this->getRequest();

		$dataDelete = $this->table->getData($id);
		$dataDelete = $dataDelete['data'];
		$dataDeleDesc = $dataDelete;
		$dscItemEliminar = $this->obtenerDatosImportantes($this->getDscEliminar(), $dataDeleDesc);

		if ($request->isPost()) {
			$del = $request->getPost('del', 'No');
			$eliminadoCorrectamente = false;
			if ($del == 'Si') {
				try {
					$eliminadoCorrectamente = $this->table->deleteData($id);

					if (!$eliminadoCorrectamente) {
						$this->flashMessenger()->addErrorMessage(['Hemos Detectado Problemas', 'No se pudo elimnar el registro, revise que no hayan datos relacionados.']);
					}
				} catch (\Exception $e) {
					$this->flashMessenger()->addErrorMessage(['Hemos Detectado Problemas', 'Detalle del error: ' . $e->getCode()]);
				}
			}

			if ($eliminadoCorrectamente) {
				$this->flashMessenger()->addInfoMessage(['Eliminado Correctamente', 'Se eliminó correctamente el registro ' . $dscItemEliminar]);
				$this->redirect()->toRoute($this->getIndexRedirect());
			}
		}

		$viewModel->setTemplate('business/crud/eliminar.phtml');

		$viewModel->ids = $id;
		$viewModel->titulo = 'Eliminar ' . $this->getTitulo();
		$viewModel->data = $dataDelete;
		$viewModel->dscItemEliminar = $dscItemEliminar;

		return $viewModel;
	}

	public function obtenerDatosImportantes($descripcionImportant, $dataDeleDesc)
	{
		$dscItemEliminar = '';
		foreach ($descripcionImportant as $desDel) {
			$dscItemEliminar .= !empty($dataDeleDesc[$desDel]) ? $dataDeleDesc[$desDel] : '' . ' ';
		}

		return $dscItemEliminar;
	}

	public function getDataFk($columnasDetalle, $listar = false)
	{
		$data = [];

		foreach ($columnasDetalle as $value) {
			if (!empty($value['FK']) || !empty($value['FUNC'])) {
				$fn = $value['FUNC'];
				if ($listar && !empty($value['PK'])) {
					$data[$fn] = $this->table->$fn($this->getTableIds());
				} else {
					$data[$fn] = $this->table->$fn();
				}
			}
		}

		return $data;
	}

	private function columnasEspeciales()
	{
		$especiales = [];
		foreach($this->describeColumnas as $name => $columnas) {
			if (!empty($columnas['DATE']) || (!empty($columnas['TIPO']) && $columnas['TIPO'] == 'DATE')) {
				$especiales[$name] = $columnas;
			}
		}
		return $especiales;
	}

	private function getCurrentClass()
	{
		$pattern = '/(?#! splitCamelCase Rev:20140412)
       # Split camelCase "words". Two global alternatives. Either g1of2:
         (?<=[a-z])      # Position is after a lowercase,
         (?=[A-Z])       # and before an uppercase letter.
       | (?<=[A-Z])      # Or g2of2; Position is after uppercase,
         (?=[A-Z][a-z])  # and before upper-then-lower case.
       /x';

		return implode('-', preg_split($pattern, get_called_class()));
	}

	public function getUrlPrefix()
	{
		return strtolower(str_replace(['\Controller\\', 'Controller'], ['-', ''], $this->getCurrentClass()));
	}
}
