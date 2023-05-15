<?php

/******************************************************************************

7- (2 pts.) borrarVenta.php(por DELETE), debe recibir un número de pedido,se borra la venta y la foto se mueve a
la carpeta /BACKUPVENTAS

ALONSO NICOLÁS GABRIEL

 ********************************************************************************/

 
$ventasPath = "Files/Ventas/Ventas.json";
echo "<br>-Borrar Venta-<br>";
if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
  $ventasArray = Utilities::JsonToArray($ventasPath);
  if ($ventasArray === false) {
    echo "<br>No se ha vendido ninguna pizza.";
  } else {
    $jsonData = json_decode(file_get_contents('php://input'), true);
    echo "Numero de venta a eliminar: ", $jsonData["_numeroDePedido"];
    Venta::BorrarVenta($jsonData["_numeroDePedido"], $ventasPath);
  }
} else{
  echo "<br>Error: La solicitud enviada es incorrecta.<br>";
}

