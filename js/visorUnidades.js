 var arregloPHP ; // en arreglo original
	  var segundoAcceso=false; // se cambia a true cueando es mayor a un acceso
		
		function getCleanedString(cadena){
		
		  var temporalCadena = cadena.toLowerCase();
		   
		   temporalCadena = temporalCadena.replace(/á/gi,"a");
		   temporalCadena = temporalCadena.replace(/é/gi,"e");
		   temporalCadena = temporalCadena.replace(/í/gi,"i");
		   temporalCadena = temporalCadena.replace(/ó/gi,"o");
		   temporalCadena = temporalCadena.replace(/ú/gi,"u");
		   temporalCadena = temporalCadena.replace(/ñ/gi,"n");
		   
		   return temporalCadena;
		}
		
		function llenarTabla(){
		
		
			if(segundoAcceso){
				$("#tabla").empty();
			   var rows = "";
			   var total = 1;
			   var banEncuestado = "";
			   var ordenar = $("#filtroUnidad").val().trim().replace(/(\s\s+)/g,' ');
				
			   var ordenarArray = ordenar.length > 0 ? ordenar.split(' ') : [];
			   var mostrar =  true;
			  
			   arregloPHP.forEach(function(row){
				   banEncuestado = row.esEncuestado ==1 ? "Si": "no";
				   mostrar = true;
				   
				   if(ordenar.length > 0){
										
						ordenarArray.forEach(function(valor){
							mostrar = getCleanedString(row.idCliente+' '+row.txtNombre).indexOf(getCleanedString(valor))==-1 ? false : mostrar; 
							
						});
					}
					
					if(mostrar || ordenar.length ==0 ){
						rows += "<tr>"+
						"<td>"+row.idCliente+"</td>"+ 
						"<td>"+row.req_codigo+"</td>"+
						"<td>"+row.txtNombre+"</td>"+
						"<td>"+row.txtResponsable+"</td>"+
						"<td>"+row.idProducto+"</td>"+
						"<td>"+banEncuestado+"</td>"+
						"</tr>"
						total ++;
						}
			   });
			   
			   $("#tabla").append(rows);
			} else {
			
				segundoAcceso= true
			}
		}
		
		
		$(function(){
			 var query = window.location.search.substring(1);
			 
			 $.ajax({
					Method: 'POST',
			        url: 'php/detalleCliente.php',
				 	dataType: 'json'			
				}).done(function(data){
					arregloPHP = data;			
			    	//console.log(arregloPHP);
				     
					llenarTabla();
				 
			 }).fail(function(error){
				document.getElementById("label").innerHTML = 'Informaci&oacute;n no disponible por el momento';
				 console.log('ERROR');
				 console.log(error);
				 
			 });
											
			$('input').keypress(function(e){
				if(e.which == 13){
				  return false;
				}
				});
							
			  
			  $("#filtroUnidad").on('keyup', llenarTabla).keyup();
			 $('th').css('padding','2px 0px 2px 0px');
			});