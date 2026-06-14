<?php
/**
 * Customizer options.
 *
 * @package SprayNova
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
		'hero_text' => array(
			'label'   => __( 'Texto de portada', 'spray-nova' ),
			'default' => 'Sprays, rotuladores y ceras seleccionados para que tu idea llegue del boceto al muro.',
		),
		'story_title' => array(
			'label'   => __( 'Título de historia', 'spray-nova' ),
			'default' => 'HECHO PARA QUIENES NO DEJAN EL MURO EN BLANCO.',
		),
		'story_text' => array(
			'label'   => __( 'Texto de historia', 'spray-nova' ),
			'default' => 'Material fiable, colores que responden y atención cercana. Seleccionamos cada producto pensando en escritores, ilustradores y gente que crea a su manera.',
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
		$setting = 'spray_nova_' . $key;
		$wp_customize->add_setting( $setting, array(
			'default'           => $field['default'],
			'sanitize_callback' => isset( $field['type'] ) && 'url' === $field['type'] ? 'esc_url_raw' : 'sanitize_text_field',
		) );
		$wp_customize->add_control( $setting, array(
			'section' => 'spray_nova_store',
			'label'   => $field['label'],
			'type'    => isset( $field['type'] ) ? $field['type'] : 'text',
		) );
	}
}
add_action( 'customize_register', 'spray_nova_customize_register' );
