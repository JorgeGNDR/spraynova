<?php
/** Homepage presentation settings. CSS owns layout; PHP supplies validated values only. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function spray_nova_home_assets() {
	wp_enqueue_style( 'spray-nova-home', get_template_directory_uri() . '/assets/css/home.css', array( 'spray-nova-theme' ), SPRAY_NOVA_VERSION );
	$clamp = static function ( $value, $minimum, $maximum ) {
		return max( $minimum, min( $maximum, absint( $value ) ) );
	};
	$section_padding = $clamp( get_theme_mod( 'spray_nova_section_padding', 56 ), 20, 180 );
	$title_size      = $clamp( get_theme_mod( 'spray_nova_section_title_size', 70 ), 42, 100 );
	$video_width     = $clamp( get_theme_mod( 'spray_nova_hero_media_width', 55 ), 40, 70 );
	$hero_height     = $clamp( get_theme_mod( 'spray_nova_hero_min_height', 680 ), 480, 960 );
	$mobile_height   = $clamp( get_theme_mod( 'spray_nova_hero_mobile_height', 360 ), 240, 700 );
	$product_columns = $clamp( get_theme_mod( 'spray_nova_products_columns', 4 ), 2, 4 );
	$category_height = $clamp( get_theme_mod( 'spray_nova_category_card_height', 430 ), 280, 700 );
	$video_fit       = 'contain' === get_theme_mod( 'spray_nova_hero_video_fit', 'cover' ) ? 'contain' : 'cover';
	$video_position  = in_array( get_theme_mod( 'spray_nova_hero_video_position', 'center' ), array( 'top', 'center', 'bottom' ), true ) ? get_theme_mod( 'spray_nova_hero_video_position', 'center' ) : 'center';
	$category_fit    = 'contain' === get_theme_mod( 'spray_nova_category_image_fit', 'cover' ) ? 'contain' : 'cover';
	$category_pos    = in_array( get_theme_mod( 'spray_nova_category_image_position', 'center' ), array( 'top', 'center', 'bottom' ), true ) ? get_theme_mod( 'spray_nova_category_image_position', 'center' ) : 'center';
	$ratio_key       = get_theme_mod( 'spray_nova_product_image_ratio', 'portrait' );
	$ratios          = array( 'portrait' => '0.83', 'square' => '1', 'landscape' => '1.28' );
	$product_ratio   = isset( $ratios[ $ratio_key ] ) ? $ratios[ $ratio_key ] : $ratios['portrait'];
	$copy_width      = 100 - $video_width;

	$custom_css = sprintf(
		':root{--home-copy:%1$dfr;--home-media:%2$dfr;--home-height:%3$dpx;--video-fit:%4$s;--video-position:%5$s;--category-fit:%6$s;--category-position:%7$s;--product-ratio:%8$s;--home-title:%9$dpx;--product-columns:%10$d;--home-space:%11$dpx;--category-height:%12$dpx;--mobile-video-height:%13$dpx}',
		$copy_width, $video_width, $hero_height, $video_fit, $video_position, $category_fit, $category_pos, $product_ratio, $title_size, $product_columns, $section_padding, $category_height, $mobile_height
	);
	wp_add_inline_style( 'spray-nova-home', $custom_css );
}
