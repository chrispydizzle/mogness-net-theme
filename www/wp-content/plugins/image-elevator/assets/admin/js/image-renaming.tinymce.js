/*
#build: premium, offline
*/

(function($) {  
    tinymce.create('tinymce.plugins.imgrenaming', {  
        plugin_url: null,
        editor: null,    
        
        init : function(editor, url) {  
            var self = this;

            // tiny mce plugin data
            this.plugin_url = url;
            this.editor = editor;

            editor.onInit.add(function(editor){
                self.initImageNames(editor);
            });
        },

        /**
         * Inits an ability for renaming of images.
         */
        initImageNames: function(ed) {
            var self = this;
            
            this.createImageNameHolder( ed );
            this.createRenameModelDialog( ed );
        },
        
        // ------------------------------------------------------------------------------
        // Methods fot the tooltip
        // ------------------------------------------------------------------------------
        
        /**
         * Creates the image name holder (the tooltip).
         */
        createImageNameHolder: function( ed ) {
            var self = this;
            
            // creates markup
            var holder = jQuery("<a href='#' id='clipimg-image-name' title='click to rename'></a>").appendTo("body");
            this.imageNameHolder = holder;
            
            // binds the hover events for all images in the editor
            $( ed.getDoc() ).find("img")
                .live("mouseover mousemove", function(e){
                    if ( $(this).data('type') ) return;
                    
                    self.mouseOnImage = true;
                    self.showImageNameHolder(ed, this);
                    e.stopImmediatePropagation();
                })
                .live("mouseout", function(e){
                    self.mouseOnImage = false;
                    e.stopImmediatePropagation();
                    
                    if ( self.hoverTimerIsActive ) return;
                    self.hoverTimerIsActive = true;
                    
                    setTimeout(function(){
                        self.hoverTimerIsActive = false;
                        if ( self.mouseOnImage || self.mouseOnTooltip ) return;
                        self.hideImageNameHolder(); 
                    }, 100);
                })
           
            holder.click(function(){
                return false;
            });
            
            $("#wp_editbtns img").live("mousedown", function(){
                self.hideImageNameHolder( true );
            });
            
            $(window).load(function(){
                $($('#content_ifr').contents()).scroll(function(){
                    self.hideImageNameHolder( true );
                }); 
            });
            
            // behaviour for the tooltip when hovered
            holder.hover(
                function(){
                    self.mouseOnTooltip = true;
                    
                    var hoverWidth = holder.data('hover-width');
                    if ( !hoverWidth ) return;

                    holder.css({
                        width: hoverWidth
                    });
                }, 
                function() {
                    self.mouseOnTooltip = false;
                    
                    if ( !self.hoverTimerIsActive ) {
                        self.hoverTimerIsActive = true;

                        setTimeout(function(){
                            self.hoverTimerIsActive = false;
                            if ( self.mouseOnImage || self.mouseOnTooltip ) return;
                            self.hideImageNameHolder(); 
                        }, 100);   
                    }
                    
                    var hoverWidth = holder.data('hover-width');
                    if ( !hoverWidth ) return;

                    holder.css({
                        width: holder.data('default-width')
                    });
                }
            );
        },        
        
        /**
         * Shows the tooltip.
         */
        showImageNameHolder: function(ed, target) {
            if ( target == this.currentImage) return;
            this.hideImageNameHolder();
            
            this.currentImage = target;
            
            // gets an iamge data
            var result = this.saveImageData( $(target) );
            if ( !result ) return;
            
            this.imageNameHolder.text(this.imageData.fullName);
            this.imageNameHolder.css("width", "auto");
            this.imageNameHolder.removeClass("no-border-radius");       
            
            // gets the tooltip position
            var size = this.getTooltipPosition(ed, target);
            if ( !size ) return;
            
            this.imageNameHolder.css({
                'top' : size.y + 'px',
                'left' : size.x + 'px',
                'display' : 'block'
            });
            
            if ( size.width > 0 ) {
                this.imageNameHolder.css("width", size.width);
                this.imageNameHolder.addClass('no-border-radius');
            } 
            
            this.imageNameHolder.data('hover-width', size.hoverWidth);
            this.imageNameHolder.data('default-width', size.width);    
        },
        
        /**
         * Retunns position and width for the tooltip.
         */
        getTooltipPosition: function(ed, target) {
            var x, y, width = 0, hoverWidth = 0;

            var DOM = tinymce.DOM;
            var img = jQuery(target);
            
            var imgWidth = img.width();
            var imgHeight = img.height();

            var tootlipWidth = this.imageNameHolder.innerWidth();
            var tootlipHeight = this.imageNameHolder.innerHeight();

            var vp = ed.dom.getViewPort(ed.getWin());
  
            var $iframe = $(ed.iframeElement);
            var iframeOffset = $iframe.offset();
                
            var p1 = DOM.getPos(ed.getContentAreaContainer());
            var p2 = ed.dom.getPos(img[0]);
            
            x = p2.x - vp.x + iframeOffset.left;
            y = p2.y - vp.y + iframeOffset.top;

            y = y + imgHeight - tootlipHeight;

            if ( y + tootlipHeight > iframeOffset.top + vp.h) return null;
            
            if ( tootlipWidth < imgWidth ) {
                x = x + imgWidth - tootlipWidth;
            } else {
                width = imgWidth;
                hoverWidth = tootlipWidth;
            }

            return {
                x: x,
                y: y,
                width: width,
                hoverWidth: hoverWidth
            }
        },
        
        /**
         * Hides the tooltip.
         */
        hideImageNameHolder: function( force ) {
            this.imageNameHolder.hide();       
            this.currentImage = false;
        },
        
        /**
         * Sets the current image data and returns the one.
         */
        saveImageData: function( img ) {
            var src = img.attr('src');
            
            var matches = src.match(/([^\/]+)(?=\.\w+$)/);
            if ( !matches || !matches[0] ) return;
            
            var name = matches[0];
            var fullNameIndex = src.lastIndexOf("/") + 1;
            var fullName = src.substr(fullNameIndex);
            
            var imgId = null;
            var classes = img.attr('class') && img.attr('class').split(/\s+/);
            if ( classes && classes.length > 0 ) {
                for(var index in classes) {
                    if ( classes[index].indexOf("wp-image-") == 0 ) {
                        var numberPattern = /\d+/g;
                        var result = classes[index].match( numberPattern );
                        if ( result && result.length > 0 ) imgId = result[0];
                        break;
                    }
                }
            }

            this.imageData = {
                name: name,
                fullName: fullName,
                img: img,
                imgId: imgId
            };
            
            return this.imageData;
        },
        
        // ------------------------------------------------------------------------------
        // The renaming dialog
        // ------------------------------------------------------------------------------

        /**
         * Creates a renaming dialog for images based on qtip2.
         */
        createRenameModelDialog: function() {
            var self = this;
            
            this.imageNameHolder.qtip2(
            {
                id: "clipimage-renaming-dialog",
                content: {
                    text: $( self.getRenamingDialogHtml() ),
                    title: {
                        text: 'Renamimg the image',
                        button: true
                    }
                },
                position: {
                    my: 'center',
                    at: 'center',
                    target: $(window)
                },
                show: {
                    event: 'click',
                    solo: true,
                    modal: true
                },
                hide: false,
                style: 'qtip2-light qtip2-rounded onp-imevr-renaming-dialog',
                events: {
                    show: function() {
                        self.onShowRenamingDialog();
                    },
                    hide: function() {
                        self.onHideRenamingDialog();
                    }
                }
            });
        },
       
        /**
         * Returns renaming dialog html
         */
        getRenamingDialogHtml: function() {
            return "<div class='clipimage-renaming-dialog-wrap'>" + 
                "<p class='clipimg-renaming-current-name-wrap'>Current name: <span class='clipimg-renaming-current-name'></span></p>" +
                "<p><input type='text' id='clipimage-title-input' />" +
                "<a href='#' id='clipimage-title-button' class='button button-primary'>Apply</a></p>" +
                "<div class='clipimages-suggestions-wrap'></div>" +
                    "<strong>Auto suggestions</strong> <small>click to select</small>" + 
                    "<div class='clipimages-suggestions-list'></div>" + 
                "</div>" +                     
            "</div>";
        },
        
        /**
         * Fired when the dialog appears.
         */
        onShowRenamingDialog: function() {
            var self = this;
            if ( window.imgevr.context ) window.imgevr.context.disabled = true;
            
            if ( !this.renamingDialogInited ) {
                this.renamingDialogInited = true;
                
                $("#clipimage-title-button").click(function(){
                    self.hideImageNameHolder();
                    
                    self.imageNameHolder.qtip2('hide');
                    var newName = $("#clipimage-title-input").val();
                    
                    // if the name is still be the same
                    if ( self.imageData.name == newName ) return false;
                    
                    // rename an image
                    self.renameImage(self.imageData, newName);
                    return false;
                });
                
                $("#clipimage-title-input").keypress(function (e) {
                    if (e.which == 13) {
                        e.preventDefault();
                        $("#clipimage-title-button").click();
                    }
                });
            }
            
            // sets visible form data by default
            $(".clipimg-renaming-current-name").text(this.imageData.fullName);
            $("#clipimage-title-input").val(this.imageData.name).select();

            setTimeout(function(){
                $("#clipimage-title-input").focus().select();   
            }, 100);
            
            // stats loading image name suggestions
            this.loadImageNameSuggestions();
        },
        
        onHideRenamingDialog: function() {
            if ( window.imgevr.context ) window.imgevr.context.disabled = false;
        },
        
        /**
         * Loads image suggestions.
         */
        loadImageNameSuggestions: function() {
            $(".clipimages-suggestions-list").html("").addClass('loading').removeClass("nothing");
            var self = this;

            var req = $.ajax({
                'url': ajaxurl,
                'type': 'POST',
                'dataType': 'json',
                'data': {
                    'action': 'imgevr_load_suggestions',
                    'imgPostId': $("#post_ID").length > 0 ? $("#post_ID").val() : null,
                    'imgPostTitle': $("#title").length > 0 ? $("#title").val() : null,
                    'imgId': self.imageData.imgId
                },
                success: function(data) {

                    $(".clipimages-suggestions-list").removeClass("loading").removeClass("nothing");
    
                    if ( data && data.items && data.items.length > 0 ) {
                        var list = $(".clipimages-suggestions-list").html("");
                        var ul = $("<ul>").appendTo(list).hide();
                        for(var index in data.items) {
                            ul.append("<li><a href='#'>" + data.items[index] + "</a></li>");
                        }
                        
                        ul.find("a").click(function(){
                            $("#clipimage-title-input").val( $(this).text() );
                            return false;
                        });
                        ul.fadeIn();
                    } else {
                        $(".clipimages-suggestions-list").text("sorry, nothing found to suggest").addClass("nothing");
                    }
                },
                error: function() {

                    $(".clipimages-suggestions-list")
                        .removeClass("loading")
                        .addClass("nothing")
                        .text("sorry, unexpected error occurred");
                }
            })
        },
        
        renameImage: function( imageData, newName, overwrite ) {
            var self = this;
            var link = this.setLoadingState( tinymce.activeEditor, imageData.img );  
            
            var r = $.ajax({
                'url': ajaxurl,
                'type': 'POST',
                'dataType': 'json',
                'data': {
                    'action': 'imgevr_rename_image',
                    'imgUrl': imageData.img.attr('src'),
                    'imgName': newName,
                    'imgId': imageData.imgId,
                    'imgPostId': $("#post_ID").length > 0 ? $("#post_ID").val() : null,
                    'imgOverwrite': overwrite ? true : null
                },
                success: function(data) {

                    if ( data.error ) {
                        self.showErrorPopup(data.error);
                        self.clearLoadingState(link, true);
                        return;
                    }
                    
                    if ( data.confirm ) {
                        self.showConfirmPopup(data.confirm, {
                            'Overwrite': function() {
                                self.renameImage(imageData, newName, true);
                            },
                            'Rename without overwriting': function() {
                                self.changeSrc(imageData.img, data.src);
                            },
                            'Cancel': function() {}
                        });
                        self.clearLoadingState(link, true);
                        return;
                    }
                    
                    self.changeSrc(imageData.img, data.src);
                    self.clearLoadingState(link);
                },
                error: function() {

                    self.showErrorPopup("Unexpected error occurred. Please contact OnePress support.");
                    self.clearLoadingState(link);
                }
            });
        },
        
        changeSrc: function( img, newSrc ) {
            img.attr('src', newSrc);
            img.attr('data-mce-src', newSrc);
        },
        
        // ------------------------------------------------------------------------------
        // Error and confirmation dialogs
        // ------------------------------------------------------------------------------
        
        showErrorPopup: function( message ) {
            
            if ( this.errorDialog ) {
                this.errorDialog.qtip2('hide');
                this.errorDialog.qtip2('destroy');
                this.errorDialog = null;
            }
            
            var content = $(
                "<div class='imgevr-error-dialog-wrap'>" + 
                    "<p>" + message + "</p>" +
                "</div>");
            
            this.errorDialog = $("<div>").appendTo("body").qtip2({
                id: "imgevr-error-dialog",
                content: {
                    text: content,
                    title: {
                        text: 'Error occurred',
                        button: true
                    }
                },
                position: {
                    my: 'center',
                    at: 'center',
                    target: $(window)
                },
                show: {
                    event: false,
                    solo: true,
                    modal: true
                },
                hide: false,
                style: 'qtip2-plain qtip2-rounded'
            });
            
            this.errorDialog.qtip2('show');
        },
        
        showConfirmPopup: function( message, options ) {
            var self = this;
            
            if ( this.confirmDialog ) {
                this.confirmDialog.qtip2('hide');
                this.confirmDialog.qtip2('destroy');
                this.confirmDialog = null;
            }
            
            var content = $(
                "<div class='imgevr-confirmation-dialog-wrap'>" + 
                    "<p>" + message + "</p>" + 
                    "<div class='imgevr-confirmation-dialog-buttons'></div>" +
                "</div>");
            
            var buttons = content.find(".imgevr-confirmation-dialog-buttons");
            
            for( var buttonLabel in options ) {

                var button = $("<a href='#' class='button'>" + buttonLabel + "</a>");
                button.data('label', buttonLabel);
                
                button.click(function(){
                    options[ $(this).data('label')]();
                    self.confirmDialog.qtip2("hide");
                    self.confirmDialog.qtip2("destroy");
                    self.confirmDialog = null;
                    return false;
                });
                buttons.append(button);
            } 
            
            buttons.find('.button:first').addClass('button-primary');
            
            this.confirmDialog = $("<div>").appendTo("body").qtip2({
                id: "imgevr-confirmation-dialog",
                content: {
                    text: content,
                    title: {
                        text: 'Confirmation request',
                        button: true
                    }
                },
                position: {
                    my: 'center',
                    at: 'center',
                    target: $(window)
                },
                show: {
                    event: false,
                    solo: true,
                    modal: true
                },
                hide: false,
                style: 'qtip2-light qtip2-rounded'
            });
            
            this.confirmDialog.qtip2('show');
        },
        
        // ------------------------------------------------------------------------------
        // Managing the loading state of an image
        // ------------------------------------------------------------------------------
        
        setLoadingState: function( ed, img ) { 
            if ( img.data("loading-link") ) return null;
            
            var overlay = $("<div class='clipimg-image-overlay'></div>").appendTo("body");
            var preloader = $("<div class='clipimg-image-overlay-preloader'></div>").appendTo("body");   
            
            var x, y;

            var vp = ed.dom.getViewPort(ed.getWin());
            var p1 = tinymce.DOM.getPos(ed.getContentAreaContainer());
            var p2 = ed.dom.getPos(img[0]);

            x = Math.max(p2.x - vp.x, 0) + p1.x;
            y = Math.max(p2.y - vp.y, 0) + p1.y;
            
            overlay.add(preloader).css({
                top: y + "px",
                left: x + "px",
                width: img.width() + "px",
                height: img.height() + "px", 
                lineHeight: img.height() + "px"
            });
            
            var link = {
                overlay: overlay,
                preloader: preloader,
                img: img
            }
            
            $(".wp-switch-editor.switch-html").click(function(){
                if ( link && link.overlay ) overlay.addClass('html-is-active');
                if ( link && link.preloader ) preloader.addClass('html-is-active');         
            });
            
            $(".wp-switch-editor.switch-tmce").click(function(){
                if ( link && link.overlay ) overlay.removeClass('html-is-active');
                if ( link && link.preloader ) preloader.removeClass('html-is-active');         
            });     
            
            img.data("loading-link", link);
            return link;
        },
        
        clearLoadingState: function( link, force ) {
            
            var clearMethod = function() {
                link.overlay.remove();
                link.preloader.remove();
                link.overlay = null;
                link.preloader = null;
                link.img.data("loading-link", null);    
            }
            
            if ( force ) {
                clearMethod();
            } else {
                link.preloader.addClass('done-icon');
                setTimeout(function(){
                    link.overlay.fadeOut(500);
                    link.preloader.fadeOut(500, function(){
                        clearMethod();
                    });
                }, 1000); 
            }
        }
    });  
    
    tinymce.PluginManager.add('imgrenaming', tinymce.plugins.imgrenaming);  
})(jQuery);  