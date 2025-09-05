(function($) {
    var args = enlightenment_buddypress_args;

    if ( typeof GeminiScrollbar != 'undefined' ) {
    	window.addEventListener('load', function() {
            var subnav = document.querySelector('.bp-subnavs');

            if ( subnav != null ) {
                new GeminiScrollbar({
                    element: subnav,
                    autoshow: true,
                }).create();
            }

			var dropdowns = document.querySelectorAll('.bp-notifications .dropdown');

			dropdowns.forEach(function( dropdown ) {
				var element = dropdown.querySelector('.notifications-list');

				if ( element != null ) {
					let geminiSB;

	                dropdown.addEventListener('shown.bs.dropdown', function () {
						if ( typeof geminiSB != 'undefined' && geminiSB._created ) {
							return true;
						}

						element.style.height = '';
						element.style.height = element.offsetHeight + 'px';

						if ( typeof geminiSB == 'undefined' ) {
							geminiSB = new GeminiScrollbar({
								element:  element,
								autoshow: true,
							});
						}

						geminiSB.create();
					});

					dropdown.addEventListener('hidden.bs.dropdown', function () {
						if ( typeof geminiSB == 'undefined' || ! geminiSB._created ) {
							return true;
						}

						geminiSB.destroy();

						element.style.height = '';
					});
				}
			});
        });
    }

    $(document).ready(function() {
        let $headerLogin   = $('#header-account-login'),
            $primaryNav    = $('.component-navigation'),
            $secondaryNav  = $('.secondary-navigation.bp-navs'),
            $coverImage    = $('#header-cover-image');
            $userHandle    = $('.bp-user .user-nicename'),
            $formWrap      = $('#bp-nouveau-activity-form'),
            $aStream       = $('#buddypress [data-bp-list="activity"]'),
            $list          = $('#buddypress [data-bp-list]'),
            $settings      = $('#your-profile'),
            $invitesNav    = $('.bp-invites-nav'),
            $groupDesc     = $('.sidebar-bp-group .group-description'),
            $rolesFilter   = $('#group-roles-filter'),
            $gmListTable   = $('#group-members-list-table'),
            $signUpForm    = $('#signup-form'),
            $siteNotice    = $('#message'),
            $rtMediaUpl    = $('#rtmedia-upload-container'),
            $memberBlocks  = $('.bp-block-member'),
            $membersBlocks = $('.bp-block-members'),
            $groupBlocks   = $('.bp-block-group'),
            $groupsBlocks  = $('.bp-block-groups');

        $headerLogin.on('shown.bs.dropdown', function () {
            $('#user_login').focus();
        });

        if ( $primaryNav.length ) {
            let $items  = $primaryNav.children('.nav-item'),
                $active = $items.filter('.selected'),
                $links  = $items.children('.nav-link');

            if ( $active.length ) {
                $active.children('.nav-link').addClass('active');

                $links.on('click', function() {
                    let $this   = $(this),
                        $link   = $active.children('.nav-link'),
                        $parent = $this.parent();

                    $link.removeClass('active');
                    $this.addClass('active');

                    $active = $parent;
                });
            } else {
                function onPrimaryNavReady() {
                    setTimeout(function() {
                        $active = $items.filter('.selected');

                        if ( $active.length ) {
                            $active.children('.nav-link').addClass('active');

                            $links.on('click', function() {
                                let $this   = $(this),
                                    $link   = $active.children('.nav-link'),
                                    $parent = $this.parent();

                                $link.removeClass('active');
                                $this.addClass('active');

                                $active = $parent;
                            });
                        } else {
                            onPrimaryNavReady();
                        }
                    }, 150);
                }

                onPrimaryNavReady();
            }
        }

        if ( $secondaryNav.length ) {
            if( $secondaryNav.prop('clientHeight') < $secondaryNav.prop('scrollHeight') ) {
                let $menu = $secondaryNav.children('.nav');

                if( ! $('#bp-more-links').length ) {
                    $menu.append('<li id="bp-more-links" class="nav-item dropdown"><a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#bp-more-links">More</a><ul class="dropdown-menu dropdown-menu-end"></ul></li>');
                }

                $($menu.children().not('#bp-more-links').get().reverse()).each(function() {
                    if( $secondaryNav.prop('clientHeight') < $secondaryNav.prop('scrollHeight') ) {
                        $(this).prependTo('#bp-more-links > .dropdown-menu');
                    }
                });

                $('#bp-more-links .dropdown-menu .nav-item').each(function() {
                    $(this).removeClass('nav-item');
                    $(this).addClass('dropdown-submenu');
                });

                $('#bp-more-links .dropdown-menu .nav-link').each(function() {
                    $(this).removeClass('nav-link');
                    $(this).addClass('dropdown-item');
                });

                $('#bp-more-links .dropdown-menu .dropdown').each(function() {
                    $(this).removeClass('dropdown');
                    $(this).addClass('dropdown-submenu');
                });
            }

            $secondaryNav.css('overflow', 'visible');
        }

        if ( $userHandle.length ) {
            if ( window.innerWidth >= 768 && $coverImage.length && ! $userHandle.is( ':last-child' ) ) {
                let $next = $userHandle.next( '.member-header-actions' );

                if ( $next.length ) {
                    $userHandle.css( 'max-width', 'calc(100% - ' + $next.outerWidth( true ) + 'px' );
                }
            }

            let ticking = false;

            function userHandleResizeOnWindowResize() {
                if ( ! ticking ) {
                    requestAnimationFrame( userHandleResize );
                }

                ticking = true;
            }
            window.addEventListener('resize', userHandleResizeOnWindowResize);

            function userHandleResize() {
                if ( $coverImage.length && ! $userHandle.is( ':last-child' ) ) {
                    let $next = $userHandle.next( '.member-header-actions' );

                    if ( $next.length ) {
                        if ( window.innerWidth >= 768 ) {
                            $userHandle.css( 'max-width', 'calc(100% - ' + $next.outerWidth( true ) + 'px)' );
                        } else {
                            $userHandle.css( 'max-width', '' );
                        }
                    }
                }

                ticking = false;
            }
        }

        $('.bp-tooltip[data-bs-toggle="tooltip"]').tooltip();

        if ( $formWrap.length ) {
            let $whatsNew;

            function onWhatsNewReady() {
                setTimeout(function() {
                    $whatsNew = $('#whats-new');

                    if ( $whatsNew.length ) {
                        let $form = $('#whats-new-form');

                        function onWhatsNewFocusIn() {
                            $form.addClass('activity-form-expanded');

                            setTimeout(function() {
                                let $options  = $('#whats-new-options'),
                                    $submit   = $('#whats-new-submit'),
                                    $button   = $('#aw-whats-new-submit'),
                                    $select   = $options.find('select'),
                                    $spinner  = $('#whats-new-spinner'),
                                    $uploader = $form.children('.rtmedia-uploader-div');



                                $submit.addClass('d-flex').addClass('align-items-center');
                                $button.addClass('btn').addClass('btn-primary');

                                if ( $select.length && typeof $.fn.select2 != 'undefined' ) {
                                    $select.select2({
                                        minimumResultsForSearch: 33,
                                    });
                                }

                                $options.addClass('visible');

                                if ( ! $spinner.length ) {
                                    let $icon = $(document.createElement('i'));

                                    $spinner = $(document.createElement('div'));
                                    $spinner.attr('id', 'whats-new-spinner');
                                    $spinner.addClass('bp-whats-new-spinner').addClass('d-none').addClass('me-2');

                                    $icon.addClass('fas').addClass('fa-spinner').addClass('fa-pulse');
                                    $icon.appendTo($spinner);

                                    $spinner.prependTo(document.getElementById('whats-new-submit'));
                                }

                                if ( $uploader.length ) {
                                    $uploader.removeClass('d-none');
                                }
                            }, 15);
                        }

                        if ( $whatsNew.is(':focus') ) {
                            onWhatsNewFocusIn();
                        }

                        $whatsNew.on('focusin', onWhatsNewFocusIn);

                        $whatsNew.on('focusout', function() {
                            if ( '' !== $whatsNew.val() ) {
                                return true;
                            }

                            if( $form.find(':hover').length ) {
                                return true;
                            }

                            let $uploader = $form.children('.rtmedia-uploader-div');

                            if ( $uploader.length ) {
                                if ( $uploader.find('.plupload_filelist_content').children().length ) {
                                    return true;
                                }

                                if ( $form.children('.plupload_filelist_content').children().length ) {
                                    return true;
                                }

                                $uploader.addClass('d-none');
                            }

                            $('#whats-new-options').removeClass('visible');
                            $form.removeClass('activity-form-expanded');
                        });
                    } else {
                        onWhatsNewReady();
                    }
                }, 150);
            }

            onWhatsNewReady();

			$formWrap.find('.bp-tooltip[data-bs-toggle="tooltip"]').tooltip();
        }

        var did_bp_ajax_request = false;

        if ( $aStream.length ) {
            var $aList;

            window.onActivityStreamReady = function() {
                setTimeout(function() {
                    $aList = $aStream.children('.activity-list');

                    if ( $aList.length ) {
                        $aList.find('.bp-tooltip[data-bs-toggle="tooltip"]').tooltip();

                        let $ac_input = $aList.find('.ac-input');

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
                                $(this).closest('.ac-reply-content').find('input[name="ac_form_submit"]').click();
                            }
                        });
                    } else {
                        onActivityStreamReady();
                    }
                }, 150);
            }

            // Race with $list.on('bp_ajax_request', ...
            if ( ! did_bp_ajax_request ) {
                onActivityStreamReady();
            }
        }

        if ( $invitesNav.length ) {
            let $subnav;

            function onInvitesNavReady() {
                setTimeout(function() {
                    $subnav = $invitesNav.children('.subnav');

                    if ( $subnav.length ) {
                        let $items   = $subnav.children('li');
                            $current = $items.filter('.current').children('.btn'),
                            $buttons = $items.children('.btn');

                        $buttons.removeClass('btn-secondary').addClass('btn-outline-secondary');
                        $current.removeClass('btn-outline-secondary').addClass('btn-secondary');
                        $items.addClass('btn-group');
                        $subnav.addClass('list-unstyled').addClass('btn-group').addClass('mb-0');

                        $buttons.on('click', function() {
                            let $this    = $(this),
                                $current = $this.closest('.subnav').children('.current').children('.btn');;

                            $current.removeClass('btn-secondary').addClass('btn-outline-secondary');
                            $this.removeClass('btn-outline-secondary').addClass('btn-secondary');
                        });
                    } else {
                        onInvitesNavReady();
                    }
                }, 150);
            }

            onInvitesNavReady();
        }

        if ( $groupDesc.length ) {
            let groupDesc = $groupDesc.get(0);

            if( groupDesc.clientHeight < groupDesc.scrollHeight - 1 ) {
                let $toggle = $(document.createElement('div')),
                    $button = $(document.createElement('button'));

                $button.addClass('group-description-more');
                $button.text( args.group_description_more );
                $button.appendTo( $toggle );

                $button.on('click', function() {
                    if ( $button.hasClass('group-description-more') ) {
                        $button.removeClass('group-description-more');
                        $button.addClass('group-description-less');
                        $button.text( args.group_description_less );

                        $groupDesc.css('max-height', groupDesc.scrollHeight + 24);
                        $groupDesc.addClass('expanded');
                    } else {
                        $button.removeClass('group-description-less');
                        $button.addClass('group-description-more');
                        $button.text( args.group_description_more );

                        $groupDesc.css('max-height', '');
                        $groupDesc.removeClass('expanded');
                    }
                });

                $toggle.addClass('group-description-toggle');
                $toggle.prependTo( $groupDesc );
            }
        }

        if ( $rolesFilter.length ) {
            let $filter;

            function onRolesFilterReady() {
                setTimeout(function() {
                    $filter = $('#group-members-role-filter');

                    if ( $filter.length ) {
                        $rolesFilter.children('label').addClass('screen-reader-text').addClass('visually-hidden');
                    } else {
                        onRolesFilterReady();
                    }
                }, 15);
            }

            onRolesFilterReady();
        }

        if ( $gmListTable.length ) {
            let $buttons;

            function onGMListTableReady() {
                setTimeout(function() {
                    $buttons = $gmListTable.find('.btn[data-action="edit"]');

                    $buttons.addClass('js-added');

                    if ( $buttons.length ) {
                        $buttons.on('click', function() {
                            let $tr = $(this).closest('tr');

                            function onBtnsClick() {
                                let $group  = $tr.children('.urole-column').children('.group-member-edit'),
									$label  = $group.children('label'),
                                    $select = $group.children('select'),
                                    $abort  = $tr.children('.uname-column').find('.btn[data-action="abort"]');

								$label.addClass('form-label');
                                $select.addClass('form-select');
                                $group.addClass('mb-3');

                                if ( typeof $.fn.select2 != 'undefined' ) {
                                    $select.select2({
                                        minimumResultsForSearch: 33,
                                    });
                                }

                                $abort.on('click', function() {
                                    let $tr = $(this).closest('tr');

                                    setTimeout(function() {
                                        let $button = $tr.find('.btn[data-action="edit"]');

                                        $button.addClass('js-added');

                                        $button.on('click', function() {
                                            let $tr = $(this).closest('tr');

                                            setTimeout(onBtnsClick, 15);
                                        });
                                    }, 15);
                                });
                            }

                            setTimeout(onBtnsClick, 15);
                        });
                    } else {
                        onGMListTableReady();
                    }
                }, 15);
            }

            onGMListTableReady();
        }

        $list.on('bp_ajax_request', function(event, data) {
            setTimeout(function() {
                switch( data.object ) {
                    case 'activity':
                        /*let $aList;

                        // Race with onActivityStreamReady() ...
                        setTimeout(function() {
                            $aList = $aStream.children('.activity-list');

                            if ( $aList.length ) {
                                $list.find('.bp-tooltip[data-bs-toggle="tooltip"]').tooltip();

                                let $ac_input = $list.find('.ac-input');

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
                                        $(this).closest('.ac-reply-content').find('input[name="ac_form_submit"]').click();
                                    }
                                });
                            } else {
                                onActivityStreamReady();
                            }
                        }, 150);*/

                        // Race with sync call further above
                        if ( $aStream.length ) {
                            onActivityStreamReady();
                        }

                        break;

                    case 'members':
                        switch (data.template) {
                            case 'member_notifications':
                                $('.bp-tooltip[data-bs-toggle="tooltip"]').tooltip();
                                $('.mark-read[data-bs-toggle="tooltip"]').tooltip();

                                if ( typeof $.fn.select2 != 'undefined' ) {
                                    $('#notification-select').select2({
                                        minimumResultsForSearch: 33,
                                    });
                                }

                                break;
                        }

                        break;
                }

                did_bp_ajax_request = true;
            }, 15);
        });

        $( document ).ajaxSend(function( event, xhr, settings ) {
            // console.log(event);
            // console.log(xhr);
            // console.log(settings);
            if ( typeof settings.data == 'undefined' ) {
                return true;
            }

            let data   = new URLSearchParams(settings.data),
                action = data.get('action');

            if ( typeof action == 'undefined' ) {
                return true;
            }

            switch( action ) {
                case 'post_update':
                    $('#whats-new-form').addClass('loading');
                    $('#whats-new-spinner').removeClass('d-none').addClass('d-flex');
                    break;

                case 'delete_activity':
                    if ( 'true' === data.get('is_comment') ) {
                        $('#acomment-' + data.get('id')).addClass('loading');
                    } else {
                        $('#activity-' + data.get('id')).addClass('loading');
                    }
                    break;

                case 'messages_get_thread_messages':
                case 'messages_dismiss_sitewide_notice':
                    $('.tooltip').remove();
                    break;

                case 'messages_get_user_message_threads':
                    let box      = data.get('box'),
                        $subnav  = $('.subnav'),
                        $current = $subnav.children('[data-bp-user-scope="' + box + '"]').children('.btn'),
                        $buttons = $subnav.find('.btn');

                    $buttons.removeClass('btn-secondary').addClass('btn-outline-secondary');
                    $current.removeClass('btn-outline-secondary').addClass('btn-secondary');
                    break;

                case 'groups_get_group_potential_invites':
                    $('.tooltip').remove();
                    break;
            }
        });

        $( document ).ajaxComplete(function( event, xhr, settings ) {
            // console.log(event);
            // console.log(xhr);
            // console.log(settings);
            if ( typeof settings.data == 'undefined' ) {
                return true;
            }

            let data   = new URLSearchParams( settings.data ),
                action = data.get('action');

            if ( typeof action == 'undefined' ) {
                return true;
            }

            switch( action ) {
                case 'post_update':
                    $('#whats-new-form').removeClass('loading');
                    $('#whats-new-spinner').removeClass('d-flex').addClass('d-none');
                    break;

                case 'delete_activity':
                    setTimeout(function() {
                        if ( 'true' === data.get('is_comment') ) {
                            $('#acomment-' + data.get('id')).removeClass('loading');
                        } else {
                            $('#activity-' + data.get('id')).removeClass('loading');
                        }
                    }, 315);

                    if ( typeof xhr.responseJSON != 'undefined' && xhr.responseJSON.success === false ) {
                        setTimeout(function() {
                            $('.bp-feedback.bp-messages.error').addClass('alert alert-danger');
                        }, 15);
                    }

                    break;

                case 'promote':
                case 'demote':
                    setTimeout(function() {
                        $button = $('#group-members-list-table').find('.btn[data-action="edit"]:not(.js-added)');

                        $button.addClass('js-added');

                        $button.on('click', function() {
                            let $tr = $(this).closest('tr');

                            function onBtnsClick() {
                                let $group  = $tr.children('.urole-column').children('.group-member-edit'),
                                    $select = $group.children('select'),
                                    $abort  = $tr.children('.uname-column').find('.btn[data-action="abort"]');

                                $select.addClass('form-control');
                                $group.addClass('mb-3');

                                if ( typeof $.fn.select2 != 'undefined' ) {
                                    $select.select2({
                                        minimumResultsForSearch: 33,
                                    });
                                }

                                $abort.on('click', function() {
                                    let $tr = $(this).closest('tr');

                                    setTimeout(function() {
                                        let $button = $tr.find('.btn[data-action="edit"]');

                                        $button.addClass('js-added');

                                        $button.on('click', function() {
                                            let $tr = $(this).closest('tr');

                                            setTimeout(onBtnsClick, 15);
                                        });
                                    }, 15);
                                });
                            }

                            setTimeout(onBtnsClick, 15);
                        });
                    }, 15);
                    break;
            }
        });

        $( document ).ajaxSuccess(function( event, xhr, settings ) {
            // console.log(event);
            // console.log(xhr);
            // console.log(settings);
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
                    case 'friends_add_friend':
                    case 'friends_withdraw_friendship':
                    case 'friends_remove_friend':
                        setTimeout(function() {
                            let $wrapper  = $('.member-header-actions').find('.generic-button.friendship-button'),
                                $button   = $wrapper.children('.friendship-button'),
                                $dropdown = $wrapper.closest('.dropdown');

                            if ( $dropdown.length ) {
                                $button.addClass('btn').addClass('dropdown-item');
                            } else {
                                $wrapper.addClass('btn-group');
                                $button.addClass('btn').addClass('btn-primary').addClass('btn-lg');
                            }
                        }, 15);

                        break;

                    case 'activity_filter':
                    case 'post_update':
                        setTimeout(function() {
                            let items  = ( 'activity_filter' == action ? response.data.contents : response.data.activity ),
                            // let items  = response.data.activity,
                                $items = $($.parseHTML(items)).filter('.activity-item'),
                                ids    = [];

                            $items.each(function() {
                                ids.push('#' + this.id);
                            });

                            let $ac_input = $( ids.join(', ') ).find('.ac-input');

                            $ac_input.on('input', function() {
                                $this = $(this);

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
                                    $(this).closest('.ac-reply-content').find('input[name="ac_form_submit"]').click();
                                }
                            });
                        }, 150);

                        break;

                    case 'new_activity_comment':
                        // Sync with form.fadeOut( 200, ...
                        setTimeout(function() {
                            let $element   = $( $.trim( response.data.contents ) ),
                                comment_id = $element.attr('id'),
                                $comment   = $('#' + comment_id),
                                $list      = $comment.parent(),
                                $parent    = $list.parent('.comment-item');

                            if ( $parent.length ) {
                                console.log($parent);
                                let $container = $parent.children('.d-flex').children('.flex-grow-1'),
                                    $replies   = $container.children('.activity-comments-list'),
                                    $ac_input  = $parent.find('.ac-input');

                                if ( ! $replies.length ) {
                                    $replies = $( document.createElement('ul') );
                                    $replies.addClass('activity-comments-list');
                                    $replies.addClass('list-unstyled');
                                    $replies.addClass('mb-0');
                                    $replies.appendTo($container);
                                }

                                $comment.appendTo($replies);
                                $list.remove();

                                $ac_input.css('height', '');
                            } else {
                                let $ac_input = $comment.closest('.activity-comments').find('.ac-input');

                                if ( ! $list.hasClass('activity-comments-list') ) {
                                    $list.addClass('activity-comments-list');
                                    $list.addClass('list-unstyled');
                                    $list.addClass('mb-0');
                                }

                                $ac_input.css('height', '');
                            }
                        }, 215);

                        break;

                    case 'bp_avatar_set':
                        setTimeout(function() {
                            let $item = $('.avatar-nav-item:not(.nav-item)'),
                                $link = $item.children();

                            $item.addClass('nav-item');

                            $link.on('click', function() {
                                let $this = $(this);

                                $this.parent().parent().find('.active').removeClass('active');
                                $this.addClass('active');
                            });
                        }, 15);

                        break;

                    case 'messages_get_user_message_threads':
                        setTimeout(function() {
                            let $filters    = $('.bp-messages-filters').children('ul'),
                                $pagination = $filters.children('.last.filter');

                            if ( typeof $.fn.select2 != 'undefined' ) {
                                $('#user-messages-bulk-actions').select2({
                                    minimumResultsForSearch: 33,
                                });
                            }

                            $filters.addClass('row').addClass('align-items-center').addClass('list-unstyled').addClass('mb-0');
                            $pagination.addClass('col-md flex-md-grow-0 mt-3 mt-md-0');

                            $('#message-threads').addClass('list-unstyled');

                            $('.bp-tooltip[data-bs-toggle="tooltip"]').tooltip();
                        }, 15);

                        break;

                    case 'messages_get_thread_messages':
                        setTimeout(function() {
                            $('.bp-tooltip[data-bs-toggle="tooltip"]').tooltip();
                        }, 15);
                        break;

                    case 'messages_star':
                    case 'messages_unstar':
                        setTimeout(function() {
                            $('.tooltip').remove();
                            $('.bp-tooltip[data-bs-toggle="tooltip"]').tooltip();
                        }, 15);
                        break;

                    case 'messages_delete':
                        setTimeout(function() {
                            $('.tooltip').remove();
                        }, 15);
                        break;

                    case 'groups_get_group_potential_invites':
                        setTimeout(function() {
                            let $list;

                            function onInviteMembersListReady() {
                                $list = $('#members-list');

                                if ( $list.length ) {
                                    let $items   = $list.children('li'),
                                        $buttons = $items.find('.button');

                                    $items.addClass('col-sm-6 col-md-4');
                                    $list.addClass('list-unstyled').addClass('row');

                                    $('.bp-tooltip[data-bs-toggle="tooltip"]').tooltip();

                                    function onBtnsClick() {
                                        $('.tooltip').remove();

                                        setTimeout(function() {
                                            let $item    = $list.children('li:not(.col-sm-6)');
                                                $buttons = $item.find('.button');

                                            $item.addClass('col-sm-6 col-md-4');

                                            $item.find('.bp-tooltip[data-bs-toggle="tooltip"]').tooltip();

                                            $buttons.on('click', onBtnsClick);
                                        }, 0);

                                        setTimeout(function() {
                                            let $subnav  = $invitesNav.children('.subnav'),
                                                $items   = $subnav.children('li'),
                                                $dynamic = $items.filter('.dynamic, .pending'),
                                                $button  = $dynamic.children('.btn');

                                            $dynamic.addClass('btn-group');

                                            $button.on('click', function() {
                                                let $this    = $(this),
                                                    $current = $this.closest('.subnav').children('.current').children('.btn');

                                                $current.removeClass('btn-secondary').addClass('btn-outline-secondary');
                                                $this.removeClass('btn-outline-secondary').addClass('btn-secondary');

                                                setTimeout(function() {
                                                    let $invites  = $('#send-invites-editor'),
                                                        $list     = $invites.children('ul'),
                                                        $items    = $list.children('li'),
                                                        $tooltips = $items.children('.bp-tooltip[data-bs-toggle="tooltip"]');

                                                    $list.addClass('list-unstyled row g-1');

                                                    $items.addClass('col flex-grow-0 flex-shrink-1');

                                                    $tooltips.addClass('d-block');
                                                    $tooltips.tooltip();

                                                    $tooltips.on('click', function() {
                                                        $('.tooltip').remove();
                                                    });
                                                }, 15);
                                            });
                                        }, 15);
                                    }

                                    $buttons.on('click', onBtnsClick);
                                } else {
                                    setTimeout(onInviteMembersListReady, 150);
                                }
                            }

                            onInviteMembersListReady();
                        }, 15);
                        break;

                    case 'groups_send_group_invites':
                        setTimeout(function() {
                            let $subnav  = $invitesNav.children('.subnav'),
                                $items   = $subnav.children('li'),
                                $dynamic = $items.filter('.dynamic, .pending'),
                                $button  = $dynamic.children('.btn');

                            $dynamic.addClass('btn-group');

                            $button.on('click', function() {
                                let $this    = $(this),
                                    $current = $this.closest('.subnav').children('.current').children('.btn');

                                $current.removeClass('btn-secondary').addClass('btn-outline-secondary');
                                $this.removeClass('btn-outline-secondary').addClass('btn-secondary');

                                setTimeout(function() {
                                    let $invites = $('#send-invites-editor'),
                                        $list    = $invites.children('ul'),
                                        $items   = $list.children('li:not(:first-child)');

                                    $list.addClass('list-unstyled form-row');

                                    $items.addClass('col flex-grow-0 flex-shrink-1 py-1');
                                }, 15);
                            });
                        }, 15);
                        break;
                }
            }, 15);
        });

        $( document ).ajaxError(function( event, xhr, settings ) {
            // console.log(event);
            // console.log(xhr);
            // console.log(settings);
            if ( typeof settings.data == 'undefined' ) {
                return true;
            }

            let data   = new URLSearchParams( settings.data ),
                action = data.get('action');

            if ( typeof action == 'undefined' ) {
                return true;
            }

            setTimeout(function() {
                switch( action ) {
                    case 'post_update':
                        setTimeout(function() {
                            let $alert = $('#message'),
                                $icon  = $alert.children('.bp-icon'),
                                $text  = $alert.children('p'),
                                type   = $alert.attr('class').replace('bp-messages bp-feedback ', ''),
                                alertClass, iconClass;

                            switch ( type ) {
                                case 'error':
                                    alertClass = 'alert-danger';
                                    iconClass = 'fa-times-circle';
                                    break;

                                case 'updated':
                                    alertClass = 'alert-success';
                                    iconClass = 'fa-check-circle';
                                    break;

                                case 'info':
                                default:
                                    alertClass = 'alert-info';
                                    iconClass = 'fa-info-circle';
                                    break;
                            }

                            $alert.addClass('alert').addClass(alertClass).addClass('d-flex').addClass('w-100').addClass('mt-3').addClass('mb-0');
                            $icon.addClass('fas').addClass(iconClass).addClass('mt-1').addClass('me-2');
                            $text.addClass('mb-0');
                        }, 15);

                        break;
                }
            }, 15);
        });

        $('.bp-user.messages .bp-subnavs .btn').on('click', function(event) {
            let $this    = $(this),
                $buttons = $this.parent().parent().find('.btn');

            $buttons.removeClass('btn-secondary').addClass('btn-outline-secondary');
            $this.removeClass('btn-outline-secondary').addClass('btn-secondary');
        });

        $('.item-options.dropdown a').on('click', function(event) {
            let $this   = $(this),
                $parent = $this.parent(),
                $toggle = $parent.prev(),
                $items  = $parent.children(),
                label   = $this.text();

            $items.removeClass('active');
            $this.addClass('active');
            $toggle.text( label );
            $toggle.dropdown('toggle');
        });

        if ( typeof BP_Uploader != 'undefined' ) {
            let $chgImage = $('.profile.change-avatar, .profile.change-cover-image, #group-create-body, .group-admin.group-avatar #group-settings-form');

            if ( $chgImage.length ) {
                let $avatarNav = $chgImage.children('.bp-avatar-nav'),
                    $uploader  = $chgImage.children('.bp-avatar, .bp-cover-image'),
                    $avatarNavItems, $uploaderArea;

                if ( $avatarNav.length ) {
                    function onAvatarNavReady() {
                        setTimeout(function() {
                            $avatarNavItems = $avatarNav.children('.avatar-nav-items');

                            if ( $avatarNavItems.length ) {
                                let $items = $avatarNavItems.children(),
                                    $links = $items.children();

                                $items.addClass('nav-item');

                                $avatarNavItems.children('.current').children().addClass('active');

                                $links.on('click', function() {
                                    $links.removeClass('active');
                                    $(this).addClass('active');
                                });

                                $('#bp-avatar-upload a').on('click', function() {
                                    setTimeout(onBPUploaderReady, 15);
                                });

                                $avatarNavItems.addClass('nav').addClass('nav-tabs').addClass('mb-3');
                            } else {
                                onAvatarNavReady();
                            }
                        }, 150);
                    }

                    onAvatarNavReady();
                }

                function onBPUploaderReady() {
                    setTimeout(function() {
                        $uploaderArea = $uploader.children('.bp-uploader-window');

                        if ( $uploaderArea.length ) {
                            let container     = $( '#' + BP_Uploader.settings.defaults.container ),
                                drop_element  = $( '#' + BP_Uploader.settings.defaults.drop_element ),
                                browse_button = $( '#' + BP_Uploader.settings.defaults.browse_button );

                            if ( container.hasClass( 'drag-drop' ) ) {
                                drop_element.on( 'dragover.wp-uploader', function() {
									console.log('dragover')
                                    drop_element.removeClass('bg-body-secondary');
                                    drop_element.addClass('bg-primary');
                                    drop_element.addClass('text-light');
                                    browse_button.removeClass('btn-primary');
                                    browse_button.addClass('btn-light');
                                } ).on( 'dragleave.wp-uploader, drop.wp-uploader', function() {
                                    drop_element.removeClass('bg-primary');
                                    drop_element.removeClass('text-light');
                                    drop_element.addClass('bg-body-secondary');
                                    browse_button.removeClass('btn-light');
                                    browse_button.addClass('btn-primary');
                                } );
                            }
                        } else {
                            onBPUploaderReady();
                        }
                    }, 150);
                }
                onBPUploaderReady();
            }
        }

        let $messagesFilters = $('.bp-messages-filters');

        if ( $messagesFilters.length ) {
            let $filters;

            function onMessagesFiltersReady() {
                setTimeout(function() {
                    $filters = $messagesFilters.children('ul');
                    let $pagination = $filters.children('.last.filter');

                    if ( $filters.length ) {
                        if ( typeof $.fn.select2 != 'undefined' ) {
                            $('#user-messages-bulk-actions').select2({
                                minimumResultsForSearch: 33,
                            });
                        }

                        $filters.addClass('row').addClass('align-items-center').addClass('list-unstyled').addClass('mb-0');
                        $pagination.addClass('col-md flex-md-grow-0 mt-3 mt-md-0');

                        $('.bp-tooltip[data-bs-toggle="tooltip"]').tooltip();
                    } else {
                        onMessagesFiltersReady();
                    }
                }, 150);
            }

            onMessagesFiltersReady();
        }

        let $messagesContent = $('.bp-messages-content');

        if ( $messagesFilters.length ) {
            let $messageThreads;

            function onMessageThreadsReady() {
                setTimeout(function() {
                    $messageThreads = $('#message-threads');

                    if ( $messageThreads.length ) {
                        $messageThreads.addClass('list-unstyled');
                    } else {
                        onMessageThreadsReady();
                    }
                }, 150);
            }

            onMessageThreadsReady();
        }

        if ( $settings.length ) {
            $settings.children('.info').show();
            $settings.find('.wp-cancel-pw').show();
            $settings.children('.pw-weak').hide();

            $settings.find('.wp-hide-pw').on('click', function() {
                let $icon = $(this).children('.far');

                if ( $icon.hasClass('fa-eye-slash') ) {
                    $icon.removeClass('fa-eye-slash');
                    $icon.addClass('fa-eye');
                } else {
                    $icon.removeClass('fa-eye');
                    $icon.addClass('fa-eye-slash');
                }
            });
        }

        if ( $signUpForm.length ) {
            $signUpForm.find('.wp-hide-pw').on('click', function() {
                let $icon = $(this).children('.far');

                if ( $icon.hasClass('fa-eye-slash') ) {
                    $icon.removeClass('fa-eye-slash');
                    $icon.addClass('fa-eye');
                } else {
                    $icon.removeClass('fa-eye');
                    $icon.addClass('fa-eye-slash');
                }
            });

			$signUpForm.find('.field-visibility-settings-toggle').each(function() {
				var $this       = $(this),
					$srText     = $(this).children('.current-visibility-level'),
					$toggleText = $(this).find('.visibility-toggle-link').children('span[aria-hidden="true"]'),
					$inputs     = $(this).find('.field-visibility-settings').find('input[type="radio"]');

				$inputs.on('change', function() {
					var text = $(this).next('.form-check-label').children('.field-visibility-text').text();

					$srText.text(text);
					$toggleText.text(text);
				});
			});
        }

        if ( $siteNotice.length ) {
            $siteNotice.find('.bp-tooltip[data-bs-toggle="tooltip"]').tooltip();

            $siteNotice.toast({
                autohide: false,
            });

            if ( typeof BP_Nouveau != 'undefined' ) {
                $siteNotice.on('hide.bs.toast', function () {
                    $.post(BP_Nouveau.ajaxurl, {
                        action : 'messages_dismiss_sitewide_notice',
                        nonce  : BP_Nouveau.nonces.messages,
                    });
                });
            }

            $siteNotice.toast('show');
        }

        if ( $memberBlocks.length ) {
            if ( window.innerWidth >= 768 ) {
                $memberBlocks.each(function() {
                    var $this = $(this);

                    if ( $this.hasClass( 'has-cover' ) && $this.hasClass( 'avatar-full' ) ) {
                        let $handle = $this.find( '.user-nicename' );

                        if ( ! $handle.is( ':last-child' ) ) {
                            let $next = $handle.next( '.bp-profile-button' );

                            if ( $next.length ) {
                                $handle.css( 'max-width', 'calc(100% - ' + $next.outerWidth( true ) + 'px' );
                            }
                        }
                    }
                });
            }

            let ticking = false;

            function memberBlocksOnWindowResize() {
                if ( ! ticking ) {
                    requestAnimationFrame( memberBlocksResize );
                }

                ticking = true;
            }
            window.addEventListener('resize', memberBlocksOnWindowResize);

            function memberBlocksResize() {
                $memberBlocks.each(function() {
                    var $this = $(this);

                    if ( $this.hasClass( 'has-cover' ) && $this.hasClass( 'avatar-full' ) ) {
                        let $handle = $this.find( '.user-nicename' );

                        if ( ! $handle.is( ':last-child' ) ) {
                            let $next = $handle.next( '.bp-profile-button' );

                            if ( $next.length ) {
                                if ( window.innerWidth >= 768 ) {
                                    $handle.css( 'max-width', 'calc(100% - ' + $next.outerWidth( true ) + 'px' );
                                } else {
                                    $handle.css( 'max-width', '' );
                                }
                            }
                        }
                    }
                });

                ticking = false;
            }
        }

        if ( $membersBlocks.length ) {
            if ( window.innerWidth >= 768 ) {
                $membersBlocks.each(function() {
                    var $this = $(this);

                    if ( ! $this.hasClass( 'is-grid' ) && $this.hasClass( 'avatar-full' ) ) {
                        let $members = $membersBlocks.children('.member-content');

                        $members.each(function() {
                            var $member = $(this);

                            if ( $member.hasClass( 'has-activity' ) ) {
                                let $activity = $member.find('.wp-block-quote'),
                                    $p        = $activity.children('p'),
                                    $first    = $( $p.get(0) );

                                if(
                                    $p.length > 1 ||
                                    $activity.prop('clientHeight') < $activity.prop('scrollHeight') ||
                                    $first.prop('clientHeight') < $first.prop('scrollHeight')
                                ) {
                                    $activity.addClass('faded');
                                }
                            }
                        });
                    }
                });
            }

            let ticking = false;

            function membersBlocksOnWindowResize() {
                if ( ! ticking ) {
                    requestAnimationFrame( membersBlocksResize );
                }

                ticking = true;
            }
            window.addEventListener('resize', membersBlocksOnWindowResize);

            function membersBlocksResize() {
                $membersBlocks.each(function() {
                    var $this = $(this);

                    if ( ! $this.hasClass( 'is-grid' ) && $this.hasClass( 'avatar-full' ) ) {
                        let $members = $membersBlocks.children('.member-content');

                        $members.each(function() {
                            var $member = $(this);

                            if ( $member.hasClass( 'has-activity' ) ) {
                                let $activity = $member.find('.wp-block-quote'),
                                    $p        = $activity.children('p'),
                                    $first    = $( $p.get(0) );

                                if ( window.innerWidth >= 768 ) {
                                    if(
                                        $p.length > 1 ||
                                        $activity.prop('clientHeight') < $activity.prop('scrollHeight') ||
                                        $first.prop('clientHeight') < $first.prop('scrollHeight')
                                    ) {
                                        $activity.addClass('faded');
                                    }
                                } else {
                                    $activity.removeClass('faded');
                                }
                            }
                        });
                    }
                });

                ticking = false;
            }
        }

        if ( $groupBlocks.length ) {
            if ( window.innerWidth >= 768 ) {
                $groupBlocks.each(function() {
                    var $this = $(this);

                    if ( $this.hasClass( 'has-description' ) && $this.hasClass( 'avatar-full' ) ) {
                        let $desc  = $this.find( '.group-description-content' ),
                            $p     = $desc.children('p'),
                            $first = $( $p.get(0) ),
                            height;

                        if ( $desc.is( ':last-child' ) ) {
                            height = $this.hasClass( 'has-cover' ) ? 96 : 144;
                        } else {
                            height = $this.hasClass( 'has-cover' ) ? 48 : 96;
                        }

                        if ( $p.length > 1 || $first.outerHeight() > height ) {
                            $desc.addClass( 'faded' );
                        } else {
                            $desc.removeClass( 'faded' );
                        }
                    }
                });
            }

            let ticking = false;

            function groupBlocksOnWindowResize() {
                if ( ! ticking ) {
                    requestAnimationFrame( groupBlocksResize );
                }

                ticking = true;
            }
            window.addEventListener('resize', groupBlocksOnWindowResize);

            function groupBlocksResize() {
                $groupBlocks.each(function() {
                    var $this = $(this);

                    if ( $this.hasClass( 'has-description' ) && $this.hasClass( 'has-cover' ) && $this.hasClass( 'avatar-full' ) ) {
                        let $desc = $this.find( '.group-description-content' );

                        if ( window.innerWidth >= 768 ) {
                            let $p     = $desc.children('p'),
                                $first = $( $p.get(0) ),
                                height;

                            if ( $desc.is( ':last-child' ) ) {
                                height = $this.hasClass( 'has-cover' ) ? 96 : 144;
                            } else {
                                height = $this.hasClass( 'has-cover' ) ? 48 : 96;
                            }

                            if ( $p.length > 1 || $first.outerHeight() > height ) {
                                $desc.addClass( 'faded' );
                            } else {
                                $desc.removeClass( 'faded' );
                            }
                        } else {
                            $desc.removeClass( 'faded' );
                        }
                    }
                });

                ticking = false;
            }
        }

        if ( $groupsBlocks.length ) {
            if ( window.innerWidth >= 768 ) {
                $groupsBlocks.each(function() {
                    var $this = $(this);

                    if ( ! $this.hasClass( 'is-grid' ) && $this.hasClass( 'avatar-full' ) ) {
                        let $groups = $groupsBlocks.children('.group-content');

                        $groups.each(function() {
                            var $group = $(this);

                            if ( $group.hasClass( 'has-description' ) ) {
                                let $desc = $group.find('.group-description-content');

                                if( $desc.prop('clientHeight') < $desc.prop('scrollHeight') ) {
                                    $desc.addClass('faded');
                                }
                            }
                        });
                    }
                });
            }

            let ticking = false;

            function groupsBlocksOnWindowResize() {
                if ( ! ticking ) {
                    requestAnimationFrame( groupsBlocksResize );
                }

                ticking = true;
            }
            window.addEventListener('resize', groupsBlocksOnWindowResize);

            function groupsBlocksResize() {
                $groupsBlocks.each(function() {
                    var $this = $(this);

                    if ( ! $this.hasClass( 'is-grid' ) && $this.hasClass( 'avatar-full' ) ) {
                        let $groups = $groupsBlocks.children('.group-content');

                        $groups.each(function() {
                            var $group = $(this);

                            if ( $group.hasClass( 'has-description' ) ) {
                                let $desc = $group.find('.group-description-content');

                                if ( window.innerWidth >= 768 ) {
                                    if( $desc.prop('clientHeight') < $desc.prop('scrollHeight') ) {
                                        $desc.addClass('faded');
                                    }
                                } else {
                                    $desc.removeClass('faded');
                                }
                            }
                        });
                    }
                });

                ticking = false;
            }
        }
    });
})(jQuery);
