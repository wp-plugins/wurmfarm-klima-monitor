function wormstation() {
    return "[ws_chart title=\"Wurmfarm Station Temperatur\" chart=\"temp\" day=\"Today\" v_title=\"Temperatur\" width=\"800px\" height=\"400px\" ] [ws_chart title=\"Wurmfarm Station Luftdruck\" chart=\"press\" day=\"Today\" v_title=\"Luftdruck\" width=\"800px\" height=\"400px\" ] ";
}

(function() {

    tinymce.create('tinymce.plugins.wormstation', {

        init : function(ed, url){
            ed.addButton('wormstation', {
                title : 'Füge den Wurmfarm Klima Monitor Shortcode hinzu',
                onclick : function() {
                    ed.execCommand(
                        'mceInsertContent',
                        false,
                        wormstation()
					);
                },
                image: url + "../../img/worm.png"
            });
        },

        getInfo : function() {
            return {
                longname : 'Worm Farm Climate Station plugin button',
                author : 'Stefan Mayer',
                authorurl : 'http://www.2komma5.org',
                infourl : '',
                version : "1.1.0"
            };
        }
    });

    tinymce.PluginManager.add('wormstation', tinymce.plugins.wormstation);
})();

