var enviarFormulario = 0;

function pulsar(e) { 
	  tecla = (document.all) ? e.keyCode :e.which; 
	  return (tecla!=13); 
	}
	
	function getQueryVariable(variable) {
		
	   var query = window.location.search.substring(1);
	       query = window.atob(query);
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
	function mesAnterior(mes){
			
			 var anio = mes.substring(0, 4);
			 var mes = mes.substring(4);
			 
			 switch(mes){
				 
				 case '01':
				 anio -= 1;
				 mes = 'Diciembre del ';
				 break;
				 case '02':
				 mes = 'Enero del ';
				 break;
				 case '03':
				 mes = 'Febrero del ';
				 break;
				 case '04':
				 mes = 'Marzo del ';
				 break;
				 case '05':
				 mes = 'Abril del ';
				 break;
				 case '06':
				 mes = 'Mayo del ';
				 break;
				 case '07':
				 mes = 'Junio del ';
				 break;
				 case '08':
				 mes = 'Julio del ';
				 break;
				 case '09':
				 mes = 'Agosto del ';
				 break;
				 case '10':
				 mes = 'Septiembre del ';
				 break;
				 case '11':
				 mes = 'Octubre del ';
				 break;
				 default:
				 mes = 'Noviembre del ';
				 break;
				 
			 }
			 return mes+anio;
		 }
	$(function(){
		
	 var unidad = getQueryVariable('requiriente');
	 
	 //Solucion a la basura que se agrego al combo mes
	 //*******************************************
		 var fecha = new Date();
		 var mesComparacion = (fecha.getMonth()+1).toString();// inicia de 0 a 11
			 mesComparacion = mesComparacion.length == 1 ? "0"+mesComparacion : mesComparacion;
		 var anioMes = fecha.getFullYear().toString() + mesComparacion;
     //******************************
	 
	 var mes = getQueryVariable('encuesta');
	
	 var encuestaManual = getQueryVariable('manual');
	 encuestaManual = encuestaManual == false ? 0 : encuestaManual;
	 
	 //Le asignamos al formulario el identificador de como se contesto
	 $("#encuestaManual").val(encuestaManual);
	 
	 if(unidad && mes == anioMes){
		 
	 $("#mes").append(mesAnterior(mes));
		//alert("Puesto: "+puesto+ " mail: "+mail+" Nombre: "+name);
			$.ajax({
				method:"POST",
				url: "php/obtener_preguntas_ciberEncuesta_mtd.php",
				dataType:"json",
				data:{unidad:unidad,mesEncuesta:mes}
				
			}).done(function(data){
					// console.log(data);
				var cuestionario = '';
				var producto = data.unidad.idProducto.trim().split(' ')
				$("#jefeLab").append(data.unidad.txtResponsable.toUpperCase())
				$("#unidad").append(data.unidad.txtNombre.toUpperCase());
				$("#producto").append(producto[0].toUpperCase());
				if(data.preguntas.length > 0){
					var numero = 1;
					var conRespuesta = 0;
					var conRespuestaTemp = 1;
					var checked = '';
					var pk_puesto = 0;
					//alert("hola");
					data.preguntas.forEach(function(entry){
								// console.log(entry);
							conRespuesta = 0;
							conRespuestaTemp = 1;
							
							if(typeof(entry['puesto']) == 'undefined'){
								cuestionario += '<div class="well" style="padding: 0px 19px 0px 19px; margin-bottom: 10px;">';
								cuestionario += '<h5 class="pregunta" ><b>'+numero+'.- '+entry['pregunta']+'</b></h5>'+
								'<div class="table-responsive"><table class="table" style="margin-bottom: 0;">'+
								'<tr>';
								
								//CUENTA EL NUMERO DE PREGUNTAS PARA PONER ACTIVO EL INPUT RADIO EN CHECKED
								data.respuestas.forEach(function(temp){
									
									if(entry['pk_pregunta'] == temp['fk_pregunta']){
										conRespuesta ++;
									}	
								});	
										
								data.respuestas.forEach(function(respuesta){
									
									
									//EN LA ULTIMA RESPUESTA ES CHECKED
									if(entry['pk_pregunta'] == respuesta['fk_pregunta']){
										
										if(conRespuesta == conRespuestaTemp){
											checked = ' checked="checked"';
										} else {
											
											checked = '';
										}
										
										if(conRespuesta > 1){
											
											cuestionario += '<th><div class="radio" style="margin-bottom: 0px;">'+
											'<h5><label onkeypress="return pulsar(event)" for="'+respuesta['pk_respuesta']+'">'+
											'<input type="radio" name="'+pk_puesto+'/'+respuesta['fk_pregunta']+'" id="'+
											 respuesta['pk_respuesta']+'" value="'+respuesta['pk_respuesta']+
											 '/'+entry['preguntaPonderacion']+'/'+respuesta['respuestaPonderacion']+'"'+checked+'>'+
											 respuesta['respuesta']+'<label></h5>'+
											'</div></th>';
											
										} else {
											cuestionario += '<input type="hidden" name="'+pk_puesto+'/'+respuesta['fk_pregunta']+'" id="'+
											 respuesta['pk_respuesta']+'" value="'+respuesta['pk_respuesta']+'/0/1">';
											
										}
											conRespuestaTemp ++ ;
									}
								});
								cuestionario += '</tr></table>';	
								if(entry.banComentario == 1){
									
									cuestionario += '<textarea class="form-control" placeholder="........" rows="5" name="abierta/'+
									entry['pk_pregunta']+'"></textarea>';
									
								}	
								
								cuestionario += '</div></div>';
								
								numero++;
								
							} else {
								pk_puesto = entry['fk_puesto'];
								cuestionario += '<div class="well" style="margin-bottom: 10px"><h4 style="margin: 0px;"><b>'+entry['puesto']+'</b></h4>'+
								'<tr></div><input type="hidden" name="ponderacion/'+entry['fk_puesto']+'" value="'+entry['puestoPonderacion']+'">';
								
							}
							
						
					});
					
					if(numero>1){
						
						$("#aceptar").show();
					}
						cuestionario += "</div>";
					
					$("#preguntas").append(cuestionario);
					
				} else {
					
					alert("Por el momento no esta disponible el apartado de preguntas, intente mas tarde (Puedes intentarlo nuevamente.)");
			
					window.location.replace("nodisponible.html");
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
			
			if(enviarFormulario == 0){
				enviarFormulario =1;
			
				event.preventDefault();
			$('#error-message').hide();
			
				var serialdata = $(this).serialize();
				console.log(serialdata);
				   
				    $.ajax({
					  method: "POST",
					  url: "php/guardar_resultados_ciberEncuesta_mtd.php",
					  dataType: "json",
					  data: serialdata
				  }).done(function(data){
				  //console.log(data);
				  if(data.status === true) {
					alert(data.msg);
					window.location.replace("gracias.html");
				  } else {
					alert(data.msg);
					window.location.replace("nodisponible.html");
				  }
				 // console.log( "success" );          
				}).fail(function() {
				 // console.log( "error" );
				 window.location.replace("nodisponible.html");
				});  
			}
				  
		});
  
	});