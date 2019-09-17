/**
* Part of jQuery Migrate plugin.
* It allows to add the support of the browser property into jquery 1.9.0+

* Copyright 2013, OnePress, http://onepress-media.com/portfolio
* Help Desk: http://support.onepress-media.com/
*/

// Limit scope pollution from any deprecated API
(function() {
    if ( jQuery.browser ) return;
    
    var matched, browser;

    // Use of jQuery.browser is frowned upon.
    // More details: http://api.jquery.com/jQuery.browser
    // jQuery.uaMatch maintained for back-compat
    jQuery.uaMatch = function( ua ) {
        ua = ua.toLowerCase();

        var match = /(chrome)[ \/]([\w.]+)/.exec( ua ) ||
            /(webkit)[ \/]([\w.]+)/.exec( ua ) ||
            /(opera)(?:.*version|)[ \/]([\w.]+)/.exec( ua ) ||
            /(msie) ([\w.]+)/.exec( ua ) ||
            ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec( ua ) ||
            [];

        return {
            browser: match[ 1 ] || "",
            version: match[ 2 ] || "0"
        };
    };

    matched = jQuery.uaMatch( navigator.userAgent );
    browser = {};

    if ( matched.browser ) {
        browser[ matched.browser ] = true;
        browser.version = matched.version;
    }

// Chrome is Webkit, but Webkit is also Safari.
    if ( browser.chrome ) {
        browser.webkit = true;
    } else if ( browser.webkit ) {
        browser.safari = true;
    }

    jQuery.browser = browser;

    jQuery.sub = function() {
        function jQuerySub( selector, context ) {
            return new jQuerySub.fn.init( selector, context );
        }
        jQuery.extend( true, jQuerySub, this );
        jQuerySub.superclass = this;
        jQuerySub.fn = jQuerySub.prototype = this();
        jQuerySub.fn.constructor = jQuerySub;
        jQuerySub.sub = this.sub;
        jQuerySub.fn.init = function init( selector, context ) {
            if ( context && context instanceof jQuery && !(context instanceof jQuerySub) ) {
                context = jQuerySub( context );
            }

            return jQuery.fn.init.call( this, selector, context, rootjQuerySub );
        };
        jQuerySub.fn.init.prototype = jQuerySub.fn;
        var rootjQuerySub = jQuerySub(document);
        return jQuerySub;
    };

})();


/**
* Image Elevator (pasting images)
* 
* Copyright 2014, OnePress, http://onepress-media.com/portfolio
* Help Desk: http://support.onepress-media.com/
*/

