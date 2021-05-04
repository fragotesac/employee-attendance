<?php

namespace Dashboard\Controller;

use Business\Model\AsistenciaTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AsistenciasController extends AbstractActionController
{
	 public $asistencia;

    public function __construct(AsistenciaTable $asistencia)
    {
         $this->asistencia = $asistencia;
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        $startDate = strtotime('last monday midnight', time());
        $endDate = strtotime('next sunday', strtotime(date('d-m-Y 23:59:59', $startDate)));

        if ($request->isPost()) {
            $post = $request->getPost();
            $startDate = strtotime($post->start_date);
            $endDate = strtotime(date('d-m-Y 23:59:59', strtotime($post->end_date)));
        }

        $dayAttendance = $this->dayAttendance(
            $this->asistencia->getAttendance($startDate, $endDate)
        );

        return new ViewModel([
            'dayList' => $dayAttendance,
            'startDate' => date('d-m-Y', $startDate),
            'endDate' => date('d-m-Y', $endDate),
        ]);
    }

    private function dayAttendance($result) : array
    {
        $data = [];
        foreach ($result as $item) {
            $createdAt = $item['created_at'];
            $date = date('Y-m-d', $createdAt);
            $item['fulldate'] = date('d-m-Y H:i:s', $createdAt);
            $item['year'] = date('Y', $createdAt);
            $item['month'] = date('M', $createdAt);
            $item['day'] = date('d', $createdAt);
            $item['hour'] = date('H:i a', $createdAt);
            $data[$item['user_id']]['full_name'] = $item['full_name'];
            $data[$item['user_id']][$date][] = $item;
        }

        return $data;
    }
}
