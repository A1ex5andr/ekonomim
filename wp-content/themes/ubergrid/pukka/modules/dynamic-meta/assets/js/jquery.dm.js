"use strict";

jQuery(document).ready(function($){
    /*
	* jQuery UI sortable
	*/
	
	 $( "#dynamic-meta-content" ).sortable({
		revert: true
	}).sortable('disable');
	
	$('#dynamic-meta-wrapper').on('mouseenter', '.dynamic-meta-box-title', function(e){
		$( "#dynamic-meta-content" ).sortable('enable');
	});
	
	
	$('#dynamic-meta-wrapper').on('mouseleave', '.dynamic-meta-box-title', function(e){
		$( "#dynamic-meta-content" ).sortable('disable');
	});
	
	$('#dynamic-meta-wrapper').on('mouseleave', '.dynamic-meta-box', function(e){
		$(this).find('.wp-picker-open').removeClass('wp-picker-open');
		$(this).find('.wp-color-picker').css('display', 'none');
	});
	    
    $('.numeric-updown').numericUpDown();
	
	$('#dynamic-meta-wrapper').on('click', '.dm-size-up', function(e){
		var box = $(this).closest('.dynamic-meta-box').get(0);
		var size = $(box).find('.dm-size');
		var sval = parseInt($(size).val());
        var max = parseInt($(size).data('max'));
        var min = parseInt($(size).data('min'));
        var step = parseInt($(size).data('step'));
		if(sval < max){
			sval += step;
            if(sval > max){
                sval = max;
            }
			$(box).css('width', sval + '%');
			$(size).val(sval);
		}else{            
			sval = max;
			$(box).css('width', sval + '%');
			$(size).val(sval);
        }
		$('#dynamic-meta-content').trigger('boxresize', [box]);
	});
    
	$('#dynamic-meta-wrapper').on('click', '.dm-size-down', function(e){
		var box = $(this).closest('.dynamic-meta-box').get(0);
		var size = $(box).find('.dm-size');
		var sval = parseInt($(size).val());
        var max = parseInt($(size).data('max'));
        var min = parseInt($(size).data('min'));
        var step = parseInt($(size).data('step'));
        
		if(sval > min){
			sval -= step;
            if(sval < min){
                sval = min;
            }
			$(box).css('width', sval + '%');
			$(size).val(sval);
		}else{
			sval = min;
			$(box).css('width', sval + '%');
			$(size).val(sval);
		}
		$('#dynamic-meta-content').trigger('boxresize', [box]);
	});	
    	
	$('#dynamic-meta-add').click(function(e){		
		var data = {
			action: 'pukka_get_dm_box',
			type: $('#dynamic-meta-select').val()
		}
		$('#dynamic-meta-loading').css('display', 'inline');
		$.post(ajaxurl, data, function(res){
			$('#dynamic-meta-loading').css('display', '');
			$('#dynamic-meta-content').append(res);
            $('#dynamic-meta-content').trigger('dmadded');
		});		
	});
    
    $('.dm-toolbar li').click(function(e){		
		var data = {
			action: 'pukka_get_dm_box',
			type: $(this).data('type')
		}
		$(this).find('.dm-tool-loading').css('display', 'block');
		$.post(ajaxurl, data, function(res){
			$('#dynamic-meta-loading').css('display', '');
			$('#dynamic-meta-content').append(res);
            $('#dynamic-meta-content').trigger('dmadded');
            $('.dm-tool-loading').css('display', '');      
		});  		
	});
    
    $('#dynamic-meta-wrapper').on('click', '.dm-edit', function(e){
		$(this).parent().parent().children('.dm-content-wrap').children('.open-editor').click();
	});	
	    
    $('#dynamic-meta-content').on('focus', 'textarea', function(e){
        $(this).closest('.dm-content-box').addClass('focus');
    });
           
    $('#dynamic-meta-content').on('blur', 'textarea', function(e){
        $(this).closest('.dm-content-box').removeClass('focus');
    });
	
	//color-picker color reset	
	$('#dynamic-meta-wrapper').on('click', '.dynamic-meta-box .dm-colors-reset', function(e){
		var box = $(this).closest('.dynamic-meta-box');
		$(box).find('.dm-select-color, .dm-edit').val('#000000').change().val('');
	});
});
