<?php

namespace Dashboard\Controller;

use Business\Model\Asistencia;
use Business\Model\AsistenciaTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
	 public $asistencia;

    public function __construct(AsistenciaTable $asistencia)
    {
         $this->asistencia = $asistencia;
    }

    public function indexAction()
    {
    	  if ($this->getRequest()->isPost()) {
    	  	   try {
		         $rt = $this->asistencia->insertData([
			         'observacion' => $this->params()->fromPost('observacion', null),
			         'user_id' => $this->identity()->id,
			         'status' => $this->params()->fromPost('status', null),
			         'created_at' => time()
		         ]);
		         $this->flashMessenger()->addSuccessMessage(['Guardado', 'Se guardaron los datos correctamente']);
	         } catch (\Exception $e) {
		         $this->flashMessenger()->addErrorMessage(['Error', 'No se pudo guardar la información enviada, e-codigo: ' . $e->getCode()]);
	         }
        }

	    $beginOfDay = strtotime('midnight', time());
	    $endOfDay = strtotime('tomorrow', $beginOfDay) - 1;

	    $dayAttendance = $this->asistencia->listOfAttendance($this->identity()->id, $beginOfDay, $endOfDay);

        return new ViewModel([
        	   'statuses' => Asistencia::STATUSES,
	         'dayList' => $dayAttendance
        ]);
    }
}
