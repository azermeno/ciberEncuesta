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

/*/ Aqui va la coneccion de SQL y se actualizara la tabla
$connectionInfo = array( 
"Database"=>$sql_config['db'],
"UID"=>$sql_config['user'],
"PWD"=>$sql_config['pass'] 
);

$connSQL = sqlsrv_connect($sql_config['host'], $connectionInfo);

if(!$connSQL){
	error_log('Error en conectar a servidor sql');
	error_log( print_R( sqlsrv_errors(), true));
	$correcto = false;
}	*/

	
	$resultado=[];
$sql="select ". 
"u.req_codigo,m.fk_mes,a.pk_aspirante,p.pk_puesto,p.puestoPonderacion,pre.preguntaPonderacion,r.respuestaPonderacion ".
" from ".
 "unidad as u,mesEncuesta as m, aspirante as a, contestado as c,respuesta as r, pregunta as pre,area as ar, puesto as p ".
" where ".
 "m.fk_unidad=u.pk_unidad and m.fk_mes < 14 and m.pk_mesEncuesta=a.fk_mesEncuesta and c.fk_aspirante=a.pk_aspirante and ".
 "c.fk_respuesta=r.pk_respuesta and r.fk_pregunta=pre.pk_pregunta and pre.fk_area=ar.pk_area and ar.fk_puesto=p.pk_puesto order by a.pk_aspirante;"; 

