<?php

/******************************************************************************

2da parte

3-
a- (1 pts.) AltaVenta.php: (por POST)se recibe el email del usuario y el sabor,tipo y cantidad ,si el ítem existe en
Pizza.json, y hay stock guardar en la base de datos( con la fecha, número de pedido y id autoincremental ) y se
debe descontar la cantidad vendida del stock .
b- (1 pt) completar el alta con imagen de la venta , guardando la imagen con el tipo+sabor+mail(solo usuario hasta
el @) y fecha de la venta en la carpeta /ImagenesDeLaVenta.

ALONSO NICOLÁS GABRIEL

 ********************************************************************************/

 echo "<br>-Alta Venta-<br>";
 $pizzasPath = "Files/Pizzas/Pizzas.json";
 $ventasPath = "Files/Ventas/Ventas.json";
 $imagenesVentasPath = "./ImagenesDeLaVenta/";
 if (isset($_POST['_fecha'])) {
  Venta::IngresarVenta($_POST["_emailUsuario"], $_POST["_sabor"], $_POST["_tipo"], intval($_POST["_cantidad"]), $pizzasPath, $ventasPath, $imagenesVentasPath, $_POST["_fecha"]);
} else {
  Venta::IngresarVenta($_POST["_emailUsuario"], $_POST["_sabor"], $_POST["_tipo"], intval($_POST["_cantidad"]), $pizzasPath, $ventasPath, $imagenesVentasPath);
}