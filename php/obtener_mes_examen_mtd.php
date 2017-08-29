<?php
  if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        
		require_once 'config.php';
		
		$conn = new mysqli($mysql_config['host'], $mysql_config['user'], $mysql_config['pass'], $mysql_config['db']);
			// Check connection
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			} 
			$conn->set_charset("utf8");
			
			$banEncuesta = isset($_POST['banEncuesta']) ? $conn->real_escape_string($_POST['banEncuesta']) : '0';
			$banEncuesta = $banEncuesta == 1 ? 0 : 1;
			
			$returnJs = [];
			$returnJs["puesto"]=[];
			$returnJs["fecha"]=[];
			$mesActual = date("Y-m");
			
			$sql = "SELECT pk_puesto,puesto FROM puesto where conPromedio={$banEncuesta};";
			
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
				$contador =0;
				while($temp = $result->fetch_assoc()) {
				  $returnJs["puesto"][] = $temp;
				  $contador ++;
				}
			}

			$sql = "SELECT substring(a.tiempo_inicio,1,7) as fecha FROM puesto as p, aspirante as a where a.fk_puesto=p.pk_puesto AND p.conPromedio={$banEncuesta} GROUP BY fecha DESC;";
			
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
				$contador =0;
				while($temp = $result->fetch_assoc()) {
				  $returnJs["fecha"][] = $temp;
				  $contador ++;
				}
			}
			$result->free();

			$conn->close();
			
			echo json_encode($returnJs);
			
	}
?>