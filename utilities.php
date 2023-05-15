<?php
class Utilities
{
  public static function JsonToArray($path)
  {
    if ($path == null) {
      echo "Se debe recibir una ruta válida.";
      return false;
    }
    if(!file_exists($path)){
      return false;
    }
    try {
      $jsonString = file_get_contents($path);
      if ($jsonString === null && json_last_error() !== JSON_ERROR_NONE) {
        echo "La cadena recibida no es un JSON válido";
        return false;
      }
      $jsonArray = json_decode($jsonString, true);
      return $jsonArray;
    } catch (\Exception $e) {
      echo "<br>", $e->getMessage(), "<br>";
    }
    return false;
  }
}