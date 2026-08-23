<?php
/**
 * Personalizador: todas las imágenes del sitio editables desde el escritorio.
 *
 * Cada imagen del tema tiene un archivo por defecto en assets/images. Si el
 * administrador sube una propia desde Apariencia → Personalizar, esa sustituye
 * a la incluida sin tocar ningún archivo por FTP.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Mapa de imágenes del tema: archivo por defecto => datos del control.
 *
 * La clave es la ruta relativa dentro de assets/images, que es exactamente
 * lo que reciben rv_img() y las plantillas.
 *
 * @return array<string,array{key:string,label:string,section:string,desc:string}>
 */
function rv_image_map() {
	return array(
		// --- Portada y cabeceras de sección ---
		'hero.jpg' => array(
			'key'     => 'hero',
			'label'   => __( 'Portada — imagen principal', 'rescate-vertical' ),
			'section' => 'rv_imgs_paginas',
			'desc'    => __( 'Fondo del bloque de bienvenida y cabecera de "Física del rescate". Horizontal, mínimo 1500 px de ancho.', 'rescate-vertical' ),
		),
		'que-es.jpg' => array(
			'key'     => 'que_es',
			'label'   => __( 'Qué es el rescate vertical', 'rescate-vertical' ),
			'section' => 'rv_imgs_paginas',
			'desc'    => __( 'Aparece en la portada y en la cabecera de esa sección.', 'rescate-vertical' ),
		),
		'tecnicas.jpg' => array(
			'key'     => 'tecnicas',
			'label'   => __( 'Técnicas', 'rescate-vertical' ),
			'section' => 'rv_imgs_paginas',
			'desc'    => __( 'Banda de la portada y cabecera de "Técnicas".', 'rescate-vertical' ),
		),
		'equipos.jpg' => array(
			'key'     => 'equipos',
			'label'   => __( 'Equipos', 'rescate-vertical' ),
			'section' => 'rv_imgs_paginas',
			'desc'    => __( 'Cabecera de la sección "Equipos".', 'rescate-vertical' ),
		),
		'protocolos.jpg' => array(
			'key'     => 'protocolos',
			'label'   => __( 'Protocolos y normativas', 'rescate-vertical' ),
			'section' => 'rv_imgs_paginas',
			'desc'    => __( 'Cabecera de "Protocolos" y foto de la sección "Practicar".', 'rescate-vertical' ),
		),
		'practicar.jpg' => array(
			'key'     => 'practicar',
			'label'   => __( 'Practicar en digital', 'rescate-vertical' ),
			'section' => 'rv_imgs_paginas',
			'desc'    => __( 'Portada y cabecera de "Practicar en digital".', 'rescate-vertical' ),
		),

		// --- Nudos ---
		'nudos/nudo-ocho.jpg' => array(
			'key'     => 'nudo_ocho',
			'label'   => __( 'Nudo ocho', 'rescate-vertical' ),
			'section' => 'rv_imgs_nudos',
			'desc'    => __( 'Formato horizontal 4:3, unos 900 px de ancho.', 'rescate-vertical' ),
		),
		'nudos/ballestrinque.jpg' => array(
			'key'     => 'ballestrinque',
			'label'   => __( 'Ballestrinque', 'rescate-vertical' ),
			'section' => 'rv_imgs_nudos',
			'desc'    => '',
		),
		'nudos/prusik.jpg' => array(
			'key'     => 'prusik',
			'label'   => __( 'Prusik', 'rescate-vertical' ),
			'section' => 'rv_imgs_nudos',
			'desc'    => '',
		),
		'nudos/mariposa.jpg' => array(
			'key'     => 'mariposa',
			'label'   => __( 'Mariposa', 'rescate-vertical' ),
			'section' => 'rv_imgs_nudos',
			'desc'    => '',
		),

		// --- Fichas de equipo ---
		'equipos/cuerda.jpg' => array(
			'key'     => 'eq_cuerda',
			'label'   => __( 'Cuerda dinámica', 'rescate-vertical' ),
			'section' => 'rv_imgs_equipos',
			'desc'    => __( 'Formato horizontal 4:3, unos 900 px de ancho.', 'rescate-vertical' ),
		),
		'equipos/arnes.jpg' => array(
			'key'     => 'eq_arnes',
			'label'   => __( 'Arnés integral', 'rescate-vertical' ),
			'section' => 'rv_imgs_equipos',
			'desc'    => '',
		),
		'equipos/mosqueton.jpg' => array(
			'key'     => 'eq_mosqueton',
			'label'   => __( 'Mosquetón', 'rescate-vertical' ),
			'section' => 'rv_imgs_equipos',
			'desc'    => '',
		),
		'equipos/descensor.jpg' => array(
			'key'     => 'eq_descensor',
			'label'   => __( 'Descensor', 'rescate-vertical' ),
			'section' => 'rv_imgs_equipos',
			'desc'    => '',
		),
		'equipos/casco.jpg' => array(
			'key'     => 'eq_casco',
			'label'   => __( 'Casco', 'rescate-vertical' ),
			'section' => 'rv_imgs_equipos',
			'desc'    => '',
		),
		'equipos/camilla.jpg' => array(
			'key'     => 'eq_camilla',
			'label'   => __( 'Camilla Stokes', 'rescate-vertical' ),
			'section' => 'rv_imgs_equipos',
			'desc'    => '',
		),
	);
}

