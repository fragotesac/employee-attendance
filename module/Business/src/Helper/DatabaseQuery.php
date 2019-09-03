<?php

namespace Business\Helper;

class DatabaseQuery
{
   public $sl;

   public function __construct($sl)
   {
      $this->sl = $sl;
   }

   public function getPksFromTable($tableName)
   {
      $query = 'show columns from ' . $tableName . ' where `Key` = "PRI";';

      return $this->executeQuery($query, []);
   }

   public function executeQuery($query, $params = [])
   {
      $results = $this->sl->query($query, $params);

      return $results;
   }

   public function deleteQuery($table, $fields, $values)
   {
      $sql = "DELETE FROM $table WHERE " . implode(' = ? AND ', $fields) . ' = ? ;';

      return $this->executeQuery($sql, $values);
   }

   public function insertQuery($table, $fields, $values)
   {
      $valuesTmp = implode(", ", array_fill(0, count($values), '?'));
      $sql = "INSERT INTO $table (" . implode(', ', $fields) .
             ") VALUES (" . $valuesTmp . ");";

      return $this->executeQuery($sql, $values);
   }

   public function obtenerTipoPlanilla()
   {
      $sql = "SELECT * FROM par_cal WHERE ESTADO = ? ORDER BY PCANOM;";
      return $this->executeQuery($sql, ['S']);
   }

   public function obtenerPeriodo($empCod, $tipoPlanilla)
   {
      $sql = "SELECT C.* FROM cronogra C 
        WHERE EMPCOD = ? AND PCACOD  = ? AND
           CALCOD <= (SELECT CALCOD FROM cronogra WHERE EMPCOD = ? AND PCACOD = ? AND CALEST = 'S')
           ORDER BY CALCOD DESC;";
      return $this->executeQuery($sql, [$empCod, $tipoPlanilla, $empCod, $tipoPlanilla]);
   }

   public function obtenerCentroCosto($empCod)
   {
      $sql = "SELECT *
        FROM ccosto
      WHERE EMPCOD = ?;";
      return $this->executeQuery($sql, [$empCod]);
   }

   public function obtenerSucursal($empCod)
   {
      $sql = "SELECT * FROM sucursal WHERE EMPCOD = ?;";
      return $this->executeQuery($sql, [$empCod]);
   }

   public function obtenerTrabajadores($empcod, $locCod, $calCodInicio, $ccoCod, $empleadoCod = '%', $calCodFin = null, $estadoCod = '%', $mostrarPersonalEmail = false)
   {
	   if (empty($calCodFin)) {
		   $calCodFin = $calCodInicio;
	   }

	   $estadoQuery = '';
	   if ($estadoCod != '%') {
		   $estadoQuery = " AND EM.LEIDO LIKE '$estadoCod' ";
	   	if ($estadoCod == -1) { // Pendientes
			   $estadoQuery = ' AND EM.ITEMS IS NULL ';
		   }
	   }

	   $permail = 'IF (EM.PERMAIL IS NULL, P.PERMAIL, EM.PERMAIL) as PERMAIL';
	   if ($mostrarPersonalEmail) {
		   $permail = 'P.PERMAIL as PERMAIL';
	   }

      $sql = "SELECT 
        P.PERCOD, P.PERNUMDOC, P.PERNOMCOM, 
        $permail, 
        '$locCod' AS LOCCOD,
         V.CALCOD AS CALCOD, S.SUCNOM, 
         EM.LEIDO, 
         DATE_FORMAT(EM.FEC_ENVIO,'%d/%m/%Y %H:%i:%s') AS FECENVIO,
         DATE_FORMAT(EM.FEC_LEIDO,'%d/%m/%Y %H:%i:%s') AS FECLEIDO,
         V.CALCOD as pcacod,
         S.EMPCOD,
         EM.EMPCOD as email_enviado,
         EM.ITEMS,
		(SELECT C.CALDES FROM cronogra C 
		WHERE C.EMPCOD = V.EMPCOD AND C.CALCOD = V.CALCOD) as CALDES
        FROM valores V 
        JOIN personal P ON (P.EMPCOD = V.EMPCOD AND
                               P.PERCOD = V.PERCOD)
        JOIN sucursal S ON (S.EMPCOD = V.EMPCOD AND
                                      S.SUCCOD = V.LOCCOD)
        LEFT JOIN email EM ON EM.EMPCOD = P.EMPCOD AND EM.PERCOD = V.PERCOD AND 
                                            EM.PCACOD = P.PCACOD AND EM.CALCOD = V.CALCOD
       WHERE 
                  V.EMPCOD = ?  AND
                   V.LOCCOD LIKE ? AND
                   V.CALCOD BETWEEN ? AND ? AND
                   V.CCOCOD like ? AND
                   V.TURCOD = '01' AND
		             P.PERCOD like ? 
		             $estadoQuery
         ORDER BY P.PERNOMCOM, V.CALCOD;";

      return $this->executeQuery($sql, [$empcod, $locCod, $calCodInicio, $calCodFin, $ccoCod, $empleadoCod]);
   }


