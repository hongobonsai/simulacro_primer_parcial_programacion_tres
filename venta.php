<?php

/******************************************************************************

2da parte

3-
a- (1 pts.) AltaVenta.php: (por POST)se recibe el email del usuario y el sabor,tipo y cantidad ,si el ítem existe en
Pizza.json, y hay stock guardar en la base de datos( con la fecha, número de pedido y id autoincremental ) y se
debe descontar la cantidad vendida del stock .

ALONSO NICOLÁS GABRIEL

 ********************************************************************************/

class Venta
{
  private $_emailUsuario;
  private $_sabor;
  private $_tipo;
  private $_cantidad;
  private $_fecha;
  private $_numeroDePedido;
  private $_id;

  public function __construct($emailUsuario, $sabor, $tipo, $cantidad, $fecha = null, $numeroDePedido = null, $id = null)
  {
    if (!filter_var($emailUsuario, FILTER_VALIDATE_EMAIL) || $emailUsuario == null) {
      throw new InvalidArgumentException("Formato de mail incorrecto");
    }
    if (!is_string($sabor) || $sabor == null) {
      throw new InvalidArgumentException("El sabor debe ser una cadena de caracteres");
    }
    if (preg_match('/\s/', $tipo) || $tipo == null) {
      if ($tipo !== "molde" && $tipo !== "piedra")
        throw new InvalidArgumentException("El tipo debe ser 'molde' o 'piedra'");
    }
    if (!is_int($cantidad) || $cantidad == null) {
      throw new InvalidArgumentException("La cantidad debe ser un numero entero");
    }
    $this->_emailUsuario = $emailUsuario;
    $this->_sabor = $sabor;
    $this->_tipo = $tipo;
    $this->_cantidad = $cantidad;
    if ($fecha == null) {
      date_default_timezone_set("America/Argentina/Buenos_Aires");
      $this->_fecha = date("d/m/Y");
    } else {
      $this->_fecha = $fecha;
    }
    if ($numeroDePedido == null) {
      $this->_numeroDePedido = rand(20000, 29999);
    } else {
      $this->_numeroDePedido = $numeroDePedido;
    }
    if ($id == null) {
      $this->_id = rand(0, 10000);
    } else {
      $this->_id = $id;
    }
  }

  public function GetEmailUsuario()
  {
    return $this->_emailUsuario;
  }
  public function GetSabor()
  {
    return $this->_sabor;
  }
  public function GetTipo()
  {
    return $this->_tipo;
  }
  public function GetCantidad()
  {
    return $this->_cantidad;
  }
  public function GetFecha()
  {
    return $this->_fecha;
  }
  public function GetNumeroPedido()
  {
    return $this->_numeroDePedido;
  }
  public function GetId()
  {
    return $this->_id;
  }

  public function VentaToArray()
  {
    if ($this == null) {
      throw new InvalidArgumentException("El objeto recibido no puede ser null.");
    }
    if (!is_a($this, 'Venta')) {
      throw new InvalidArgumentException("El objeto recibido no es una venta.");
    }

    $ventaArray = array(
      "_emailUsuario" => $this->GetEmailUsuario(),
      "_sabor" => $this->GetSabor(),
      "_tipo" => $this->GetTipo(),
      "_cantidad" => $this->GetCantidad(),
      "_fecha" => $this->GetFecha(),
      "_numeroDePedido" => $this->GetNumeroPedido(),
      "_id" => $this->GetId()
    );
    return $ventaArray;
  }
  //a- (1 pts.) AltaVenta.php: (por POST)se recibe el email del usuario y el sabor,tipo y cantidad ,si el ítem existe en
  //Pizza.json, y hay stock guardar en la base de datos( con la fecha, número de pedido y id autoincremental ) y se
  //debe descontar la cantidad vendida del stock .

