jQuery(document).ready(function($) {
    // jQuery('.moreimages span').remove();

    jQuery('#la-loader').hide();
  jQuery('#la-saved').hide();


    setTimeout(function() {
        jQuery('#faqs-container >.ui-accordion-content').first().addClass('firstelement');
    }, 40);


    setTimeout(function() {
        $('.content').each(function(index, el) {
            $(this).find('.ui-accordion-content').first().addClass('firstelement');
        });
    }, 50);


    var sCounter = jQuery('#caption').find('.fullshortcode:last').attr('id');

    jQuery("div.accordian").accordion({
    heightStyle: "content",
    collapsible: true, 
    changestart: function (event, ui) {
        if ($(event.currentTarget).hasClass("item")) {
            event.preventDefault();
            $(event.currentTarget).removeClass("ui-corner-top").addClass("ui-corner-all");
            }
        }
    });
    function create_accordian(str) {
        $( str )
            .accordion({
                header: '> div > h3',
                autoHeight: false,
                collapsible: true
            })
            .sortable({
                axis: 'y',
                handle: 'h3',
                stop: function( event, ui ) {
                    // IE doesn't register the blur when sorting
                    // so trigger focusout handlers to remove .ui-state-focus
                    ui.item.children( 'h3' ).triggerHandler( 'focusout' );
                }
            });
    }
    create_accordian('.accordion');
    //    Adding Icmage

     jQuery('#caption').on('click','.addimage',function( event ){
     
        event.preventDefault();
     
         var parent = jQuery(this).closest('.ui-accordion-content').find('.image');
        // Create the media frame.
        la_caption_hover = wp.media.frames.la_caption_hover = wp.media({
          title: 'Select Images for Smiling Field',
          button: {
            text: 'Add Image',
          },
          multiple: false  // Set to true to allow multiple files to be selected
        });
     
        // When an image is selected, run a callback. 
        la_caption_hover.on( 'select', function() {
            // We set multiple to false so only get one image from the uploader
            var selection = la_caption_hover.state().get('selection');
            selection.map( function( attachment ) {
                attachment = attachment.toJSON();
                console.log(parent);
                parent.append('<span><img src="'+attachment.url+'"><span class="dashicons dashicons-dismiss"></span></span>');

            });  
        });
     
        // Finally, open the modal 
        la_caption_hover.open();
    });
    
    // Removing Uploades Image


    jQuery('#caption').on('click', '.dashicons-dismiss', function() {
            jQuery(this).parent('span').remove();
    }); 

    // Cloning Add More Images 

    jQuery('#caption').on('click', '.moreimg', function() { 
            jQuery(this).closest('.content').find('h3:last').css({
                'background': '',
                'color': ''
            });
            var parent = jQuery(this).closest('.content');
            var heading = jQuery(this).closest('.content').find('h3:first').clone(true);
            var heading_text = heading.find('a').text('New Field');
            var content = jQuery(this).closest('.content').find('h3:first').next().clone(true).removeClass('firstelement');
            jQuery(parent).append(heading).append(content);
            // jQuery(parent).append(heading);
            jQuery('.accordian').accordion('refresh');

    });

        jQuery('#caption').on('click', '.addcat', function() { 
            sCounter++;
            console.log(sCounter);
            jQuery('#faqs-container').find('h3:first').css({
                'background': '',
                'color': ''
            });
            var parent = jQuery('#faqs-container');
            var head = jQuery('#faqs-container').find('h3:first').clone(true).appendTo(parent);
            var heading_text = head.find('a').text('New Form');
            var content = jQuery('#faqs-container').find('h3:first').next().clone(true).removeClass('firstelement').appendTo(parent);

            jQuery("div.accordian").accordion({
            heightStyle: "content",
            collapsible: true, 
            changestart: function (event, ui) {
                if ($(event.currentTarget).hasClass("item")) {
                    event.preventDefault();
                    $(event.currentTarget).removeClass("ui-corner-top").addClass("ui-corner-all");
                    }
                }
            });
            content.find('button.fullshortcode').attr('id', sCounter);
            jQuery('.accordian').accordion('refresh');

    });

    // Removing Category
        jQuery('#caption #faqs-container').on('click', '.removecat', function(event) {
        var cat_name = jQuery(this).prev('a').text();
        var result = confirm("Want to delete "+cat_name+" ?");
        if (result) {
            if (jQuery(this).closest('.ui-accordion-header').next('#faqs-container > .ui-accordion-content').hasClass('firstelement')) {
                  alert('You can not delete it as it is first element!');
              } else {
                  
                  var head = jQuery(this).closest('.ui-accordion-header');
                  var body = jQuery(this).closest('.ui-accordion-header').next('#faqs-container > .ui-accordion-content');
                  head.remove();
                  body.remove();
              }  
        }
          
        });

    // Removing Add More Images

    jQuery('#caption').on('click','.removeitem',function() {
        var img_name = jQuery(this).prev('a').text();
        var result = confirm("Want to delete "+img_name+" ?");
        if (result) {
            if (jQuery(this).closest('.ui-accordion-header').next('.ui-accordion-content').hasClass('firstelement')) {
                alert('You can not delete it as it is first element!');
            } else {
                var head = jQuery(this).closest('.ui-accordion-header');
                var body = jQuery(this).closest('.ui-accordion-header').next('.ui-accordion-content');
                head.remove();
                body.remove();
            }
        }
    });

    jQuery('#caption').on('click', '.save-meta', function(event) {
        console.log('save-meta');
        event.preventDefault();     
        jQuery('.se-saved-con').show();
         jQuery('#la-saved').hide();
        var allcats = []; 
          jQuery('.accordian>.content').each(function(index,val) {
            var cats = {};
            cats.shortcode=jQuery(this).find('.fullshortcode').attr('id');
            // cats.cap_style = jQuery(this).find('.styleopt').val();
              cats.cap_effect = jQuery(this).find('.effectopt').val();
              cats.cap_direction = jQuery(this).find('.directionopt').val();
              cats.speed = jQuery(this).find('.speed').val();
            cats.allcapImages = [];
            jQuery(this).find('.ui-accordion-content').each(function(index, val) {
                var images = {};
                images.img_name = jQuery(this).find('.imgname').val();
                images.cap_img = jQuery(this).find('img').attr('src');
                cats.allcapImages.push(images);
            });
            allcats.push(cats);
            console.log(cats);
        });
        var data = {
            action : 'la_save_caption_options',
             posts : allcats
        } 

         jQuery.post(laAjax.url, data, function(resp) {
            // window.location.reload(true);
            jQuery('.se-saved-con').hide();
            jQuery('.overlay-message').show();
            jQuery('.overlay-message').delay(2000).fadeOut();
        });
          
    });

    jQuery('#faqs-container').on('click','button.fullshortcode',function(event) {
        event.preventDefault();
        prompt("Copy and use this Shortcode", '[smiling-form id="'+jQuery(this).attr('id')+'"]');
    });
});
