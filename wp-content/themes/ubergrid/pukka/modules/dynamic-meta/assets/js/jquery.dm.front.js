jQuery(document).ready(function($){
	/*
	*	Accordion
	************************************/

	$('.acc-box-title').click(function(e){
		$(this).parent().children('.acc-box-content').slideToggle(500, function(e){
			if($(this).css('display') == 'block'){
				$(this).parent().children('.acc-box-title').children('.acc-title-arr').addClass('open').html('&#x25B2;');
			}else{
				$(this).parent().children('.acc-box-title').children('.acc-title-arr').removeClass('open').html('&#x25BC;');
			}
		});
	});

	/*
	*	Tabs
	************************************/
	$('.tabs-title li').click(function(e){
		var parent = $(this).closest('.dm-tabs').get(0);
		var list = $(parent).find('.tabs-title li').get();
		var index = $.inArray(this, list);

		$(parent).find('.tabs-title .current').removeClass('current');
		$(this).addClass('current');

		$(parent).find('.tabs-body .current').removeClass('current');
		var content = $(parent).find('.tabs-body li').get();
		$(content[index]).addClass('current');
	});

	$('.tabs-title h4').click(function(e){
		$(this).closest('li').click();
	});


	/*
	*	Contact form
	***********************************/

	$('.dm-contact-form').submit(function(e){
			e.preventDefault();
			var form = $(this);
			$.post($(form).attr('action'), $(form).serialize() , function(response){
				if( response.error != "false" ){
				   $(form).get(0).reset();
				   alert(response.message);
				}
				else{
				   alert('Error: ' + response.message);
				}
			}, "json");

			return false;
	   });

});

