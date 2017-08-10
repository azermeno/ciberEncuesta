
<!doctype html>

<html lang="en">
    <head>
        <meta charset="utf-8">

        <title>Cuestionario</title>
        <meta name="description" content="redlab">
        <meta name="author" content="redlab">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
		
		<link rel="shortcut icon" href="img/favicon.png">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
		<link rel="stylesheet" href="css/examen.css">
        <script src="js/jquery-1.11.3.min.js" type="text/javascript"></script>
        <script src="js/bootstrap.min.js"></script>
		<script src="js/ver_resultados.js "type="text/javascript"></script>
   </head>
    <body>

      <?php include_once 'nav_menu.php'; ?>

        <div class="container centrado">
            <div class="starter-template">
               <img src="img/logo.jpg" height="200" id="logo">

                <h2><span class="glyphicon glyphicon-paste" aria-hidden="true"></span> Resultados de examen</h2>

				<div class="centrado">
						<form  id="form-send" style="width: 50%; margin: 0 auto">
						<div class="panel panel-info">
							<div class="form-group">
								<div class="panel panel-body">
									
									<div style="text-align: center;">
									    <label>Seleccione un filtro:</label>
										<br>
										<label class="radio-inline"><input type="radio" name="filtro" value="0" checked>Por nombre</label>
                                        <label class="radio-inline"><input type="radio" name="filtro" value="1">Por examen/puesto</label>
										<input type="text" class="form-control" id="nombre"  placeholder="Ingrese el texto o una parte para filtrar">
										<br>
									</div>
									
									<div style="text-align: center">
										<button type="submit" class="btn btn-gray"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar</button>
									
										<!-- <button type="botton" class="btn btn-warning" id="imprimir"><span class="glyphicon glyphicon-print" aria-hidden="true"></span> Imprimir</button> -->
									</div>
								</div>
							</div>
						</div>
					</form>
					<div class="panel panel-info" style="width: 50%; margin: 0 auto">
				<div class="panel panel-body" >
									
									<div style="text-align: center;">
									    <label>Reporte Excel</label>
										<br>
										
										<select class="form-control" id="examen">
										<option value="0">seleccione un ex&aacute;men</option>
										<option value="1">seleccione un ex&aacute;men</option>
										
										</select>
										<br>
										<select class="form-control" id="fecha">
										<option value="0">Seleccione una fecha</option>
										<option value="1">Seleccione una fecha</option>
										</select>
										
										<br>
									</div>
									
									<div style="text-align: center">
										<button type="button" class="btn btn-gray" id="reporte"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Reporte</button>
									
										<!-- <button type="botton" class="btn btn-warning" id="imprimir"><span class="glyphicon glyphicon-print" aria-hidden="true"></span> Imprimir</button> -->
									</div>
								</div>
								</div>
					<br>
					
					<input tipe="hidden" style="display: none" id="pagina" value="1">
					<input tipe="hidden" style="display: none" id="timerOff" value="0">
					<table id="tabla-contenido" class="table table-striped">
						<tr class="warning">
							<th style="width: 12%;" class="info centrado">Nombre</th>
							<th style="width: 11%;" class="info centrado">Correo</th>
							<th style="width: 11%;" class="info centrado">Puesto solicitado</th>
							<th style="width:  9%;" class="info centrado">Realizado</th>
							<th style="width: 38%;" class="info centrado">&Aacute;rea</th>
							<th style="width: 5%;" class="info centrado">Preguntas</th>
							<th style="width: 5%;" class="info centrado">Aciertos</th>
							<th style="width: 5%;" class="info centrado">Resultado</th>
							<th style="width: 4%;" class="info centrado">Ver</th>
							
													
						</tr>
					   
					</table>

				</div>

            </div>

        </div><!-- /.container -->

    </body>
</html>