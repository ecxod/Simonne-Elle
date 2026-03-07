<?php
function areoi_render_block_carousel( $attributes, $content ) 
{
	libxml_use_internal_errors( true );
	

	$dom 	= new DOMDocument;
	$dom->encoding = 'utf-8';

	if (version_compare(PHP_VERSION, '8.1.0', '>=')) {
	    $map = array(
	        0x80, 0x10FFFF, 0, 0xFFFF
	    );
	    $content = mb_encode_numericentity($content, $map, 'UTF-8');
	} else {
	    $content = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');
	}

	$dom->loadHTML( $content );
	$xpath 	= new DOMXpath($dom);
	$items 	= $xpath->query('//div[contains(@class, "carousel-item")]');

	$class 			= 	trim( 
		areoi_get_class_name_str( array( 
			'carousel',
			'slide',
			( !empty( $attributes['className'] ) ? $attributes['className'] : '' ),
			( !empty( $attributes['style'] ) && empty( $attributes['item_style'] ) ? $attributes['style'] : '' ),
			( !empty( $attributes['item_style'] ) ? $attributes['item_style'] : '' ),
			( !empty( $attributes['transition'] ) ? $attributes['transition'] : '' ),
			( !empty( $attributes['align'] ) ? 'align' . $attributes['align'] : '' ),
		) ) 
		. ' ' . 
		areoi_get_display_class_str( $attributes, 'block' ) 
	);

	$block_id = ( !empty( $attributes['anchor'] ) ? '#' . esc_attr( $attributes['anchor'] ) : '.block-' . esc_attr( $attributes['block_id'] ) );

	$buttons = null;
	if ( !empty( $attributes['controls'] ) && $items->length > 1 ) {
		$buttons = '
			<button class="carousel-control-prev" type="button" data-bs-target="' . $block_id . '" data-bs-slide="prev">
				<span class="carousel-control-prev-icon" aria-hidden="true"></span>
				<span class="visually-hidden">Previous</span>
			</button>
			<button class="carousel-control-next" type="button" data-bs-target="' . $block_id . '" data-bs-slide="next">
				<span class="carousel-control-next-icon" aria-hidden="true"></span>
				<span class="visually-hidden">Next</span>
			</button>
		';
	}

	$indicators = null;
	if ( !empty( $attributes['indicators'] ) && $items->length > 1 ) {
		$indicators = '<div class="carousel-indicators">';
			foreach ( $items as $item_key => $item ) {
				$indicators .= '
					<button 
						type="button" 
						data-bs-target="' . $block_id . '" 
						data-bs-slide-to="' . esc_attr( $item_key ) . '" 
						class="' . ( $item_key == 0 ? 'active' : '' ) . '" 
						aria-current="true" 
						aria-label="Slide ' . esc_attr( $item_key ) . '"
					></button>
				';
			}
		$indicators .= '</div>';
	}

	$newdoc = new DOMDocument();
	if ( !empty( $items ) ) {
		foreach ( $items as $item_key => $item ) {
			$cloned = $item->cloneNode(TRUE);
			if ( $item_key == 0 ) {
				$cloned->setAttribute( 'class', 'carousel-item active' );
			}
		    $newdoc->appendChild($newdoc->importNode($cloned,TRUE));
		}
		$content = $newdoc->saveHTML();
	}

	$auto_scroll = false;
	if ( empty( $attributes['auto_scroll'] ) && $attributes['interval'] === true ) {
	    $auto_scroll = 'carousel';
	}
	if ( !empty( $attributes['auto_scroll'] ) ) {
	    $auto_scroll = ( $attributes['auto_scroll'] ? 'carousel' : false );
	}

	$interval = false;
	if ( $auto_scroll === 'carousel' ) {
	    if ( $attributes['interval'] === true ) {
	        $interval = 4000;
	    } elseif ( is_numeric( $attributes['interval'] ) ) {
	        $interval = (int) $attributes['interval'];
	    }
	}

	$output = '
		<div 
			' . areoi_return_id( $attributes ) . '
			class="' . areoi_format_block_id( $attributes['block_id'] ) . ' ' . $class . '"
			' . ( $auto_scroll !== false ? 'data-bs-ride="' . esc_attr( $auto_scroll ) . '"' : '' ) . '
			data-bs-touch="' .( $attributes['touch'] ? 'true' : 'false') . '"
			data-bs-pause="' .( $attributes['pause'] ? esc_attr( $attributes['pause'] ) : 'hover') . '" 
			' . ( $interval !== false ? 'data-bs-interval="' . esc_attr( $interval ) . '"' : '' ) . '
		>
			' . $buttons . '
			' . $indicators . '
			' . $content . ' 
			<div class="clearfix"></div>
		</div>
	';

	return $output;
}