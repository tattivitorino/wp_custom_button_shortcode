(function($){
    var media = wp.media,
    shortcode_btndownload = 'link_btndownload';
    
    wp.mce = wp.mce || {};
    
    /**
     * ******** VIEW BTN DOWNLOAD ***********
     * */
    
    wp.mce.link_btndownload = {
        shortcode_data:{},
        View:{
            template: media.template('editor-link_btndownload'),
            postID: $('#post_ID').val(),
            initialize: function(options){
                //console.log(options);
                this.shortcode = options.shortcode;
                wp.mce.link_btndownload.shortcode_data = this.shortcode;
            },
            getHtml: function() {
                var options = this.shortcode.attrs.named;
                options['innercontent'] = this.shortcode.content;
                return this.template(options);
            }
        },
        edit:function(node){
            //console.log(this);
            //console.log(node);
            
            var data = window.decodeURIComponent($(node).attr('data-wpview-text'));
            //console.log(data);
            
            var $sh = $(node).find('.wpview-content a[data-sh="btn"]');
            //console.log($sh);
            
            var values = {
                'arquivo':$sh.attr('href'),
                'classes':$sh.attr('data-sh-classes'),
                'ic_classes':$sh.attr('data-sh-ic-classes'),
                'target':$sh.attr('data-sh-target'),
                'innercontent':$sh.attr('data-sh-innercontent')
            };
            //console.log(values);
            
            this.popupwindow(tinyMCE.activeEditor, values);
        },
        
        popupwindow:function(ed, values, onsubmit_callback){
            
            if(typeof onsubmit_callback != 'function'){
                onsubmit_callback = function(e){
                    var s = '['+shortcode_btndownload;
                    for(var i in e.data){
                        if(e.data.hasOwnProperty(i) && i != 'innercontent' && i != 'selectfile'){
                            s += ' ' + i + '="' + e.data[i] + '"';
                        }
                    }
                    s+=']';
                    if(typeof e.data.innercontent != 'undefined'){
                        s += e.data.innercontent;
                        s += '[/' + shortcode_btndownload + ']';
                    }
                    //console.log(s);
                    ed.insertContent(s);
                };
            }//typeof onsubmit_callback
            
            var attachment;
            if(typeof values == null) var values;
            
            ed.windowManager.open({
                title:'Add Custom Button',
                    body:[
                        {
                            type:'button',
                            /*text:'Add Media',*/
                            icon:'icon dashicons-admin-media',
                            name:'selectfile',
                            label:'Media Uploader',
                            onclick:function(e){
                                var frame = wp.media({
                                  title:'Choose file',
                                  multiple:false,
                                  button:{ text:'Insert file..' }
                                });
                                
                                frame.on('close', function(){
                                    try{
                                        attachment = frame.state().get('selection').first().toJSON();
                                        $('input#arquivo').val(attachment.url);
                                        console.log(attachment);
                                    }catch(err){
                                        console.log(err.stack);
                                    }
                                }); 
                                frame.open();
                            },
                        },
                        {
                            type:'textbox',
                            name:'arquivo',
                            id:'arquivo',
                            label:'File URL',
                            value: (values && values['arquivo']) ? values['arquivo'] : ''
                        },
                        {
                            type:'textbox',
                            name:'classes',
                            label:'Classes',
                            tooltip:'Separated by spaces',
                            value: (values) ? values['classes'] : ''
                        },
                        {
                            type:'textbox',
                            name:'ic_classes',
                            label:'Icon Classes',
                            tooltip:'Separated by spaces',
                            value: (values) ? values['ic_classes'] : ''
                        },
                        {
                            type:'listbox',
                            name:'target',
                            label:'Open in:',
                            value: (values) ? values['target'] : '',
                            'values': [
                                {text: 'a new window', value: '_blank'},
                                {text: 'the same window', value: '_self'}
                            ]
                        },
                        {
                            type:'textbox',
                            name:'innercontent',
                            label:'Link Text',
                            value: (values) ? values['innercontent'] : ''
                        }
                    ],
                    onsubmit:onsubmit_callback
            });//ed.windowManager.open()
            
        }//popupwindow()
    };
    
    wp.mce.views.register(shortcode_btndownload, wp.mce.link_btndownload);
    
}(jQuery));
