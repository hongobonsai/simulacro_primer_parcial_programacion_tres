<?php

/******************************************************************************

SIMULACRO PRIMER PARCIAL

Se debe realizar una aplicación para dar de ingreso con imagen del item.
Se deben respetar los nombres de los archivos y de las clases.
Se debe crear una clase en PHP por cada entidad y los archivos PHP solo deben llamar a métodos de las clases.

1era parte

1-
A- (1 pt.) index.php:Recibe todas las peticiones que realiza el postman, y administra a que archivo se debe incluir.
B- (1 pt.) PizzaCarga.php: (por POST)se ingresa Sabor, precio, Tipo (“molde” o “piedra”), cantidad( de unidades). Se
guardan los datos en en el archivo de texto Pizza.json, tomando un id autoincremental como
identificador(emulado) .Sí el sabor y tipo ya existen , se actualiza el precio y se suma al stock existente.
2-
(1pt.) PizzaConsultar.php: (por GET)Se ingresa Sabor,Tipo, si coincide con algún registro del archivo Pizza.json,
retornar “Si Hay”. De lo contrario informar si no existe el tipo o el sabor.

ALONSO NICOLÁS GABRIEL

 ********************************************************************************/

class Pizza
{
  private $_sabor;
  private $_precio;
  private $_tipo;
  private $_cantidad;
  private $_id;

