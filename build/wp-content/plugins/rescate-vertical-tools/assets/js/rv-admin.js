/* global jQuery, wp, rvAdminGallery */
( function ( $ ) {
	'use strict';

	$( function () {
		$( '.rv-gal' ).each( function () {
			var $box   = $( this );
			var $items = $box.find( '.rv-gal-items' );
			var $input = $box.find( '.rv-gal-input' );
			var frame;

			/** Recalcula el campo oculto a partir de las miniaturas visibles. */
			function sync() {
				var ids = $items.find( '.rv-gal-item' ).map( function () {
					return $( this ).data( 'id' );
				} ).get();
				$input.val( ids.join( ',' ) );
			}

			$box.on( 'click', '.rv-gal-add', function ( e ) {
				e.preventDefault();

				// Se reconstruye cada vez para que la selección refleje lo ya elegido
				frame = wp.media( {
					title: rvAdminGallery.title,
					button: { text: rvAdminGallery.button },
					library: { type: 'image' },
					multiple: 'add'
				} );

				frame.on( 'select', function () {
					var seleccion = frame.state().get( 'selection' );

					seleccion.each( function ( att ) {
						var a  = att.toJSON();
						var id = parseInt( a.id, 10 );
						if ( ! id || $items.find( '.rv-gal-item[data-id="' + id + '"]' ).length ) {
							return; // ya estaba
						}

						var src = a.sizes && a.sizes.thumbnail ? a.sizes.thumbnail.url : a.url;

						$items.append(
							$( '<div class="rv-gal-item"></div>' )
								.attr( 'data-id', id )
								.append( $( '<img>' ).attr( 'src', src ).attr( 'alt', '' ) )
								.append(
									$( '<button type="button" class="rv-gal-quitar"></button>' )
										.attr( 'aria-label', rvAdminGallery.remove )
										.text( '×' )
								)
						);
					} );

					sync();
				} );

				frame.open();
			} );

			$box.on( 'click', '.rv-gal-quitar', function ( e ) {
				e.preventDefault();
				$( this ).closest( '.rv-gal-item' ).remove();
				sync();
			} );
		} );
	} );
} )( jQuery );
