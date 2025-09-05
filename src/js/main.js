(function($) {
    var args = enlightenment_main_args;

	(function() {
		if ( 'auto' != args.navbar_nav_separate ) {
			return;
		}

		var navbar = document.getElementById('masthead');

		if ( ! navbar ) {
			return;
		}

		var menuWrap  = document.getElementById('menu-primary-container'),
			navbarNav = document.getElementById('site-navigation'),
			geminiSB;

		function separateNavbarNav() {
			if (
				! navbar.classList.contains( 'navbar-nav-wrap' )
				&&
				typeof GeminiScrollbar != 'undefined'
				&&
				typeof geminiSB != 'undefined'
			) {
				geminiSB.destroy();
			}

			var vw = Math.max( document.documentElement.clientWidth || 0, window.innerWidth || 0 );

			if ( vw < 992 ) {
				return true;
			}

			var bp = 999999;

			if ( navbar.classList.contains('navbar-expand-xl') ) {
				bp = 1200;
			} else if ( navbar.classList.contains('navbar-expand-lg') ) {
				bp = 992;
			} else if ( navbar.classList.contains('navbar-expand-md') ) {
				bp = 768;
			} else if ( navbar.classList.contains('navbar-expand-sm') ) {
				bp = 576;
			} else if ( navbar.classList.contains('navbar-expand') ) {
				bp = 0;
			}

			if ( vw < bp ) {
				return true;
			}

			navbar.classList.remove('navbar-nav-auto');
			navbar.classList.remove('navbar-nav-separate');
			document.body.classList.remove('has-navbar-nav-auto');
			document.body.classList.remove('has-navbar-nav-separate');

			var navbarHeight =  64;

			if ( ! navbar.classList.contains('is-navbar-scroll') ) {
				if ( navbar.classList.contains('navbar-xl') ) {
					navbarHeight = 192;
				} else if ( navbar.classList.contains('navbar-lg') ) {
					navbarHeight = 128;
				} else if ( navbar.classList.contains('navbar-md') ) {
					navbarHeight =  80;
				}
			}

			if ( navbar.offsetHeight > navbarHeight ) {
				navbar.classList.add('navbar-nav-separate');
				document.body.classList.add('has-navbar-nav-separate');

				if ( ! navbar.classList.contains( 'navbar-nav-wrap' ) && typeof GeminiScrollbar != 'undefined' ) {
					if ( typeof geminiSB == 'undefined' ) {
						geminiSB = new GeminiScrollbar({
							element:  menuWrap,
							autoshow: true,
						}).create();
					} else {
						if ( ! geminiSB._created ) {
							geminiSB.create();
						} else {
							geminiSB.update();
						}
					}
			    }
			}
		}

		function hookSeparateNavbarNav() {
			requestAnimationFrame( separateNavbarNav );
		}
        window.addEventListener( 'load',   hookSeparateNavbarNav );
		window.addEventListener( 'resize', hookSeparateNavbarNav );
	})();

	(function() {
		if ( typeof GeminiScrollbar == 'undefined' ) {
			return;
		}

		if ( 'always' != args.navbar_nav_separate ) {
			return;
		}

		var navbar = document.getElementById('masthead');

		if ( ! navbar ) {
			return;
		}

		if ( navbar.classList.contains( 'navbar-nav-wrap' ) ) {
			return;
		}

    	window.addEventListener('load', function() {
            var element = document.getElementById('menu-primary-container');

			if ( element != null ) {
                new GeminiScrollbar({
                    element:  element,
                    autoshow: true,
                }).create();
            }
        });
	})();

	(function() {
		if ( typeof GeminiScrollbar == 'undefined' ) {
			return;
		}

		var navbar = document.getElementById('masthead');

		if ( ! navbar ) {
			return;
		}

    	window.addEventListener('load', function() {
			var navbarNav = document.getElementById('menu-primary-container');

			if ( ! navbarNav ) {
				return true;
			}

			var megaMenus = navbarNav.querySelectorAll('.mega-menu');

			megaMenus.forEach(function( megaMenu ) {
				var element = megaMenu.querySelector('.dropdown-menu');

				if ( element != null ) {
					let geminiSB;

	                megaMenu.addEventListener('shown.bs.dropdown', function () {
						if ( typeof geminiSB != 'undefined' && geminiSB._created ) {
							return true;
						}

						var vw = Math.max( document.documentElement.clientWidth || 0, window.innerWidth || 0 );

						if ( vw < 992 ) {
							return true;
						}

						var bp = 999999;

						if ( navbar.classList.contains('navbar-expand-xl') ) {
							bp = 1200;
						} else if ( navbar.classList.contains('navbar-expand-lg') ) {
							bp = 992;
						} else if ( navbar.classList.contains('navbar-expand-md') ) {
							bp = 768;
						} else if ( navbar.classList.contains('navbar-expand-sm') ) {
							bp = 576;
						} else if ( navbar.classList.contains('navbar-expand') ) {
							bp = 0;
						}

						if ( vw < bp ) {
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

					megaMenu.addEventListener('hidden.bs.dropdown', function () {
						if ( typeof geminiSB == 'undefined' || ! geminiSB._created ) {
							return true;
						}

						geminiSB.destroy();

						element.style.height = '';
					});

					window.addEventListener('resize', function() {
						var vw = Math.max( document.documentElement.clientWidth || 0, window.innerWidth || 0 );

						if ( vw < 992 ) {
							if ( typeof geminiSB != 'undefined' && geminiSB._created ) {
								geminiSB.destroy();

								element.style.height = '';
							}

							return true;
						}

						if ( ! element.classList.contains('show') ) {
							return true;
						}

						var bp = 999999;

						if ( navbar.classList.contains('navbar-expand-xl') ) {
							bp = 1200;
						} else if ( navbar.classList.contains('navbar-expand-lg') ) {
							bp = 992;
						} else if ( navbar.classList.contains('navbar-expand-md') ) {
							bp = 768;
						} else if ( navbar.classList.contains('navbar-expand-sm') ) {
							bp = 576;
						} else if ( navbar.classList.contains('navbar-expand') ) {
							bp = 0;
						}

						if ( vw < bp ) {
	                        return true;
	                    }

						if ( typeof geminiSB == 'undefined' ) {
							geminiSB = new GeminiScrollbar({
			                    element:  element,
			                    autoshow: true,
			                });
						}

						if ( geminiSB._created ) {
							geminiSB.destroy();
						}

						element.style.height = '';
						element.style.height = element.offsetHeight + 'px';

						geminiSB.create();
					});
	            }
			});
        });
	})();

    (function() {
        var navbar      = document.getElementById( 'masthead' ),
            lastScrollY = window.scrollY,
            ticking     = false;

		if ( ! navbar ) {
			return;
		}

        if ( ! navbar.classList.contains( 'fixed-top' ) ) {
            return;
        }

        if ( ! navbar.classList.contains( 'navbar-lg' ) && ! navbar.classList.contains( 'navbar-md' ) ) {
            return;
        }

		function toggleInversedNavbar() {
			if (
				args.color_mode != 'dark'
				&&
				navbar.classList.contains( 'is-navbar-transparent' )
				&&
				args.navbar_color == 'body'
				&&
				(
					!document.documentElement.dataset.bsTheme
					||
					document.documentElement.dataset.bsTheme == 'light'
				)
				&&
				navbar.dataset.bsTheme == 'dark'
			) {
				navbar.classList.add( 'is-navbar-inversed' );

				if ( navbar.classList.contains( 'is-navbar-scroll' ) ) {
					navbar.dataset.bsTheme = 'light';
				}

				navbar.querySelectorAll( '.dropdown-menu' ).forEach( function( dropdownMenu ) {
					dropdownMenu.dataset.bsTheme = 'light';
				} );
			} else if ( navbar.classList.contains( 'is-navbar-inversed' ) ) {
				navbar.classList.remove( 'is-navbar-inversed' );

				navbar.dataset.bsTheme = 'dark';

				navbar.querySelectorAll( '.dropdown-menu' ).forEach( function( dropdownMenu ) {
					dropdownMenu.dataset.bsTheme = false;
				} );
			}
		}

		toggleInversedNavbar();

		document.documentElement.addEventListener( 'enlightenment_switch_color_mode', function( event ) {
			args.color_mode = event.detail.newColorMode;

			toggleInversedNavbar();
		} );

        function onScroll() {
            if ( ! ticking ) {
                requestAnimationFrame( affixNavbar );
            }

            ticking = true;
        }
        window.addEventListener('scroll', onScroll);

        function affixNavbar() {
            if ( window.scrollY >= lastScrollY ) {
                if ( window.scrollY >= 208 ) {
                    navbar.classList.add( 'is-navbar-scroll' );
					document.body.classList.add( 'has-navbar-scroll' );

                    if ( navbar.classList.contains( 'is-navbar-inversed' ) ) {
                        if ( args.navbar_color == 'dark' ) {
                            if ( ! navbar.classList.contains( 'bg-body' ) ) {
                                navbar.classList.add( 'no-transition' );
                                navbar.classList.add( 'bg-body' );

								if ( navbar.classList.contains( 'is-navbar-transparent' ) ) {
									navbar.classList.remove( 'bg-transparent' );
								}

                                setTimeout( function() {
                                    navbar.classList.remove( 'no-transition' );
                                }, 25 );
                            }
                        } else {
                            if ( navbar.dataset.bsTheme != 'light' ) {
                                navbar.classList.add( 'no-transition' );
								navbar.dataset.bsTheme = 'light';
                                navbar.classList.add( 'bg-body' );

								if ( navbar.classList.contains( 'is-navbar-transparent' ) ) {
									navbar.classList.remove( 'bg-transparent' );
								}

                                setTimeout( function() {
                                    navbar.classList.remove( 'no-transition' );
                                }, 25 );
                            }
                        }
                    } else {
						if ( ! navbar.classList.contains( 'bg-body' ) ) {
							navbar.classList.add( 'no-transition' );
							navbar.classList.add( 'bg-body' );

							if ( navbar.classList.contains( 'is-navbar-transparent' ) ) {
								navbar.classList.remove( 'bg-transparent' );
							}

							setTimeout( function() {
								navbar.classList.remove( 'no-transition' );
							}, 25 );
						}
					}
                }
            } else if (
                ( navbar.classList.contains( 'navbar-lg' ) && window.scrollY <= 35 ) ||
                ( navbar.classList.contains( 'navbar-md' ) && window.scrollY <=  4 )
            ) {
                navbar.classList.remove( 'is-navbar-scroll' );
				document.body.classList.remove( 'has-navbar-scroll' );

                if ( navbar.classList.contains( 'is-navbar-inversed' ) ) {
                    if ( args.navbar_color == 'dark' ) {
                        if ( navbar.classList.contains( 'bg-body' ) ) {
                            navbar.classList.add( 'no-transition' );
                            navbar.classList.remove( 'bg-body' );

							if ( navbar.classList.contains( 'is-navbar-transparent' ) ) {
								navbar.classList.add( 'bg-transparent' );
							}

                            setTimeout( function() {
                                navbar.classList.remove( 'no-transition' );
                            }, 25 );
                        }
                    } else {
                        if ( navbar.dataset.bsTheme != 'dark' ) {
                            navbar.classList.add( 'no-transition' );
                            navbar.classList.remove( 'bg-body' );
							navbar.dataset.bsTheme = 'dark';

							if ( navbar.classList.contains( 'is-navbar-transparent' ) ) {
								navbar.classList.add( 'bg-transparent' );
							}

                            setTimeout(function() {
                                navbar.classList.remove( 'no-transition' );
                            }, 25);
                        }
                    }
                } else {
					if ( navbar.classList.contains( 'bg-body' ) ) {
						navbar.classList.add( 'no-transition' );
						navbar.classList.remove( 'bg-body' );

						if ( navbar.classList.contains( 'is-navbar-transparent' ) ) {
							navbar.classList.add( 'bg-transparent' );
						}

						setTimeout( function() {
							navbar.classList.remove( 'no-transition' );
						}, 25 );
					}
				}
            }

            setTimeout(function() {
                navbar.classList.remove( 'no-animation' );
            }, 0);

            lastScrollY = window.scrollY;
            ticking     = false;
        }

        if ( window.scrollY > 0 ) {
            navbar.classList.add( 'no-animation' );
            requestAnimationFrame( affixNavbar );
        }
    })();

    if ( typeof GeminiScrollbar != 'undefined' ) {
    	window.addEventListener('load', function() {
            var element = document.querySelector('.secondary-navigation > div');

			if ( element != null ) {
                new GeminiScrollbar({
                    element:  element,
                    autoshow: true,
					// forceGemini: true,
                }).create();
            }
        });
    }

    $('.dropdown-hover').children('.dropdown-toggle[href="#"]').on('click', function(event) {
        event.preventDefault();
    });

    $('.secondary-navigation .dropdown-hover').on({
        mouseenter: function(event) {
            let $this     = $(this),
                $nav      = $this.closest('.secondary-navigation'),
                $dropdown = $this.children('.dropdown-menu'),
                right     = $nav.outerWidth() - $this.outerWidth() - $this.position().left;

            $dropdown.css('right', right);

            setTimeout(function() {
                $dropdown.addClass('show');
            }, 15);
        },
        mouseleave: function() {
            let $this     = $(this),
                $dropdown = $this.children('.dropdown-menu');

            $dropdown.css('right', '');
            $dropdown.removeClass('show');
        },
        click:  function(event) {
            if ( ! $(this).children('.dropdown-menu').hasClass('show') ) {
                event.preventDefault();
            }
        },
    });

	( function() {
		var navbar = document.getElementById( 'masthead' );

		if ( ! navbar ) {
			return;
		}

		var socialNav = document.getElementById( 'social-navigation' );

		if ( ! socialNav ) {
			return;
		}

	    var toggle = socialNav.querySelector( ':scope > .social-navigation-toggle' );
		var menu   = socialNav.querySelector( ':scope > .menu' );

		if ( toggle && menu ) {
			toggle.addEventListener( 'click', function() {
		        menu.classList.toggle( 'show' );
		    });

		    document.addEventListener( 'click', function( event ) {
		        $target = $(event.target);

		        if ( ! event.target.classList.contains( 'social-navigation' ) && ! event.target.closest( '.social-navigation' ) ) {
		            menu.classList.remove( 'show' );
		        }
		    });
		}

		if ( navbar.classList.contains( 'is-navbar-inversed' ) && menu ) {
			if ( window.innerWidth < 992 ) {
				menu.dataset.bsTheme = 'light';
			}

			window.addEventListener( 'resize', function() {
				if ( window.innerWidth < 992 ) {
					menu.dataset.bsTheme = 'light';
				} else {
					menu.dataset.bsTheme = '';
				}
			} );
		}
	} )();

    $('.searchform-dropdown').on('shown.bs.dropdown', function () {
        $('.searchform-dropdown .search-query').focus();
    });

    $('.author-social-link i[data-bs-toggle="tooltip"]').each(function() {
        new bootstrap.Tooltip( this );
    });

    (function() {
        var $blocks = $('.entry-content > .wp-block-cover.alignfull.has-parallax[class*=" is-position-bottom-"][style*="min-height:100vh"]');

		if ( ! $blocks.length ) {
			return;
		}

        var navbar       = document.getElementById('masthead'),
	        offsetHeight = ( navbar && navbar.classList.contains('fixed-top') ) ? 64 : 0;

        $blocks.each(function() {
            var element = this;

            if ( ! element.childNodes.length ) {
                return true;
            }

            var container     = element.querySelector('.wp-block-cover__inner-container'),
                paddingTop    = parseInt( window.getComputedStyle(element).paddingTop ),
                paddingBottom = parseInt( window.getComputedStyle(element).paddingBottom );

            if ( container.offsetHeight <= window.innerHeight - offsetHeight - paddingTop - paddingBottom ) {
                element.classList.add('is-js-rendered');
                return true;
            }

            element.classList.add('has-scroll-effect');

            setTimeout(function() {
                element.classList.add('is-js-rendered');
            }, 30);

            var image   = element.querySelector('.wp-block-cover__image-background'),
                overlay = element.querySelector('.wp-block-cover__gradient-background'),
                ticking = false;

			if ( ! image ) {
				image = document.createElement('span');
	            image.classList.add('wp-block-cover__background-image');
	            element.insertBefore(image, container);
			}

			if ( ! overlay ) {
				overlay = element.querySelector('.wp-block-cover__background');
			}

            if ( ! overlay ) {
                overlay = document.createElement('span');
                overlay.classList.add('wp-block-cover__background-overlay');
                element.insertBefore(overlay, image);
            }

            function onScroll() {
                if ( ! ticking ) {
                    requestAnimationFrame( affixCover );
                }

                ticking = true;
            }
            window.addEventListener('scroll', onScroll);

            function affixCover() {
                var elementOffset   = element.getBoundingClientRect(),
                    containerOffset = container.getBoundingClientRect();

                if ( elementOffset.top <= offsetHeight && elementOffset.bottom > window.innerHeight ) {
                    element.classList.add('affix-background');
                    element.classList.remove('position-background-bottom');
                } else {
                    if ( elementOffset.bottom <= window.innerHeight ) {
                        element.classList.add('position-background-bottom');
                    } else {
                        element.classList.remove('position-background-bottom');
                    }

                    element.classList.remove('affix-background');
                }

                if ( containerOffset.top > 0 && containerOffset.top < window.innerHeight ) {
                    image.style.filter = 'blur(' + ( ( window.innerHeight - containerOffset.top ) / window.innerHeight * 25 ) + 'px)';
                    overlay.style.opacity = ( window.innerHeight - containerOffset.top ) / window.innerHeight * .5;
                } else {
                    if ( containerOffset.top <= 0 ) {
                        image.style.filter = 'blur(25px)';
                        overlay.style.opacity = .5;
                    } else {
                        image.style.filter = 'blur(0px)';
                        overlay.style.opacity = 0;
                    }
                }

                ticking = false;
            }

            if ( window.scrollY > 0 ) {
                requestAnimationFrame( affixCover );
            }
        });
    })();

    (function() {
        var $blocks = $('.wp-block-navigation');

		if ( ! $blocks.length ) {
			return;
		}

		$blocks.each(function() {
			if ( typeof GeminiScrollbar != 'undefined' ) {
				let scrollNav = this.querySelector('.nav.flex-nowrap:not(.flex-column)');

				if ( scrollNav ) {
					window.addEventListener('load', function() {
						new GeminiScrollbar({
							element:  scrollNav,
							autoshow: true,
						}).create();
			        });
				}
			}

			var $dropdowns = $(this).find('.dropdown');

			$dropdowns.on('shown.bs.dropdown', function (event) {
				setTimeout(function() {
					if ( event.target.getAttribute('aria-expanded') == 'false' ) {
						event.target.setAttribute('aria-expanded', 'true');
					}
				}, 15);

				if ( event.target.classList.contains('dropdown-item') ) {
					setTimeout(function() {
						$(event.target).closest('.dropdown-menu').parent('.dropdown').children('.dropdown-toggle').dropdown('show');
					}, 15);
				}
		    });

			$dropdowns.on('hidden.bs.dropdown', function (event) {
				setTimeout(function() {
					if ( event.target.getAttribute('aria-expanded') == 'true' ) {
						event.target.setAttribute('aria-expanded', 'false');
					}
				}, 15);
		    });

			$dropdowns.children('.nav-link:not(.dropdown-toggle), .dropdown-item:not(.dropdown-toggle)').on('click', function(event) {
				if ( event.target.getAttribute('href') == '#' ) {
					event.preventDefault();
				}
			});
		});
    })();

    (function() {
		if ( typeof GeminiScrollbar == 'undefined' ) {
			return;
		}

		var $captions = $('.wp-caption-text, .wp-element-caption');

		if ( ! $captions.length ) {
			return;
		}

		window.addEventListener('load', function() {
			$captions.each(function() {
				if (this.clientHeight >= this.scrollHeight) {
					return true;
				}

				new GeminiScrollbar({
					element:  this,
					autoshow: true,
				}).create();
			});
		});
	})();

    var $wpcf7Forms = $('.wpcf7 > form');

    if ( $wpcf7Forms.length ) {
        $wpcf7Forms.each(function() {
            var form = this;

            form.addEventListener( 'wpcf7beforesubmit', function(event) {
                var $form   = $(form),
                    $loader = $(form).find('.ajax-loader'),
                    $alert  = $form.find('.wpcf7-response-output');

                $loader.addClass('fas fa-spinner fa-pulse ms-3');
                $alert.addClass('d-none').removeClass('alert-danger alert-success');
            });

            form.addEventListener( 'wpcf7submit', function(event) {
                $(form).find('.ajax-loader').removeClass('fas fa-spinner fa-pulse ms-3');
            });

            var onSubmit = function(event) {
                var $form  = $(form),
                    $alert = $form.find('.wpcf7-response-output');

                setTimeout(function() {
                    $form.find('.wpcf7-not-valid-tip').addClass( 'd-block form-text text-danger' );
                }, 15);

                if ( $form.hasClass( 'sent' ) ) {
                    $alert.addClass( 'alert-success' );
                } else if (
                    $form.hasClass( 'failed' )     ||
                    $form.hasClass( 'aborted' )    ||
                    $form.hasClass( 'spam' )       ||
                    $form.hasClass( 'invalid' )    ||
                    $form.hasClass( 'unaccepted' )
                ) {
                    $alert.addClass( 'alert-danger' );
                }

                $alert.removeClass( 'd-none' );
            };

            form.addEventListener( 'wpcf7invalid',    onSubmit);
            form.addEventListener( 'wpcf7unaccepted', onSubmit);
            form.addEventListener( 'wpcf7spam',       onSubmit);
            form.addEventListener( 'wpcf7aborted',    onSubmit);
        });
    }

    $('.user-account').on('shown.bs.dropdown', function () {
        $('#username').focus();
    });

    $('.shopping-cart').on('hide.bs.dropdown', function (event) {
        if ( typeof event.clickEvent != 'undefined' && $(event.clickEvent.target).closest('.cart-contents').length ) {
            event.preventDefault();
        }
    });

    var $cartContentsCount = $('.cart-contents-count');

    if ( $cartContentsCount.length ) {
        var cart_timeout = null,
            day_in_ms    = ( 24 * 60 * 60 * 1000 );

        var refresh_count = function() {
            $.ajax({
                url: wc_cart_fragments_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'enlightenment_get_cart_contents_count',
                },
                timeout: wc_cart_fragments_params.request_timeout,
                success: function( data ) {
                    if ( data && ( 'count' in data ) ) {
                        var count = parseInt( data.count );

                        $cartContentsCount.text(count);

                        if ( count > 0 ) {
                            $cartContentsCount.removeClass('empty');
                        } else {
                            $cartContentsCount.addClass('empty');
                        }

                        $( document.body ).trigger( 'enlightenment_cart_contents_count_refreshed' );
                    }
                },
                error: function() {
                    $( document.body ).trigger( 'enlightenment_cart_contents_count_ajax_error' );
                }
            });
        }

        $( document.body ).on( 'added_to_cart removed_from_cart wc_fragments_refreshed', function(event) {
            refresh_count();

            clearTimeout( cart_timeout );
            cart_timeout = setTimeout( refresh_count, day_in_ms );

    		// Refresh when storage changes in another tab
    		$( window ).on( 'storage onstorage', function ( e ) {
    			if (
    				wc_cart_fragments_params.cart_hash_key === e.originalEvent.key && localStorage.getItem( wc_cart_fragments_params.cart_hash_key ) !== window.sessionStorage.getItem( wc_cart_fragments_params.cart_hash_key )
    			) {
    				refresh_count();
    			}
    		});
        });
    }

    if ( typeof GeminiScrollbar != 'undefined' ) {
    	window.addEventListener('load', function() {
            var wcTabs = document.querySelector('.wc-tabs-wrap');

            if ( wcTabs != null ) {
                new GeminiScrollbar({
                    element:  wcTabs,
                    autoshow: true,
                }).create();
            }

			var wcAccountNav = document.querySelector('.woocommerce-MyAccount-navigation');

            if ( wcAccountNav != null ) {
                new GeminiScrollbar({
                    element:  wcAccountNav,
                    autoshow: true,
                }).create();
            }

			var wcCartDropdown = document.querySelector('.shopping-cart.dropdown');

			if ( wcCartDropdown != null ) {
				var element = wcCartDropdown.querySelector('.woocommerce-mini-cart');

				if ( element != null ) {
					let geminiSB;

	                wcCartDropdown.addEventListener('shown.bs.dropdown', function () {
						if ( typeof geminiSB != 'undefined' && geminiSB._created ) {
							return true;
						}

						element.style.height = '';

						if ( typeof geminiSB == 'undefined' ) {
							geminiSB = new GeminiScrollbar({
								element:  element,
								autoshow: true,
							});
						}

						geminiSB.create();
					});

					wcCartDropdown.addEventListener('hidden.bs.dropdown', function () {
						if ( typeof geminiSB == 'undefined' || ! geminiSB._created ) {
							return true;
						}

						geminiSB.destroy();

						element.style.height = '';
					});
				}
            }
        });
    }

	window.addEventListener('hashchange', function(event) {
		const panel_id = window.location.hash.slice(1);

		if ( panel_id.length ) {
			const panel = document.getElementById( panel_id );

			if ( Object.prototype.toString.call( panel ) === '[object HTMLDivElement]' ) {
				const tab_id = panel.getAttribute('aria-labelledby');

				if ( typeof tab_id == 'string' && tab_id.length ) {
					const tab = document.getElementById( tab_id );

					if ( Object.prototype.toString.call( tab ) === '[object HTMLButtonElement]' ) {
						tab.addEventListener( 'shown.bs.tab', function( event ) {
							let scrollY = panel.offsetTop;

							const navbar = document.getElementById( 'masthead' );

							if ( Object.prototype.toString.call( navbar ) === '[object HTMLElement]' ) {
								scrollY -= navbar.offsetHeight;
							}

							window.scrollTo(0, scrollY);
						}, {
							once: true,
						} );

						let tabInstance = bootstrap.Tab.getInstance( tab );

						if ( Object.prototype.toString.call( tabInstance ) == '[object Object]' && typeof tabInstance.show == 'function' ) {
							tabInstance.show();
						}

						if (
							typeof tab.dataset.bsToggle == 'string'
							&&
							tab.dataset.bsToggle == 'tab'
							&&
							typeof tab.dataset.bsTarget == 'string'
							&&
							tab.dataset.bsTarget.length
						) {
							tabInstance = new bootstrap.Tab( tab );

							if ( Object.prototype.toString.call( tabInstance ) == '[object Object]' && typeof tabInstance.show == 'function' ) {
								tabInstance.show();
							}
						}
					}
				}
			}
		}
	});

	if ( typeof $.fn.select2 != 'undefined' ) {
    	$('select').select2({
    		minimumResultsForSearch: 33,
    	});
    }

    $('.woocommerce-product-gallery__image').on('click', function(event) {
        $('.woocommerce-product-gallery__trigger').click();
    });

    var manipulateQuantity = function(event) {
        event.preventDefault();
        $('button[name="update_cart"]').prop('disabled', false);

		// Elementor listens to click event to automatically update the cart
		$(this).parent().children('.qty').trigger('click');
    }

    var decreaseQuantity = function() {
        var $this = $(this),
            $qty  = $this.parent().children('.qty'),
			$inc  = $this.parent().children('.increase-quantity'),
            min   = parseInt( $qty.attr('min') ) || 0,
            max   = parseInt( $qty.attr('max') ) || false,
            val   = parseInt( $qty.val() );

        if ( ! Number.isInteger( val ) ) {
            val = 0;
        }

        if ( val > min ) {
            val--;

			// Klarna Checkout listens to change event
            $qty.val( val ).trigger('change');

			if ( val == min ) {
				$this.prop('disabled', true);
			}

			if ( $inc && max !== false && val < max ) {
				$inc.prop('disabled', false);
			}
        }
    }

    var increaseQuantity = function() {
        var $this = $(this),
            $qty  = $this.parent().children('.qty'),
			$dec  = $this.parent().children('.decrease-quantity'),
            min   = parseInt( $qty.attr('min') ) || 0,
            max   = parseInt( $qty.attr('max') ) || false,
            val   = parseInt( $qty.val() );

        if ( ! Number.isInteger( val ) ) {
            val = 0;
        }

        if ( max === false || val < max ) {
            val++;

			// Klarna Checkout listens to change event
            $qty.val( val ).trigger('change');

			if ( val == max ) {
				$this.prop('disabled', true);
			}

			if ( $dec && val > min ) {
				$dec.prop('disabled', false);
			}
        }
    }

    $('.manipulate-quantity').on('click', manipulateQuantity);
    $('.decrease-quantity').on('click', decreaseQuantity);
    $('.increase-quantity').on('click', increaseQuantity);

    $( document.body ).on( 'wc_cart_button_updated', function(event, $button) {
		var $next = $button.next();

		if ( $next.is('a') ) {
			$next.addClass('btn btn-outline-secondary');
		} else if ( $next.is('span') ) {
			$next.addClass('btn-group');
			$next.children('a').addClass('btn btn-outline-secondary');
		}
    });

    $( document.body ).on( 'updated_wc_div updated_checkout', function(event) {
        $('.manipulate-quantity').on('click', manipulateQuantity);
        $('.decrease-quantity').on('click', decreaseQuantity);
        $('.increase-quantity').on('click', increaseQuantity);
    });

    if ( typeof $.fn.selectWoo != 'undefined' ) {
        $('.wc_payment_methods select').selectWoo();

        $( document.body ).on( 'updated_checkout', function(event) {
            $('.wc_payment_methods select').selectWoo();
        });
    }

    $( document.body ).on( 'wc-password-strength-added', function() {
        $( '.woocommerce-password-strength' ).addClass('alert').addClass('mt-3').addClass('mb-2');
    } );

    $( document.body ).on(
        'keyup change',
        'form.register #reg_password, form.checkout #account_password, ' +
        'form.edit-account #password_1, form.lost_reset_password #password_1',
        function() {
            $wrapper = $( '.woocommerce-password-strength' );

            setTimeout(function() {
                $wrapper.removeClass('alert-danger alert-warning alert-info alert-success');

                if ( $wrapper.hasClass('short') ) {
                    $wrapper.addClass('alert-danger');
                } else if ( $wrapper.hasClass('bad') ) {
                    $wrapper.addClass('alert-warning');
                } else if ( $wrapper.hasClass('good') ) {
                    $wrapper.addClass('alert-info');
                } else if ( $wrapper.hasClass('strong') ) {
                    $wrapper.addClass('alert-success');
                }
            }, 150);
        }
    );

	function waitForElements( selector ) {
		return new Promise( function( resolve ) {
			const observer = new MutationObserver( function( mutations, observer ) {
				const elements = document.querySelectorAll( selector );

				if (elements.length) {
					observer.disconnect();

					resolve( elements );
				}
			} );

			observer.observe( document.body, {
				childList: true,
				subtree: true,
			} );
		} );
	}

	waitForElements( '.show-password-input' ).then( function( elements ) {
		elements.forEach(function ( element ) {
			element.parentNode.classList.add( 'input-group' );

	        element.classList.add( 'btn' );
			element.classList.add( 'btn-theme-inverse' );
			element.classList.add( 'd-inline-flex' );
			element.classList.add( 'align-items-center' );

			const icon = document.createElement( 'i' );

			icon.classList.add( 'far' );
			icon.classList.add( 'fa-eye' );
			icon.classList.add( 'fa-fw' );

			icon.ariaHidden = 'true';

			element.addEventListener( 'click', function() {
	    		icon.classList.toggle( 'fa-eye' );
				icon.classList.toggle( 'fa-eye-slash' );
	    	} );

			element.append( icon );
		} );
	} );

    $( document ).on( 'updated_checkout', function() {
        if ( typeof mollieComponentsSettings == 'undefined' ) {
            return true;
        }

        var $components = $('.mollie-components');

        $components.addClass('row');

        setTimeout(function() {
            $components.children('#cardHolder, #cardNumber').addClass('mb-3').addClass('col-12');
            $components.children('#expiryDate, #verificationCode').addClass('mb-3').addClass('col-6');

            $('.mollie-component').addClass('form-control');
        }, 150);
    });

    $( document.body ).on( 'updated_checkout', function(event) {
        var $wc_od = $('#wc-od');

        if ( $wc_od.length ) {
            var $children = $wc_od.children('.form-row');

            if ( $children.length > 1 ) {
                $children.removeClass('col-12').addClass('col-md-6');

                if ( typeof $.fn.selectWoo != 'undefined' ) {
                    $('#delivery_time_frame').selectWoo();
                }
            }
        }
    });

    $('.tribe-events-c-search__input-control').on('click', function(event) {
        if ( event.target === this ) {
            $(this).children('.tribe-events-c-search__input').focus();
        }
    });

    (function() {
        if ( typeof GeminiScrollbar != 'undefined' ) {
        	window.addEventListener('load', function() {
                var element = document.querySelector('.tribe-events-filters-horizontal .tribe-events-filters-content, .tribe-filter-bar--horizontal .tribe-filter-bar__form');

                if ( element != null ) {
                    new GeminiScrollbar({
                        element:  element,
                        autoshow: true,
                    }).create();
                }
            });
        }

        function initFilterBar() {
            $(document).on('click', '.tribe-filter-bar-c-filter__remove-button', function(event) {
                event.preventDefault();

                var id = $(this).prev('.tribe-filter-bar-c-filter__toggle').attr('id').replace('-toggle-', '-pill-toggle-');

                $('#' + id).next('.tribe-filter-bar-c-pill__remove-button').click();
            });

            var $fb_horizontal = $('.tribe-filter-bar--horizontal');

            if ( $fb_horizontal.length ) {
                var $filters = $('.tribe-filter-bar-c-filter'),
					bp       = 999999;

				if ( $fb_horizontal.hasClass('navbar-expand-xl') ) {
					bp = 1200;
				} else if ( $fb_horizontal.hasClass('navbar-expand-lg') ) {
					bp = 992;
				} else if ( $fb_horizontal.hasClass('navbar-expand-md') ) {
					bp = 768;
				} else if ( $fb_horizontal.hasClass('navbar-expand-sm') ) {
					bp = 576;
				} else if ( $fb_horizontal.hasClass('navbar-expand') ) {
					bp = 0;
				}

                $filters.on('shown.bs.dropdown', function (event) {
                    var vw = Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0);

                    if ( vw < bp ) {
                        return true;
                    }

                    var $this = $( event.target ),
                        $menu = $this.children('.dropdown-menu'),
                        left  = $this.offset().left - $fb_horizontal.offset().left + $this.outerWidth();

                    if ( ! $this.is(':first-child') ) {
                        left -= $menu.outerWidth();
                    }

                    $menu.css('transform', 'translate3d(' + left + 'px,0,0)');
                });

                $filters.on('hidden.bs.dropdown', function (event) {
                    $( event.target ).children('.dropdown-menu').css('transform', '');
                });
            }
        }

        function initMap() {
            var $map, state;

            function disableMapScroll( state ) {
                if ( window.innerWidth < 1200 ) {
                    state.map.setOptions({
                        // Hide full screen control on mobile devces
                        fullscreenControl: false,
                    });
                } else {
                    state.map.setOptions({
                        // Assume non-touch user and disable scrollwheel
                        scrollwheel: false,
                    });
                }



                window.addEventListener('touchstart', function onTouchStart(event) {
					state.map.setOptions({
                        // User is using touch gestures, enable scroll
                        scrollwheel: true,
                        gestureHandling: 'greedy',
                    });
                });

                window.addEventListener('resize', function() {
                    if ( window.innerWidth < 1200 ) {
                        state.map.setOptions({
                            // Assume touch user and ensable scrollwheel
                            scrollwheel: true,
                            // Hide full screen control on mobile devces
                            fullscreenControl: false,
                        });
                    } else {
                        state.map.setOptions({
                            // If exiting a touch-enabled simulator such as element inspector disable scrollwheel
                            scrollwheel: false,
                            gestureHandling: 'auto',
                            // Hide full screen control on mobile devces
                            fullscreenControl: true,
                        });
                    }
                });
            }

            function affixMapOnScroll() {
                var $map   = $('.tribe-events-pro-map__map'),
                    navbar = document.getElementById('masthead');

                $map.each(function() {
                    var element = this;

                    if ( ! element.childNodes.length ) {
                        return true;
                    }

                    var ticking = false;

                    function onScroll() {
                        if ( ! ticking ) {
                            requestAnimationFrame( affixMap );
                        }

                        ticking = true;
                    }
                    window.addEventListener('scroll', onScroll);

                    function affixMap() {
                        if ( window.innerWidth >= 992 ) {
                            var elementOffset = element.getBoundingClientRect(),
								navbarOffset  = navbar.classList.contains('fixed-top') ? navbar.offsetHeight : 0;

                            if ( elementOffset.top <= navbarOffset && elementOffset.bottom > window.innerHeight ) {
                                element.classList.add('affix-map');
                                element.classList.remove('position-map-bottom');
                            } else {
                                if ( elementOffset.bottom <= window.innerHeight ) {
                                    element.classList.add('position-map-bottom');
                                } else {
                                    element.classList.remove('position-map-bottom');
                                }

                                element.classList.remove('affix-map');
                            }
                        } else {
                            element.classList.remove('affix-map');
                            element.classList.remove('position-map-bottom');
                        }

                        ticking = false;
                    }

                    if ( window.scrollY > 0 ) {
                        requestAnimationFrame( affixMap );
                    }
                });
            }

            function toggleMapButtonOnScroll() {
                var container = document.querySelector('.site-content'),
                    $button   = $('.tribe-events-pro-map__map-mobile-show-map-button-wrapper');

                $button.each(function() {
                    var element = this;

                    if ( ! element.childNodes.length ) {
                        return true;
                    }

                    var ticking = false;

                    function onScroll() {
                        if ( ! ticking ) {
                            requestAnimationFrame( toggleMapButton );
                        }

                        ticking = true;
                    }
                    window.addEventListener('scroll', onScroll);

                    function toggleMapButton() {
                        if ( window.innerWidth < 992 ) {
                            var containerOffset = container.getBoundingClientRect();

                            if ( containerOffset.bottom < window.innerHeight ) {
                                element.classList.add('tribe-events-pro-map__map-mobile-show-map-button-wrapper-hide');
                            } else {
                                element.classList.remove('tribe-events-pro-map__map-mobile-show-map-button-wrapper-hide');
                            }
                        }

                        ticking = false;
                    }

                    if ( window.scrollY > 0 ) {
                        requestAnimationFrame( toggleMapButton );
                    }
                });
            }

            function scrollToEventCard( eventId ) {
                var navbar    = document.getElementById('masthead'),
                    offsetTop = navbar.classList.contains('fixed-top') ? navbar.offsetHeight : 0,
                    container = document.querySelector('.tribe-events-pro-map__map'),
                    selector  = '[data-js="tribe-events-pro-map-event-card-wrapper"]',
                    element   = document.querySelector( selector + '[data-event-id="' + eventId + '"]' ),
                    offset    = element.getBoundingClientRect();

                if ( offset.top < offsetTop || offset.top + element.offsetHeight > window.innerHeight ) {
                    var scrollTop    = window.scrollY + offset.top - offsetTop,
                        maxScrollTop = window.innerWidth < 992 ? document.body.offsetHeight : container.getBoundingClientRect().top + window.scrollY + container.offsetHeight - window.innerHeight;

                    if ( scrollTop > maxScrollTop ) {
                        scrollTop = maxScrollTop;
                    }

                    $('html, body').animate({
                        scrollTop: scrollTop,
                    }, 'fast');
                }
            }

            function scrollToEventCardOnSlideChange( state ) {
                if ( typeof state == 'undefined' ) {
                    state = $map.data('tribeEventsState');
                }

                if ( typeof state == 'undefined' ) {
                    setTimeout(scrollToEventCardOnSlideChange, 150);
                    return true;
                }

                if ( typeof state.slider == 'undefined' ) {
                    return true;
                }

                if ( state.slider == null ) {
                    return true;
                }

                state.slider.on( 'slideChange', function() {
                    var eventId = $( state.slider.slides[ state.slider.activeIndex ] ).attr( 'data-event-id' );
                    scrollToEventCard( eventId );
                });
            }

            function setupMap( state ) {
                if ( typeof state == 'undefined' ) {
                    state = $map.data('tribeEventsState');
                }

                if ( typeof state == 'undefined' ) {
                    setTimeout(setupMap, 150);
                    return true;
                }

                disableMapScroll( state );
                affixMapOnScroll();
                toggleMapButtonOnScroll();

                state.markers.forEach(function ( marker ) {
                    marker.addListener( 'click', function() {
                        var eventIds = marker.get( 'eventIds' );
                        scrollToEventCard( eventIds[0] );
                    });
                });

                state.tooltip.addListener( 'domready', function() {
                    if ( state.slider == null ) {
                        state = $map.data('tribeEventsState');
                    }

                    if ( typeof state == 'undefined' ) {
                        setTimeout(scrollToEventCardOnSlideChange, 150);
                        return true;
                    }

                    scrollToEventCardOnSlideChange( state );
                });

				var navbar = document.getElementById('masthead');

				/**
				 * Ugly hack to fix the edge case where the window is scrolled
				 * just enough to not set the is-navbar-scroll class
				 */
				function preventScroll(event) {
					event.preventDefault();
					window.scrollTo(0, 0);
				}

				if ( window.innerWidth < 992 ) {
					document.body.classList.add('overflow-hidden');

					if ( window.scrollY && navbar.classList.contains('fixed-top') && ! navbar.classList.contains('is-navbar-scroll') ) {
						window.scrollTo(0, 0);

						window.addEventListener('scroll', preventScroll);
					}
				} else {
					window.removeEventListener('scroll', preventScroll);
				}

				window.addEventListener('resize', function() {
					if ( window.innerWidth < 992 ) {
						if ( ! $('.tribe-events-pro-map__map').hasClass('tribe-events-pro-map__map-hidden') ) {
							document.body.classList.add('overflow-hidden');

							if ( window.scrollY && navbar.classList.contains('fixed-top') && ! navbar.classList.contains('is-navbar-scroll') ) {
								window.scrollTo(0, 0);
							}
						}
                    } else {
						document.body.classList.remove('overflow-hidden');
						window.removeEventListener('scroll', preventScroll);
					}
				});

                $('.tribe-events-pro-map__map-mobile-close-button').on('click', function(event) {
                    event.preventDefault();
                    event.stopPropagation();

                    $('.tribe-events-pro-map__map').addClass('tribe-events-pro-map__map-hidden');
					document.body.classList.remove('overflow-hidden');
					window.removeEventListener('scroll', preventScroll);
                });

                $('.tribe-events-pro-map__map-mobile-trigger-show-map').on('click', function(event) {
                    event.preventDefault();
                    event.stopPropagation();

                    $('.tribe-events-pro-map__map').removeClass('tribe-events-pro-map__map-hidden');
					document.body.classList.add('overflow-hidden');

					if ( window.scrollY && navbar.classList.contains('fixed-top') && ! navbar.classList.contains('is-navbar-scroll') ) {
						window.scrollTo(0, 0);
					}
                });
            }

            $(document).on('afterSetup.tribeEvents', function() {
                $map  = $('[data-js="tribe-events-pro-map-google-maps-premium"]'),
                state = $map.data('tribeEventsState');

                if ( ! $map.length ) {
                    return true;
                }

                if ( typeof state == 'undefined' ) {
                    setTimeout(setupMap, 150);
                    return true;
                }

                setupMap( state );
            });
        }

        function beforeAjaxSuccess( event, data ) {
            var $html = $($.parseHTML(data)),
                $data = $html.find('[data-js="tribe-events-view-data"]');

    		// Bail in case we dont find data script.
    		if ( ! $data.length ) {
    			return true;
    		}

    		var data = JSON.parse( $.trim( $data.text() ) );

    		// Bail when the data is not a valid object
    		if ( ! _.isObject( data ) ) {
    			return true;
    		}

            setTimeout(function() {
                initFilterBar();
            }, 150);

            switch ( data.slug ) {
                case 'photo':
                    if ( typeof enlightenment_masonry_args != 'undefined' ) {
                        setTimeout(function() {
            				var $container = $(enlightenment_masonry_args.container);

            				$container.imagesLoaded( function() {
            					$container.masonry(enlightenment_masonry_args);
            				});
                        }, 150);
        			}

                    break;

                case 'map':
                    setTimeout(function() {
                        initMap();
                    }, 150);

                    break;
            }

            setTimeout(function() {
                $('[data-js="tribe-events-view"]').on( 'beforeAjaxSuccess.tribeEvents', beforeAjaxSuccess );
            }, 150);
        }

        initFilterBar();

        initMap();

        $('[data-js="tribe-events-view"]').on( 'beforeAjaxSuccess.tribeEvents', beforeAjaxSuccess );

        $('.recurring-event[data-bs-toggle="tooltip"]').each(function() {
            new bootstrap.Tooltip( this );
        });

        $('.event-status[data-bs-toggle="tooltip"]').each(function() {
            new bootstrap.Tooltip( this );
        });

        $('.fa-question-circle[data-bs-toggle="tooltip"]').each(function() {
            new bootstrap.Tooltip( this );
        });

        $('.tribe-tooltip[data-bs-toggle="tooltip"]').each(function() {
            new bootstrap.Tooltip( this );
        });

		$(document).on('verify.dependency', function() {
			$('.tribe-sticky-tooltip[data-bs-toggle="tooltip"]').each(function() {
	            new bootstrap.Tooltip( this );
	        });
		});

        $('#event_tickets').on('after_panel_swap.tickets', function(event) {
            $('.fa-question-circle[data-bs-toggle="tooltip"]').each(function() {
                new bootstrap.Tooltip( this );
            });
        });

        $(document).on('afterSetup.tribeEvents', function() {
            $('.tribe-events-filters-group-heading').off('click');
        });

        $(document).on('click', '.tribe-tickets__rsvp-ar-guest-list-item-button', function(event) {
            event.preventDefault();

            var $this = $(this),
                $btns = $this.closest('.tribe-tickets__rsvp-ar-guest-list').find('.tribe-tickets__rsvp-ar-guest-list-item-button');

            $btns.removeClass('active');
            $this.addClass('active');
        });

        $(document).on('click', '.tribe-tickets__rsvp-form-button--next', function(event) {
			var $next = $(this).closest('.tribe-tickets__rsvp-ar').find('.tribe-tickets__rsvp-ar-guest-list-item-button.nav-link.active').parent().next();

            if ( $next.hasClass('tribe-tickets__rsvp-ar-guest-list-item-template') ) {
                $next = $next.next();
            }

            var $trigger = $next.children('.tribe-tickets__rsvp-ar-guest-list-item-button');

			if ( $trigger.length ) {
				( new bootstrap.Tab( $trigger.get( 0 ) ) ).show();
			}
        });

        $(document).ready(function() {
            $('.tribe-tickets-loader__dots').addClass('d-none').removeClass('d-flex');

            if ( typeof tribe == 'undefined' ) {
                return true;
            }

            if ( typeof tribe.dialogs == 'undefined' ) {
                return true;
            }

            if ( typeof tribe.dialogs.events == 'undefined' ) {
                return true;
            }

            $( tribe.dialogs.events ).on('tribe_dialog_show_ar_modal', function(event, dialog) {
                var content = dialog.querySelector('[role="document"]'),
                    header  = dialog.querySelector('.modal-header'),
                    title   = dialog.querySelector('.tribe-dialog__title'),
                    dismiss = dialog.querySelector('.tribe-dialog__close-button'),
                    overlay = document.createElement('div');

                dialog.classList.add('modal', 'fade');
                content.classList.add('modal-content');

                if ( null === header ) {
                    header  = document.createElement('div');
                    header.classList.add('modal-header');
                    content.prepend(header);
                    header.append(title);
                    header.append(dismiss);
                }

                overlay.classList.add('modal-backdrop', 'fade');
                dialog.parentNode.insertBefore(overlay, dialog.nextSibling);

                dialog.addEventListener('click', function(event) {
                    if ( null === event.target.closest('.tribe-dialog__wrapper') ) {
                        dismiss.click();
                    }
                });

                dialog.style.display = 'block';

                setTimeout(function() {
                    dialog.classList.add('show');
                    document.body.classList.add('modal-open');
                    overlay.classList.add('show');
                    $('.tribe-tickets-loader__dots').addClass('d-none').removeClass('d-flex');
                }, 0);
            });

            $( tribe.dialogs.events ).on('tribe_dialog_close_ar_modal', function(event, dialog) {
                var overlay = dialog.nextSibling;

                setTimeout(function() {
                    // dialog.style.display = '';
                    dialog.classList.remove('show');
                    document.body.classList.remove('modal-open');

                    if ( overlay.classList.contains('modal-backdrop') ) {
                        overlay.classList.remove('show');

                        setTimeout(function() {
                            dialog.style.display = '';
                            overlay.remove();
                        }, 150);
                    }
                }, 0);
            });
        });

        $('#tribe-community-events form').on('submit', function(event) {
            let $form_wrapper = $(this).parent();

            setTimeout(function() {
                let $notice = $form_wrapper.find('.tribe-community-js-notice');

                $notice.find('p').each(function() {
                    $(this).addClass('alert').addClass('alert-danger');
                });
            }, 150);
        });

		$('.tribe-validation').on( 'validation.tribe', function() {
			$(this).find('input, select, textarea').removeClass('is-invalid');
		});

		$('.tribe-validation').on('displayErrors.tribe', function() {
			$(this).find('.tribe-validation-error').not(':disabled').addClass('is-invalid');
		});

		$.ajaxPrefilter(function( options, originalOptions, jqXHR ) {
			if ( typeof originalOptions.data == 'undefined' ) {
	            return true;
	        }

	        if ( typeof originalOptions.data.action == 'undefined' ) {
	            return true;
	        }

	        if ( 'tribe-ticket-panels' != originalOptions.data.action ) {
	            return true;
	        }

			var optSuccess   = options.success,
				origSuccess  = originalOptions.success,
				prevCallback = function( response ) {
					if ( optSuccess.toString() != origSuccess.toString() ) {
						optSuccess( response );
					} else {
						origSuccess( response );
					}
				};

			options.success = function( response ) {
				prevCallback( response );

				$('.tribe-validation').on('displayErrors.tribe', function() {
					var $this = $(this);

					$this.find('input, select, textarea').removeClass('is-invalid');
					$this.find('.tribe-validation-error').not(':disabled').addClass('is-invalid');
				});
			}
		});

        $('.tribe-tickets__rsvp-wrapper').on( 'afterAjaxComplete.tribeTicketsRsvp', function(event, data) {
            $('.tribe-tickets-loader__dots').addClass('d-none').removeClass('d-flex');
        });

        $('.tribe-tickets__rsvp-wrapper').on( 'afterAjaxSuccess.tribeTicketsRsvp', function(event, data) {
            $('.tribe-common-form-control-toggle__label [data-bs-toggle="tooltip"]').each(function() {
                new bootstrap.Tooltip( this );
            });
        });

		$(document).on( 'autodetect.complete', function() {
			setTimeout(function() {
				$('.tec-events-virtual-meetings-autodetect-source__dropdown--tooltip [data-bs-toggle="tooltip"]').each(function() {
	                new bootstrap.Tooltip( this );
	            });
			}, 25);
		} );
    })();

    $(document).ready(function() {
        $('.hide-if-js').css('display', 'none');
        $('.hide-if-no-js').removeClass('hide-if-no-js');
    });
})(jQuery);
