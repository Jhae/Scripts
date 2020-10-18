<?php 
$conexion=new PDO('mysql:host=127.0.0.1;dbname=Romeros;port=3306;charset=utf8','root','');
#Datos armados provenientes de $_GET, $POST o$_REQUEST .
#La llaves serán la columnas en DB y los valores serán los datos a insertar.
#PDO se encargará de los tipos de datos.
$datos=[
	'nombre'=>'nuez',
	'estado'=>'1',
	'cantidad'=>'500',
	'precio'=>20,
];
$nombre_tabla='productos';
#Para validar el tipo de parámetro en PDO de tipo entero.
#El tipo "decimal" no debe ser incluido.
$PDO_tipos_numericos=['int','tinyint'];
#Consulta para obtener nombre de columna y su tipo de dato.
#	[
#		['columna','tipo_dato']
#	]
$result_set=$conexion->query("SELECT column_name,data_type FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$nombre_tabla';");
$result_set=$result_set->fetchAll(PDO::FETCH_ASSOC);

#Conversión del resultado para obtener array asociativo 
#	[
#		['columna'=>'tipo_dato']
#	]
$db_columnas=[];
foreach ($result_set as $db_columna) {
	$db_columnas[$db_columna['column_name']]=$db_columna['data_type'];
}
#Armado de parametros PDO. [':columna1',':columna2'...]
$datos_columnas=array_keys($datos);

for ($i=0;$i<count($datos_columnas);$i++) {
	$datos_columnas[$i]=':'.$datos_columnas[$i];
}

$sql_columnas=implode(',', array_keys($datos));
$sql_params=implode(',', $datos_columnas);

#Obtención de $columna1='valor1'; $$columna2='valor2'...
extract($datos);
#
$statement=$conexion->prepare("INSERT INTO $nombre_tabla($sql_columnas) VALUES($sql_params);");

foreach ($datos as $columna=>$valor) {
	$PDO_type_param=PDO::PARAM_STR;
	if (in_array($db_columnas[$columna], $PDO_tipos_numericos))
		$PDO_type_param=PDO::PARAM_INT;
	$statement->bindParam(":$columna",${$columna},$PDO_type_param);
}
if ($statement->execute()) {
	echo 'hecho';
	$select_result=$conexion->query("SELECT * FROM $nombre_tabla WHERE nombre='$datos[nombre]' AND estado=$datos[estado];");
	var_dump($select_result->fetchAll(PDO::FETCH_ASSOC));
}else
	echo 'Error de inserción';
#Liberación de recursos
$statement->closeCursor();
$conexion=null;
