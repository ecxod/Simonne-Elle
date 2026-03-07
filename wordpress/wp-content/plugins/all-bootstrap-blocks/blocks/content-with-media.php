<?php
function areoi_render_block_content_with_media( $attributes, $content ) 
{
	$allow_pattern = true;
	
	$class 			= 	trim( 
		areoi_get_class_name_str( array( 
			'areoi-content-with-media',
			( !empty( $attributes['align'] ) ? 'align' . $attributes['align'] : '' ),
			( !empty( $attributes['className'] ) ? $attributes['className'] : '' ),
		) ) 
		. ' ' . 
		areoi_get_display_class_str( $attributes, 'block' )
	);

	$alignment = ( !empty( $attributes['alignment'] ) ? esc_attr( $attributes['alignment'] ) : 'start' );
	$background_media_row = $alignment == 'start' ? 'justify-content-end' : 'justify-content-start';
	$order = $alignment == 'start' ? 0 : 1;
	$background 	= include( AREOI__PLUGIN_DIR . '/blocks/_partials/background.php' );

	$url = null;
	if ( !empty( $attributes['url'] ) ) {
		$url = '
			<a class="areoi-full-link"
		';
		if ( !empty( $attributes['url'] ) ) {
			$url .= ' href="' . esc_url( $attributes['url'] ) . '"';
		}
		if ( !empty( $attributes['url_title'] ) ) {
			$url .= ' title="' . esc_attr( $attributes['url_title'] ) . '"';
		}
		if ( !empty( $attributes['rel'] ) ) {
			$url .= ' rel="' . esc_attr( $attributes['rel'] ) . '"';
		}
		if ( !empty( $attributes['linkTarget'] ) ) {
			$url .= ' target="' . esc_attr( $attributes['linkTarget'] ) . '"';
		}
		$url .= '></a>';
	}

	$media = '';
	$background_media = '';
	$alt = !empty( $attributes['image']['alt'] ) ? esc_attr( $attributes['image']['alt'] ) : null;
	$width = !empty( $attributes['image']['width'] ) ? esc_attr( $attributes['image']['width'] ) : null;
	$height = !empty( $attributes['image']['height'] ) ? esc_attr( $attributes['image']['height'] ) : null;

	if ( ( !empty( $attributes['image'] ) || !empty( $attributes['video'] ) ) ) {
		
		$media .= '<div class="' . ( ( !empty( $attributes['layout'] ) && $attributes['layout'] == 'full-width' ) ? 'd-lg-none' : '' ) . '">';
			if ( !empty( $attributes['image'] ) ) {
				$media .= '<img src="' . esc_url( $attributes['image']['url'] ) . '" width="' . $width . '" height="' . $height . '" alt="' . $alt . '" class="img-fluid areoi-banner-media" />';
			}

			if ( !empty( $attributes['video'] ) ) {
				$media .= '<video class="img-fluid areoi-banner-media" autoplay loop playsinline muted>';
					$media .= '<source src="' . esc_url( $attributes['video']['url'] ) . '" />';
				$media .= '</video>';
			}
		$media .= '</div>';
		

		if ( ( !empty( $attributes['layout'] ) && $attributes['layout'] == 'full-width' ) ) {
			$background_media .= '<div class="areoi-background d-none d-lg-block">
				<div class="container-fluid p-0">
					<div class="row ' . $background_media_row . '">
						<div class="col-6 position-relative overflow-hidden">

		                    ' . ( !empty( $attributes['image'] ) ? 
		                        '
		                        	<div class="areoi-background__image" style="background-image:url(' . esc_url( $attributes['image']['url'] ) . ')"></div>
		                        '  : ''
		                    ) . '

		                    ' . ( !empty( $attributes['video'] ) ? 
		                        '<video autoplay loop playsinline muted>
		                            <source src="' . esc_url( $attributes['video']['url'] ) . '" />
		                        </video>'  : ''
		                    ) . '
		    			</div>
		    		</div>
		    	</div>
		    </div>';
		}
	}

	$output = '
		<div ' . areoi_return_id( $attributes ) . ' class="' . areoi_format_block_id( $attributes['block_id'] ) . ' ' . $class . ' position-relative">

			' . $background . '
			<div class="d-flex flex-grow-1 position-relative">
				' . $background_media . '

				<div class="container h-100 position-relative">
					<div class="row justify-content-between align-items-center h-100">
						<div class="col-11 col-md-8 col-lg-6 col-xl-5 order-lg-' . $order . '">
							' . $content . ' 
						</div>
						
						<div class="col-12 col-lg-6">
							' . $media . '
						</div>
					</div>
				</div>
			</div>

			' . $url . '
		</div>
	';

	return $output;
}