jQuery(document).ready(function($){

    if ( !window.imgevr ) window.imgevr = {};
    window.imgevr.context = {
        
        init: function() {
            this.contentWrap = $("#wp-content-wrap");
            this.disabled = false;
            
            this.initClipboardEvents();
        },

        /**
         * Inits the clipboard evetns for editors on the page.
         */
        initClipboardEvents: function(){
            if ( $(".wp-editor-area").length === 0 ) return;

            var self = this;
 
            // - creates a capture to insert images from clipboards
            
            // fake editable content
            this.createPasteCapture();

            // focuses on the fake editable content when ctrl+v is presed
            this.preventPaste = function() {
                if (!document.activeElement) return;

                // we can insert images only into wp editor
                if ( !$(document.activeElement).is(".wp-editor-area") ) return;

                self.saveCurrentSelection();
                $("#paster").focus();
            }

            var ctrlDown = false, metaDown = false;         
            var ctrlKey = 17, metaKey = 224, vKey = 86;

            $(document).keydown(function(e) {
                if (e.keyCode == ctrlKey) ctrlDown = true;
                if (e.keyCode == metaKey) metaDown = true; 
            }).keyup(function(e) {
                if (e.keyCode == ctrlKey) ctrlDown = false;
                if (e.keyCode == metaKey) metaDown = false;      
            });

            $(document).keydown(function(e){
                if ( !self.isClipboardActive() ) return;
                if ( (ctrlDown || metaDown ) && e.keyCode == vKey) {
                    if ( $.browser.msie ) {
                        self.setState("error", "Sorry, IE doesn't support inserting images from clipboard.");
                        return;
                    }
                    if ( $.browser.opera ) {
                        self.setState("error", "Sorry, Opera doesn't support inserting images from clipboard.");
                        return;
                    }
                    self.preventPaste();
                };
            });
            
            // catchs the "paste" event to upload image on a server
            
            document.onpaste = function (e) {

                if (!document.activeElement) return;
                if ( !self.isClipboardActive() ) return;
                
                // we can insert images only into wp editor or editable content
                if ( 
                    !$(document.activeElement).is(".wp-editor-area") && 
                    !$(document.activeElement).attr('contenteditable') ) return;
                
                    var options = {
                        before: function() {
                            self.setLoadingStateForTextarea();    
                        },
                        success: function(html) {
                            self.insertHtmlForTextarea(html);
                        },
                        error: function() {
                            self.clearLoadingStateForTextarea();    
                        }
                    };

                    if ( e.clipboardData && e.clipboardData.items )  {
                        self.uploadFromClipboard(e, options);
                    } else {
                        self.uploadFromCapture(options);   
                    }
            };
        },
        
        /**
         * This method is called from 'paste_preprocess' of the Paste plugin of TinyMCE
         */
        processPastedContent: function( content ) {
            if ( !content ) return content;

            var editor = tinyMCE.activeEditor;
            var self = this;
            
            var $container = $("<div></div>").append( content );
            var images = $container.find('img');
             
            if ( images.length > 0 ) {
                var count = images.length;

                images.each(function(){
                    var image = $(this);
                    
                    var src = image.attr('src');
                    if ( src.indexOf('data:image') !== 0 ) return true;
  
                    var preloader = $(self.getPreloaderHtml());
                    var preloaderId = preloader.attr('id');
                    
                    image.before( preloader );
                    image.remove();

                    self.uploadImage(
                        {
                            image: image.attr('src'),
                            type: null
                        }, 
                        {
                            success: function(html){
                                var preloader = $(editor.getDoc()).find("#" + preloaderId);
                                preloader.after(html);
                                preloader.remove();
                                count--;
                            }, 
                            error: function() {
                                var preloader = $(editor.getDoc()).find("#" + preloaderId);
                                preloader.remove();
                                count--;
                            }
                        }
                    ); 
                });
            }
            
            return $container.html();
        },
        
        // --------------------------------------------------------------------------
        // Working with states
        // --------------------------------------------------------------------------
        
        /**
         * Is the plugin is active now?
         */
        isActive: function() {
            if ( this.disabled ) return false;
            
            if ( window.localStorage ) {
                value = localStorage.getItem('insertimage-state');
                if ( value ) return value == "yes" ? true : false;
            }
            
            return true;
        },

        isClipboardActive: function() {
            if ( !this.isActive() ) return false;
            if ( !window.imgevr_clipboard_active ) return false;
            return true;
        },
        
        isDropAndDragActive: function() {
            if ( !this.isActive() ) return false;
            if ( !window.imgevr_dragdrop_active ) return false;
            
            if ( this.contentWrap.hasClass("tmce-active") || this.contentWrap.hasClass("html-active") ) return true;
            return false;
        },
        
        /**
         * Sets state a set the working Clipboard Images
         */
        setState: function( state, message ) {
            var buttons = $(".imgevr-controller");

            if ( "error" === state ) {
                
                buttons.addClass("imgevr-disabled");   
                if ( window.localStorage ) localStorage.setItem('insertimage-state', "no");

                $(".insertimage-status")
                    .addClass('insertimage-status-disabled')
                    .removeClass('insertimage-status-enabled');
            
                $("#imgevr-btn-manage").removeClass("imgevr-active");
                
                this.showErrorState(message ? message : "Image Elevator <strong>deactivated</strong>.");
                
            } else if ( state ) {
                
                buttons.removeClass("imgevr-disabled");
                if ( window.localStorage ) localStorage.setItem('insertimage-state', "yes");

                $(".insertimage-status")
                    .addClass('insertimage-status-enabled')
                    .removeClass('insertimage-status-disabled');    

                $("#imgevr-btn-manage").addClass("imgevr-active");
                
            } else {
                
                buttons.addClass("imgevr-disabled");   
                if ( window.localStorage ) localStorage.setItem('insertimage-state', "no");

                $(".insertimage-status")
                    .addClass('insertimage-status-disabled')
                    .removeClass('insertimage-status-enabled');
                    
                $("#imgevr-btn-manage").removeClass("imgevr-active");
            }
        },
        
        getTooltipOptions: function( extraClasses, leftPosition ) {

            var options = {
                content: "...",
                position: {
                    my: !leftPosition ? 'bottom center' : 'top left',
                    at: !leftPosition ? 'top center' : 'bottom center',
                    target: $(".imgevr-controller")
                },
                show: {
                    event: null,
                    solo: true
                },
                hide: {
                    event: 'unfocus'
                },
                style: {
                    classes: 'qtip2-light qtip2-shadow qtip2-rounded ' + extraClasses
                }
            };
            return options;
        },
        
        showActiveState: function( message ) {
            var tooltip = $("#qtip2-active-state");
            
            if ( tooltip.length == 0 ) {
                tooltip = $("<div id='qtip2-active-state'></div>").appendTo("body");
                var options = this.getTooltipOptions( "clipboad-images-active-state" );
                tooltip.qtip2(options);
            }
            
            var api = tooltip.qtip2("api");
            api.set("content.text", message);
          
            api.show();
        },
        
        showDeactiveState: function( message ) {
            var tooltip = $("#qtip2-deactive-state");
            
            if ( tooltip.length == 0 ) {
                tooltip = $("<div id='qtip2-deactive-state'></div>").appendTo("body");
                var options = this.getTooltipOptions( "clipboad-images-deactive-state" );
                tooltip.qtip2(options);
            }
            
            var api = tooltip.qtip2("api");
            api.set("content.text", message);
            
            api.show();
        },
        
        showErrorState: function( message ) {
            var tooltip = $("#qtip2-error-state");

            if ( tooltip.length === 0 ) {
                tooltip = $("<div id='qtip2-error-state'></div>").appendTo("body");
                var options = this.getTooltipOptions("qtip2-red clipboad-images-error-state", true);
                tooltip.qtip2(options);
            }
            
            var api = tooltip.qtip2("api");
            api.set("content.text", message);
            
            api.show();
        },
        
        showError: function(response, data) {
            
            if ( data && data.error ) {
                this.showErrorState(data.error);
            } else {
                if ( response.responseText) {
                    this.showErrorState(response.responseText);
                } else {
                    this.showErrorState("Sorry, unknown error. Please contact the support.");
                }
            }
        },

        // --------------------------------------------------------------------------
        // Methods for uploading
        // --------------------------------------------------------------------------
        
        /**
         * Uploads image by using Clipbord API.
         */
        uploadFromClipboard: function(e, options) {
            var self = this;

            // if the image is inserted into the textarea, then return focus
            self.returnFocusForTextArea();
            
            // read data from the clipborad and upload the first file

            if ( e.clipboardData.items ) {
                var items = e.clipboardData.items;
                for (var i = 0; i < items.length; ++i) {
                    if (items[i].kind === 'file' && items[i].type.indexOf('image/') !== -1) {

                        if ( options.before ) options.before();

                        // only paste 1 image at a time
                        e.preventDefault();

                        // uploads image on a server
                        this.uploadImage({
                            image: items[i].getAsFile(),
                            type: items[i].type,
                            ref: 'clipboard'
                        }, options);

                        return;
                    }
                } 
            }
            
            if ( e.clipboardData.files ) {
                var items = e.clipboardData.files;
                for (var i = 0; i < items.length; ++i) {
                    if (items[i].type.indexOf('image/') !== -1) {

                        if ( options.before ) options.before();

                        // only paste 1 image at a time
                        e.preventDefault();

                        // uploads image on a server
                        this.uploadImage({
                            image: items[i],
                            type: items[i].type,
                            ref: 'clipboard'
                        }, options);

                        return;
                    }
                }
            }
        },
        
        /**
         * Uploads image by using the capture.
         */
        uploadFromCapture: function(options) {
            var self = this;
            
            if ( options.before ) options.before();

            var timeout = 5000, step = 100;
            $("#paster").html("");

            var timer = setInterval(function(){

                var html = $("#paster").html();

                // in nothing found, carry on to wait
                if ( html.length > 0 ) {
                    clearInterval(timer);

                    if ( html.indexOf("<img") == 0 ) {

                        self.uploadImage({
                            image: $("#paster img").attr('src'),
                            type: null,
                            ref: 'dragdrop'
                        }, options);  

                    } else {
                        
                        // issue #IMEL-2
                        html = html.replace(/<\/\s*p>/g, "</p>{br}{br}");
                        html = html.replace(/<\/?\s*br>/g, "</br>{br}");
                        var insertedText = $("<div>").html(html).text();
                        insertedText = insertedText.replace(/\{br\}/g, "\n");
                        
                        self.insertHtmlForTextarea( insertedText );
                    }

                } else {

                    timeout = timeout - step;
                    if ( timeout < 0 ) {
                        clearInterval(timer);
                        // call error?
                    }
                    return;
                }

            }, 100);

            setTimeout(function(){
               self.returnFocusForTextArea(); 
            }, 500);
        },
        
        /**
         * Uploads image data on the server.
         */
        uploadImage: function(data, options) {
            var self = this;
            this.lockTabs();

            // adds the default error handler
            var oldCallback = options.error;
            options.error = function(response, data){
                if ( oldCallback ) oldCallback(response, data);
                self.showError(response, data);
            };
            
            var oData = new FormData();
            oData.append('file', data.image);
            oData.append('action', 'imageinsert_upload');

            oData.append('imgMime', data.type); 
            if ( data.name ) oData.append('imgName', data.name); 
            if ( data.ref ) oData.append('imgRef', data.ref);      
            oData.append('imgParent', this.getCurrentPostId());

            var req = new XMLHttpRequest();
            req.open("POST", ajaxurl);

            req.onreadystatechange = function() {
                if (req.readyState == 4) {
                    if(req.status == 200) {

                        try {
                            var response = JSON.parse( req.responseText );
                        }
                        catch(e) {
                            self.unlockTabs();
                            options.error(req, null);
                            return;
                        }

                        if ( response && response.error ) {
                            self.unlockTabs();
                            options.error(req, response);
                            return;
                        }

                        self.unlockTabs();
                        options.success(response.html, response);
                        return;
                    }

                    self.unlockTabs();
                    options.error(req, null);
                }
            }

            req.send(oData);  
        },
        
        lockTabs: function() {
            var self = this;
            
            var inited = self.tabsClearened;
            
            $("#wp-content-editor-tools a.wp-switch-editor").each(function(index, item){
                 $(item).addClass('disabled');
                if ( !inited ) {
                    $(item).prop("onclick", null);
                    self.tabsClearened = true;
                } else {
                    $(item).unbind("click.elevator");
                }
            }); 
        },
        
        unlockTabs: function() {
            var self = this;
            
            $("#wp-content-editor-tools a.wp-switch-editor").each(function(index, item){
                $(item).removeClass("disabled");
                $(item).bind("click.elevator", function(e){
                    switchEditors.switchto(this);
                });
            }); 
        },
        
        /**
         * Creates athe capture.
         */
        createPasteCapture: function() {
            
            this.paster = $("<div id='paster'></div>").attr({
                "contenteditable": "true",
                "_moz_resizing": "false"
            }).css({
                "position": "absolute",
                "height": "1",
                "width": "1",
                "opacity": "0",
                "outline": "0",
                "overflow": "auto",
                "z-index": "-9999"})
            .prependTo("body");  
        },
        
        // --------------------------------------------------------------------------
        // Methods for working with textarea
        // --------------------------------------------------------------------------

        /**
         * Sets a loading label into a current textarea of the editor.
         */
        setLoadingStateForTextarea: function() {
            if ( !this.selection ) return;
            var selection = this.selection;
            
            var state = "[{ loading ... }]";
            
            this.original = selection.end > 0
                ? selection.editor.value.slice(selection.start, this.selection.end)
                : "a";

            selection.editor.value = selection.editor.value.slice(0, selection.start) + state 
                + selection.editor.value.slice(selection.end);
            
            this.selection = {
                start: selection.start,
                end: selection.start + state.length,
                editor: selection.editor
            }
        },
        
        /**
         * Remores the loading label from the current editor textarea.
         */
        clearLoadingStateForTextarea: function() {
            if ( !this.selection ) return;
            this.selection.editor.value = 
                this.selection.editor.value.slice(0, this.selection.start) 
                + this.original 
                + this.selection.editor.value.slice(this.selection.end);
            
            this.selection = null;
        },
        
        /**
         * Returns a focus on the current editor textarea.
         */
        returnFocusForTextArea: function() {
            if ( !this.selection ) return;
            
            $(this.selection.editor).focus();
            this.selection.editor.selectionStart =  this.selection.start;
            this.selection.editor.selectionEnd = this.selection.end;     
        },
        
        insertHtmlForTextarea: function(html) {
            if ( !this.selection ) return;
            this.selection.editor.value = 
                this.selection.editor.value.slice(0, this.selection.start) 
                + html 
                + this.selection.editor.value.slice(this.selection.end);
            
            $(this.selection.editor).focus();
            this.selection.editor.selectionStart =  this.selection.end + html.length;
            this.selection.editor.selectionEnd = this.selection.editor.selectionStart;
            
            this.selection = null;
        },
        
        /**
         * Credits:
         * http://stackoverflow.com/questions/3964710/replacing-selected-text-in-the-textarea
         */
        getInputSelection: function(editor) {
            var start = 0, end = 0;

            if (typeof editor.selectionStart == "number" && typeof editor.selectionEnd == "number") {
                start = editor.selectionStart;
                end = editor.selectionEnd;
            }

            return {
                start: start,
                end: end,
                editor: editor
            };
        },
        
        saveCurrentSelection: function() {
            var self = this;
            
            if (!document.activeElement || !$(document.activeElement).is(".wp-editor-area")) {
                var editor = self.contentWrap.find(".wp-editor-area");
                this.selection = {
                    start: editor.val().length,
                    end: editor.val().length,
                    editor: editor[0]
                };
            } else {
                this.selection = this.getInputSelection(document.activeElement);
            }
        },
        
        // --------------------------------------------------------------------------
        // Methods for working with TinyMCE editor
        // --------------------------------------------------------------------------
        
        /**
         * Inserts the image html into the editor replacing the preloader.
         */
        insertImageHtml: function( editor, html ) {
            editor = ( !editor) ? tinyMCE.activeEditor : editor;
            var preloader = $(editor.getDoc()).find("img[data-type=preloader]");
            if ( preloader.length == 0 ) return;

            preloader.after(html);
            preloader.remove();
        },
        
        removePlaceholder: function( editor ) {
            editor = ( !editor) ? tinyMCE.activeEditor : editor;
            var preloader = $(editor.getDoc()).find("img[data-type=preloader]");
            preloader.parent().remove();
        },
        
        /**
         * Creteats html for the preloader.
         */
        getPreloaderHtml: function() {
            var id = this.generateId( 7 );
            var preloader = window.imgevr.assetsUrl + "/img/circle-preloader.gif";
            return "<p id='" + id + "'><img data-type='preloader' src='" + preloader + "' alt='' /></p>";
        },

        /**
         * Generates a unique id.
         */
        generateId: function( length ) {
            var text = "";
            var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

            for( var i=0; i < length; i++ )
                text += possible.charAt(Math.floor(Math.random() * possible.length));

            return text;
        },
       
        // --------------------------------------------------------------------------
        // Helpers
        // --------------------------------------------------------------------------
        
        /**
         * Returns current post id pr nothing.
         */
        getCurrentPostId: function() {
            return jQuery("#post_ID").length > 0 ? jQuery("#post_ID").val() : null;  
        }
    };
    
    $(function(){
        window.imgevr.context.init();
    });
});

