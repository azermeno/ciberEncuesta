function pulsar(e) { 
	  tecla = (document.all) ? e.keyCode :e.which; 
	  return (tecla!=13); 
	}
	
	function getQueryVariable(variable) {
	   var query = window.location.search.substring(1);
	   var vars = query.split("&");
	   for (var i=0; i < vars.length; i++) {
		   vars[i] = vars[i].replace(/=/, "|");
		   var pair = vars[i].split("|",2);
		   if(pair[0] == variable) {
			   return pair[1];
		   }
	   }
	   return false;
	}	
	
	$(function(){
	 var informacion = getQueryVariable('info');
	 var identificador = getQueryVariable('identificador');
	  $('#indentificador').val(identificador);
	  $('#puesto').val(informacion);
	if(informacion){
		//alert("Puesto: "+puesto+ " mail: "+mail+" Nombre: "+name);
		$.ajax({
			method:"POST",
			url: "php/obtener_preguntas_encuesta_mtd.php",
			dataType:"json",
			data:{info:informacion,ident:identificador}
			
		}).done(function(data){
			
				var cuestionario = '';
				
				if(data.preguntas.length > 0){
					var numero = 1;
					var conRespuesta = 0;
					var conRespuestaTemp = 1;
					var checked = '';
					var soloComentario = '';
					var respuestaTemporal = '';
					data.preguntas.forEach(function(entry){
								
							conRespuesta = 0;
							conRespuestaTemp = 1;
							cuestionario += '<div class="well well-sm">';
							cuestionario += '<h4>'+numero+'.- '+entry['pregunta']+'</h4>'+
							'<div class="table-responsive"><table class="table">'+
							'<tr>';
							data.respuestas.forEach(function(temp){
								
								if(entry['pk_pregunta'] == temp['fk_pregunta']){
									conRespuesta ++;
								}	
							});	
									
							data.respuestas.forEach(function(respuesta){
								
								if(entry['pk_pregunta'] == respuesta['fk_pregunta']){
									
									if(conRespuesta == conRespuestaTemp){
										checked = ' checked="checked"';
									} else {
										
										checked = '';
									}
									if(conRespuesta > 1){
										cuestionario += '<th><div class="radio">'+
										'<h5><label onkeypress="return pulsar(event)" for="'+respuesta['pk_respuesta']+'">'+
										'<input type="radio" name="'+respuesta['fk_pregunta']+'" id="'+
										 respuesta['pk_respuesta']+'" value="'+respuesta['pk_respuesta']+'"'+checked+'>'+
										 respuesta['respuesta']+'<label></h5>'+
										'</div></th>';
									} else {
										
										 soloComentario = "";
										 respuestaTemporal = respuesta['respuesta'];
										if(respuesta['respuesta'] === '.'){
											
											soloComentario = 'style="display:none;"';
											respuestaTemporal = "";
										} 
										cuestionario += '<input type="radio" name="'+respuesta['fk_pregunta']+'" id="'+
										 respuesta['pk_respuesta']+'" value="'+respuesta['pk_respuesta']+'" '+soloComentario+' '+checked+'>'+
										 respuestaTemporal;
										
									}
										conRespuestaTemp ++ ;
								}
							});
							cuestionario += '</tr></table>';	
							if(entry.banComentario == 1){
								
								cuestionario += '<textarea class="form-control" placeholder="Comentario" rows="3" name="abierta/'+
								entry['pk_pregunta']+'"></textarea>';
								
							}	
							cuestionario += '</div></div>';
							
							numero++;
						
					});
						cuestionario += "</div>";
					
					$("#preguntas").append(cuestionario);
					
				} else {
					
					alert("Por el momento no esta disponible el apartado de preguntas, intente mas tarde (Puedes intentarlo nuevamente.)");
			
					window.location.replace("index.php");
				}
			
		}).fail(function(){
			
			alert("Por el momento no esta disponible el apartado de preguntas, intente mas tarde (Puedes intentarlo nuevamente..)");
		
			window.location.replace("nodisponible.html");
			
		});
		
		
		
		
	} else {
		
		alert("No han sido registrados sus datos, intentelo de la forma correcta \\(¬¬)/ ");
		
		 window.location.replace("nodisponible.html");
	}
	
	$('#form-send').submit(function(event) {
	event.preventDefault();
	$('#error-message').hide();
	
		var serialdata = $(this).serialize();
		console.log(serialdata);
		  $.ajax({
			  method: "POST",
			  url: "php/guardar_resultados_encuesta_mtd.php",
			  dataType: "json",
			  data: serialdata
		  }).done(function(data){
		  
		  if(data.status === true) {
			alert(data.msg);
			window.location.replace("index.php");
		  } else {
			alert(data.msg);
			window.location.replace("index.php");
		  }
		  console.log( "success" );          
		}).fail(function() {
		  console.log( "error" );
		});
		   
  });
  
	});