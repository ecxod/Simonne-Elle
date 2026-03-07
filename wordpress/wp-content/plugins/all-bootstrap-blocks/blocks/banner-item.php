<?php
function areoi_render_block_banner_item( $attributes, $content ) 
{	
	$allow_pattern = true;
	
	$parent 	= areoi_get_parent_block( $attributes['parent_id'] );
	$layout 	= !empty( $parent['attrs']['layout'] ) ? esc_attr( $parent['attrs']['layout'] ) : 'grid';
	$container 	= $layout == 'grid' ? 'container-fluid' : 'container';
	$size 		= !empty( $parent['attrs']['size'] ) ? esc_attr( $parent['attrs']['size'] ) : 'areoi-large';

	$class 			= 	trim( 
		areoi_get_class_name_str( array( 
			'areoi-banner-item',
			( !empty( $attributes['className'] ) ? $attributes['className'] : '' )
		) ) 
		. ' ' . 
		areoi_get_display_class_str( $attributes, 'block' ) 
	);

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

	if ( !empty( $attributes['image'] ) || !empty( $attributes['video'] ) ) {
		$media .= '<div class="col-12 col-lg-6">';

			if ( !empty( $attributes['image'] ) ) {
				$media .= '<img src="' . esc_url( $attributes['image']['url'] ) . '" width="' . esc_attr( $attributes['image']['width'] ) . '" height="' . esc_attr( $attributes['image']['height'] ) . '" alt="' . esc_attr( $attributes['image']['alt'] ) . '" class="img-fluid areoi-banner-media" />';
			}

			if ( !empty( $attributes['video'] ) ) {
				$media .= '<video class="img-fluid areoi-banner-media" autoplay loop playsinline muted>';
					$media .= '<source src="' . esc_url( $attributes['video']['url'] ) . '" />';
				$media .= '</video>';
			}

		$media .= '</div>';
	}

	switch ( $layout ) {
		case 'grid':
			
			$output = '
				<div ' . areoi_return_id( $attributes ) . ' class="' . areoi_format_block_id( $attributes['block_id'] ) . ' ' . $class . ' d-flex">
					' . $background . '
					<div class="areoi-banner-content flex-grow-1">
						' . $content . ' 
					</div>
					' . $url . '
				</div>
			';

			break;
		
		default:
			

			$output = '
				<div ' . areoi_return_id( $attributes ) . ' class="' . areoi_format_block_id( $attributes['block_id'] ) . ' ' . $class . ' position-relative">

					' . $background . '

					<div class="' . $container . ' h-100 position-relative">
						<div class="row justify-content-' . ( $media ? 'between' : 'center text-center' ) . ' align-items-center h-100">
							<div class="col-11 col-md-8 col-lg-6 col-xl-5">
								' . $content . ' 
							</div>
							' . $media . ' 
							' . $url . '
						</div>
					</div>
				</div>
			';

			break;
	}

	return $output;
}