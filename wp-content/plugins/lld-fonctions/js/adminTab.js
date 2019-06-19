jQuery(document).ready(function(){  
    var active_tab = window.location.hash.replace('#top#','');
    if ( active_tab == '' )
        active_tab = jQuery('.wptab').attr('id');
    jQuery('#'+active_tab).addClass('active');
    jQuery('#'+active_tab+'-tab').addClass('nav-tab-active');
    
    jQuery('#wp-tabs a').click(function() {
        jQuery('#wp-tabs a').removeClass('nav-tab-active');
        jQuery('.wptab').removeClass('active');
    
        var id = jQuery(this).attr('id').replace('-tab','');
        jQuery('#'+id).addClass('active');
        jQuery(this).addClass('nav-tab-active');
    });
});

// jQuery(document).ready(function(){  
//     var active_tab = window.location.hash.replace('#top#','');
//     if ( active_tab == '' )
//         active_tab = jQuery('.wptab').attr('id');
//     jQuery('#'+active_tab).addClass('active');
//     jQuery('#'+active_tab+'-tab').addClass('nav-tab-active');

//     var simpleUrl = jQuery('#formOption').attr('action').split('#top')[0];
//     jQuery('#formOption').attr('action', simpleUrl + '#top#' + active_tab);

//     jQuery('#wp-tabs a').click(function() {
//         jQuery('#wp-tabs a').removeClass('nav-tab-active');
//         jQuery('.wptab').removeClass('active');
        
//         var id = jQuery(this).attr('id').replace('-tab','');
//         jQuery('#'+id).addClass('active');
//         jQuery(this).addClass('nav-tab-active');

//         var simpleUrl = jQuery('#formOption').attr('action').split('#top')[0];
//         jQuery('#formOption').attr('action', simpleUrl + '#top#' + id);
//     });
// });