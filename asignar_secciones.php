
<!doctype html>

<html lang="en">
    <head>
        <meta charset="utf-8">

        <title>Resultados Encuesta</title>
        <meta name="description" content="redlab">
        <meta name="author" content="redlab">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
		
		<link rel="shortcut icon" href="img/favicon.png">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
		
		 <link rel="stylesheet" href="css/bootstrap-select.css">
		
        <script src="js/jquery-1.11.3.min.js" type="text/javascript"></script>
        <script src="js/bootstrap.min.js"></script>
		<script src="js/bootstrap-select.js" type="text/javascript"></script>
		<script src="js/asignar_secciones.js" type="text/javascript"></script>
		<style>
			th {
				
				text-align:center;
			}
		</style>
       
       
</style>
    </head>
    <body>
	<?php include_once 'nav_menu.php'; ?>


        <div class="container centrado">
            <div class="starter-template" style="text-align:center">
			<br>
               <!--img src="img/logo.jpg" style="height:200px" id="logo"-->
				
			<div class="tab-content">
				<div id="resultados" class="tab-pane fade in active">
					

						<div class="centrado">
							<form  id="form-send">
								<div class="panel panel-info" style="width: 50%; margin: 0 auto; background: rgb(217,237,247);">
									<div class="form-group" style="margin-bottom:0px">
										<div class="panel panel-body" style="background: rgb(217,237,247);margin-bottom: 0px;">
											
												<br>
										   <h4 style="margin-top: 0px;">
											   <label class="radio-inline"><input type="radio" name="general" id="checked" value="0" checked>Por unidad</label>
											   <label class="radio-inline"><input type="radio" name="general" value="1" >General</label>
										   </h4>
											<div id="ocultar">
												<select id="unidad" data-width="100%" class="selectpicker" data-live-search="true" title='Unidades..'>

												</select>
											</div>

												<button id="actualiza" type="button" class="btn btn-warning" style="display:none"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span>&nbsp;Modificar prioridades</button>
												<div id="guardarCambios" style="display:none">
												<button id="guarda" type="submit" class="btn btn-success"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span>&nbsp;Guardar</button>
												<button id="cancelar" type="button" class="btn btn-danger"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span>&nbsp;Cancelar</button>
												</div>
										
											<div style="text-align: center; display:none;" id="verEstado">
											<br>
											  <label style="font-size:150%" id="estado"></label>  
											</div>
										</div>
									</div>
								</div>
								
								<table class="table table-bordered"style="margin-top: 20px;">
									<thead >
									  <tr class="info">
										<th>Nombre</th>
										<th>Estado</th>
										<th>Prioridad</th>
										<th>Sesión</th>
										<th>Agregar/Quitar</th>
									  </tr>
									</thead>
									<tbody id="secciones">
									  
									</tbody>
								</table>
							
							</form>
							<br>
							
						</div>
					</div>
					 
				</div>
			</div>

        </div><!-- /.container -->

    </body>
</html>