/**
 * Registra en el Personalizador un control por cada imagen del tema.
 *
 * @param WP_Customize_Manager $wp_customize Gestor del personalizador.
 */
function rv_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'rv_imagenes',
		array(
			'title'       => __( 'Imágenes del sitio', 'rescate-vertical' ),
			'description' => __( 'Cambia cualquier fotografía del sitio sin tocar archivos. Sube la tuya y pulsa Publicar; para volver a la incluida en el tema, usa "Eliminar".', 'rescate-vertical' ),
			'priority'    => 25,
		)
	);

	$sections = array(
		'rv_imgs_paginas' => __( 'Portada y cabeceras', 'rescate-vertical' ),
		'rv_imgs_nudos'   => __( 'Fotos de nudos', 'rescate-vertical' ),
		'rv_imgs_equipos' => __( 'Fotos de equipos', 'rescate-vertical' ),
	);
	$i = 10;
	foreach ( $sections as $id => $title ) {
		$wp_customize->add_section(
			$id,
			array(
				'title'    => $title,
				'panel'    => 'rv_imagenes',
				'priority' => $i,
			)
		);
		$i += 10;
	}

	foreach ( rv_image_map() as $file => $data ) {
		$setting = 'rv_img_' . $data['key'];

		$wp_customize->add_setting(
			$setting,
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				$setting,
				array(
					'label'       => $data['label'],
					'description' => $data['desc'],
					'section'     => $data['section'],
				)
			)
		);
	}
}
add_action( 'customize_register', 'rv_customize_register' );

/**
 * Aviso en el escritorio con el atajo al personalizador de imágenes.
 */
function rv_images_admin_menu() {
	add_theme_page(
		__( 'Imágenes del sitio', 'rescate-vertical' ),
		__( 'Imágenes del sitio', 'rescate-vertical' ),
		'edit_theme_options',
		'rv-imagenes',
		'rv_images_admin_page'
	);
}
add_action( 'admin_menu', 'rv_images_admin_menu' );

/**
 * Página guía: explica dónde se cambia cada imagen y enlaza al personalizador.
 */
function rv_images_admin_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$groups = array(
		'rv_imgs_paginas' => __( 'Portada y cabeceras', 'rescate-vertical' ),
		'rv_imgs_nudos'   => __( 'Fotos de nudos', 'rescate-vertical' ),
		'rv_imgs_equipos' => __( 'Fotos de equipos', 'rescate-vertical' ),
	);
	$map = rv_image_map();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Imágenes del sitio', 'rescate-vertical' ); ?></h1>
		<p><?php esc_html_e( 'Todas las fotografías del sitio se cambian desde el Personalizador: subes la tuya desde la biblioteca de medios y se sustituye al instante. No hace falta tocar archivos ni FTP.', 'rescate-vertical' ); ?></p>

		<?php foreach ( $groups as $section => $title ) : ?>
			<h2><?php echo esc_html( $title ); ?></h2>
			<p>
				<a class="button button-primary"
					href="<?php echo esc_url( admin_url( 'customize.php?autofocus[section]=' . $section ) ); ?>">
					<?php esc_html_e( 'Cambiar estas imágenes', 'rescate-vertical' ); ?>
				</a>
			</p>
			<table class="widefat striped" style="max-width:900px;margin-bottom:26px;">
				<thead>
					<tr>
						<th style="width:230px;"><?php esc_html_e( 'Imagen', 'rescate-vertical' ); ?></th>
						<th style="width:110px;"><?php esc_html_e( 'Estado', 'rescate-vertical' ); ?></th>
						<th><?php esc_html_e( 'Vista previa', 'rescate-vertical' ); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				foreach ( $map as $file => $data ) :
					if ( $data['section'] !== $section ) {
						continue;
					}
					$custom = get_theme_mod( 'rv_img_' . $data['key'] );
					?>
					<tr>
						<td><strong><?php echo esc_html( $data['label'] ); ?></strong></td>
						<td>
							<?php if ( $custom ) : ?>
								<span style="color:#1F7A4D;font-weight:600;"><?php esc_html_e( 'Personalizada', 'rescate-vertical' ); ?></span>
							<?php else : ?>
								<span style="color:#666;"><?php esc_html_e( 'Por defecto', 'rescate-vertical' ); ?></span>
							<?php endif; ?>
						</td>
						<td>
							<img src="<?php echo esc_url( rv_img( $file ) ); ?>" alt=""
								style="height:56px;width:auto;border-radius:3px;display:block;">
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endforeach; ?>
	</div>
	<?php
}