  public static function IngresarVenta($emailUsuario, $sabor, $tipo, $cantidad, $pizzasPath, $ventasPath, $imagesPath, $fecha = null)
  {
    if ($sabor == null) {
      echo "El sabor recibido no puede ser null.";
      return false;
    }
    if (preg_match('/\s/', $tipo) || $tipo == null) {
      if ($tipo != "molde" && $tipo != "piedra")
        echo "El tipo debe ser 'molde' o 'piedra'";
        return false;
    }
    $pizzasArray = Utilities::JsonToArray($pizzasPath);
    if($pizzasArray !== false){
      $pizzaEncontrada = Pizza::ExistePizza($sabor, $tipo, $pizzasPath);
      if($pizzaEncontrada === false){
        echo "No hay pizzas de ", $sabor, " tipo ", $tipo, " cargadas en el sistema.";
        return false;
      }
      if ($pizzasArray[$pizzaEncontrada]["_cantidad"] >= $cantidad && $pizzaEncontrada !== false) {
        //Actualizado de Stock
        $pizzasArray[$pizzaEncontrada]["_cantidad"] -= $cantidad;
        $json = json_encode($pizzasArray);
        file_put_contents($pizzasPath, $json);
        echo "<br>Se actualizó el stock de la pizza de ", $sabor, " del tipo ", $tipo,
        " correctamente.<br>Nuevo stock: ", $pizzasArray[$pizzaEncontrada]["_cantidad"], "<br>";
      } else {
        echo "No hay stock de ", $sabor, ".";
        return false;
      }
      if (file_exists($ventasPath)) {
        $venta = new Venta($emailUsuario, $sabor, $tipo, $cantidad, $fecha);
        $ventaArray = $venta->VentaToArray();
        $ventasArray = Utilities::JsonToArray($ventasPath);
        array_push($ventasArray, $ventaArray);
        $json = json_encode($ventasArray);
        $bytes = file_put_contents($ventasPath, $json);
        echo "<br>Se sobrescribió el archivo 'Ventas.json'. Peso actual: $bytes bytes.";
        Venta::SubirImagenVenta($emailUsuario, $sabor, $tipo, $venta->GetFecha(), $imagesPath);
      } else {
        $venta = new Venta($emailUsuario, $sabor, $tipo, $cantidad, $fecha);
        $ventaArray = $venta->VentaToArray();
        $ventasArray = array();
        array_push($ventasArray, $ventaArray);
        $json = json_encode($ventasArray);
        $bytes = file_put_contents($ventasPath, $json);
        echo "<br>El archivo de ventas no existe. Se creo el archivo 'Ventas.json'. Peso actual: $bytes bytes.";
        Venta::SubirImagenVenta($emailUsuario, $sabor, $tipo, $venta->GetFecha(), $imagesPath);
      }
    } else{
      echo "No hay pizzas cargadas en el sistema.";
      return false;
    }
  }

  //b- (1 pt) completar el alta con imagen de la venta , guardando la imagen con el tipo+sabor+mail(solo usuario hasta
  //el @) y fecha de la venta en la carpeta /ImagenesDeLaVenta.

  public static function SubirImagenVenta($emailUsuario, $sabor, $tipo, $fecha, $imagesPath)
  {
    if (isset($_FILES['_imagenVenta'])) {
      $nombreArchivo = $tipo . "_" . $sabor . "_" . substr($emailUsuario, 0, strpos($emailUsuario, "@")) . "_" . str_replace('/', '-', $fecha) . "." . pathinfo($_FILES['_imagenVenta']['name'], PATHINFO_EXTENSION);
      $destino = $imagesPath . $nombreArchivo;
      if (move_uploaded_file($_FILES['_imagenVenta']['tmp_name'], $destino)) {
        echo "<br>Se cargó el archivo '", $nombreArchivo, "' en el servidor.<br>";
      }
    } else {
      echo "<br>No hay archivos seleccionados.<br>";
    }
  }
  public static function OrdenarSaborAscendente($ventas)
  {
    function comparar_por_sabor($a, $b)
    {
      return strcmp($a["_sabor"], $b["_sabor"]);
    }
    usort($ventas, 'comparar_por_sabor');
    return $ventas;
  }

