<?php
/**
 * Customizer options.
 *
 * @package SprayNova
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sanitize checkbox values.
 *
 * @param mixed $checked Checkbox value.
 * @return bool
 */
function spray_nova_sanitize_checkbox( $checked ) {
	return ( isset( $checked ) && true === (bool) $checked );
}

/**
 * Register Spray Nova customization fields.
 *
 * @param WP_Customize_Manager $wp_customize Customizer instance.
 */
function spray_nova_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'spray_nova_store', array(
		'title'    => __( 'Spray Nova', 'spray-nova' ),
		'priority' => 30,
	) );

	$fields = array(
		'announcement' => array(
			'label'   => __( 'Barra de anuncio', 'spray-nova' ),
			'default' => 'Envío gratis desde 60 € · Entrega 24/48 h en península',
		),
		'hero_kicker' => array(
			'label'   => __( 'Antetítulo de portada', 'spray-nova' ),
			'default' => 'Material para dejar huella',
		),
		'hero_title' => array(
			'label'   => __( 'Título principal de portada', 'spray-nova' ),
			'default' => "COLOR.\nCONTROL.\nACTITUD.",
			'type'    => 'textarea',
		),
		'hero_text' => array(
			'label'   => __( 'Texto de portada', 'spray-nova' ),
			'default' => 'Sprays, rotuladores y ceras seleccionados para que tu idea llegue del boceto al muro.',
			'type'    => 'textarea',
		),
		'hero_primary_button' => array(
			'label'   => __( 'Botón principal de portada', 'spray-nova' ),
			'default' => 'Ver productos',
		),
		'hero_secondary_button' => array(
			'label'   => __( 'Botón secundario de portada', 'spray-nova' ),
			'default' => 'Explorar categorías',
		),
		'ticker_text' => array(
			'label'       => __( 'Frases de la banda animada', 'spray-nova' ),
			'description' => __( 'Separa frases con barras verticales: Sprays | Rotuladores | Ceras', 'spray-nova' ),
			'default'     => '+250 COLORES | MARCAS SELECCIONADAS | PARA TODAS LAS SUPERFICIES',
			'type'        => 'textarea',
		),
		'show_categories' => array(
			'label'   => __( 'Mostrar sección de categorías', 'spray-nova' ),
			'default' => true,
			'type'    => 'checkbox',
		),
		'categories_kicker' => array(
			'label'   => __( 'Antetítulo de categorías', 'spray-nova' ),
			'default' => 'Encuentra tu herramienta',
		),
		'categories_title' => array(
			'label'   => __( 'Título de categorías', 'spray-nova' ),
			'default' => 'COMPRA POR CATEGORÍA',
		),
		'category_sprays_label' => array(
			'label'   => __( 'Nombre categoría Sprays', 'spray-nova' ),
			'default' => 'SPRAYS',
		),
		'category_markers_label' => array(
			'label'   => __( 'Nombre categoría Rotuladores', 'spray-nova' ),
			'default' => 'ROTULADORES',
		),
		'category_wax_label' => array(
			'label'   => __( 'Nombre categoría Ceras', 'spray-nova' ),
			'default' => 'CERAS',
		),
		'show_products' => array(
			'label'   => __( 'Mostrar productos destacados', 'spray-nova' ),
			'default' => true,
			'type'    => 'checkbox',
		),
		'products_kicker' => array(
			'label'   => __( 'Antetítulo de productos', 'spray-nova' ),
			'default' => 'Lo último en llegar',
		),
		'products_title' => array(
			'label'   => __( 'Título de productos', 'spray-nova' ),
			'default' => 'PRODUCTOS DESTACADOS',
		),
		'show_story' => array(
			'label'   => __( 'Mostrar sección nosotros', 'spray-nova' ),
			'default' => true,
			'type'    => 'checkbox',
		),
		'story_kicker' => array(
			'label'   => __( 'Antetítulo de historia', 'spray-nova' ),
			'default' => 'Spray Nova Graffiti Shop',
		),
		'story_title' => array(
			'label'   => __( 'Título de historia', 'spray-nova' ),
			'default' => 'HECHO PARA QUIENES NO DEJAN EL MURO EN BLANCO.',
			'type'    => 'textarea',
		),
		'story_text' => array(
			'label'   => __( 'Texto de historia', 'spray-nova' ),
			'default' => 'Material fiable, colores que responden y atención cercana. Seleccionamos cada producto pensando en escritores, ilustradores y gente que crea a su manera.',
			'type'    => 'textarea',
		),
		'story_button' => array(
			'label'   => __( 'Botón de historia', 'spray-nova' ),
			'default' => 'Conócenos',
		),
		'show_newsletter' => array(
			'label'   => __( 'Mostrar newsletter', 'spray-nova' ),
			'default' => true,
			'type'    => 'checkbox',
		),
		'newsletter_kicker' => array(
			'label'   => __( 'Antetítulo newsletter', 'spray-nova' ),
			'default' => 'Sin spam. Solo color.',
		),
		'newsletter_title' => array(
			'label'   => __( 'Título newsletter', 'spray-nova' ),
			'default' => 'NOVEDADES EN TU BANDEJA',
		),
		'newsletter_placeholder' => array(
			'label'   => __( 'Placeholder email newsletter', 'spray-nova' ),
			'default' => 'Tu email',
		),
		'newsletter_button' => array(
			'label'   => __( 'Botón newsletter', 'spray-nova' ),
			'default' => 'Suscribirme',
		),
		'instagram_url' => array(
			'label'   => __( 'URL de Instagram', 'spray-nova' ),
			'default' => '',
			'type'    => 'url',
		),
		'tiktok_url' => array(
			'label'   => __( 'URL de TikTok', 'spray-nova' ),
			'default' => '',
			'type'    => 'url',
		),
	);

	foreach ( $fields as $key => $field ) {
		$setting           = 'spray_nova_' . $key;
		$sanitize_callback = 'sanitize_text_field';

		if ( isset( $field['type'] ) && 'url' === $field['type'] ) {
			$sanitize_callback = 'esc_url_raw';
		} elseif ( isset( $field['type'] ) && 'textarea' === $field['type'] ) {
			$sanitize_callback = 'sanitize_textarea_field';
		} elseif ( isset( $field['type'] ) && 'checkbox' === $field['type'] ) {
			$sanitize_callback = 'spray_nova_sanitize_checkbox';
		}

		$wp_customize->add_setting( $setting, array(
			'default'           => $field['default'],
			'sanitize_callback' => $sanitize_callback,
		) );
		$wp_customize->add_control( $setting, array(
			'section'     => 'spray_nova_store',
			'label'       => $field['label'],
			'description' => isset( $field['description'] ) ? $field['description'] : '',
			'type'        => isset( $field['type'] ) ? $field['type'] : 'text',
		) );
	}
}
add_action( 'customize_register', 'spray_nova_customize_register' );
