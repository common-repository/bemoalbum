jQuery(document).ready(function($){
  
	$("#loading-div-background").css({ opacity: 0.8 });
	
	$( ".category_selector" ).click(function() {
     	var name =  $( this ).attr('name');	
		var values = "";
		var i = 0;
		$( this ).children(':selected').each(function() {
		  if(i > 0)
				values += ",";
		  values += $( this ).val();
		  i++;
		});

		// $("#loading-div-background").show();
		 
		jQuery.ajax({
			type: "POST",
			url: "admin-ajax.php",
			data: { action: 'bemoalbum_category_update', name: name, values: values }
		}).done(function( response ) {
			// $("#loading-div-background").hide();
		});		
		
	});
});
