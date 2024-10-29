jQuery.noConflict();
(function($) 
{
	//Show / Hide content fields
	$('form#bemoalbum_enter select[name=posttype]').change(function()  {
		var optionSelected = $(this).find("option:selected");
		var valueSelected  = optionSelected.val();
		
		$('option.category_options').hide();
		
		var taxonomies = optionSelected.attr('taxonomies').split(' ');
		
		$( "option.category_options" ).each(function() {
			var category = $( this ).attr("taxonomy");
			
			var arrayLength = taxonomies.length;
				
			for (var i = 0; i < arrayLength; i++) 
			{
				if (taxonomies[i] == category)
					$( this ).show();
			}
		});
	});

	
	
	//Add the shortcode
	function addShortcode(ed,dialog)
	{
		var bemoalbum_string = '[album';
		
		var name = $("select[name=album_id]").val();
		var albumcaptions = $("input#albumcaptions").filter(":checked").val();
		var picturecaptions = $("input#picturecaptions").filter(":checked").val();
		var albumcolumns = $("input#albumcolumns").val();
		var picturecolumns = $("input#picturecolumns").val();
		var backlink_text = $("input#backlink_text").val();
		var albumsclass = $("input#albumsclass").val();
		var albumclass = $("input#albumclass").val();
		var picturesclass = $("input#picturesclass").val();
		var pictureclass = $("input#pictureclass").val();
		
		bemoalbum_string += ' name="' + name + '"';
		
		if(albumcaptions == '1')
			bemoalbum_string += ' albumcaptions="true"';
		else
			bemoalbum_string += ' albumcaptions="false"';
		

		if(picturecaptions == '1')
			bemoalbum_string += ' picturecaptions="true"';
		else
			bemoalbum_string += ' picturecaptions="false"';

		bemoalbum_string += ' albumcolumns="' + albumcolumns + '"';
		bemoalbum_string += ' picturecolumns="' + picturecolumns + '"';
		bemoalbum_string += ' backlink_text="' + backlink_text + '"';
		bemoalbum_string += ' albumsclass="' + albumsclass + '"';
		bemoalbum_string += ' picturesclass="' + picturesclass + '"';
		bemoalbum_string += ' pictureclass="' + pictureclass + '"';
				
		bemoalbum_string += ']';
		
		ed.selection.setContent(bemoalbum_string + ed.selection.getContent() );
		dialog.dialog( "close" );
	}
	
    tinymce.create('tinymce.plugins.bemoalbum', {
        init : function(ed, url) {
            ed.addButton('bemoalbum', {
                title : 'Add An Album',
                image : url+'/icon.png',
                onclick : function() {
					$("div.filter_specific").show();

					dialog = $( "#bemoalbum-dialog-form" ).dialog({
					  height: 600,
					  width: 350,
					  modal: true,
					  buttons: {
						"Add An Album": function() {
							addShortcode(ed,dialog);
						},
						Cancel: function() {
						  dialog.dialog( "close" );
						}
					  },
					  close: function() {
						form[ 0 ].reset();
//						allFields.removeClass( "ui-state-error" );
					  }
					});
				 
					form = dialog.find( "form" ).on( "submit", function( event ) {
					  event.preventDefault();
					  alert('Submit');
					});
 
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
    });
    tinymce.PluginManager.add('bemoalbum', tinymce.plugins.bemoalbum);
})(jQuery);
