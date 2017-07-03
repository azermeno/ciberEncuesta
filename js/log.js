  	
		
		$(function(){
			
			$.ajax({
				method:"POST",
				url: "php/obtener_log_mtd.php",
				dataType:"json"
					
			}).done(function(dato){
			   
				 google.charts.load('current', {'packages':['table']});
				 google.charts.setOnLoadCallback(function(){
				 
					var data = new google.visualization.DataTable();
					data.addColumn('string', 'Fecha de captura');
					data.addColumn('string', 'Detalle');
					var arregloTabla = [];
					var contador = 0;
					if(dato.length > 0){
						dato.forEach(function(fila){
							mostrar = true;
							
							
							arregloTabla[contador] = [
								fila.fecha,
								fila.accion
							];

							contador ++; 
							
						});
						if(contador == 0){
						
							arregloTabla[contador] = [
								'No hay resultados',
								'No hay resultados',
							];
						}
						
					} 
				
					data.addRows(arregloTabla);

					var table = new google.visualization.Table(document.getElementById('table_div'));

					table.draw(data, {showRowNumber: false, width: '100%', height: '100%'});
				 
				 
				 });

		 
			
			}).fail(function(){
			
			
			
			})
							
			
			$(window).resize(function() {
				tamanoPantalla();  
			});
					
			$('input').keypress(function(e){
				if(e.which == 13){
				  return false;
				}
				});
					
			 $('th').css('padding','2px 0px 2px 0px');
			});