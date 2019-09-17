(function() {  
    tinymce.create('tinymce.plugins.imgelevator', {  
        plugin_url: null,
        editor: null,
        
        _dragAndDropOverlayCreated: false,
        _dargAndDropDelay: 2000,
        _dargAndDropTimeoutStep: 1000,       
        
        init : function(editor, url) {  
            var self = this;

            // tiny mce plugin data
            this.plugin_url = url;
            this.editor = editor;

            // when the editor is inited we can get access to iframe document
            // in order to make binding to the 'paste' event
            editor.onInit.add(function(editor){
                self.initCopyPaste(editor);
            });
        },
        
        /**
         * Inits Copy & Paste for the editor.
         */
        initCopyPaste: function(editor) {

            editor.getDoc().onpaste = function (e) {

                if ( !imgevr.context.isClipboardActive() ) return;
                
                // if a browser supports clipboard data
                if ( e.clipboardData && ( e.clipboardData.items || e.clipboardData.files.length > 0 ) ) {

                    imgevr.context.uploadFromClipboard(e, {
                        before: function(){
                            editor.selection.setContent(imgevr.context.getPreloaderHtml());
                        },
                        success: function(html){
                            imgevr.context.insertImageHtml(editor, html);
                        },
                        error: function() {
                            imgevr.context.removePlaceholder(editor);
                        }
                    });

                // if a browser doesn't support clipboard data
                } else {

                    // a function that finds pasted images in the editor
                    // if found something, then upload the first image
                    var checkImages = function() {

                        var images = jQuery(editor.getDoc()).find('img[src^="data:image"]');
                        if ( images.length > 0 ) {
                            var count = images.length;
                            clearInterval(timer);

                            images.each(function(){
                                var image = jQuery(this);

                                var preloader = jQuery(imgevr.context.getPreloaderHtml());                           
                                image.before(jQuery("<p></p>").append(preloader));
                                image.remove();

                                imgevr.context.uploadImage(
                                    {
                                        image: image.attr('src'),
                                        type: null
                                    }, 
                                    {
                                        success: function(html){
                                            preloader.after(html);
                                            preloader.remove();
                                            count--;
                                        }, 
                                        error: function() {
                                            imgevr.context.removePlaceholder(editor);
                                            count--;
                                        }
                                    }
                                ); 
                            });

                            return true;
                        }

                        return false;
                    }

                    checkImages();

                    // waits 3 seconds untile the image is inserted
                    var timeout = 3000, step = 50;
                    var timer = setInterval(function(){
                        if ( !checkImages() ) {
                            timeout = timeout - step;
                            if ( timeout < 0 ) clearInterval(timer);
                        }
                    }, 50);
                }
            }   
        }
    });  
    
    tinymce.PluginManager.add('imgelevator', tinymce.plugins.imgelevator);  
})();  