  public function __construct($sabor, $precio, $tipo, $cantidad)
  {
    if (!is_string($sabor) || $sabor == null) {
      throw new InvalidArgumentException("El sabor debe ser una cadena de caracteres");
    }

    if (!is_float($precio) || $precio == null) {
      throw new InvalidArgumentException("El precio debe ser un numero flotante");
    }

    if (preg_match('/\s/', $tipo) || $tipo == null) {
      if ($tipo != "molde" && $tipo != "piedra")
        throw new InvalidArgumentException("El tipo debe ser 'molde' o 'piedra'");
    }

    if (!is_int($cantidad) || $cantidad == null) {
      throw new InvalidArgumentException("La cantidad debe ser un numero entero");
    }
    $this->_sabor = $sabor;
    $this->_precio = $precio;
    $this->_tipo = $tipo;
    $this->_cantidad = $cantidad;
    $this->_id = rand(0, 10000);
  }
  public function GetSabor()
  {
    return $this->_sabor;
  }
  public function GetPrecio()
  {
    return $this->_precio;
  }
  public function SetPrecio($precio)
  {
    if (!is_float($precio) || $precio == null) {
      return false;
    }
    $this->_precio = $precio;
  }
  public function GetTipo()
  {
    return $this->_tipo;
  }
  public function GetCantidad()
  {
    return $this->_cantidad;
  }
  public function SetCantidad($cantidad)
  {
    if (!is_int($cantidad) || $cantidad == null) {
      return false;
    }
    $this->_cantidad = $cantidad;
  }
  public function GetId()
  {
    return $this->_id;
  }
  public function PizzaToArray()
  {
    if ($this == null) {
      throw new InvalidArgumentException("El objeto recibido no puede ser null.");
    }
    if (!is_a($this, 'Pizza')) {
      throw new InvalidArgumentException("El objeto recibido no es una pizza.");
    }

    $pizzaArray = array(
      "_sabor" => $this->GetSabor(),
      "_precio" => $this->GetPrecio(),
      "_tipo" => $this->GetTipo(),
      "_cantidad" => $this->GetCantidad(),
      "_id" => $this->GetId()
    );
    return $pizzaArray;
  }
  public static function IngresarPizzaJson($pizza, $path, $imagesPath)
  {
    if ($pizza == null) {
      throw new InvalidArgumentException("El objeto recibido no puede ser null.");
    }
    if (!is_a($pizza, 'Pizza')) {
      throw new InvalidArgumentException("El objeto recibido no es una pizza.");
    }
    if (file_exists($path)) {
      $pizzaArray = $pizza->PizzaToArray();
      if (!Pizza::ActualizarPrecioCantidad($pizzaArray, $path)) {
        try {
          $pizzasArray = Utilities::JsonToArray($path);
          array_push($pizzasArray, $pizzaArray);
          $json = json_encode($pizzasArray);
          $bytes = file_put_contents($path, $json);
          echo "<br>Se sobrescribió el archivo 'Pizzas.json'. Peso actual: $bytes bytes.";
          Pizza::SubirImagenPizza($pizza->GetTipo(), $pizza->GetSabor(), $imagesPath);
        } catch (\Exception $e) {
          echo "<br>", $e->getMessage(), "<br>";
        }
      }
    } else {
      try {
      $pizzaArray = $pizza->PizzaToArray();
      $pizzasArray = array();
      array_push($pizzasArray, $pizzaArray);
      $json = json_encode($pizzasArray);
      $bytes = file_put_contents($path, $json);
      echo "<br>El archivo de pizzas no existe. Se creo el archivo 'Pizzas.json'. Peso actual: $bytes bytes.";
      Pizza::SubirImagenPizza($pizza->GetTipo(), $pizza->GetSabor(), $imagesPath);
    } catch (\Exception $e) {
      echo "<br>", $e->getMessage(), "<br>";
    }
    }
    return true;
  }
  public static function SubirImagenPizza($tipo, $sabor, $imagesPath)
  {
    if (isset($_FILES['_imagenPizza'])) {
      $nombreArchivo = $tipo . "_" . $sabor . "." . pathinfo($_FILES['_imagenPizza']['name'], PATHINFO_EXTENSION);
      $destino = $imagesPath . $nombreArchivo;
      if (move_uploaded_file($_FILES['_imagenPizza']['tmp_name'], $destino)) {
        echo "<br>Se cargó el archivo '", $nombreArchivo, "' en el servidor.<br>";
      }
    } else {
      echo "<br>No hay archivos seleccionados.<br>";
    }
  }
  public static function ExistePizza($sabor, $tipo, $path)
  {
    $pizzasArray = Utilities::JsonToArray($path);
    $pizzaEncontrada = false;
    foreach ($pizzasArray as $i => $pizza) {
      if (isset($pizza["_sabor"]) && ($pizza["_sabor"] === $sabor)) {
        if (isset($pizza["_tipo"]) && ($pizza["_tipo"] === $tipo)) {
          $pizzaEncontrada = $i;
          break;
        }
      }
    }
    //devuelve el index de la pizza encontrada, o false si no encuentra la pizza.
    return $pizzaEncontrada;
  }
  public static function InformarSiExisteSaborTipo($sabor, $tipo, $path)
  {
    $pizzasArray = Utilities::JsonToArray($path);
    $seEncontroSabor = false;
    $seEncontroTipo = false;
    foreach ($pizzasArray as $pizza) {
      if (isset($pizza["_sabor"]) && ($pizza["_sabor"] === $sabor)) {
        $seEncontroSabor = true;
        if (isset($pizza["_tipo"]) && ($pizza["_tipo"] === $tipo)) {
          $seEncontroTipo = true;
          echo "<br>Si hay.<br>";
          break;
        }
      }
    }
    if ($seEncontroSabor == false) {
      echo "<br>No se encontró el sabor ", $sabor, ".<br>";
    }
    if ($seEncontroSabor == true && $seEncontroTipo == false) {
      echo "<br>Se encontró el sabor ", $sabor, " pero no es tipo ", $tipo, ".<br>";
    }
  }
  public static function ActualizarPrecioCantidad($pizzaBuscada, $path)
  {
    $pizzaEncontrada = Pizza::ExistePizza($pizzaBuscada["_sabor"], $pizzaBuscada["_tipo"], $path);
    $pizzasArray = Utilities::JsonToArray($path);
    if ($pizzaEncontrada !== false) {
      $pizzasArray[$pizzaEncontrada]["_precio"] = $pizzaBuscada["_precio"];
      $pizzasArray[$pizzaEncontrada]["_cantidad"] += $pizzaBuscada["_cantidad"];
      $json = json_encode($pizzasArray);
      file_put_contents($path, $json);
      echo "<br>Se actualizó la pizza de ", $pizzaBuscada["_sabor"], " del tipo ", $pizzaBuscada["_tipo"], " correctamente.<br>";
      return true;
    }
    return false;
  }
}
