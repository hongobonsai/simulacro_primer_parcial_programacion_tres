<?php

/******************************************************************************

4- (3 pts.)ConsultasVentas.php: necesito saber :
a- la cantidad de pizzas vendidas
b- el listado de ventas entre dos fechas ordenado por sabor.
c- el listado de ventas de un usuario ingresado
d- el listado de ventas de un sabor ingresado

ALONSO NICOLÁS GABRIEL

 ********************************************************************************/

echo "<br>-Consultas Ventas-<br>";
$pizzasPath = "Files/Pizzas/Pizzas.json";
$ventasPath = "Files/Ventas/Ventas.json";
$imagenesVentasPath = "./ImagenesDeLaVenta/";
$ventasArray = Utilities::JsonToArray($ventasPath);
if($ventasArray === false){
  echo "<br>No se ha vendido ninguna pizza.";
}else{
  switch ($_GET["_consulta"]) {
    case 'a':
      echo "<br>Cantidad de pizzas vendidas: ", Venta::CantidadPizzasVendidas($ventasArray);
      break;
    case 'b':
      $ventasEncontradas = Venta::ObtenerVentasEntreFechas($ventasArray);
      $ventasOrdenadas = Venta::OrdenarSaborAscendente($ventasEncontradas);
      echo "<br>Ventas ordenadas entre ",  $_GET["_fechaInicial"], " y ", $_GET["_fechaFinal"], " por sabor ascendente: <br>";
      Venta::ImprimirVentas($ventasOrdenadas);
      break;
    case 'c':
      $ventasEncontradas = Venta::ObtenerVentasDeUsuario($ventasArray);
      echo "<br>Ventas del usuario '", $_GET["_emailUsuario"], "':";
      Venta::ImprimirVentas($ventasEncontradas);
      break;
    case 'd':
      $ventasEncontradas = Venta::ObtenerVentasDeSabor($ventasArray);
      echo "<br>Ventas de sabor '", $_GET["_sabor"], "': <br>";
      Venta::ImprimirVentas($ventasEncontradas);
      break;
    default:
    echo "<br>Error: La solicitud enviada es incorrecta.<br>";
      break;
  } 
}
