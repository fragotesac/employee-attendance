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
}