  public static function CantidadPizzasVendidas($ventasArray)
  {
    $pizzasVendidas = 0;
    foreach ($ventasArray as $venta) {
      $pizzasVendidas += intval($venta["_cantidad"]);
    }
    return $pizzasVendidas;
  }
  public static function ObtenerVentasEntreFechas($ventasArray)
  {
    $ventasEncontradas = array();
    foreach ($ventasArray as $venta) {
      if ($venta["_fecha"] > $_GET["_fechaInicial"] && $venta["_fecha"] < $_GET["_fechaFinal"]) {
        $venta = array(
          "_emailUsuario" => $venta["_emailUsuario"],
          "_id" => $venta["_id"],
          "_sabor" => $venta["_sabor"],
          "_fecha" => $venta["_fecha"]
        );
        array_push($ventasEncontradas, $venta);
      }
    }
    return $ventasEncontradas;
  }
  public static function ObtenerVentasDeUsuario($ventasArray)
  {
    $ventasEncontradas = array();
    foreach ($ventasArray as $venta) {
      if ($venta["_emailUsuario"] == $_GET["_emailUsuario"]) {
        $venta = array(
          "_emailUsuario" => $venta["_emailUsuario"],
          "_id" => $venta["_id"],
          "_sabor" => $venta["_sabor"],
          "_fecha" => $venta["_fecha"]
        );
        array_push($ventasEncontradas, $venta);
      }
    }
    return $ventasEncontradas;
  }
  public static function ObtenerVentasDeSabor($ventasArray)
  {
    $ventasEncontradas = array();
    foreach ($ventasArray as $venta) {
      if ($venta["_sabor"] == $_GET["_sabor"]) {
        $venta = array(
          "_emailUsuario" => $venta["_emailUsuario"],
          "_id" => $venta["_id"],
          "_sabor" => $venta["_sabor"],
          "_fecha" => $venta["_fecha"]
        );
        array_push($ventasEncontradas, $venta);
      }
    }
    return $ventasEncontradas;
  }
  public static function ImprimirVentas($ventasArray)
  {
    foreach ($ventasArray as $i => $venta) {
      echo "<br>Venta número ", $i + 1;
      echo "<br>Vendedor: ", $venta["_emailUsuario"];
      echo "<br>Id venta: ", $venta["_id"];
      echo "<br>Sabor: ", $venta["_sabor"];
      echo "<br>Fecha: ", $venta["_fecha"], "<br>";
    }
  }
  public static function ModificarVenta($numeroDePedido, $emailUsuario, $sabor, $tipo, $cantidad, $path)
  {
    $ventasArray = Utilities::JsonToArray($path);
    $arrayModificado = array();
    $ventaEncontrada = false;
    foreach ($ventasArray as $i => $venta) {
      if (isset($venta["_numeroDePedido"]) && ($venta["_numeroDePedido"] === $numeroDePedido)) {
        $venta["_emailUsuario"] = $emailUsuario;
        $venta["_sabor"] = $sabor;
        $venta["_tipo"] = $tipo;
        $venta["_cantidad"] = $cantidad;
        $ventaEncontrada = true;
      }
      array_push($arrayModificado, $venta);
    }
    if ($ventaEncontrada == true) {
      $json = json_encode($arrayModificado);
      if (file_put_contents($path, $json) !== false) {
        echo "<br>Se modifico la venta con numero de pedido: ", $numeroDePedido, ".";
        return $arrayModificado;
      } else {
        echo "<br>No se pudo modificar la venta.";
        return false;
      }
    } else {
      echo "<br>No se encontró el numero de pedido. No se modificará la venta.";
      return false;
    }
  }
  public static function BorrarVenta($numeroDePedido, $path)
  {
    //7- (2 pts.) borrarVenta.php(por DELETE), debe recibir un número de pedido,se borra la venta y la foto se mueve a
    //la carpeta /BACKUPVENTAS
    $ventasArray = Utilities::JsonToArray($path);
    $arrayModificado = array();
    $ventaEncontrada = false;
    foreach ($ventasArray as $i => $venta) {
      if (isset($venta["_numeroDePedido"]) && ($venta["_numeroDePedido"] !== $numeroDePedido)) {
        array_push($arrayModificado, $venta);
      } else {
        $ventaEncontrada = $venta;
      }
    }
    if ($ventaEncontrada !== false) {
      $json = json_encode($arrayModificado);
      if (file_put_contents($path, $json) !== false) {
        echo "<br>Se eliminó la venta con numero de pedido: ", $numeroDePedido, ".";
        if(Venta::EliminarImagenRelacionada($ventaEncontrada,  "./ImagenesDeLaVenta/")){
          echo "<br>La imagen de la venta fue movida a 'BACKUPVENTAS'";
        }
        return $arrayModificado;
      } else {
        echo "<br>No se pudo eliminar la venta.";
        return false;
      }
    } else {
      echo "<br>No se encontró el numero de pedido. No se eliminará la venta.";
      return false;
    }
  }
  public static function EliminarImagenRelacionada($venta, $path)
  {
    $datosImg = $venta["_tipo"] . "_" . $venta["_sabor"] . "_" . substr($venta["_emailUsuario"], 0, strpos($venta["_emailUsuario"], "@")) .
    "_" . str_replace('/', '-', $venta["_fecha"]) . ".png";
    $pathEliminar = $path . $datosImg;
    $pathDestino = "BACKUPVENTAS" . "/" . $datosImg;
    if (file_exists($pathEliminar)) {
      if(rename($pathEliminar, $pathDestino)){
        return true;
      }
    }
  }
}
