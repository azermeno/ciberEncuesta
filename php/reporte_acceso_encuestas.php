<?php
set_time_limit(0);
$tiempo_inicio = microtime(true);
require_once 'configMySQL.php';
require_once 'configSQL.php';

$conn = new mysqli($mysql_config['host'], $mysql_config['user'], $mysql_config['pass'], $mysql_config['db']);
			// Check connection
if ($conn->connect_error) {	
		die("Connection failed: " . $conn->connect_error);
	} 
$conn->set_charset("utf8");

// Aqui va la coneccion de SQL y se actualizara la tabla
$connectionInfo = array( 
"Database"=>$sql_config['db'],
"UID"=>$sql_config['user'],
"PWD"=>$sql_config['pass'],
"CharacterSet" => "UTF-8" 
);

$connSQL = sqlsrv_connect($sql_config['host'], $connectionInfo);

if(!$connSQL){
	error_log('Error en conectar a servidor sql');
	error_log( print_R( sqlsrv_errors(), true));
	$correcto = false;
	echo "No se puedo conectar mssql";
}	

	
$resultado=[];
$sql="Select u.req_codigo,mes.mes,m.pk_mesEncuesta,a.pk_aspirante,a.contestadoManual,a.tiempo_inicio from unidad as u, mesEncuesta as m,mes, aspirante as a  where u.pk_unidad=m.fk_unidad && m.fk_mes=mes.pk_mes && mes.mes='201706' &&  m.pk_mesEncuesta=a.fk_mesEncuesta group by u.req_codigo,m.pk_mesEncuesta order by a.contestadoManual,u.req_codigo;"; 

$result = $conn->query($sql);

if($result->num_rows > 0){
		while($row = $result->fetch_assoc()){
			
			// print_r($row);
			 //echo($row['req_codigo']);
			// echo $row['req_codigo'];
			
			$sql="select Nombre from [CustomerCare].[dbo].[Unidades] where Codigo=".$row['req_codigo'];
			$stmt = sqlsrv_query( $connSQL, $sql );
	
			if( $stmt === false) {
				die( print_r( sqlsrv_errors(), true) );
			}
			$rows_affected = sqlsrv_rows_affected( $stmt);
				//print_r($stmt,true);
				$unidad = '';
				while( $row1 = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
					 // echo $row1['Nombre']."<br />";
					  $unidad = $row1['Nombre'];
				}
				$contesto = $row['contestadoManual'] == 0 ? 'Automático' : 'Manual'; 
				echo $resultado[] = 'Unidad: '.$unidad.' Código:'.$row['req_codigo'].' Contestó:'.$contesto.' Fecha: '.$row['tiempo_inicio'].' Mes: '.$row['mes'].'<br/>' ;
			
		}
				sqlsrv_free_stmt($stmt);
		//print_r($resultado,true);
		//var_dump( $resultado);
}
		
	//print_r($resultado[0]);
	
 //$result->free();
 $conn->close();
 sqlsrv_close( $connSQL );
 $tiempo_fin = microtime(true); 
 
 echo $tiempo_fin - $tiempo_inicio;

?>