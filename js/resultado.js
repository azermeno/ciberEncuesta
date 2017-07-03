var unidadesWs = [];
var banderaUnidad = true;
var anioMesEnCurso;		

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
	
		function mesAnterior(mes){
			
			 var anio = mes.substring(0, 4);
			 var mes = mes.substring(4);
			 
			 switch(mes){
				 
				 case '01':
				 anio -= 1;
				 mes = 'Diciembre de ';
				 break;
				 case '02':
				 mes = 'Enero de ';
				 break;
				 case '03':
				 mes = 'Febrero de ';
				 break;
				 case '04':
				 mes = 'Marzo de ';
				 break;
				 case '05':
				 mes = 'Abril de ';
				 break;
				 case '06':
				 mes = 'Mayo de ';
				 break;
				 case '07':
				 mes = 'Junio de ';
				 break;
				 case '08':
				 mes = 'Julio de ';
				 break;
				 case '09':
				 mes = 'Agosto de ';
				 break;
				 case '10':
				 mes = 'Septiembre de ';
				 break;
				 case '11':
				 mes = 'Octubre de ';
				 break;
				 default:
				 mes = 'Noviembre de ';
				 break;
				 
			 }
			 return mes+anio;
		 
		}
		
		
		function window_size(){
			
			if($(window).width() < 750){
				
				$("#form-send").css('width','100%');
				$("#logo").css('height','100px');
				//alert($(window).width());
			}  else {
				
				//alert($(window).width());
				$("#logo").css('height','200px');
				$("#form-send").css('width','50%');
				
				
				
			}
			
			
			
			
		}
		
	function obtener_resultados(){
		
	 var unidad = getQueryVariable('requiriente');
	 var mes = getQueryVariable('encuesta');
      console.log(unidad+'  '+mes);
	$.ajax({
			method:"POST",
			url: "php/obtener_respuesta_mtd.php",
			dataType:"json",
			data:{codigo:unidad,mesEncuesta:mes}
			
		}).done(function(data){
			
			//INICIAMOS CON EL DYNAMIC TAB DE BOOTSTRAP
			var tabs = '<ul class="nav nav-tabs">';
			var contenido = '<div class="tab-content">';
			var active = "";
			var porcentaje = 0;
			
			var ponderacionTotal = [];
			var total = [];
			var nombre_encuesta = '';
			var nombre_encuesta_inicial = '';
			var contador_pregunta = [];
		
			
			var array_contenido = [];
			var indice_array_contenido = 0;
			var pk_aspirante = 0;
			$("#preguntas").html("");
			
			$("#mes").append("Mes de "+mesAnterior(mes));
			$("#unidad").append(data.unidad.txtNombre);
			
			data.contestado.forEach(function(entry){
				
				
				if(pk_aspirante == 0){
					
					pk_aspirante = entry.pk_aspirante;
					nombre_encuesta = entry.puesto;
					nombre_encuesta_inicial = entry.puesto;
					
					
					array_contenido[indice_array_contenido] = '<tr class="success"><td>'+entry.puesto+'</td><td></td></tr>';
					
					contador_pregunta[indice_array_contenido]=1;
				} 
				
				if(nombre_encuesta != entry.puesto) {
					pk_aspirante = entry.pk_aspirante;
					indice_array_contenido = 0;
					nombre_encuesta = entry.puesto;
					array_contenido[indice_array_contenido] = typeof(array_contenido[indice_array_contenido]) == 'undefined' ?
					'' : array_contenido[indice_array_contenido];
					array_contenido[indice_array_contenido] += '<tr class="success"><td>'+entry.puesto+'</td><td></td></tr>';
					
				} else if(pk_aspirante != entry.pk_aspirante){
					 pk_aspirante = entry.pk_aspirante;
					 indice_array_contenido ++;
					 contador_pregunta[indice_array_contenido] = typeof(contador_pregunta[indice_array_contenido])=='undefined' ? 1 : contador_pregunta[indice_array_contenido];
					 array_contenido[indice_array_contenido] = typeof(array_contenido[indice_array_contenido]) == 'undefined' ?
					'' : array_contenido[indice_array_contenido];
					 array_contenido[indice_array_contenido] += '<tr class="success"><td>'+entry.puesto+'</td><td></td></tr>';
				 }
															
					array_contenido[indice_array_contenido] += '<tr>';
																
					array_contenido[indice_array_contenido] += '<td>'+contador_pregunta[indice_array_contenido] +'.- ' + entry.pregunta +'</td>';
					
					contador_pregunta[indice_array_contenido] ++ ;
																			
					ponderacionTotal[indice_array_contenido] = typeof(ponderacionTotal[indice_array_contenido]) == 'undefined' ? 
					parseInt(entry.preguntaPonderacion) : 
					ponderacionTotal[indice_array_contenido]+parseInt(entry.preguntaPonderacion); 
					
						total[indice_array_contenido] = typeof(total[indice_array_contenido]) == 'undefined' ? 0 : total[indice_array_contenido];
						
					switch(entry.respuestaOrden){
				
							case '5':
							total[indice_array_contenido] += (10*entry.preguntaPonderacion);
							break;
							case '4':
							total[indice_array_contenido] += (9*entry.preguntaPonderacion);
							break;
							case '3':
							total[indice_array_contenido] += (8*entry.preguntaPonderacion);
							break;
							case '2':
							total[indice_array_contenido] += (7*entry.preguntaPonderacion);
							break;
							default:
							total[indice_array_contenido] += (5*entry.preguntaPonderacion);
							break;
							
						
						
					}
					
						if(entry.banComentario == 1){
							
								array_contenido[indice_array_contenido] += entry.comentario == null ? 
								'<td>C.- No realiz&oacute; comentario</td>': 
								'<td>C.- '+entry.comentario + '</td>';
						   } else {
							   
								array_contenido[indice_array_contenido] += '<td>R.- '+ entry.respuesta + '</td>';
							   
						   }
						
						array_contenido[indice_array_contenido] += '</tr>';
						
			});
					
													
				for(var i = 0; i <= indice_array_contenido; i++ ){
					
					active = i == 0 ? 'class="active"' : '';
					
					tabs +='<li '+active+'><a data-toggle="tab" href="#id'+i+'">'+(i+1)+'&deg contestaci&oacute;n</a></li>';
					
					active = i == 0 ? ' in active' : '';
					
					contenido += '<div id="id'+i+'" class="tab-pane fade'+active+'">';
					
					porcentaje = parseInt(total[i] / ponderacionTotal[i] * 10);
					contenido += '<div class="well" style="color:blue;text-align:center">&Iacute;ndice de satisfacci&oacute;n del cliente (ISC): '+porcentaje+'%</div>';
					
					contenido += '<table class="table table-bordered"><thead><tr class="info">'+
					'<td>Secci&oacute;n / Pregunta </td>'+
					'<td>Respuesta / Comentario</td>'+
					'</tr></thead><tbody>';
					
					
					contenido += array_contenido[i];
					
					contenido += '</tbody></table>';
															
					contenido += '</div>';
				}
				
				
				tabs += '</ul><br>';
				contenido += '</div>';
				tabs += contenido;

				$("#preguntas").append(tabs);
			

		}).fail(function(error){
					console.log(error);
					alert("Por el momento no esta disponible el apartado de preguntas, intente mas tarde (Puedes intentarlo nuevamente..)");
				
					
		});
	}
		
		
	$(function () {
		
			obtener_resultados();
		
						 
	});
			
			