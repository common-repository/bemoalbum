jQuery(document).ready(function($){
	//Init the colorbox
	$('a.colorbox_popup').colorbox(
	{ 
		opacity:0.5, 
		rel:'group1',
		current: "{current} of {total}" 
	}
	);
	
	if ($(".additional_info")[0])
	{
		$(".additional_info").css("position", "absolute");
		$(".additional_info").css("right", "5px");
		$(".additional_info").css("top", "5px");		
		$(".additional_info").css("background", "#ebebeb");
		$(".additional_info").css("color", "black");
		$(".additional_info").css("padding", "5px 5px 5px 5px");
		$(".additional_info").css("font-size", "8px");	
		$(".additional_info").html('<a href="http://www.bemoore.com/bemoalbum">BEMOAlbum Free</a>');	
		$(".additional_info a").css("z-index", "1000");	
	}
	
});