$result = $conn->query($sql);
$puestoInicial = 0;
$numeroDeSecciones = 0;
$totalEncuesta = 0;
$totalSeccion = 0;
$numeroDePreguntas = 0;
$pk_aspirante = 0;
$pk_aspirante_anterior = 0;
$resultado_por_seccion = 0;
$ponderacionSeccion = 0;
$resultado_total_secciones = 0;
$numero_de_secciones = 0;
$correcto = 1;
$req_codigo_anterior = 0;
$query_total_sql ='';
$query_total_mysql ='';
$fk_mes_anterior = 0;
$idEncuestaMsSQL = 0;
if($result->num_rows > 0){
		while($row = $result->fetch_assoc()){
			$resultado[] = $row;
			// print_r($row);
			 //echo($row['req_codigo']);
			 
			 //Entra en la primera interacción del la consulta
			 if($puestoInicial == 0){
				 
				 $puestoInicial = $puestoInicial == 0 ? $row['pk_puesto'] : $puestoInicial;
				 $pk_aspirante = $row['pk_aspirante'];
				 $req_codigo_anterior = $row['req_codigo'];
				 $fk_mes_anterior = $row['fk_mes'];
				 $pk_aspirante_anterior = $row['pk_aspirante'];
				 $numero_de_secciones += $row['puestoPonderacion'] > 0 ? 1 : 0;  
				 $ponderacionSeccion = $row['puestoPonderacion'];
			 } 
			 
			 if($pk_aspirante == $row['pk_aspirante']){
				 
				 if($row['puestoPonderacion'] > 0){
					 $numeroDePreguntas += $row['preguntaPonderacion'] > 0 ? 1 : 0;
					 $totalSeccion += $row['preguntaPonderacion'] * $row['respuestaPonderacion']; 
				 }
				 
			 } else {
				 
				 /*
				 * Es el resultado de la sección anterior
				 */
				 
				if($totalSeccion > 0){
					$resultado_por_seccion = ($totalSeccion / $numeroDePreguntas) * $ponderacionSeccion;
					//echo $pk_aspirante.'.--('.$totalSeccion.' / '.$numeroDePreguntas.') * '.$ponderacionSeccion.'='.$resultado_por_seccion. "<br>";
					$resultado_por_seccion = round($resultado_por_seccion*10);
					//echo "division por sección redondiada: ".$resultado_por_seccion."<br>";
				
				} else {
					
					$resultado_por_seccion = 0;
				}
				
			     $resultado_total_secciones += $resultado_por_seccion ;
				 //echo "Resultado tota por sección: ".$resultado_por_seccion."<br>";
			   /*
				*aquí va el update MySQL de sección
				*/				 
				$sql = "UPDATE aspirante SET calificacion={$resultado_por_seccion} WHERE pk_aspirante={$pk_aspirante} ";
				//echo $sql.'<br>';	
				$query_total_sql = $query_total_sql.$sql.";<br>";	
				/*$conn->query($sql);
				if($conn->affected_rows != 1){
					$correcto = 0;							
				} */ 
				  //Empieza una nueva encuesta 
				  if($puestoInicial == $row['pk_puesto']){
						$pk_puesto = $row['pk_puesto'];
					    $totalEncuesta = floatval($resultado_total_secciones / $numero_de_secciones);
						//echo "Total de la encuesta = ".$resultado_total_secciones ."/". $numero_de_secciones."=".floatval($resultado_total_secciones / $numero_de_secciones).'<br>';
						$totalEncuesta = round($totalEncuesta);
					   // echo $req_codigo_anterior.".-- Redondeado = ".$totalEncuesta.'<br>';
					   /*
					   *Aquí va el update MS SQL
					   *
					   *
					  //  */
					  switch($fk_mes_anterior){
						  case '4':
						  $idEncuesta = '05D094AA-90AE-43CF-8224-E7F8B27C4EA0';
						  break;
						  case '11':
						  $idEncuesta = '9B749F48-7C1B-43BD-9574-D3BC43CE209A';
						  break;
						  case '12':
						  $idEncuesta = 'FB74A40B-944C-4CC5-BD02-21E87DAA756E';
						  break;
						  case '13':
						  $idEncuesta = 'FB74A40B-944C-4CC5-BD02-21E87DAA756E';
						  break;
						  
					  }
					  
					  $mssql = "update [ciberencuesta].[dbo].[contesta] set Indicador={$totalEncuesta} where idEncuesta='$idEncuesta' and req_codigo=$req_codigo_anterior";
					  $query_total_mysql = $query_total_mysql.$mssql.";<br>";
					   $numero_de_secciones = 0;
					   $resultado_por_seccion =0;
					   $totalEncuesta = 0;
					   $resultado_total_secciones =0;
				  }
				  $ponderacionSeccion = $row['puestoPonderacion'];
				  $totalSeccion = 0;
				  $totalSeccion += $row['preguntaPonderacion'] * $row['respuestaPonderacion'];
				  $resultado_por_seccion = 0;
				  $numero_de_secciones += $row['puestoPonderacion'] > 0 ? 1 : 0;
				  $pk_puesto = $row['pk_puesto'];
				  $pk_aspirante = $row['pk_aspirante'];	
				  $req_codigo_anterior = $row['req_codigo'];				  
				  $pk_aspirante_anterior = $row['pk_aspirante'];
				  $fk_mes_anterior = $row['fk_mes'];
				  $numeroDePreguntas = 1;
			 }
		
		}
		
		// para la última interacción
		if($totalSeccion > 0){
			$resultado_por_seccion = ($totalSeccion / $numeroDePreguntas) * $ponderacionSeccion;
			//echo $pk_aspirante.'.--('.$totalSeccion.' / '.$numeroDePreguntas.') * '.$ponderacionSeccion.'='.$resultado_por_seccion. "<br>";
			$resultado_por_seccion = round($resultado_por_seccion*10);
			//echo "division por sección redondiada: ".$resultado_por_seccion."<br>";
		
		} else {
			
			$resultado_por_seccion = 0;
		}
		$sql = "UPDATE aspirante SET calificacion={$resultado_por_seccion} WHERE pk_aspirante={$pk_aspirante} ";
		//echo $sql.'<br>';	
		$query_total_sql = $query_total_sql.$sql.";<br>";
		
		$totalEncuesta = floatval($resultado_total_secciones / $numero_de_secciones);
		//echo "Total de la encuesta = ".$resultado_total_secciones ."/". $numero_de_secciones."=".floatval($resultado_total_secciones / $numero_de_secciones).'<br>';
		$totalEncuesta = round($totalEncuesta);
		//echo $req_codigo_anterior.".-- Redondeado = ".$totalEncuesta.'<br>';
	   /*
	   *Aquí va el update MS SQL
	   *
	   */
	     switch($fk_mes_anterior){
		  case '4':
		  $idEncuesta = '05D094AA-90AE-43CF-8224-E7F8B27C4EA0';
		  break;
		  case '11':
		  $idEncuesta = '9B749F48-7C1B-43BD-9574-D3BC43CE209A';
		  break;
		  case '12':
		  $idEncuesta = 'FB74A40B-944C-4CC5-BD02-21E87DAA756E';
		  break;
		  case '13':
		  $idEncuesta = 'FB74A40B-944C-4CC5-BD02-21E87DAA756E';
		  break;
		  
	  }
	  
	  $mssql = "update [ciberencuesta].[dbo].[contesta] set Indicador={$totalEncuesta} where idEncuesta='$idEncuesta' and req_codigo=$req_codigo_anterior";
	  $query_total_mysql = $query_total_mysql.$mssql.";<br>";
	   echo $query_total_sql;
	   echo $query_total_mysql;
	}
	//print_r($resultado[0]);
	
 //$result->free();
 $conn->close();
 
 $tiempo_fin = microtime(true); 
 
 echo $tiempo_fin - $tiempo_inicio;

