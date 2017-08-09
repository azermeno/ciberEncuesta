var unidadesWs = [];
var unidadesMySQL = [];
var unidadesConComentarios = [];
var banderaUnidad = true;
var anioMesEnCurso;		
		function mesAnterior(mes){
			
			 var anio = mes.substring(0, 4);
			 var mes = mes.substring(4);
			 
			 switch(mes){
				 
				 case '01':
				 anio -= 1;
				 mes = 'diciembre de ';
				 break;
				 case '02':
				 mes = 'enero de ';
				 break;
				 case '03':
				 mes = 'febrero de ';
				 break;
				 case '04':
				 mes = 'marzo de ';
				 break;
				 case '05':
				 mes = 'abril de ';
				 break;
				 case '06':
				 mes = 'mayo de ';
				 break;
				 case '07':
				 mes = 'junio de ';
				 break;
				 case '08':
				 mes = 'julio de ';
				 break;
				 case '09':
				 mes = 'agosto de ';
				 break;
				 case '10':
				 mes = 'septiembre de ';
				 break;
				 case '11':
				 mes = 'octubre de ';
				 break;
				 default:
				 mes = 'noviembre de ';
				 break;
				 
			 }
			 return mes+anio;
		 
		}
		
		function obtenerMes(){
			
			/*
			*Se va a obtener todos los meses y a su ves del ultimo mes la informacion de las unidades con respuetas
			*a la encuesta, se obtendran todas las unidades que existen en la base de datos original 'SQL' para compara
			*con la existente
			*/
			$.ajax({
			  method: "POST",
			  url: "php/obtener_mes_mtd.php",
			  dataType: "json"
			  }).done(function(data){
				
				 anioMesEnCurso = data.anio;
				 var totalUnidadesSinEncuestar = 0;
				 var unidadesSinEncuestar = "<tbody>";
				 var banderaUnidadFaltante;
				 
				 //Para indicar el número de unidades encustadas por mes
				
				 
					  data.unidadesWs.forEach(function(faltaEncuesta){
						  banderaUnidadFaltante = true;
						  if(data.mesRegistrado==true){
							  data.unidadesMySQL.forEach(function(mysql){
								  
								  if(mysql.req_codigo==faltaEncuesta.req_codigo){
									 
									  banderaUnidadFaltante = false;
									  
								  }
								  
							  });
						  }
						  if(banderaUnidadFaltante==true){
								 unidadesSinEncuestar += '<tr>'+
								 '<th>'+faltaEncuesta.txtNombre+'</th>'+
								 '<th>'+faltaEncuesta.idProducto+'</th>'+
								 '<th>'+faltaEncuesta.sitio+'</th>'+
								 '<th>'+faltaEncuesta.zona+'</th>'+
								 '<th style="text-align:center"><button type="button" class="btn btn-info" data-codigo="'+faltaEncuesta.req_codigo+'" style="width:60%;text-align:center"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></th>'+
								 '</tr>';
								 totalUnidadesSinEncuestar++;
						  } 
					 });							 
				 
				 
					
					unidadesSinEncuestar += "</tbody>";
					
					$("#tablaFaltantes").append(unidadesSinEncuestar);
					
					$("#totalFaltantes").append("<b>"+totalUnidadesSinEncuestar+" UNIDADES SIN ENCUESTAR</b>");
					
					//Se asignan a variables globales los resultados
					unidadesWs = data.unidadesWs;
					unidadesMySQL = data.unidadesMySQL;
					unidadesConComentarios = data.unidadesComentarios;
					
				  $(".btn-info").on('click',realizarEncuesta);	
				 //Pasando informacion al select id=mes
				  data.mes.forEach(function(entry){
							 
				 $('#mes').append(
					'<option value="' + entry.pk_mes + '">' + mesAnterior(entry.mes) + '</option>'
					);
					
				  });
				  if(typeof(data.unidadesMySQL)!= 'undefined'){
				   cargarUnidad();
				  }
				   
			  }).fail(function(error){
				  console.log(error);
				  alert("Por el momento no esta disponible el servicio, intente m\u00E1s tarde");
				  
			  });
					
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
		
		function realizarEncuesta(){
						
			 window.location='ciberEncuesta.php?'+window.btoa('requiriente='+$(this).attr('data-codigo')+'&encuesta='+anioMesEnCurso+'&manual=1');
			
		};
		function limpiarUnidad(){
			
			$("#puesto").empty();
			$("#puesto").append(
			'<option value="0" style="background: white; color:black;">&Aacutereas a seleccionar</option>'
			)
			
		}
		
		function changeMes(){
			$("#unidad").empty();
			$("#preguntas").empty();
			$("#unidad").selectpicker('refresh');
			if(banderaUnidad==true){
												
				banderaUnidad = false;
				var idMes = $(this).val();
				
				buscarUnidadesPorMes(idMes);
			}
		}
		
		function cargarUnidad(){
			
			var unidadesMes = "";
			$("#unidad").empty();
			
			//Unidades encuestadas
		   
		   var unidadesContestadas = 0;
			if(unidadesMySQL.length > 0){
				
				unidadesWs.forEach(function(row){
					
					//para sacar las unidades encuestadas
					unidadesMySQL.forEach(function(constestado){

							if(constestado.req_codigo == row.req_codigo){
								
								unidadesContestadas++;
							}
					});
					
					var radioSeleccionado = $('input:radio[name=comentarios]:checked').val();
					if(radioSeleccionado == 0 || radioSeleccionado ==2){
						
						unidadesMySQL.forEach(function(entry){

							if(entry.req_codigo == row.req_codigo){
								
								var producto = row.idProducto == "Hematix" ? 1:2;
								if(radioSeleccionado == 2){
									if(row.contestadoManual==1){
										unidadesMes += '<option value="'+entry.fk_unidad+'/'+producto+'">'+row.txtNombre+'</option>';
									}
								} else {
									unidadesMes += '<option value="'+entry.fk_unidad+'/'+producto+'">'+row.txtNombre+'</option>';
								}
							}
						});
					} else {
						
						unidadesConComentarios.forEach(function(entry){

							if(entry.req_codigo == row.req_codigo){
								
								var producto = row.idProducto == "Hematix" ? 1:2;
								unidadesMes += '<option value="'+entry.fk_unidad+'/'+producto+'">'+row.txtNombre+'</option>';
							}
							
						});
						
					}				
				});
				$("#contestadas").html(unidadesContestadas);
			} else {
				
				 
				  
					//Agretamos el valor de unidades encuestadas
					$("#contestadas").html(unidadesContestadas);
			}
			       
					$("#unidad").append(unidadesMes);
					$("#unidad").selectpicker('refresh');
			      banderaUnidad=true;
			
		}
		
		function buscarUnidadesPorMes(fechaBusqueda){
			
			$.ajax({
				method: "POST",
				url: "php/obtener_unidad_mtd.php",
				dataType: "json",
				data: {mes:fechaBusqueda}
				
			}).done(function(data){
				
				unidadesConComentarios = data.unidadesComentarios;
				unidadesMySQL = data.unidades;	
				cargarUnidad();
				
			}).fail(function(error){
				
				console.log(error);
				banderaUnidad=true;
				
			});
		}
		
		    function unidadesConComentario(){
								
				cargarUnidad();
			}
		
		    $(function () {
				
				$("input:radio[name=comentarios]").on('change',unidadesConComentario);
				
				 window_size();
			
			$(window).resize(window_size);
				
			obtenerMes();
			
			$("#mes").on("change",changeMes);
			$("#unidad").on("change",function(){
				
				$("#preguntas").html('');
			});
			
			$("#form-send").submit(function(event){
				
					 event.preventDefault();
					
					 var mes = $("#mes").val();
					 var codigoUnionProducto = $("#unidad").val().split('/');
					 var unidad = codigoUnionProducto[0];
					 
				     var producto = [codigoUnionProducto[1] == 2 ? 'PASTEUR' : 'HEMATIX',
					 codigoUnionProducto[1] == 2 ? 'LABORATORIO' : 'BANCO DE SANGRE'];
				     var preguntaTemporal="";
										
					if( unidad == 0){
						
						$("#unidad").focus();
						alert("Debe seleccionar una unidad");
						
					} else {
						
							 $.ajax({
								method:"POST",
								url: "php/obtener_respuesta_mtd.php",
								dataType:"json",
								data:{codigo:unidad,mes:mes}
								
							}).done(function(data){
								
								//INICIAMOS CON EL DYNAMIC TAB DE BOOTSTRAP
								var tabs = '<ul class="nav nav-tabs">';
								var contenido = '<div class="tab-content">';
								var active = "";
								var porcentaje = 0;
								
								var ponderacionTotal = [];
								var total = [];
								var porcentajeIndividual = [];
								var nombre_encuesta = '';
								
								var contador_pregunta = [];
								var contador_pregunta_seccion = [];
								var numero_secciones = [];
							
								
								var array_contenido = [];
								var indice_array_contenido = 0;
								var pk_aspirante = 0;
								var ponderacionRespuesta = 0;
								var ponderacionSeccion = 0;
								var pk_puesto_anterior = 0;
								var pk_puesto_actual = 0;
								//Va a obtener el valor pk_aspirante inicial para saber cuantas encuesta son
								var pk_puesto_incial = 0;
								
								
								//nuevo esquema
								var contadorPreguntas =0;
								var ponderacionRespuestaXseccion =0;
								
								$("#preguntas").html("");
								
								/*
								*Manda todas las respuesta ordenadas por encuesta contestada
								*para saber cuando cambia de encuesta se va a tomar como referencia el pk_puesto
								*cuanod se repita quiere decir que es otra ronda de encuestas
								*/
								data.contestado.forEach(function(entry){
									
									
									if(pk_aspirante == 0){
										
										pk_aspirante = entry.pk_aspirante;
										nombre_encuesta = entry.puesto;
										pk_puesto_anterior = entry.pk_puesto;
										pk_puesto_incial = entry.pk_puesto;
										
										array_contenido[indice_array_contenido] = '<tr class="success"><td>'+entry.puesto+'</td><td><span id="'+indice_array_contenido+'-'+entry.pk_puesto+'">'+indice_array_contenido+'-'+entry.pk_puesto+'</span></td></tr>';
										
										//Inicilizando variables
										contador_pregunta[indice_array_contenido]=1;
										ponderacionTotal[indice_array_contenido] = 0;
										numero_secciones[indice_array_contenido] = 0;
									}
									
									/*
									*Entra cada que cambia de seccion
									*seccion(Es una encuesta del conjunto de encuestas que forma la ciberEncuesta)
									*/
									if(pk_puesto_anterior != entry.pk_puesto) {
											pk_aspirante = entry.pk_aspirante;
											
											//Contestó más de una ves y se va a obtener la nueva ciberEncuesta
										if(pk_puesto_incial == entry.pk_puesto){
												
												//se va a obtenere la calificación de cada seccion
											porcentajeIndividual[indice_array_contenido+'-'+pk_puesto_anterior] = 
											typeof(porcentajeIndividual[indice_array_contenido+'-'+pk_puesto_anterior]) == 'undefined' ? 
											0 : porcentajeIndividual[indice_array_contenido+'-'+pk_puesto_anterior];
											
											
											//Obtenemos el valor por sección
											//Se hizo un cambio para que fuera correcta la informacion de sección
											porcentajeIndividual[indice_array_contenido+'-'+pk_puesto_anterior] = ponderacionRespuestaXseccion == 0 ? 0 :
											(ponderacionRespuestaXseccion / contadorPreguntas) % 1 == 0 ? ponderacionRespuestaXseccion / contadorPreguntas :
											Math.round(ponderacionRespuestaXseccion / contadorPreguntas*10)/10;
											
											pk_puesto_anterior = entry.pk_puesto;
											
											//se hace al calculo de la sección anterior, antes de reiniciar el indice_array_contenido
											
											ponderacionTotal[indice_array_contenido] += ponderacionSeccion == 0 ? 0 :(ponderacionRespuestaXseccion/contadorPreguntas)*ponderacionSeccion; 
											
											ponderacionRespuestaXseccion = 0;
											contadorPreguntas = 0 ;
											total[indice_array_contenido] = 0;
											
											//inicializamos (si no lo esta) el erreglo con el índice que nos va  decir el número de secciones que esa formada cada encuesta
											numero_secciones[indice_array_contenido] = typeof(numero_secciones[indice_array_contenido]) == 'undefined' ?
											0 : numero_secciones[indice_array_contenido];
											
											numero_secciones[indice_array_contenido] += ponderacionSeccion == 0 ? 0 : 1;
											
											 
											nombre_encuesta = entry.puesto;
											array_contenido[indice_array_contenido] = typeof(array_contenido[indice_array_contenido]) == 'undefined' ?
											'' : array_contenido[indice_array_contenido];
											
											//se cambia el indice para empezar con la nueva pestaña
											indice_array_contenido ++;
											ponderacionTotal[indice_array_contenido] = 0;
											
											array_contenido[indice_array_contenido] += '<tr class="success"><td>'+entry.puesto+
											'</td><td><span id="'+indice_array_contenido+'-'+entry.pk_puesto+'">'+indice_array_contenido+'-'+entry.pk_puesto+
											'</span></td></tr>';
										
											//Se incremente el indice contenido
											 contador_pregunta[indice_array_contenido] =1;
											 
										} else {
																						
											porcentajeIndividual[indice_array_contenido+'-'+pk_puesto_anterior] = 
											typeof(porcentajeIndividual[indice_array_contenido+'-'+pk_puesto_anterior]) == 'undefined' ? 
											0 : porcentajeIndividual[indice_array_contenido+'-'+pk_puesto_anterior];
											
												porcentajeIndividual[indice_array_contenido+'-'+pk_puesto_anterior] = ponderacionRespuestaXseccion == 0 ? 0 :
												(ponderacionRespuestaXseccion / contadorPreguntas) % 1 == 0 ? ponderacionRespuestaXseccion / contadorPreguntas :  
												Math.round(ponderacionRespuestaXseccion / contadorPreguntas*10)/10;
												
											pk_puesto_anterior = entry.pk_puesto;
											 contador_pregunta_seccion[indice_array_contenido]=0;
											//se hace al calculo de la sección anterior, antes de cambiar el indice_array_contenido
											 
											ponderacionTotal[indice_array_contenido] += ponderacionSeccion == 0 ? 0 :(ponderacionRespuestaXseccion/contadorPreguntas)*ponderacionSeccion; 
											
											ponderacionRespuestaXseccion = 0;
											contadorPreguntas = 0 ;
											total[indice_array_contenido] = 0;
											 
											 //inicializamos (si no lo esta) el erreglo con el índice que nos va  decir el número de secciones que esa formada cada encuesta
											numero_secciones[indice_array_contenido] = typeof(numero_secciones[indice_array_contenido]) == 'undefined' ?
											0 : numero_secciones[indice_array_contenido];
											
											//Contador de secciones
											numero_secciones[indice_array_contenido] += ponderacionSeccion == 0 ? 0 : 1;
											
											
											// contador_pregunta[indice_array_contenido] = typeof(contador_pregunta[indice_array_contenido])=='undefined' ? 1 : contador_pregunta[indice_array_contenido];
											 array_contenido[indice_array_contenido] = typeof(array_contenido[indice_array_contenido]) == 'undefined' ?
											'' : array_contenido[indice_array_contenido];
											
											 array_contenido[indice_array_contenido] += '<tr class="success"><td>'+entry.puesto+'</td><td><span id="'+
											 indice_array_contenido+'-'+entry.pk_puesto+'">OOOO</span></td></tr>';
												
											
										}
									} 



									       
													
										array_contenido[indice_array_contenido] += '<tr>';
																					
										array_contenido[indice_array_contenido] += '<td>'+contador_pregunta[indice_array_contenido] +'.- ' + 
										entry.pregunta +'</td>';
										
										contador_pregunta[indice_array_contenido] ++ ;
 																
										total[indice_array_contenido] = typeof(total[indice_array_contenido]) == 'undefined' ? 0 :
										total[indice_array_contenido];
										
										//si es 0 no se incrementa el contador de sección
										ponderacionSeccion = entry.puestoPonderacion;
										
										//Se obtiene la suma x seccion de las preguntas y respuestas con su ponderación										
										ponderacionRespuestaXseccion += ponderacionSeccion==0 ? 0 : parseInt(entry.respuestaPonderacion)* parseInt(entry.preguntaPonderacion);
										contadorPreguntas += entry.preguntaPonderacion > 0 ? 1 : 0;
										//inicializamos la variable 
										contador_pregunta_seccion[indice_array_contenido] = 
										typeof(contador_pregunta_seccion[indice_array_contenido]) == 'undefined' ? 
										0 : contador_pregunta_seccion[indice_array_contenido];
										//si contador es 0 no se necesita contar para que no afecte al dividir
										contador_pregunta_seccion[indice_array_contenido] += entry.respuestaPonderacion > 0 ? 1:0;
										
									//	total[indice_array_contenido] += ponderacionRespuesta;
										total[indice_array_contenido] += ponderacionRespuestaXseccion;
										
											
											if(entry.banComentario == 1){
												
													array_contenido[indice_array_contenido] += entry.comentario == null ? 
													'<td>C.- No realiz&oacute; comentario</td></tr>': 
													'<td>C.- '+entry.comentario + '</td></tr>';
								               } else {
												   
													array_contenido[indice_array_contenido] += '<td>R.- '+ entry.respuesta + '</td></tr>';
													
												   if(entry.comentario != null){
													 array_contenido[indice_array_contenido] += ' </tr><td>Comentario: </td><td>'+entry.comentario+'</td></tr>'
													   
												   }
											   }
											
											//array_contenido[indice_array_contenido] += '</tr>';
											pk_puesto_actual = entry.pk_puesto;
								});
								
								//se va a obtenere la calificación de cada seccion
										
										porcentajeIndividual[indice_array_contenido+'-'+pk_puesto_anterior] = ponderacionRespuestaXseccion == 0 ? 0 :
										(ponderacionRespuestaXseccion / contadorPreguntas) % 1 == 0 ? ponderacionRespuestaXseccion / contadorPreguntas :  
										Math.round(Math.round((ponderacionRespuestaXseccion / contadorPreguntas)*10)/10);
										
											
								//se hace al calculo de la sección anterior, para la ultima interaccion
										 ponderacionTotal[indice_array_contenido] += ponderacionSeccion == 0 ? 0 :(ponderacionRespuestaXseccion/contadorPreguntas)*ponderacionSeccion; 
										
										numero_secciones[indice_array_contenido] += ponderacionSeccion == 0 ? 0 : 1;
																  
									//se forman las pestañas y se coloca el porcentaje total	

								    var estado;
									for(var i = 0; i <= indice_array_contenido; i++ ){

										active = i == 0 ? 'class="active"' : '';
										
										tabs +='<li '+active+'><a data-toggle="tab" href="#id'+i+'">'+(i+1)+'&deg contestaci&oacute;n</a></li>';
										
										active = i == 0 ? ' in active' : '';
										
										contenido += '<div id="id'+i+'" class="tab-pane fade'+active+'">';
										
										//Hacerlo a dos decimales
										porcentaje = parseFloat(ponderacionTotal[i] / numero_secciones[i] * 10);
										
										porcentaje = porcentaje % 1 == 0 ? porcentaje : Math.round(porcentaje);
										
										if(porcentaje > 90){
												estado = 'Muy Satisfecho';
											} else if(porcentaje > 80 && porcentaje <= 90){
												
												estado = 'Satisfecho';
												
											} else if(porcentaje > 70 && porcentaje <= 80){
												
												estado = 'Medianamente Satisfecho';
												
											} else if(porcentaje > 60 && porcentaje <= 70){
												
												estado = 'Insatisfecho';
												
											}else if (porcentaje >= 20 && porcentaje < 60){
												
												estado = 'Muy Insatisfecho';
												
											} else {
												
												estado = '';
																								
											}
										
									    contenido += '<div class="well" style="color:blue;text-align:center">&Iacute;ndice de satisfacci&oacute;n del cliente (ISC): '+porcentaje+'%  '+estado+'</div>';
										
										contenido += '<table class="table table-bordered"><thead><tr class="info">'+
										'<td>Secci&oacute;n / Pregunta </td>'+
										'<td style="width:35%">Respuesta / Comentario</td>'+
										'</tr></thead><tbody>';
										
										
										contenido += array_contenido[i];
										
										contenido += '</tbody></table>';
																				
										contenido += '</div>';
									}
									
										
									
									tabs += '</ul><br>';
									contenido += '</div>';
									tabs += contenido;

									$("#preguntas").append(tabs);
								
									var resultado = 0;
									var indice ;
									
									for (var i in porcentajeIndividual) {
										    estado = '';
										if (porcentajeIndividual.hasOwnProperty(i)) {
											//resultado += 'porcentajeIndividual' + "." + i + " = " + porcentajeIndividual[i] + "\n";
											indice = i.split('-');
											
											//Para dejarlo a dos decimales
											resultado = porcentajeIndividual[i] * 10;
											if(resultado > 90){
												estado = 'Muy Satisfecho';
											} else if(resultado > 80 && resultado <= 90){
												
												estado = 'Satisfecho';
												
											} else if(resultado > 70 && resultado <= 80){
												
												estado = 'Medianamente Satisfecho';
												
											} else if(resultado > 60 && resultado <= 70){
												
												estado = 'Insatisfecho';
												
											}else if (resultado >= 20 && resultado < 60){
												
												estado = 'Muy Insatisfecho';
												
											} else {
												
												estado = '';
																								
											}
											
											$('#'+i).html(resultado+'%'+'  '+estado);
											
										}
									  }
						
							}).fail(function(error){
										console.log(error);
										alert("Por el momento no esta disponible el apartado de preguntas, intente mas tarde (Puedes intentarlo nuevamente..)");
									
										
							});
					}
				
				});
				 
			});
			
			