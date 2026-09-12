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
 * Sanitize a comma-separated list of numeric product IDs.
 *
 * @param string $value Raw value.
 * @return string
 */
function spray_nova_sanitize_product_ids( $value ) {
	$ids = array_filter( array_map( 'absint', preg_split( '/[\s,]+/', (string) $value ) ) );
	return implode( ',', array_values( array_unique( $ids ) ) );
}

/**
 * Register Spray Nova customization fields.
 *
 * @param WP_Customize_Manager $wp_customize Customizer instance.
 */
function spray_nova_customize_register( $wp_customize ) {
	$wp_customize->add_panel( 'spray_nova_home', array(
		'title'    => __( 'Portada Spray Nova', 'spray-nova' ),
		'priority' => 30,
	) );

	$sections = array(
		'spray_nova_general'    => __( 'Orden y ajustes generales', 'spray-nova' ),
		'spray_nova_hero'       => __( 'Portada y vídeo', 'spray-nova' ),
		'spray_nova_products'   => __( 'Productos destacados', 'spray-nova' ),
		'spray_nova_categories' => __( 'Categorías', 'spray-nova' ),
		'spray_nova_newsletter' => __( 'Newsletter', 'spray-nova' ),
		'spray_nova_social'     => __( 'Redes sociales', 'spray-nova' ),
	);

	foreach ( $sections as $section_id => $section_title ) {
		$wp_customize->add_section( $section_id, array(
			'title' => $section_title,
			'panel' => 'spray_nova_home',
		) );
	}

	$fields = array(
		'show_announcement' => array( 'label' => __( 'Mostrar barra de anuncios', 'spray-nova' ), 'default' => true, 'type' => 'checkbox' ),
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
			'default' => 'Sprays, markers y material para graffiti.',
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
		'show_ticker' => array(
			'label'   => __( 'Mostrar banda animada', 'spray-nova' ),
			'default' => true,
			'type'    => 'checkbox',
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
		'categories_description' => array(
			'label'   => __( 'Descripción breve de categorías', 'spray-nova' ),
			'default' => 'Explora sprays, rotuladores y ceras para distintas superficies, estilos y formas de trabajar.',
			'type'    => 'textarea',
		),
		'categories_all_label' => array(
			'label'   => __( 'Texto del enlace Ver todo', 'spray-nova' ),
			'default' => 'Ver todo',
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
		'products_description' => array(
			'label'   => __( 'Descripción breve de productos destacados', 'spray-nova' ),
			'default' => 'Una selección de material para graffiti con formatos, acabados y colores para cada proyecto.',
			'type'    => 'textarea',
		),
		'products_show_filters' => array(
			'label'   => __( 'Mostrar filtros de categorías', 'spray-nova' ),
			'default' => true,
			'type'    => 'checkbox',
		),
		'products_all_filter_label' => array(
			'label'   => __( 'Nombre del filtro general', 'spray-nova' ),
			'default' => 'Todos',
		),
		'shop_description' => array(
			'label'   => __( 'Descripción breve de la tienda', 'spray-nova' ),
			'default' => 'Sprays, markers, ceras y material seleccionado para graffiti. Compara formatos, presiones y acabados para encontrar la herramienta adecuada para cada trabajo.',
			'type'    => 'textarea',
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
		if ( 0 === strpos( $key, 'hero_' ) ) {
			$section = 'spray_nova_hero';
		} elseif ( 0 === strpos( $key, 'products_' ) || 'show_products' === $key ) {
			$section = 'spray_nova_products';
		} elseif ( 0 === strpos( $key, 'categories_' ) || 0 === strpos( $key, 'category_' ) || 'show_categories' === $key ) {
			$section = 'spray_nova_categories';
		} elseif ( 0 === strpos( $key, 'newsletter_' ) || 'show_newsletter' === $key ) {
			$section = 'spray_nova_newsletter';
		} elseif ( in_array( $key, array( 'instagram_url', 'tiktok_url' ), true ) ) {
			$section = 'spray_nova_social';
		} else {
			$section = 'spray_nova_general';
		}

		$wp_customize->add_control( $setting, array(
			'section'     => $section,
			'label'       => $field['label'],
			'description' => isset( $field['description'] ) ? $field['description'] : '',
			'type'        => isset( $field['type'] ) ? $field['type'] : 'text',
		) );
	}

	$category_images = array(
		'category_sprays_image'  => __( 'Imagen de categoría: Sprays', 'spray-nova' ),
		'category_markers_image' => __( 'Imagen de categoría: Rotuladores', 'spray-nova' ),
		'category_wax_image'     => __( 'Imagen de categoría: Ceras', 'spray-nova' ),
	);

	foreach ( $category_images as $key => $label ) {
		$setting = 'spray_nova_' . $key;

		$wp_customize->add_setting( $setting, array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		) );
		$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, $setting, array(
			'section'     => 'spray_nova_categories',
			'label'       => $label,
			'description' => __( 'Si no eliges una imagen, se usará la miniatura de la categoría de WooCommerce.', 'spray-nova' ),
			'mime_type'   => 'image',
		) ) );
	}

	$select_controls = array(
		'spray_nova_content_order' => array(
			'section' => 'spray_nova_general',
			'label'   => __( 'Orden de las secciones centrales', 'spray-nova' ),
			'default' => 'products_categories',
			'choices' => array(
				'products_categories' => __( 'Productos y después categorías', 'spray-nova' ),
				'categories_products' => __( 'Categorías y después productos', 'spray-nova' ),
			),
		),
		'spray_nova_hero_video_fit' => array(
			'section' => 'spray_nova_hero',
			'label'   => __( 'Ajuste del vídeo', 'spray-nova' ),
			'default' => 'cover',
			'choices' => array( 'cover' => __( 'Rellenar el espacio', 'spray-nova' ), 'contain' => __( 'Mostrar el vídeo completo', 'spray-nova' ) ),
		),
		'spray_nova_hero_video_position' => array(
			'section' => 'spray_nova_hero',
			'label'   => __( 'Posición del vídeo', 'spray-nova' ),
			'default' => 'center',
			'choices' => array( 'top' => __( 'Arriba', 'spray-nova' ), 'center' => __( 'Centro', 'spray-nova' ), 'bottom' => __( 'Abajo', 'spray-nova' ) ),
		),
		'spray_nova_products_source' => array(
			'section' => 'spray_nova_products',
			'label'   => __( 'Productos que se muestran', 'spray-nova' ),
			'default' => 'featured',
			'choices' => array( 'featured' => __( 'Marcados como destacados', 'spray-nova' ), 'recent' => __( 'Más recientes', 'spray-nova' ) ),
		),
		'spray_nova_product_image_ratio' => array(
			'section' => 'spray_nova_products',
			'label'   => __( 'Proporción de las fotos de producto', 'spray-nova' ),
			'default' => 'portrait',
			'choices' => array( 'portrait' => __( 'Vertical', 'spray-nova' ), 'square' => __( 'Cuadrada', 'spray-nova' ), 'landscape' => __( 'Horizontal', 'spray-nova' ) ),
		),
		'spray_nova_category_order' => array(
			'section' => 'spray_nova_categories',
			'label'   => __( 'Orden de las categorías', 'spray-nova' ),
			'default' => 'sprays_markers_wax',
			'choices' => array(
				'sprays_markers_wax' => __( 'Sprays, Rotuladores, Ceras', 'spray-nova' ),
				'sprays_wax_markers' => __( 'Sprays, Ceras, Rotuladores', 'spray-nova' ),
				'markers_sprays_wax' => __( 'Rotuladores, Sprays, Ceras', 'spray-nova' ),
				'markers_wax_sprays' => __( 'Rotuladores, Ceras, Sprays', 'spray-nova' ),
				'wax_sprays_markers' => __( 'Ceras, Sprays, Rotuladores', 'spray-nova' ),
				'wax_markers_sprays' => __( 'Ceras, Rotuladores, Sprays', 'spray-nova' ),
			),
		),
		'spray_nova_category_image_fit' => array(
			'section' => 'spray_nova_categories',
			'label'   => __( 'Ajuste de las fotos de categoría', 'spray-nova' ),
			'default' => 'cover',
			'choices' => array( 'cover' => __( 'Rellenar la tarjeta', 'spray-nova' ), 'contain' => __( 'Mostrar la imagen completa', 'spray-nova' ) ),
		),
		'spray_nova_category_image_position' => array(
			'section' => 'spray_nova_categories',
			'label'   => __( 'Posición de las fotos de categoría', 'spray-nova' ),
			'default' => 'center',
			'choices' => array( 'top' => __( 'Arriba', 'spray-nova' ), 'center' => __( 'Centro', 'spray-nova' ), 'bottom' => __( 'Abajo', 'spray-nova' ) ),
		),
	);

	foreach ( $select_controls as $setting => $control ) {
		$wp_customize->add_setting( $setting, array( 'default' => $control['default'], 'sanitize_callback' => 'sanitize_key' ) );
		$wp_customize->add_control( $setting, array(
			'section' => $control['section'],
			'label'   => $control['label'],
			'type'    => 'select',
			'choices' => $control['choices'],
		) );
	}

	$range_controls = array(
		'spray_nova_section_padding'       => array( 'section' => 'spray_nova_general', 'label' => __( 'Espaciado vertical de las secciones (px)', 'spray-nova' ), 'default' => 56, 'min' => 20, 'max' => 180, 'step' => 5 ),
		'spray_nova_section_title_size'    => array( 'section' => 'spray_nova_general', 'label' => __( 'Tamaño máximo de títulos (px)', 'spray-nova' ), 'default' => 70, 'min' => 42, 'max' => 100, 'step' => 2 ),
		'spray_nova_hero_media_width'      => array( 'section' => 'spray_nova_hero', 'label' => __( 'Anchura del vídeo en escritorio (%)', 'spray-nova' ), 'default' => 55, 'min' => 40, 'max' => 70, 'step' => 1 ),
		'spray_nova_hero_min_height'       => array( 'section' => 'spray_nova_hero', 'label' => __( 'Altura de portada en escritorio (px)', 'spray-nova' ), 'default' => 680, 'min' => 480, 'max' => 960, 'step' => 10 ),
		'spray_nova_hero_mobile_height'    => array( 'section' => 'spray_nova_hero', 'label' => __( 'Altura del vídeo en móvil (px)', 'spray-nova' ), 'default' => 360, 'min' => 240, 'max' => 700, 'step' => 10 ),
		'spray_nova_products_count'        => array( 'section' => 'spray_nova_products', 'label' => __( 'Número de productos', 'spray-nova' ), 'default' => 4, 'min' => 1, 'max' => 12, 'step' => 1 ),
		'spray_nova_products_columns'      => array( 'section' => 'spray_nova_products', 'label' => __( 'Columnas de productos en escritorio', 'spray-nova' ), 'default' => 4, 'min' => 2, 'max' => 4, 'step' => 1 ),
		'spray_nova_category_card_height'  => array( 'section' => 'spray_nova_categories', 'label' => __( 'Altura de tarjetas de categoría (px)', 'spray-nova' ), 'default' => 430, 'min' => 280, 'max' => 700, 'step' => 10 ),
	);

	foreach ( $range_controls as $setting => $control ) {
		$wp_customize->add_setting( $setting, array( 'default' => $control['default'], 'sanitize_callback' => 'absint' ) );
		$wp_customize->add_control( $setting, array(
			'section'     => $control['section'],
			'label'       => $control['label'],
			'type'        => 'range',
			'input_attrs' => array( 'min' => $control['min'], 'max' => $control['max'], 'step' => $control['step'] ),
		) );
	}

	$wp_customize->add_setting( 'spray_nova_products_ids', array( 'default' => '', 'sanitize_callback' => 'spray_nova_sanitize_product_ids' ) );
	$wp_customize->add_control( 'spray_nova_products_ids', array(
		'section'     => 'spray_nova_products',
		'label'       => __( 'Elegir productos manualmente por ID', 'spray-nova' ),
		'description' => __( 'Opcional. Separa los ID con comas y se respetará ese orden. Si está vacío se usa la selección anterior.', 'spray-nova' ),
		'type'        => 'text',
	) );

	$category_text_controls = array(
		'spray_nova_category_sprays_slug'   => array( 'label' => __( 'Slug categoría Sprays', 'spray-nova' ), 'default' => 'sprays' ),
		'spray_nova_category_markers_slug'  => array( 'label' => __( 'Slug categoría Rotuladores', 'spray-nova' ), 'default' => 'rotuladores' ),
		'spray_nova_category_wax_slug'      => array( 'label' => __( 'Slug categoría Ceras', 'spray-nova' ), 'default' => 'ceras' ),
		'spray_nova_category_sprays_number' => array( 'label' => __( 'Número categoría Sprays', 'spray-nova' ), 'default' => '01' ),
		'spray_nova_category_markers_number'=> array( 'label' => __( 'Número categoría Rotuladores', 'spray-nova' ), 'default' => '02' ),
		'spray_nova_category_wax_number'    => array( 'label' => __( 'Número categoría Ceras', 'spray-nova' ), 'default' => '03' ),
	);

	foreach ( $category_text_controls as $setting => $control ) {
		$is_slug = false !== strpos( $setting, '_slug' );
		$wp_customize->add_setting( $setting, array( 'default' => $control['default'], 'sanitize_callback' => $is_slug ? 'sanitize_title' : 'sanitize_text_field' ) );
		$wp_customize->add_control( $setting, array( 'section' => 'spray_nova_categories', 'label' => $control['label'], 'type' => 'text' ) );
	}

	$hero_media = array(
		'spray_nova_hero_video'        => array( 'label' => __( 'Vídeo de portada', 'spray-nova' ), 'mime_type' => 'video' ),
		'spray_nova_hero_video_poster' => array( 'label' => __( 'Imagen previa del vídeo', 'spray-nova' ), 'mime_type' => 'image' ),
	);

	foreach ( $hero_media as $setting => $control ) {
		$wp_customize->add_setting( $setting, array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
		$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, $setting, array(
			'section'     => 'spray_nova_hero',
			'label'       => $control['label'],
			'description' => __( 'Si no eliges un archivo se utiliza el vídeo incluido en el tema.', 'spray-nova' ),
			'mime_type'   => $control['mime_type'],
		) ) );
	}
}
add_action( 'customize_register', 'spray_nova_customize_register' );