// QUERYS PARA MEJORAR LO ANTERIOR //
/*
                        
$sql="select ". 
"u.req_codigo,m.fk_mes,a.pk_aspirante,p.pk_puesto,avg( p.puestoPonderacion * pre.preguntaPonderacion * r.respuestaPonderacion )".
" from ".
 "unidad as u,mesEncuesta as m, aspirante as a, contestado as c,respuesta as r, pregunta as pre,area as ar, puesto as p ".
" where ".
 "m.fk_unidad=u.pk_unidad and m.fk_mes < 14 and m.pk_mesEncuesta=a.fk_mesEncuesta and c.fk_aspirante=a.pk_aspirante and ".
 "c.fk_respuesta=r.pk_respuesta and r.fk_pregunta=pre.pk_pregunta and pre.fk_area=ar.pk_area and ar.fk_puesto=p.pk_puesto
 where p.puestoPonderacion <> 0
 group by u.req_codigo, m.fk_mes, a.pk_aspirante, p.pk_puesto; ";                        


select * from (
select 
u.req_codigo,m.fk_mes,a.pk_aspirante,p.pk_puesto,avg( p.puestoPonderacion * pre.preguntaPonderacion * r.respuestaPonderacion ) * 10 as Califi
 from 
 unidad as u,mesEncuesta as m, aspirante as a, contestado as c,respuesta as r, pregunta as pre,area as ar, puesto as p 
 where 
 m.fk_unidad=u.pk_unidad and m.fk_mes < 14 and m.pk_mesEncuesta=a.fk_mesEncuesta and c.fk_aspirante=a.pk_aspirante and 
 c.fk_respuesta=r.pk_respuesta and r.fk_pregunta=pre.pk_pregunta and pre.fk_area=ar.pk_area and ar.fk_puesto=p.pk_puesto
 AND p.puestoPonderacion <> 0 
 group by u.req_codigo, m.fk_mes, a.pk_aspirante, p.pk_puesto 
 ORDER BY a.pk_aspirante ) as Tabla1
  where (req_codigo, fk_mes, pk_aspirante, pk_puesto) in (
 select req_codigo, fk_mes, MAX(pk_aspirante), pk_puesto from (
select 
u.req_codigo,m.fk_mes,a.pk_aspirante,p.pk_puesto,avg( p.puestoPonderacion * pre.preguntaPonderacion * r.respuestaPonderacion ) * 10 as Califi
 from 
 unidad as u,mesEncuesta as m, aspirante as a, contestado as c,respuesta as r, pregunta as pre,area as ar, puesto as p 
 where 
 m.fk_unidad=u.pk_unidad and m.fk_mes < 14 and m.pk_mesEncuesta=a.fk_mesEncuesta and c.fk_aspirante=a.pk_aspirante and 
 c.fk_respuesta=r.pk_respuesta and r.fk_pregunta=pre.pk_pregunta and pre.fk_area=ar.pk_area and ar.fk_puesto=p.pk_puesto
 AND p.puestoPonderacion <> 0 
 group by u.req_codigo, m.fk_mes, a.pk_aspirante, p.pk_puesto 
 ORDER BY a.pk_aspirante ) as Tabla group by req_codigo, fk_mes, pk_puesto )
*/


?>