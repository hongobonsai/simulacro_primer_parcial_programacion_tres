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

echo "<br>-Pizza Carga-<br>";
$imagenesPizzasPath = "./ImagenesDePizzas/";
$pizzasPath = "Files/Pizzas/Pizzas.json";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $pizza = new Pizza($_POST["_sabor"], floatval($_POST["_precio"]), $_POST["_tipo"], intval($_POST["_cantidad"]));
  Pizza::IngresarPizzaJson($pizza, $pizzasPath, $imagenesPizzasPath);
} else {
  echo "<br>Error: La solicitud enviada es incorrecta.<br>";
}
