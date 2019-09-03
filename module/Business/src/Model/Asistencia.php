<?php

/**
 * Asistencia Model
 * @autor Francis Gonzales <fgonzalestello91@gmail.com>
 */

namespace Business\Model;

class Asistencia
{
	const STATUS_ENTRADA = 1;
	const STATUS_SALIDA_ALMUERZO = 2;
	const STATUS_REGRESO_ALMUERZO = 3;
	const STATUS_SALIDA = 4;
	const STATUSES = [
		self::STATUS_ENTRADA => 'ENTRADA',
		self::STATUS_SALIDA_ALMUERZO => 'SALIDA ALMUERZO',
		self::STATUS_REGRESO_ALMUERZO => 'REGRESO ALMUERZO',
		self::STATUS_SALIDA => 'SALIDA',
	];

   public $id;
   public $userId;
	public $observacion;
	public $status;
   public $createdAt;

   public function exchangeArray($data)
   {
      $this->id = (isset($data['id'])) ? $data['id'] : null;
      $this->userId = (isset($data['user_id'])) ? $data['user_id'] : null;
	   $this->status = (isset($data['status'])) ? $data['status'] : null;
      $this->observacion = (isset($data['observacion'])) ? $data['observacion'] : null;
      $this->createdAt= (isset($data['created_at'])) ? $data['created_at'] : null;
   }

   public function getArrayCopy()
   {
      return get_object_vars($this);
   }
}
