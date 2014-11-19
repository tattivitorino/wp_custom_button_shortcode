
(function(){
    tinymce.create(
        'tinymce.plugins.addButtons', {
            
            init:function(ed, url){
                
                ed.addButton('link_btndownload', {
                    title:'Add Custom Buttom',
                    icon: 'icon plgtv-icon-download',
                    onclick:function(){
                        wp.mce.link_btndownload.popupwindow(ed, null);
                        return;
                    }
                });
            }
        }        
    );
    tinymce.PluginManager.add('link_buttons', tinymce.plugins.addButtons);
})();