/**
* Image Elevator (quick settings)
* 
* Copyright 2014, OnePress, http://onepress-media.com/portfolio
* Help Desk: http://support.onepress-media.com/
*/

(function($){
    
    if ( !window.imgevr ) window.imgevr = {};
    window.imgevr.quickSettings = {
        
        init: function() {
            var self = this;
            this.$settings = $("#imgevr-quick-settings");
            this.$corner = $("#imgevr-quick-settings-corner");            
            
            $("body").on('click', '.imgevr-controller', function(){
                
                if ( self._active ) {
                    self.hide();
                } else {
                    self.show( $(this) );
                }

                return false;
            });
                        
            if ( !imgevr.context.isActive() ) {
                $('.imgevr-controller').addClass("imgevr-disabled");
                $("#imgevr-btn-manage").removeClass("imgevr-active");
            }

            $(window).resize(function(){
                self.updatePosition();
            });
            
            $(window).scroll(function(){
                self.updatePosition();
            });
            
            $("#imgevr-ctrl-resizing").change(function(){
                if ( $(this).is(":checked") ) {
                    self.$settings.find(".imgevr-resizing-options").fadeIn(300);
                    self.$settings.find("label[for=imgevr-ctrl-resizing]").addClass('imgevr-active');
                } else {
                    self.$settings.find(".imgevr-resizing-options").fadeOut(100);
                    self.$settings.find("label[for=imgevr-ctrl-resizing]").removeClass('imgevr-active');
                }
                
                setTimeout(function(){
                    self.updatePosition();
                }, 350);
            });
            
            $("#imgevr-ctrl-compression").change(function(){
                if ( $(this).is(":checked") ) {
                    self.$settings.find(".imgevr-compression-options").fadeIn(300);
                    self.$settings.find("label[for=imgevr-ctrl-compression]").addClass('imgevr-active');
                } else {
                    self.$settings.find(".imgevr-compression-options").fadeOut(100);
                    self.$settings.find("label[for=imgevr-ctrl-compression]").removeClass('imgevr-active');
                }
                
                setTimeout(function(){
                    self.updatePosition();
                }, 350);
            });
            
            $("#imgevr-btn-update-rules").click(function(){
                self.save();
                return false;
            });
            
            $("#imgevr-btn-cancel").click(function(){
                self.hide();
                return false;
            });
            
            $("#imgevr-btn-manage").click(function(){
                if ( imgevr.context.isActive() ) imgevr.context.setState(false);
                else imgevr.context.setState(true);
                return false;
            });
        },
        
        show: function( $btn ) {
            var self = this;
            this._active = true;
            
            if ( !$("#imgevr-ctrl-resizing").is(":checked") ) {
                this.$settings.find(".imgevr-resizing-options").hide();
            }
            
            if ( !$("#imgevr-ctrl-compression").is(":checked") ) {
                this.$settings.find(".imgevr-compression-options").hide();
            }
            
            this.$settings.data('imgevr-btn', $btn);
            this.updatePosition();
            
            this.$settings.hide();
            this.$settings.addClass('imgevr-visible');
            this.$settings.fadeIn(200, function(){
               self.$settings.addClass('imgevr-transition'); 
            });
            
            this.$corner.fadeIn(200);
        },
        
        hide: function(callback) {
            var self = this;

            this.$settings.fadeOut(200, function(){
                self.$settings.removeClass('imgevr-visible');
                self.$settings.removeClass('imgevr-transition');
                self._active = false;
                callback && callback();
            });
            
            this.$corner.fadeOut(200);
        },
        
        updatePosition: function() {
    
            var $btn = this.$settings.data('imgevr-btn');
            if ( !$btn ) return;

            var btnOffset = $btn.offset();
            var btnWidth = $btn.innerWidth();
            var btnHeight = $btn.innerHeight();
            
            var htmlTopPadding = parseInt( $('html').css('paddingTop') );
            btnOffset.top -= htmlTopPadding;

            var settingsWidth = this.$settings.innerWidth();
            var settingsHeight = this.$settings.innerHeight();
            
            var top = Math.round( btnOffset.top  + ( btnHeight - settingsHeight ) / 2 );
            var left = btnOffset.left + btnWidth;

            var scrollTop = $(window).scrollTop();
            var minTopMargin = 20;
            
            if ( top < scrollTop + minTopMargin ) {
                top = scrollTop + minTopMargin;
            }
            
            if ( top > btnOffset.top ) {
                top = btnOffset.top; 
            }

            this.$settings.css({
                'top': top + 'px',
                'left': left  + 'px'
            });
            
            this.$corner.css({
                'top': btnOffset.top + Math.round( ( btnHeight - 20 ) / 2 ) + 'px',
                'left': btnOffset.left + btnWidth - 7 + 'px'
            });
        },
        
        save: function() {
            var self = this;
            
            var btnTitle = $("#imgevr-btn-update-rules").text();
            
            var data = {
                'action': 'imgevr_save_quick_settings',
                'imgevr_links_enable': $("#imgevr-ctrl-links").is(":checked") ? 1 : 0,
                'imgevr_css_class': $("#imgevr-ctrl-css-class").val(),
                'imgevr_resizing_enable': $("#imgevr-ctrl-resizing").is(":checked") ? 1 : 0,
                'imgevr_resizing_max_width': $("#imgevr-ctrl-max-width").val(),
                'imgevr_resizing_max_height': $("#imgevr-ctrl-max-height").val(),
                'imgevr_resizing_crop_mode': $("#imgevr-ctrl-crop-mode").is(":checked") ? 1 : 0,
                'imgevr_resizing_save_original': $("#imgevr-ctrl-save-original").is(":checked") ? 1 : 0,         
                'imgevr_compression_enable': $("#imgevr-ctrl-compression").is(":checked") ? 1 : 0,
                'imgevr_compression_size': $("#imgevr-ctrl-compression-size").val(),
                'imgevr_compression_jpeg_quality': $("#imgevr-ctrl-jpeg-quality").val()   
            };
            
            console.log(data);
 
            $("#imgevr-btn-update-rules")
                .text('Saving ...')
                .addClass('disabled');
            
            var req = $.ajax({
                url: window.imgevr.ajaxurl,
                data: data,
                type: 'post',
                dataType: 'json',
                success: function(data) {

                    $("#imgevr-btn-update-rules")
                        .text('Done!')
                        .removeClass('disabled');
                
                    if ( data.success ) {
                        $("#imgevr-ctrl-css-class").val(data.imgevr_css_class);
                        $("#imgevr-ctrl-max-width").val(data.imgevr_resizing_max_width);
                        $("#imgevr-ctrl-max-height").val(data.imgevr_resizing_max_height);
                        $("#imgevr-ctrl-compression-size").val(data.imgevr_compression_size);
                        $("#imgevr-ctrl-jpeg-quality").val(data.imgevr_compression_jpeg_quality);
                    } else {   
                        console && console.log && console.log(req.responseText);
                        imgevr.context.showErrorState('[#err-1] Unable to save new rules for the Image Elevator.<br />Please contact OnePress support to fix this issue.');
                    }
            
                    setTimeout(function(){
                        self.hide(function(){ 
                            $("#imgevr-btn-update-rules").text(btnTitle);
                        });
                    }, 500);
                },
                error: function() {
                    console && console.log && console.log(req.responseText);
                    imgevr.context.showErrorState('[#err-2] Unable to save new rules for the Image Elevator.<br />Please contact OnePress support to fix this issue.');

                    $("#imgevr-btn-update-rules")
                        .removeClass('disabled')
                        .text(btnTitle);
                
                    self.hide();
                },
                complete: function() {
          
                }
            });
        }
    };  
    
    $(function(){
        window.imgevr.quickSettings.init();
    });
    
})(jQuery);