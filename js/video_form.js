(function($) {
	$(function(){

		form = $('form.crud');
		
		$('input[name="title"]', form).keyup($.debounce(350, function(e){
			$.post(SITE_URL + 'ajax/url_title', { title : $(this).val() }, function(slug){
				$('input[name="slug"]', form).val( slug );
			});
		}));
		
		$('textarea[name="embed_code"]', form).keyup($.debounce(350, function(e){	
			width = this.value.match(/width=("|')?([0-9]+)("|')?/);
			height = this.value.match(/height=("|')?([0-9]+)("|')?/);
			
			width != null && $('input[name="width"]', form).val(width[2]);
			height != null && $('input[name="height"]', form).val(height[2]);
		}));
		
		$('input[name="width"]', form).keyup($.debounce(350, function(e){	
			embed_code = $('textarea[name="embed_code"]', form);
			embed_code.val(embed_code.val().replace(/width=("|')?([0-9]+)("|')?/g, 'width="'+ (this.value) +'"'));
		}));
		
		$('input[name="height"]', form).keyup($.debounce(350, function(e){	
			embed_code = $('textarea[name="embed_code"]', form);
			embed_code.val(embed_code.val().replace(/height=("|')?([0-9]+)("|')?/g, 'height="'+ (this.value) +'"'));
		}));
		
		
		$('#video-options-tab li:first a').colorbox({
			srollable: false,
			innerWidth: 600,
			innerHeight: 280,
			href: SITE_URL + 'admin/videos/channels/create_ajax',
			onComplete: function() {
				$.colorbox.resize();
				$('form#channels').removeAttr('action');
				$('form#channels').live('submit', function(e) {
					
					var form_data = $(this).serialize();
					
					$.post(SITE_URL + 'admin/videos/channels/create_ajax', form_data, function(data, status) {
						
						if(status == 'success') {
							
							//succesfull db insert do this stuff
							var select = 'select[name=channel_id]';
							var opt_val = data.channel_id;
							var opt_text = data.title;
							var option = '<option value="'+opt_val+'" selected="selected">'+opt_text+'</option>';
							
							//append to dropdown the new option
							$(select).append(option);
															
							//uniform workaround
							$('#video-options-tab li:first span').html(data.title);
							
							//close the colorbox
							$.colorbox.close();
						}
						else {			
							//append the message to the dom
							$('#cboxLoadedContent').html(data.message + data.form);
							$('#cboxLoadedContent p:first').addClass('notification error').show();
						}
					}, 'json');
					
					e.preventDefault();
				});
				
			}
		});
	});
})(jQuery);