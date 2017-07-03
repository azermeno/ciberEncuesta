<?php 
session_start();


require_once 'php/ver_detalle_resultado_mtd.php';

	
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
		<!--<link rel="stylesheet" href="css/bootstrap-switch.css"> -->
		<link rel="stylesheet" href="css/examen.css">
        <script src="js/jquery-1.11.3.min.js" type="text/javascript"></script>
        <script src="js/bootstrap.min.js"></script>
		<!--<script src="js/bootstrap-switch.js"></script>
		<script src="js/jquery.blockUI.js"></script> -->

       
        <script type="text/javascript">
		$(function(){
			
			$("#imprimir").on('click',function(){
				
				$("#boton").hide();
			window.print();
			setTimeout(function(){
				
				$("#boton").show();
				
			},500);
			});
			$("#regresar").on('click',function(){
				window.history.back();
			});
			});
        </script>

    </head>
    <body>

      <?php include_once 'nav_menu.php'; ?>

        <div class="container centrado">
            <div class="starter-template">
               <img src="img/logo.jpg" height="200" id="logo">
				<div id="boton">
                <h2><span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;&nbsp;Detalle individual </h2>
				<button type="botton" class="btn btn-warning" id="imprimir"><span class="glyphicon glyphicon-print" aria-hidden="true"></span> Imprimir</button>
				<button type="botton" class="btn btn-success" id="regresar"><span class="glyphicon glyphicon-backward" aria-hidden="true"></span> Regresar</button>
				</div>
			<div class="centrado">
				<table id="tabla-contenido" class="table table-striped">
						<tr class="warning">
							<th style="width: 13%;" class="info centrado">Nombre</th>
							<th style="width: 13%;" class="info centrado">Correo</th>
							<th style="width: 13%;" class="info centrado">Puesto solicitado</th>
							<th style="width: 12%;" class="info centrado">Realizado</th>
							<th style="width: 35%;" class="info centrado">&Aacute;rea</th>
							<th style="width: 6%;" class="info centrado">Preguntas</th>
							<th style="width: 6%;" class="info centrado">Aciertos</th>
							<th style="width: 12%;" class="info centrado">Resultado total</th>
							
							
													
						</tr>
						
						<?php
							$count = 0;
						foreach($returnJs[0]['areas'] as $area){
						
						?>
						
						<tr class="warning">
							<th style="width: 13%;" class="info centrado"><?php echo $count==0 ? $returnJs[0]['aspirante']['Nombre'] : ''; ?></th>
							<th style="width: 13%;" class="info centrado"><?php echo $count==0 ? $returnJs[0]['aspirante']['email'] : ''; ?></th>
							<th style="width: 13%;" class="info centrado"><?php echo $count==0 ? $returnJs[0]['aspirante']['puesto'] : ''; ?></th>
							<th style="width: 12%;" class="info centrado"><?php echo $count==0 ? $returnJs[0]['aspirante']['tiempo_inicio'] : ''; ?></th>
							<th style="width: 35%;" class="info centrado"><?php echo $area['area']; ?></th>
							<th style="width: 6%;" class="info centrado"><?php echo $area['total']; ?></th>
							<th style="width: 6%;" class="info centrado"><?php echo $area['correctas']; ?></th>
							<th style="width: 12%;" class="info centrado"><?php echo $count==0 ? $resultado : ''; ?></th>
							
							
													
						</tr>
						
						
						<?php 
						$count ++; 
						}
						?>
					   
					</table>
				
				<br>
				<label style="background:yellow">
				<span class="glyphicon glyphicon-asterisk" aria-hidden="true"></span>
				<span class="glyphicon glyphicon-asterisk" aria-hidden="true"></span>
				<u>
				Las respuestas subrayadas son las que contestó el usuario 
				</u>
				<span class="glyphicon glyphicon-asterisk" aria-hidden="true"></span>
				<span class="glyphicon glyphicon-asterisk" aria-hidden="true"></span>
				</label>
				<br>
				<br>
				<?php
							$contadorPreguntas=1;
						foreach($returnJs['preguntas'] as $preguntas){
							if(isset($preguntas['fk_area'])){ //es pregunta
							?>
								<div class="well" style="text-align:left">
								<h4><?php echo $contadorPreguntas.'.- '.$preguntas['pregunta']  ?></h4>
								<?php foreach($returnJs['respuestas'] as $respuesta){ 
								    if($preguntas['pk_pregunta'] == $respuesta['fk_pregunta']){
								     if($respuesta['correcta']==1){ //
									   
								?>
											 
											 <h5>
											 <label for="<?php echo $respuesta['pk_respuesta']  ?>">
											 <span style="color:green" class="glyphicon glyphicon-ok" aria-hidden="true">
											 </span>
											 <?php  if(isset($respuesta['contestado'])){//es la que contesto el usuario ?>
												<span style="background:yellow"><u><i>
											 <?php }?>
											 &nbsp;&nbsp;<?php echo $respuesta['respuesta']  ?>
											<?php if(isset($respuesta['contestado'])){//es la que contesto el usuario
											?>
											</u></i></span>
											 <?php } ?>
											 </label>
											 </h5>
									 <?php } else {
												
										?>
													
													 <h5>
											 <label for="<?php echo $respuesta['pk_respuesta']  ?>">
											 <span style="color:red" class="glyphicon glyphicon-remove" aria-hidden="true">
											 </span>
											 <?php  if(isset($respuesta['contestado'])){//es la que contesto el usuario ?> 
										          <span style="background:yellow"><u><i>
													 <?php }?>
											 &nbsp;&nbsp;<?php echo $respuesta['respuesta']  ?>
													 <?php if(isset($respuesta['contestado'])){?>
													 </u></i></span>
													 <?php } ?>
											 </label>
											 </h5>
								<?php
											}
								}
								} ?>
								</div>
					<?php 
								$contadorPreguntas++;
							} else {//es area
							?>
							
								<div class="well" style="text-align:center">
								<h3><?php echo $preguntas['area']  ?></h3>
								</div>
							<?php 
							}
						}
						?>
				</div>

            </div>

        </div><!-- /.container -->

    </body>
</html>