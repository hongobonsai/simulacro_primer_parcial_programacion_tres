<?php

/******************************************************************************

6- (2 pts.) ModificarVenta.php(por PUT), debe recibir el número de pedido, el email del usuario, el sabor,tipo y
cantidad, si existe se modifica , de lo contrario informar.

ALONSO NICOLÁS GABRIEL

 ********************************************************************************/


$ventasPath = "Files/Ventas/Ventas.json";
echo "<br>-Modificar Venta-<br>";
if ($_SERVER["REQUEST_METHOD"] === "PUT") {
  $ventasArray = Utilities::JsonToArray($ventasPath);
  if ($ventasArray === false) {
    echo "<br>No se ha vendido ninguna pizza.";
  } else {
    $jsonData = json_decode(file_get_contents('php://input'), true);
    Venta::ModificarVenta($jsonData["_numeroDePedido"], $jsonData["_emailUsuario"], $jsonData["_sabor"], $jsonData["_tipo"], $jsonData["_cantidad"], $ventasPath);
  }
} else {
  echo "<br>Error: La solicitud enviada es incorrecta.<br>";
}
