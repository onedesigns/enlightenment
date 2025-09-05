(function($) {
    var args = enlightenment_rtmedia_args;

    $(document).ajaxSuccess(function( event, xhr, settings ) {
        if ( typeof xhr.responseJSON == 'undefined' ) {
            return true;
        }

        let response = xhr.responseJSON;

        if ( typeof response.success == 'undefined' ) {
            return true;
        }

        if ( false === response.success ) {
            return true;
        }

        let data   = new URLSearchParams( settings.data ),
            action = data.get('action');

        if ( typeof action == 'undefined' ) {
            return true;
        }

        if ( action != 'post_update' ) {
            return true;
        }

        setTimeout(function() {
            $('#whats-new-form').children('.rtmedia-uploader-div').addClass('d-none');
        }, 15);
    });

    $(document).ajaxSuccess(function( event, xhr, settings ) {
        if ( typeof xhr.responseJSON == 'undefined' ) {
            return true;
        }

        let response = xhr.responseJSON;

        if ( typeof response.error == 'undefined' ) {
            return true;
        }

        if ( false !== response.error ) {
            return true;
        }

        let data   = new URLSearchParams( settings.data ),
            action = data.get('action');

        if ( typeof action == 'undefined' ) {
            return true;
        }

        setTimeout(function() {
            switch( action ) {
                case 'rtmedia_create_album':
                    if ( typeof response.album != 'undefined' ) {
                        let $modal = $('#rtmedia-create-album-modal');

                        $modal.modal('hide');

                        setTimeout(function() {
                            let $alert = $modal.children('.rtmedia-create-album-alert');

                            $alert.prependTo('#rtm-gallery-title-container');
                            $alert.addClass('alert alert-success w-100');
                        }, 15);
                    }
                    break;
            }
        }, 15);
    });

    $(document).ajaxSuccess(function( event, xhr, settings ) {
        if ( typeof xhr.responseJSON == 'undefined' ) {
            return true;
        }

        let response = xhr.responseJSON;

        if ( typeof response.next == 'undefined' ) {
            return true;
        }

        let data = new URLSearchParams( settings.url.split('?')[1] ),
            page = data.get('rtmedia_page');

        if ( typeof page == 'undefined' ) {
            return true;
        }

        setTimeout(function() {
            $('.bp-user.media .rtmedia-list').addClass('row').addClass('list-unstyled');
            $('.rtmedia-nodata').addClass('alert').addClass('alert-info');

            if ( typeof response.pagination != 'undefined' ) {
                let $pagination = $('.rtm-pagination'),
                    $pageNumber = $pagination.children('.rtm-page-number'),
                    $label      = $pageNumber.children('.rtm-label'),
                    $goToNum    = $pageNumber.children('.rtm-go-to-num'),
                    $button     = $pageNumber.children('.button'),
                    $paginate   = $pagination.children('.rtm-paginate'),
                    $pages      = $paginate.children(),
					$icons      = $paginate.find('.dashicons');

                $pageNumber.addClass('d-flex align-items-center ms-auto');
                $label.addClass('d-none d-sm-block text-nowrap');
                $goToNum.addClass('form-control text-center mx-1');
                $button.addClass('btn btn-outline-secondary');
                $paginate.addClass('order-first pagination');
                $pages.addClass('page-link');
                $pages.wrap('<li class="page-item"></li>');
                $pages.filter('.current').parent().addClass('active').attr('aria-current', 'page');
				$pages.filter('[data-page="' + page + '"]').parent().addClass('active').attr('aria-current', 'page');
				$icons.removeClass('dashicons').addClass('fas');
				$icons.filter('.dashicons-arrow-left-alt2').removeClass('dashicons-arrow-left-alt2').addClass('fa-arrow-left')
				$icons.filter('.dashicons-arrow-right-alt2').removeClass('dashicons-arrow-right-alt2').addClass('fa-arrow-right')

                $pagination.removeClass('clearfix').addClass('d-flex align-items-center');
            }
        }, 15);
    });

    $(document).ajaxSend(function( event, xhr, settings ) {
        if ( typeof rtMediaHook == 'undefined' ) {
            return true;
        }

        if ( typeof settings.data == 'undefined' ) {
            return true;
        }

        let data = new URLSearchParams(settings.data);

        if ( 'true' !== data.get('rtajax') ) {
            return true;
        }

        if ( '1' == data.get('comment') ) {
            $('#comment_content').prop('disabled', true);
        }
    });

    $(document).ajaxSuccess(function( event, xhr, settings ) {
        if ( typeof xhr.responseJSON == 'undefined' ) {
            return true;
        }

        let response = xhr.responseJSON;

        if ( typeof response.success == 'undefined' ) {
            return true;
        }

        if ( false === response.success ) {
            return true;
        }

        let data   = new URLSearchParams( settings.data ),
            action = data.get('action');

        if ( typeof action == 'undefined' ) {
            return true;
        }

        setTimeout(function() {
            switch( action ) {
                case 'activity_filter':
                case 'post_update':
                    setTimeout(function() {
                        let items  = ( 'activity_filter' == action ? response.data.contents : response.data.activity ),
                            $html  = $($.parseHTML(items)),
                            $items = $html.filter('.activity-item'),
                            ids    = [];

                        if ( ! $items.length ) {
                            $items = $html.children('.activity-item');
                        }

                        $items.each(function() {
                            ids.push('#' + this.id);
                        });

                        $items = $( ids.join(', ') );

                        if ( typeof $.fn.select2 != 'undefined' ) {
                            let $select = $items.find('.rtm-activity-privacy-opt');

                            $select.select2({
                                minimumResultsForSearch: 33,
                            });
                        }

                        if ( typeof rtmedia_comment_media_upload != 'undefined' ) {
                            $items.find('.d-none[type="file"]').remove();

                            function enlightement_rtmedia_comment_media_upload() {
                                if ( typeof UploadView != 'undefined' ) {
                                    $items.each(function() {
                                        rtmedia_comment_media_upload( this );
                                    });
                                } else {
                                    setTimeout(enlightement_rtmedia_comment_media_upload, 150);
                                }
                            }

                            enlightement_rtmedia_comment_media_upload();
                        }

                        if ( typeof apply_rtMagnificPopup != 'undefined' ) {
                            apply_rtMagnificPopup( '.rtmedia-activity-container ul.rtmedia-list' );
                        }
                    }, 150);
                    break;
            }
        }, 15);
    });

    $.ajaxPrefilter(function( options, originalOptions, jqXHR ) {
        if ( typeof originalOptions.data == 'undefined' ) {
            return true;
        }

        if ( typeof originalOptions.data.action == 'undefined' ) {
            return true;
        }

        if ( 'new_activity_comment' != originalOptions.data.action ) {
            return true;
        }

        let form_id  = 'activity-' + originalOptions.data.form_id,
            $form    = $( '.' + comment_media_wrapper + form_id ),
            disabled = '1',
            temp     = $form.find('input[name="rtMedia_attached_files[]"]').val();

        if ( typeof rtmedia_disable_media_in_commented_media != 'undefined' ) {
            disabled = rtmedia_disable_media_in_commented_media;
        }

        if ( typeof temp == 'undefined' ) {
            temp = 0;
        }

        if ( typeof temp == '' ) {
            temp = 0;
        }

        if( disabled == '1' ) {
            if ( originalOptions.data.form_id != originalOptions.data.comment_id && temp > 0 ) {
                setTimeout(function() {
                    $form.children('.bp-ajax-message').addClass('alert alert-danger mt-3 mb-0').children('p').addClass('mb-0');
                }, 15);
            }
        }
    });

    $(document).ready(function() {
        let $document   = $(document),
            $rtMediaUpl = $('#rtmedia-upload-container'),
            $ac_input   = $('#comment_content'),
			geminiSB;

        $('.rt-tooltip[data-bs-toggle="tooltip"]').tooltip();

        if( $rtMediaUpl.length ) {
            let container     = $( '#rtmedia-upload-container' ),
                drop_element  = $( '#drag-drop-area' ),
                browse_button = $( '#rtMedia-upload-button' );

            if ( drop_element.hasClass( 'drag-drop' ) ) {
                $document.on( 'dragover', function() {
                    drop_element.removeClass('bg-body-secondary');
                    drop_element.addClass('bg-primary');
                    drop_element.addClass('text-light');
                    browse_button.removeClass('btn-primary');
                    browse_button.addClass('btn-light');
                } ).on( 'drop dragleave', function() {
                    drop_element.removeClass('bg-primary');
                    drop_element.removeClass('text-light');
                    drop_element.addClass('bg-body-secondary');
                    browse_button.removeClass('btn-light');
                    browse_button.addClass('btn-primary');
                } );
            }
        }

        if ( typeof uploaderObj != 'undefined' ) {
            uploaderObj.uploader.bind( 'FilesAdded', function( up, files ) {
                setTimeout(function() {
                    $.each(files, function(index, file) {
                        let $container = $(document.getElementById(file.id)),
                            type       = file.type,
                            title      = file.name,
                            ext        = title.substring( title.lastIndexOf( '.' ) + 1, title.length );

                    	if ( /image/i.test( type ) ) {
                    		if ( ext !== 'gif' && typeof mOxie != 'undefined' ) {
                                let $thumb = $( '#file_thumb_' + file.id ),
                                    img    = new mOxie.Image();

                    			img.onload = function() {
                                    $thumb.html('');

                    				this.embed( $thumb.get( 0 ), {
                    					width: 220,
                    					height: 220,
                    					crop: true
                    				} );
                    			};

                    			img.onembedded = function() {
                    				this.destroy();
                    			};

                    			img.onerror = function() {
                    				this.destroy();
                    			};

                    			img.load( file.getSource() );
                            }
                        }

                        $container.find('.plupload_file_status').addClass('progress');
                        $container.find('.plupload_file_progress').addClass('progress-bar progress-bar-striped progress-bar-animated');

                        let $editBtn = $container.find('.dashicons-edit');
                        $container.find('.plupload_file_name_wrapper').addClass('d-none');
                        $editBtn.removeClass('dashicons dashicons-edit').addClass('far fa-edit');
                        $editBtn.on('click', function() {
                            let $this    = $(this),
                                $parent  = $this.parent();

                            setTimeout(function() {
                                let $title   = $parent.children('.rtm-upload-edit-title-wrapper'),
                                    $desc    = $parent.children('.rtm-upload-edit-desc-wrapper'),
                                    $save    = $parent.children('.dashicons-yes'),
                                    $modal   = $(document.createElement('div')),
                                    $dialog  = $(document.createElement('div')),
                                    $content = $(document.createElement('div')),
                                    $header  = $(document.createElement('div')),
                                    $close   = $(document.createElement('button')),
                                    $icon    = $(document.createElement('span')),
                                    $body    = $(document.createElement('div')),
                                    $footer  = $(document.createElement('div'));

                                $header.addClass('modal-header');
                                $header.appendTo( $content );

                                $body.addClass('modal-body');
                                $body.appendTo( $content );

                                $footer.addClass('modal-footer');
                                $footer.appendTo( $content );

                                $content.addClass('modal-content');
                                $content.appendTo( $dialog );

                                $dialog.addClass('modal-dialog');
                                $dialog.appendTo( $modal );

                                $modal.addClass('modal').addClass('fade');
                                $modal.appendTo( $('body') );
                                $modal.modal({
                                    show: false,
                                });
                                $modal.on('hidden.bs.modal', function() {
                                    $modal.remove();
                                    $this.css('display', '');
                                });

                                $close.addClass('btn-close');
								$close.attr('aria-label', args.close_label);
                                $close.on('click', function() {
                                    $modal.modal('hide');
                                });
                                $close.appendTo( $header );

                                $title.addClass('mb-3');
								$title.find('label').addClass('form-label');
                                $title.find('.rtm-upload-edit-title').addClass('form-control w-100');
                                $title.appendTo( $body );

								$desc.find('label').addClass('form-label');
                                $desc.find('.rtm-upload-edit-desc').addClass('form-control');
                                $desc.appendTo( $body );

                                $save.removeClass('dashicons').removeClass('dashicons-yes')
                                $save.addClass('btn btn-primary');
                                $save.text( $save.attr('title') );
                                $save.on('click', function() {
                                    $modal.modal('hide');
                                });
                                $save.appendTo( $footer );

                                $modal.modal('show');
                            }, 15);
                        });

                        $container.find('.dashicons-dismiss').removeClass('dashicons dashicons-dismiss').addClass('fas fa-times fa-fw');

                        $container.addClass('col-2');
                    });
                }, 15);
            });

            uploaderObj.uploader.bind( 'FileUploaded', function( up, file ) {
                setTimeout(function() {
                    $(document.getElementById(file.id)).find('.rtmedia-delete-uploaded-media').removeClass('dashicons dashicons-dismiss').addClass('fas fa-times fa-fw');
                }, 15);
            });
        }

        if ( typeof objUploadView != 'undefined' ) {
            objUploadView.uploader.bind( 'Init', function( up, file ) {
                setTimeout(function() {
                    $('#rtmedia_uploader_filelist').appendTo('#whats-new-form');
                }, 15);
            });

            objUploadView.uploader.bind( 'FilesAdded', function( up, files ) {
                setTimeout(function() {
                    $.each(files, function(index, file) {
                        let $container = $(document.getElementById(file.id)),
                            type       = file.type,
                            title      = file.name,
                            ext        = title.substring( title.lastIndexOf( '.' ) + 1, title.length );

                    	if ( /image/i.test( type ) ) {
                    		if ( ext !== 'gif' && typeof mOxie != 'undefined' ) {
                                let $thumb = $( '#file_thumb_' + file.id ),
                                    img    = new mOxie.Image();

                    			img.onload = function() {
                                    $thumb.html('');

                    				this.embed( $thumb.get( 0 ), {
                    					width: 220,
                    					height: 220,
                    					crop: true
                    				} );
                    			};

                    			img.onembedded = function() {
                    				this.destroy();
                    			};

                    			img.onerror = function() {
                    				this.destroy();
                    			};

                    			img.load( file.getSource() );
                            }
                        }

                        $container.find('.plupload_file_status').addClass('progress');
                        $container.find('.plupload_file_progress').addClass('progress-bar progress-bar-striped progress-bar-animated');

                        let $editBtn = $container.find('.dashicons-edit');
                        $container.find('.plupload_file_name_wrapper').addClass('d-none');
                        $editBtn.removeClass('dashicons dashicons-edit').addClass('far fa-edit');
                        $editBtn.on('click', function() {
                            let $this    = $(this),
                                $parent  = $this.parent();

                            setTimeout(function() {
                                let $title   = $parent.children('.rtm-upload-edit-title-wrapper'),
                                    $desc    = $parent.children('.rtm-upload-edit-desc-wrapper'),
                                    $save    = $parent.children('.dashicons-yes'),
                                    $modal   = $(document.createElement('div')),
                                    $dialog  = $(document.createElement('div')),
                                    $content = $(document.createElement('div')),
                                    $header  = $(document.createElement('div')),
                                    $close   = $(document.createElement('button')),
                                    $icon    = $(document.createElement('span')),
                                    $body    = $(document.createElement('div')),
                                    $footer  = $(document.createElement('div'));

                                $header.addClass('modal-header');
                                $header.appendTo( $content );

                                $body.addClass('modal-body');
                                $body.appendTo( $content );

                                $footer.addClass('modal-footer');
                                $footer.appendTo( $content );

                                $content.addClass('modal-content');
                                $content.appendTo( $dialog );

                                $dialog.addClass('modal-dialog');
                                $dialog.appendTo( $modal );

                                $modal.addClass('modal').addClass('fade');
                                $modal.appendTo( $('body') );
                                $modal.modal({
                                    show: false,
                                });
                                $modal.on('hidden.bs.modal', function() {
                                    $modal.remove();
                                    $this.css('display', '');
                                });

								$close.addClass('btn-close');
								$close.attr('aria-label', args.close_label);
                                $close.on('click', function() {
                                    $modal.modal('hide');
                                });
                                $close.appendTo( $header );

                                $title.addClass('mb-3');
								$title.find('label').addClass('form-label');
                                $title.find('.rtm-upload-edit-title').addClass('form-control w-100');
                                $title.appendTo( $body );

								$desc.find('label').addClass('form-label');
                                $desc.find('.rtm-upload-edit-desc').addClass('form-control');
                                $desc.appendTo( $body );

                                $save.removeClass('dashicons').removeClass('dashicons-yes')
                                $save.addClass('btn btn-primary');
                                $save.text( $save.attr('title') );
                                $save.on('click', function() {
                                    $modal.modal('hide');
                                });
                                $save.appendTo( $footer );

                                $modal.modal('show');
                            }, 15);
                        });

                        $container.find('.dashicons-dismiss').removeClass('dashicons dashicons-dismiss').addClass('fas fa-times fa-fw');

                        $container.addClass('col-3');
                    });
                }, 15);
            });

            objUploadView.uploader.bind( 'FileUploaded', function( up, file ) {
                setTimeout(function() {
                    $(document.getElementById(file.id)).find('.rtmedia-delete-uploaded-media').removeClass('dashicons dashicons-dismiss').addClass('fas fa-times fa-fw');
                }, 15);
            });
        }

        if ( $ac_input.length ) {
            $ac_input.on('input', function() {
                let $this = $(this);

                if( this.clientHeight < this.scrollHeight ) {
                    $this.css('height', this.scrollHeight);
                } else {
                    while( this.clientHeight >= this.scrollHeight ) {
                        $this.css('height', this.clientHeight - 22);
                    }

                    if ( this.scrollHeight > 40 ) {
                        $this.css('height', this.scrollHeight);
                    } else {
                        $this.css('height', '');
                    }
                }
            });

            $ac_input.on('keydown', function(event) {
                if(event.keyCode === 13 && ! event.altKey && ! event.ctrlKey && ! event.shiftKey) {
                    event.preventDefault();
                    $('#rt_media_comment_submit').click();
                }
            });
        }

        if ( typeof rtMediaHook != 'undefined' ) {
            rtMediaHook.register('rtmedia_js_after_comment_added', function() {
                var instance    = $.magnificPopup.instance,
					$meta       = instance.contentContainer.find('.rtmedia-single-meta'),
					$scroll     = $meta.find('.rtmedia-scroll'),
                    $comments_c = $scroll.find('.rtmedia-comments-container'),
                    $comments_l = $scroll.find('#rtmedia_comment_ul'),
                    $comm_link  = $scroll.find('.rtm-comments-link'),
                    $comm_title = $scroll.find('.rtmedia-comments-title'),
                    $ac_input   = $scroll.find('#comment_content'),
                    $tooltips   = $scroll.find('.rt-tooltip[data-bs-toggle="tooltip"]'),
                    count, label;

				if ( $scroll.hasClass('gm-scrollbar-container') ) {
					$scroll = $scroll.find('.gm-scroll-view');
				}

                $ac_input.prop('disabled', false).css('height', '').focus();
                $tooltips.tooltip();

                $comments_c.css('bottom', '');
                $comments_l.css('max-height', '');

                $scroll.css('min-height', '');
                $scroll.css('max-height', '');

                count = $comments_l.children('.rtmedia-comment').length;

                switch ( count ) {
                    case 0:
                        label = args.rtm_comments_link_label.zero;
                        break;

                    case 1:
                        label = args.rtm_comments_link_label.one;
                        break;

                    default:
                        label = args.rtm_comments_link_label.more;
                        label = label.replace( '%s', count );
                        break;
                }

                $comm_link.text( label );
                $comm_title.text( label );

				setTimeout(function() {
					if (!geminiSB || !geminiSB._created) {
						return;
					}

					geminiSB.update();
				}, 0);

				if (
					Math.max( document.documentElement.clientWidth || 0, window.innerWidth || 0 ) >= 992
					&&
					$scroll.prop('clientHeight') < $scroll.prop('scrollHeight')
				) {
					$meta.addClass('has-comment-form-affixed-to-bottom');
				}

                return true;
            });

            rtMediaHook.register('rtmedia_js_after_comment_deleted', function() {
                var $comments_l = $('#rtmedia_comment_ul'),
                    $comm_link  = $('.rtm-comments-link'),
                    $comm_title = $('.rtmedia-comments-title'),
                    $tooltips   = $('.tooltip'),
                    count, label;

                $tooltips.remove();

                count = $comments_l.children('.rtmedia-comment').length - 1;

                switch ( count ) {
                    case 0:
                        label = args.rtm_comments_link_label.zero;
                        break;

                    case 1:
                        label = args.rtm_comments_link_label.one;
                        break;

                    default:
                        label = args.rtm_comments_link_label.more;
                        label = label.replace( '%s', count );
                        break;
                }

                $comm_link.text( label );
                $comm_title.text( label );
            });

            rtMediaHook.register('rtmedia_js_after_files_added', function(params) {
                let files = params[1];

                setTimeout(function() {
                    $.each(files, function(index, file) {
                        let type       = file.type,
                            title      = file.name,
                            ext        = title.substring( title.lastIndexOf( '.' ) + 1, title.length ),
                            $container = $(document.getElementById(file.id)),
                            $remove    = $container.find('.remove-from-queue');

                    	if ( /image/i.test( type ) ) {
                    		if ( ext !== 'gif' && typeof mOxie != 'undefined' ) {
                                let $thumb = $( '#file_thumb_' + file.id ),
                                    img    = new mOxie.Image();

                    			img.onload = function() {
                                    $thumb.html('');

                    				this.embed( $thumb.get( 0 ), {
                    					width: 240,
                    					height: 180,
                    					crop: true
                    				} );
                    			};

                    			img.onembedded = function() {
                    				this.destroy();

                                    $('#comment_content').trigger('input');
                    			};

                    			img.onerror = function() {
                    				this.destroy();
                    			};

                    			img.load( file.getSource() );
                            }
                        }

                        $container.find('.plupload_file_status').addClass('progress');
                        $container.find('.plupload_file_progress').addClass('progress-bar progress-bar-striped progress-bar-animated');

                        let $editBtn = $container.find('.dashicons-edit');
                        $container.find('.plupload_file_name_wrapper').addClass('d-none');
                        $editBtn.removeClass('dashicons dashicons-edit').addClass('far fa-edit');
                        $editBtn.on('click', function() {
                            let $this    = $(this),
                                $parent  = $this.parent();

                            setTimeout(function() {
                                let $title   = $parent.children('.rtm-upload-edit-title-wrapper'),
                                    $desc    = $parent.children('.rtm-upload-edit-desc-wrapper'),
                                    $save    = $parent.children('.dashicons-yes'),
                                    $modal   = $(document.createElement('div')),
                                    $dialog  = $(document.createElement('div')),
                                    $content = $(document.createElement('div')),
                                    $header  = $(document.createElement('div')),
                                    $close   = $(document.createElement('button')),
                                    $icon    = $(document.createElement('span')),
                                    $body    = $(document.createElement('div')),
                                    $footer  = $(document.createElement('div'));

                                $header.addClass('modal-header');
                                $header.appendTo( $content );

                                $body.addClass('modal-body');
                                $body.appendTo( $content );

                                $footer.addClass('modal-footer');
                                $footer.appendTo( $content );

                                $content.addClass('modal-content');
                                $content.appendTo( $dialog );

                                $dialog.addClass('modal-dialog');
                                $dialog.appendTo( $modal );

                                $modal.addClass('modal').addClass('fade');
                                $modal.appendTo( $('body') );
                                $modal.modal({
                                    show: false,
                                });
                                $modal.on('hidden.bs.modal', function() {
                                    $modal.remove();
                                    $this.css('display', '');
                                });

								$close.addClass('btn-close');
								$close.attr('aria-label', args.close_label);
                                $close.on('click', function() {
                                    $modal.modal('hide');
                                });
                                $close.appendTo( $header );

                                $title.addClass('mb-3');
								$title.find('label').addClass('form-label');
                                $title.find('.rtm-upload-edit-title').addClass('form-control w-100');
                                $title.appendTo( $body );

								$desc.find('label').addClass('form-label');
                                $desc.find('.rtm-upload-edit-desc').addClass('form-control');
                                $desc.appendTo( $body );

                                $save.removeClass('dashicons').removeClass('dashicons-yes')
                                $save.addClass('btn btn-primary');
                                $save.text( $save.attr('title') );
                                $save.on('click', function() {
                                    $modal.modal('hide');
                                });
                                $save.appendTo( $footer );

                                $modal.modal('show');
                            }, 15);
                        });

                        $remove.removeClass('dashicons dashicons-dismiss').addClass('fas fa-times fa-fw');
                        $remove.on('click', function() {
                            setTimeout(function() {
                                $('#comment_content').trigger('input');
                            }, 15);
                        });

                        $container.addClass('js-done');
                    });
                }, 15);

                return true;
            });

            function enlightenment_rtmedia_js_popup_toggle_controls(event) {
                var $target = $( event.target ),
                    $this   = $('.mfp-content.modal-content'),
                    $meta   = $this.find('.rtmedia-single-meta');

                if ( $target.closest('#mobile-swipe-overlay').length ) {
                    return true;
                }

                if (
                    // $meta.hasClass( 'comments-visible' ) &&
                    $this.hasClass( 'comments-visible' ) &&
                    ! $target.hasClass('rtm-comments-link') &&
                    ! $target.closest('.rtmedia-comments-container').length &&
                    ! $target.closest('.rtm-media-single-comments').length
                ) {
                    if (
                        $target.closest('.close').length ||
                        $target.closest('.mfp-arrow').length
                    ) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    // $meta.removeClass( 'comments-visible' );
                    $this.removeClass( 'comments-visible' );

                    return true;
                }

                if (
                    $target.closest('.close').length ||
                    $target.closest('.mfp-arrow').length ||
                    $target.closest('.rtmedia-single-meta').length ||
                    $target.closest('.rtm-ltb-action-container').length
                ) {
                    return true;
                }

                $this.toggleClass('controls-hidden');
            }

            rtMediaHook.register('rtmedia_js_popup_after_content_added', function() {
                let instance    = $.magnificPopup.instance,
                    $overlay    = $( instance.bgOverlay.get(0) ),
                    $modal      = $( instance.wrap.get(0) ),
                    $media      = instance.contentContainer.find('.carousel-inner'),
                    $image      = $media.children('img'),
                    $meta       = instance.contentContainer.find('.rtmedia-single-meta'),
                    $scroll     = $meta.children('.rtmedia-scroll'),
					scroll      = $scroll.get(0),
					$ac_form    = $scroll.children('.rtm-media-single-comments'),
                    $allcontent = $scroll.find('.allcontent'),
                    $comments_c = $scroll.find('.rtmedia-comments-container'),
					$show_all_c = $('#rtmedia_show_all_comment'),
                    $comments_l = $('#rtmedia_comment_ul'),
                    $ac_input   = $scroll.find('.ac-input'),
                    $filelist   = $scroll.find('.plupload_filelist_content'),
					vw          = Math.max( document.documentElement.clientWidth || 0, window.innerWidth || 0 );

                instance.bgOverlay.addClass('d-none');
                instance.wrap.addClass('modal');// fade');
                instance.container.addClass('modal-dialog').css('max-width', 1140);
                instance.contentContainer.addClass('modal-content');
                instance.preloader.addClass('d-none');

				geminiSB = new GeminiScrollbar({
					element:  scroll,
					autoshow: true,
					// forceGemini: true,
				});

				setTimeout(function() {
					if ( vw < 992 ) {
						return;
					}

					geminiSB.create();

					if (geminiSB._created) {
						scroll = geminiSB._viewElement;
					}
				}, 0);

				window.addEventListener('resize', function() {
					let vw = Math.max( document.documentElement.clientWidth || 0, window.innerWidth || 0 );

                    if ( vw < 992 && geminiSB._created) {
                        geminiSB.destroy();

						scroll = $scroll.get(0);
                    } else if ( vw >= 992 && !geminiSB._created) {
                        geminiSB.create();

						if (geminiSB._created) {
							scroll = geminiSB._viewElement;
						}
                    }
                });

				$show_all_c.on('click', function() {
					$comments_l.find('.rtmedia-comment').prop('hidden', false);

					if (
						Math.max( document.documentElement.clientWidth || 0, window.innerWidth || 0 ) >= 992
						&&
						scroll.clientHeight < scroll.scrollHeight
					) {
						$meta.addClass('has-comment-form-affixed-to-bottom');
					}

					setTimeout(function() {
						if (!geminiSB._created) {
							return;
						}

						geminiSB.update();
					}, 0);
				});

                $( instance.wrap.get(0) ).modal({
                    show: false,
                }).on('shown.bs.modal', function() {
					let vw = Math.max( document.documentElement.clientWidth || 0, window.innerWidth || 0 );

                    if ( vw < 992 ) {
                        return true;
                    }

                    let $meta   = instance.contentContainer.find('.rtmedia-single-meta'),
                        $scroll = $meta.find('.rtm-media-single-comments');

					if ( ! $ac_form.length ) {
                        return true;
                    }

                    // Race with arrow navigation code below
                    if ( $meta.hasClass('has-comment-form-affixed-to-bottom') ) {
                        return true;
                    }

                    if ( scroll.clientHeight < scroll.scrollHeight ) {
                        $meta.addClass('has-comment-form-affixed-to-bottom');
                    }
                }).on('hidden.bs.modal', function() {
                    instance.close();
                });;

                instance.contentContainer.off('click', enlightenment_rtmedia_js_popup_toggle_controls);
                instance.contentContainer.on('click', enlightenment_rtmedia_js_popup_toggle_controls);

                if ( $allcontent.length && ! $scroll.find('.rtm-like-comments-wrap').length && $scroll.find('.rtmedia-like-info').hasClass('hide') ) {
                    $allcontent.css('max-height', 'calc(100vh - 9.625rem)');
                }

                $scroll.find('.rtm-comments-link').on('click', function(event) {
                    var $meta     = $(this).closest('.rtmedia-single-meta'),
                        $ac_input = $meta.find('.ac-input');

                    // $meta.addClass('comments-visible');
                    instance.contentContainer.addClass('comments-visible');
                    $ac_input.focus();
                });

                $scroll.find('.rtmedia-comments-close').on('click', function(event) {
                    // $(this).closest('.rtmedia-single-meta').removeClass('comments-visible');
                    instance.contentContainer.removeClass('comments-visible');
                });

                $modal.modal('show');

                $media.swipe({
        			swipeLeft: function( event, direction, distance, duration, fingerCount ) {
        				instance.next();
        			},
        			swipeRight: function( event, direction, distance, duration, fingerCount ) {
        				instance.prev();
        			},
                    swipeStatus: function( event, phase, direction, distance ) {
                        return true;
                        /*switch ( phase ) {
                            case 'move':
                                switch ( direction ) {
                                    case 'left':
                                        $image.css('transform', 'translate3d(-' + distance + 'px, 0, 0)');
                                        break;

                                    case 'right':
                                        $image.css('transform', 'translate3d(' + distance + 'px, 0, 0)');
                                        break;

                                    case 'up':
                                        $modal.css('top', 'translate3d(0, -' + distance + 'px, 0)');
                                        if ( distance < 200 ) {
                                            $modal.css('transition', 'transform .25s ease');
                                            $modal.css('transform', '');
                                            setTimeout(function() {
                                                $modal.css('transition', '');
                                            }, 250);
                                        }
                                        break;

                                    case 'down':
                                        $modal.css('top', distance + 'px');
                                        if ( distance >= 200 ) {
                                            $modal.css('transition', 'transform .25s ease');
                                            $modal.css('transform', 'scale(.8)');
                                            setTimeout(function() {
                                                $modal.css('transition', '');
                                            }, 250);
                                        }
                                        break;
                                }
                                break;

                            case 'end':
                                switch ( direction ) {
                                    case 'left':
                                        $image.css('transition', 'transform .25s ease');
                                        $image.css('transform', '');
                                        setTimeout(function() {
                                            $image.css('transition', '');
                                        }, 250);
                                        break;

                                    case 'right':
                                        $image.css('transition', 'transform .25s ease');
                                        $image.css('transform', '');
                                        setTimeout(function() {
                                            $image.css('transition', '');
                                        }, 250);
                                        break;

                                    case 'up':
                                        $modal.css('transition', 'transform .25s ease');
                                        $modal.css('transform', '');
                                        setTimeout(function() {
                                            $modal.css('transition', '');
                                        }, 250);
                                        break;

                                    case 'down':
                                        // $modal.css('transition', 'transform .25s ease');
                                        $modal.css('transition', 'top .25s ease, transform .25s ease');
                                        $modal.css('transform', '');
                                        $modal.css('top', '');
                                        setTimeout(function() {
                                            $modal.css('transition', '');
                                        }, 250);
                                        break;
                                }
                                break;
                        }*/
                    },
                    tap: enlightenment_rtmedia_js_popup_toggle_controls,
        			threshold: 0,
                    // threshold: 200,
                    // triggerOnTouchEnd: true,
        		});

                if ( vw >= 992 ) {
                    $media.swipe('disable');
                }

                window.addEventListener('resize', function() {
					let vw = Math.max( document.documentElement.clientWidth || 0, window.innerWidth || 0 );

                    if ( vw < 992 ) {
                        $media.swipe('enable');
                    } else {
                        $media.swipe('disable');
                    }
                });

                // When modal is already open and nav arrows have been clicked
                if ( vw >= 992 && $ac_form.length && scroll.clientHeight < scroll.scrollHeight ) {
                    $meta.addClass('has-comment-form-affixed-to-bottom');
                }

                let ticking = false;

                function affixACFormToBottomOnWindowResize() {
                    if ( ticking ) {
						return true;
					}

                    requestAnimationFrame( affixACFormToBottom );

                    ticking = true;
                }
                window.addEventListener('resize', affixACFormToBottomOnWindowResize);

                function affixACFormToBottom() {
					let vw = Math.max( document.documentElement.clientWidth || 0, window.innerWidth || 0 );

                    if ( vw < 992 ) {
                        $meta.removeClass('has-comment-form-affixed-to-bottom');
                    } else if( $ac_form.length && scroll.clientHeight < scroll.scrollHeight ) {
                        $meta.addClass('has-comment-form-affixed-to-bottom');
                    }

                    ticking = false;
                }

                $('.rt-tooltip[data-bs-toggle="tooltip"]').tooltip();

                $ac_input.on('input', function() {
                    let $this = $(this),
					    vw    = Math.max( document.documentElement.clientWidth || 0, window.innerWidth || 0 );

                    if ( this.clientHeight < this.scrollHeight ) {
                        let height = this.scrollHeight < 164 ? this.scrollHeight : 164;

                        $this.css('height', height);

                        if ( this.scrollHeight >= 164 ) {
                            $this.css('overflow', 'visible');
                        } else {
                            $this.css('overflow', '');
                        }

                        if( vw < 992 ) {
                            if ( this.scrollHeight > 40 || $filelist.prop('clientHeight') ) {
                                let offset = height + $filelist.prop('clientHeight');

                                $comments_c.css('bottom', 'calc(1.5rem + ' + ( offset - 40 + 15 ) + 'px)');
                                $comments_l.css('max-height', 'calc(100vh - 11.375rem - ' + ( offset - 40 + 30 ) + 'px)');
                            }
                        } else {
                            if ( $meta.hasClass('has-comment-form-affixed-to-bottom') ) {
                                if ( this.scrollHeight > 40 || $filelist.prop('clientHeight') ) {
                                    let offset = height + $filelist.prop('clientHeight');

                                    scroll.style.minHeight = 'calc(100vh - 5.5rem - ' + offset + 'px)';
                                    scroll.style.maxHeight = 'calc(100vh - 5.5rem - ' + offset + 'px)';
                                }
                            } else {
                                if ( $ac_form.length && scroll.clientHeight < scroll.scrollHeight - this.scrollHeight ) {
                                    $meta.addClass('has-comment-form-affixed-to-bottom');

                                    if ( this.scrollHeight > 40 || $filelist.prop('clientHeight') ) {
                                        let offset = height + $filelist.prop('clientHeight');

                                        scroll.style.minHeight = 'calc(100vh - 5.5rem - ' + offset + 'px)';
                                        scroll.style.maxHeight = 'calc(100vh - 5.5rem - ' + offset + 'px)';
                                    }
                                }
                            }
                        }
                    } else {
                        while ( this.clientHeight >= this.scrollHeight ) {
                            $this.css('height', this.clientHeight - 22);
                        }

                        if ( this.scrollHeight > 40 ) {
                            $this.css('height', this.scrollHeight);
                        } else {
                            $this.css('height', '');
                        }

                        if ( vw < 992 ) {
                            $comments_c.css('bottom', '');
                            $comments_l.css('max-height', '');
                        } else {
                            if ( $ac_form.length && scroll.clientHeight < scroll.scrollHeight - this.scrollHeight ) {
                                $meta.addClass('has-comment-form-affixed-to-bottom');

                                if ( this.scrollHeight > 40 || $filelist.prop('clientHeight') ) {
                                    let height = this.scrollHeight + $filelist.prop('clientHeight');

                                    scroll.style.minHeight = 'calc(100vh - 5.5rem - ' + height + 'px)';
                                    scroll.style.maxHeight = 'calc(100vh - 5.5rem - ' + height + 'px)';
                                } else {
                                    scroll.style.minHeight = '';
                                    scroll.style.maxHeight = '';
                                }
                            }
                        }
                    }
                });

                $ac_input.on('keydown', function(event) {
                    if(event.keyCode === 13 && ! event.altKey && ! event.ctrlKey && ! event.shiftKey) {
                        event.preventDefault();
                        $('#rt_media_comment_submit').click();
                    }
                });

                rtMagnificPopup.on('mfpBeforeClose', function() {
                    setTimeout(function() {
                        $( $.magnificPopup.instance.wrap.get(0) ).modal('hide').modal('dispose');
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                        $('html').css('overflow', '');
                    }, 15);
                });

                return true;
            });

            rtMediaHook.register('rtmedia_js_upload_file', function() {
                setTimeout(function() {
                    $('.rt_alert_msg').attr('style', '').addClass('alert alert-danger');
                }, 15);

                return true;
            });

            rtMediaHook.register('rtmedia_js_before_activity_added', function() {
                setTimeout(function() {
                    $('.rt_alert_msg').attr('style', '').addClass('alert alert-danger');
                }, 15);

                return true;
            });

            $('#rtmedia_upload_terms_conditions').on('change', function() {
                $('.rt_alert_msg').attr('style', '').addClass('alert alert-danger d-block mt-2 mb-0');

                return true;
            });
        }
    });
})(jQuery);
