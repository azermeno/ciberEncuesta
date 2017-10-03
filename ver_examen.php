<?php 
session_start();
?>
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
		<link rel="stylesheet" href="css/bootstrap-switch.css">
		<link rel="stylesheet" href="css/examen.css">
		`
		<script src="https://cdn.ckeditor.com/4.6.1/standard-all/ckeditor.js"></script>
        <script src="js/jquery-1.11.3.min.js" type="text/javascript"></script>
        <script src="js/bootstrap.min.js"></script>
		<script src="js/bootstrap-switch.js"></script>
		<script src="js/jquery.blockUI.js"></script>
		<script src="js/ver_examen.js" type="text/javascript"></script>
		
       

</style>
    </head>
    <body>
	<div id="over" class="overbox">
		<div style="text-align:center">
			<button id="guardaCkeditor" type="button" class="btn btn-success" onclick="guardarPreguntaRespuesta()"><span class="glyphicon glyphicon-ok" aria-hidden="button"></span>&nbsp;Guardar</button>
			<button id="cancelarCkeditor" type="button" onclick="hideLightbox()" class="btn btn-danger"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span>&nbsp;Cancelar</button>
		</div>			
		<textarea id="editor1" style="height=90%">
		</textarea>
	</div>
    <div id="fade" class="fadebox">&nbsp;</div>

      <?php include_once 'nav_menu.php'; ?>
		
        <div class="container centrado">
            <div class="starter-template">
               <img src="img/logo.jpg" height="200" id="logo">

                <h2><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Estados y detalles de ex&aacute;menes</h2>
				 <br>	
				 <ul class="nav nav-tabs">
				  <li class="active">
				  <a data-toggle="tab" href="#axamen"><h3><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>&nbsp;Ver ex&aacute;menes </h3></a>
				  </li>
				  <li>
				  <a data-toggle="tab" href="#encuesta"><h3><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span>&nbsp;Ver encuestas </h3></a>
				  </li>
				  <li>
				  <a data-toggle="tab" href="#encuestaCompuesta"><h3><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span>
				  <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
				  <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span>
				  <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
				  <span class="glyphicon glyphicon-option-horizontal" aria-hidden="true"></span>
				  &nbsp;Encuesta compuesta</h3></a>
				  </li>
				</ul>
				<div class="tab-content">
					<div id="axamen" class="tab-pane fade in active">
						<div class="centrado">
							<form  id="form-send" style="width: 50%; margin: 0 auto">
								<div class="panel panel-info">
									<div class="form-group">
										<div class="panel panel-body">
										   <h3>Seleccione el examen (Los rojos son inactivos)</h3>
												<select class="form-control" id="puestoExamen" name="puesto" >
												<option value="0" style="background: white; color:black;">&Aacutereas a seleccionar</option>
												</select>	
												<br>
											<div style="text-align: center">
												<button type="submit" class="btn btn-gray"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>&nbsp;Ver detalle</button>
											</div>
											<div style="text-align: center; display:none;" id="verEstado">
											<br>
											  <label style="font-size:150%" id="estado"></label>
												<br>											  
											  <label style="font-size:150%" id="estadoBoton"></label>  
											</div>
										</div>
									</div>
								</div>
							</form>
							
							<br>
							<div id="preguntas" style="text-align:left; width: 80%; margin: 0 auto">
							<input type="hidden" style="display: none" id="idPuesto" value="">
							
							</div>

						</div>
					</div>
					<div id="encuesta" class="tab-pane fade">
						<div class="centrado">
							<form  id="form-send1" style="width: 50%; margin: 0 auto">
								<div class="panel panel-info">
									<div class="form-group">
										<div class="panel panel-body">
										   <h3>Seleccione la encuesta(Los rojos son inactivos)</h3>
												<select class="form-control" id="puestoEncuesta" name="puestoEncuesta" >
												<option value="0" style="background: white; color:black;">Encuesta a seleccionar</option>
												</select>	
												<br>
											<div style="text-align: center">
												<button type="submit" class="btn btn-gray"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>&nbsp;Ver detalle</button>
											</div>
											<br>
											
											<div style="text-align: center; display:none;" id="verEstadoEncuesta">
											<select class="form-control" id="identificadoresExistenetes">
											<option selected value="0">Identificadores existentes </option>
											</select>
											<br>
											<input type="Text" class="form-control" id="inputText" placeholder="Crear nuevo identificador">
											<br>
											<div>
												<input type="hidden" id="urlParaCopiar" style="display: none"  value="">
												<label style="font-size:100%" id="estadoEncuesta"></label><!--span id="identificador"></span-->
											</div>											  
												<br>
												<button onclick="copiarAlPortapapeles('identificador')" style="margin-bottom: 10px;">Copiar url</button>
												
											<div>												
											  <label style="font-size:150%" id="estadoEncuestaBoton"></label>
											  <br>
											  <button id="actualiza" type="button" class="btn btn-warning"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span>&nbsp;Modificar ponderaci&oacute;n</button> <button id="actualizaPreguntaRespuesta" type="button" class="btn btn-warning"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span>&nbsp;Modificar pregunta/respuesta</button>
												<div id="guardarCambios" style="display:none">
													<button id="guarda" type="button" class="btn btn-success"><span class="glyphicon glyphicon-ok" aria-hidden="button"></span>&nbsp;Guardar</button>
													<button id="cancelar" type="button" class="btn btn-danger"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span>&nbsp;Cancelar</button>
												</div>											  
											 </div>
											</div>
											
										</div>
									</div>
								</div>
							</form>
							
							<br>
							<form  id="form-ponderacion" >
								<div id="preguntasEncuesta" style="text-align:left; width: 80%; margin: 0 auto">
								</div>
							</form>
							
						</div>
					</div>
					<div id="encuestaCompuesta" class="tab-pane fade">
						<br>
						<br>
						<label>En construcci&oacute;n....</label>
						<br>
						
						<div class="centrado">
							<form  id="form-send1" style="width: 50%; margin: 0 auto">
								<div class="panel panel-info">
									<div class="form-group">
										<div class="panel panel-body">
											<h3>Seleccione un nombre de empaquetado</h3>
											<div style="text-align: center;" id="verEstadoEncuesta">
											<select class="form-control" id="identificadoresExistenetes">
											<option selected value="0" >Identificadores existentes </option>
											<option value="1">CiberEncuesta </option>
											</select>
											<br>
											<div class="input-group">
											<input type="text" class="form-control" id="inputText" placeholder="Crear nuevo identificador">
											  <span class="input-group-btn">
											  
												<button class="btn btn-default" type="button"  style="color:blue;">
												<span class="glyphicon glyphicon-floppy-saved" aria-hidden="true"></span>&nbsp;Crear</button>
											  </span>
											</div><!-- /input-group -->
											
											<br>
											  <label style="font-size:150%" id="estadoEncuesta"></label> 
												<br>											  
											  <label style="font-size:150%" id="estadoEncuestaBoton"></label>  
											</div>
											
										</div>
									</div>
								</div>
							</form>
							
							<br>
							<div id="encuestas" style="text-align:left; width: 80%; margin: 0 auto">
							</div>

						</div>
					</div>
			    </div>
            </div>
		<a href="javascript:showLightbox();">Show LightBox</a>
        <a href="javascript:hideLightbox();">HideLightBox</a>
        </div><!-- /.container -->
		<script src="js/ckeditorPersonalizado.js" type="text/javascript"></script>
    </body>
</html>