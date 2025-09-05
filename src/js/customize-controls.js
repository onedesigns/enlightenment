(function( exports, $ ) {
	"use strict";
	var api  = wp.customize;

    api.bind( 'ready', function() {
        api( 'custom_logo', function( value ) {
            api.control( 'custom_logo_alt', function( control ) {
                /**
                 * Toggling function
                 */
                var toggle = function( to ) {
                    control.toggle( !! to );
                }

                // 1. On loading.
                toggle( value.get() );

                // 2. On value change.
                value.bind( toggle );
            } );
        } );

		api( 'navbar_nav_overflow', function( value ) {
			value.bind( function( to ) {
				if ( 'wrap' == to ) {
					api( 'site_header_position', function( setting ) {
						setting.set('static-top');
					} );
				}
			} );
		} );
    });
})( wp, jQuery );