	public function obtenerTrabajadoresCTS($empcod, $locCod, $calCodInicio, $ccoCod, $empleadoCod = '%', $calCodFinal = null, $estadoCod = '%', $mostrarPersonalEmail = false)
	{
		if (empty($calCodFinal)) {
			$calCodFinal = $calCodInicio;
		}

		$estadoQuery = '';
		if ($estadoCod != '%') {
			$estadoQuery = " AND EM.LEIDO LIKE '$estadoCod' ";
			if ($estadoCod == -1) { // Pendientes
				$estadoQuery = ' AND EM.ITEMS IS NULL ';
			}
		}

		$permail = 'IF (EM.PERMAIL IS NULL, P.PERMAIL, EM.PERMAIL) as PERMAIL';
		if ($mostrarPersonalEmail) {
			$permail = 'P.PERMAIL as PERMAIL';
		}

		$sql = "SELECT 
		P.PERCOD, P.PERNUMDOC, P.PERNOMCOM, 
      $permail, '$locCod' AS LOCCOD,
		CONCAT('CT', V.CALANO, V.CALMES, '0') AS CALCOD, S.SUCNOM, 
		EM.LEIDO, 
		DATE_FORMAT(EM.FEC_ENVIO,'%d/%m/%Y %H:%i:%s') AS FECENVIO,
		DATE_FORMAT(EM.FEC_LEIDO,'%d/%m/%Y %H:%i:%s') AS FECLEIDO,
		CONCAT('CT', V.CALANO, V.CALMES, '0') as pcacod,
		S.EMPCOD,
		EM.EMPCOD as email_enviado,
		EM.ITEMS,
		(SELECT C.CALDES FROM cronogra C 
		WHERE C.EMPCOD = V.EMPCOD AND C.PCACOD  = 'CTS' AND
		  C.CALCOD = CONCAT('CT', V.CALANO, V.CALMES, '0')) as CALDES
		FROM cts V 
		JOIN personal P ON (P.EMPCOD = V.EMPCOD AND
		                                        P.PERCOD = V.PERCOD)
		JOIN sucursal S ON (S.EMPCOD = V.EMPCOD AND
		                             S.SUCCOD = V.LOCCOD)
		LEFT JOIN email EM ON EM.EMPCOD = P.EMPCOD AND EM.PERCOD = V.PERCOD AND 
		                                   EM.PCACOD = 'CTS' AND EM.CALCOD = CONCAT('CT', V.CALANO, V.CALMES, '0')
		WHERE 
		         V.EMPCOD = ?  AND
		          V.LOCCOD LIKE ? AND
		          CONCAT('CT', V.CALANO, V.CALMES, '0') BETWEEN ? AND ? AND
		          V.CCOCOD like ? AND
		          P.PERCOD like ?
		          $estadoQuery
		ORDER BY P.PERNOMCOM, V.CALANO, V.CALMES;";

		return $this->executeQuery($sql, [$empcod, $locCod, $calCodInicio, $calCodFinal, $ccoCod, $empleadoCod]);
	}

    public function emailDetalle($empcod, $pcacod, $calcod, $percod)
    {
        $sql = "SELECT empcod, percod, pcacod, calcod, item, email, 
				leido, fec_envio, coalesce(fec_leido, '') as fec_leido, usucod, personal_email
				FROM email_detalle WHERE EMPCOD = ? AND PCACOD = ? AND CALCOD = ? AND PERCOD = ?;";
        return $this->executeQuery($sql, [$empcod, $pcacod, $calcod, $percod]);
    }

    public function obtenerEmail($percod)
    {
        $sql = "SELECT * FROM personal WHERE PERCOD = ?";
        return $this->executeQuery($sql, [$percod]);
    }
}
