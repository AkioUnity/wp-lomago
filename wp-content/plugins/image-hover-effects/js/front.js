jQuery(document).ready(function($) {
	$('.ih-item a.taphover').on('touchstart', function (e) {
	    'use strict'; //satisfy code inspectors
	    var link = $(this); //preselect the link
	    if (link.hasClass('hover')) {
	        return true;
	    } else {
	        link.addClass('hover');
	        $('.ih-item a.taphover').not(this).removeClass('hover');
	        e.preventDefault();
	        return false; //extra, and to make sure the function has consistent return points
	    }
	});
}); 