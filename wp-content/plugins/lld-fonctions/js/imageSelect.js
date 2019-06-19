jQuery(document).ready(function(){  
	jQuery('.selectImage').click(function() {
		if ( !jQuery(this).hasClass('disabled') )
		{
	    	jQuery(this).parent().find('.selectImage').removeClass('active');
	    	jQuery(this).addClass('active');
	    	jQuery('input[name="' + jQuery(this).parent().attr('data-field') + '"]').attr('value', jQuery(this).attr('data-value'));

	    	jQuery.event.trigger({
				type: "imageSelected",
				attr: '#' + jQuery(this).closest('.contentSubtab').attr('id'),
				value: jQuery(this).attr('data-value')
			});
	    }
	});
});