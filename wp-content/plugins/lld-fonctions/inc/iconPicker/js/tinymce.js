(function() {
    tinymce.create('tinymce.plugins.IconPicker', {
        init : function(ed, url) {
            ed.addButton('iconpicker', {
                title : 'Ajouter une icone',
                cmd : 'iconpicker',
                classes: 'iconPicker', 
                image : url + '/iconpicker.png'
            });
 
 
            ed.addCommand('iconpicker', function() {
                var selected_text = ed.selection.getContent();
                var return_text = '';
                return_text = '<span class="iconpicker">' + selected_text + '</span>';
                ed.execCommand('mceInsertContent', 0, return_text);
            });

        },
        // ... Hidden code
    });
    // Register plugin
    tinymce.PluginManager.add( 'iconpicker', tinymce.plugins.IconPicker );
})();