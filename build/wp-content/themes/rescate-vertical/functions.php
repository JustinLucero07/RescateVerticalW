<?php
/**
 * Rescate Vertical theme functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RV_THEME_VERSION', '3.4.0' );

require_once get_template_directory() . '/inc/customizer.php';

/**
 * Theme setup.
 */
function rv_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );

	/*
	 * Logo propio desde Apariencia > Personalizar > Identidad del sitio.
	 * Mientras no se suba ninguno, la cabecera muestra el icono del nudo
	 * ocho junto al nombre del sitio.
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 96,
			'width'       => 320,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Menú principal', 'rescate-vertical' ),
		)
	);
}
add_action( 'after_setup_theme', 'rv_theme_setup' );

/**
 * Enqueue styles, fonts and the scroll-reveal script.
 */
function rv_enqueue_assets() {
	wp_enqueue_style(
		'rv-google-fonts',
		'https://fonts.googleapis.com/css2?family=Archivo:wght@600;700;800&family=Chakra+Petch:wght@600;700&family=Source+Sans+3:wght@400;500;600;700&display=swap',
		array(),
		null
	);

	wp_enqueue_style( 'rv-style', get_stylesheet_uri(), array(), RV_THEME_VERSION );

	wp_enqueue_script(
		'rv-main',
		get_template_directory_uri() . '/assets/js/rv-main.js',
		array(),
		RV_THEME_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'rv_enqueue_assets' );

/**
 * Preload the hero image on the front page so it paints fast (LCP).
 */
function rv_preload_hero() {
	if ( ! is_front_page() ) {
		return;
	}
	printf(
		'<link rel="preload" as="image" href="%s" fetchpriority="high">' . "\n",
		esc_url( rv_img( 'hero.jpg' ) )
	);
}
add_action( 'wp_head', 'rv_preload_hero', 1 );

/**
 * Favicon del tema (nudo ocho). Se omite si el sitio ya tiene un
 * icono configurado en Ajustes > Generales, para no pisarlo.
 */
function rv_favicon() {
	if ( function_exists( 'has_site_icon' ) && has_site_icon() ) {
		return;
	}
	printf(
		'<link rel="icon" type="image/svg+xml" href="%s">' . "\n",
		esc_url( get_template_directory_uri() . '/assets/img/favicon.svg' )
	);
}
add_action( 'wp_head', 'rv_favicon' );

/**
 * Sections used by the fallback menu and the footer links.
 *
 * @return array<string,string> slug => label
 */
function rv_sections() {
	return array(
		'que-es'     => __( 'Qué es', 'rescate-vertical' ),
		'fisica'     => __( 'Física', 'rescate-vertical' ),
		'tecnicas'   => __( 'Técnicas', 'rescate-vertical' ),
		'equipos'    => __( 'Equipos', 'rescate-vertical' ),
		'protocolos' => __( 'Protocolos', 'rescate-vertical' ),
		'practicar'  => __( 'Practicar', 'rescate-vertical' ),
	);
}

/**
 * Permalink for a section slug, falling back to a pretty URL if the page
 * has not been created yet.
 *
 * @param string $slug Page slug.
 * @return string
 */
function rv_section_url( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
}

/**
 * Menu shown when no menu has been assigned to the "primary" location yet.
 */
function rv_fallback_menu() {
	echo '<ul>';
	foreach ( rv_sections() as $slug => $label ) {
		printf(
			'<li><a href="%s">%s</a></li>',
			esc_url( rv_section_url( $slug ) ),
			esc_html( $label )
		);
	}
	echo '</ul>';
}

/**
 * URL of a bundled theme image.
 *
 * @param string $file File name inside assets/images.
 * @return string
 */
function rv_img( $file ) {
	$file = ltrim( $file, '/' );

	/*
	 * Si el administrador subió una imagen propia desde
	 * Apariencia > Personalizar > Imágenes del sitio, esa manda sobre la
	 * incluida en el tema. Así todas las plantillas heredan el cambio sin
	 * tener que tocarlas una por una.
	 */
	if ( function_exists( 'rv_image_map' ) ) {
		$map = rv_image_map();
		if ( isset( $map[ $file ] ) ) {
			$custom = get_theme_mod( 'rv_img_' . $map[ $file ]['key'] );
			if ( $custom ) {
				return $custom;
			}
		}
	}

	return get_template_directory_uri() . '/assets/images/' . $file;
}

/**
 * Renders a page's featured image if set, otherwise the bundled default.
 *
 * @param string $default_file Bundled fallback file name.
 * @param string $alt          Alt text.
 * @param string $sizes        Responsive sizes attribute.
 */
function rv_section_image( $default_file, $alt, $sizes = '(max-width: 860px) 100vw, 45vw' ) {
	if ( has_post_thumbnail() ) {
		the_post_thumbnail(
			'large',
			array(
				'alt'     => $alt,
				'sizes'   => $sizes,
				'loading' => 'lazy',
			)
		);
		return;
	}
	printf(
		'<img src="%1$s" alt="%2$s" loading="lazy" decoding="async" sizes="%3$s">',
		esc_url( rv_img( $default_file ) ),
		esc_attr( $alt ),
		esc_attr( $sizes )
	);
}

if ( ! function_exists( 'rv_content_width' ) ) {
	/**
	 * Sets the global content width.
	 */
	function rv_content_width() {
		if ( ! isset( $GLOBALS['content_width'] ) ) {
			$GLOBALS['content_width'] = 1200;
		}
	}
	add_action( 'after_setup_theme', 'rv_content_width' );
}

/**
 * Technical schematic drawings used on the "Técnicas" cards.
 * Each is a line diagram on the card's light background.
 *
 * @param string $which One of: rapel, polipasto, anclaje, camilla.
 */
function rv_technique_diagram( $which ) {
	$grid = '<g stroke="#C3CED5" stroke-width="1" stroke-dasharray="3 5"><path d="M0 40 H400 M0 86 H400 M0 132 H400"/></g>';

	switch ( $which ) {
		case 'rapel':
			?>
			<svg viewBox="0 0 400 172" aria-hidden="true" focusable="false"><?php echo $grid; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<g stroke="#101820" stroke-width="2" fill="none" stroke-linecap="round">
					<path d="M158 26 H242"/><path d="M170 26 V16 M230 26 V16"/>
				</g>
				<circle cx="200" cy="36" r="6" fill="#FFFFFF" stroke="#101820" stroke-width="2"/>
				<path d="M200 42 V150" stroke="#D9502B" stroke-width="2.6" stroke-linecap="round"/>
				<path d="M205 42 V150" stroke="#D9502B" stroke-width="2.4" stroke-dasharray="1 8" opacity="0.5" stroke-linecap="round"/>
				<rect x="188" y="80" width="24" height="30" rx="4" fill="#FFFFFF" stroke="#101820" stroke-width="2"/>
				<circle cx="200" cy="89" r="4.2" fill="none" stroke="#101820" stroke-width="1.7"/>
				<circle cx="200" cy="101" r="3.2" fill="none" stroke="#101820" stroke-width="1.7"/>
				<g stroke="#1B7FC4" stroke-width="1.5" fill="none" stroke-linecap="round">
					<path d="M228 88 h16 M240 84 l4 4 -4 4"/><path d="M228 104 h16 M240 100 l4 4 -4 4"/>
				</g>
				<path d="M200 150 l-10 12 M200 150 l10 12" stroke="#101820" stroke-width="2" stroke-linecap="round"/>
				<g font-family="Source Sans 3, Segoe UI, sans-serif" letter-spacing="0.04em" font-size="9.5" font-weight="600" fill="#5A6873">
					<path d="M148 95 H130" stroke="#B0BDC6" stroke-width="1"/>
					<text x="62" y="98">FRENADO 2 kN</text>
				</g>
			</svg>
			<?php
			break;

		case 'polipasto':
			?>
			<svg viewBox="0 0 400 172" aria-hidden="true" focusable="false"><?php echo $grid; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<g stroke="#101820" stroke-width="2" fill="none" stroke-linecap="round">
					<path d="M120 24 H280"/><path d="M132 24 V14 M268 24 V14"/>
				</g>
				<circle cx="164" cy="40" r="11" fill="#FFFFFF" stroke="#101820" stroke-width="2"/>
				<circle cx="164" cy="40" r="3" fill="#101820"/>
				<circle cx="236" cy="40" r="11" fill="#FFFFFF" stroke="#101820" stroke-width="2"/>
				<circle cx="236" cy="40" r="3" fill="#101820"/>
				<circle cx="200" cy="118" r="12" fill="#FFFFFF" stroke="#101820" stroke-width="2"/>
				<circle cx="200" cy="118" r="3" fill="#101820"/>
				<g stroke="#D9502B" stroke-width="2.4" fill="none" stroke-linecap="round">
					<path d="M153 40 V112 A12 12 0 0 0 200 130"/>
					<path d="M200 106 A12 12 0 0 1 247 40"/>
					<path d="M247 40 V22"/>
				</g>
				<g stroke="#1B7FC4" stroke-width="1.6" fill="none" stroke-linecap="round">
					<path d="M268 60 V34 M264 42 l4 -6 4 6"/>
				</g>
				<path d="M200 130 V150" stroke="#101820" stroke-width="2" stroke-linecap="round"/>
				<rect x="184" y="150" width="32" height="14" rx="2" fill="#FFFFFF" stroke="#101820" stroke-width="2"/>
				<text x="292" y="64" font-family="Source Sans 3, Segoe UI, sans-serif" letter-spacing="0.04em" font-size="11" font-weight="700" fill="#D9502B">3:1</text>
				<g font-family="Source Sans 3, Segoe UI, sans-serif" letter-spacing="0.04em" font-size="9.5" font-weight="600" fill="#5A6873">
					<path d="M96 118 H140" stroke="#B0BDC6" stroke-width="1" stroke-dasharray="3 3"/>
					<text x="30" y="121">VENTAJA</text>
				</g>
			</svg>
			<?php
			break;

		case 'anclaje':
			?>
			<svg viewBox="0 0 400 172" aria-hidden="true" focusable="false"><?php echo $grid; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<path d="M96 30 H304" stroke="#101820" stroke-width="2.5" stroke-linecap="round"/>
				<g stroke="#8A98A2" stroke-width="1.4">
					<path d="M108 30 l-8 -10 M138 30 l-8 -10 M168 30 l-8 -10 M198 30 l-8 -10 M228 30 l-8 -10 M258 30 l-8 -10 M288 30 l-8 -10"/>
				</g>
				<circle cx="150" cy="42" r="7" fill="#FFFFFF" stroke="#101820" stroke-width="2"/>
				<circle cx="250" cy="42" r="7" fill="#FFFFFF" stroke="#101820" stroke-width="2"/>
				<path d="M150 49 L200 108" stroke="#D9502B" stroke-width="2.5" stroke-linecap="round"/>
				<path d="M250 49 L200 108" stroke="#D9502B" stroke-width="2.5" stroke-linecap="round"/>
				<path d="M170 72 A 42 42 0 0 0 230 72" fill="none" stroke="#1B7FC4" stroke-width="1.6" stroke-dasharray="4 3"/>
				<text x="181" y="90" font-family="Source Sans 3, Segoe UI, sans-serif" letter-spacing="0.04em" font-size="10.5" font-weight="700" fill="#1B7FC4">≤60°</text>
				<circle cx="200" cy="114" r="9" fill="#FFFFFF" stroke="#101820" stroke-width="2.4"/>
				<path d="M200 123 V148" stroke="#101820" stroke-width="2" stroke-linecap="round"/>
				<path d="M192 148 h16" stroke="#101820" stroke-width="2" stroke-linecap="round"/>
				<g stroke="#101820" stroke-width="1.8" fill="none" stroke-linecap="round">
					<path d="M200 152 V162 M196 158 l4 5 4 -5"/>
				</g>
				<g font-family="Source Sans 3, Segoe UI, sans-serif" letter-spacing="0.04em" font-size="9.5" font-weight="600" fill="#5A6873">
					<path d="M116 42 H86" stroke="#B0BDC6" stroke-width="1" stroke-dasharray="3 3"/>
					<text x="24" y="45">30 kN c/u</text>
				</g>
			</svg>
			<?php
			break;

		case 'camilla':
		default:
			?>
			<svg viewBox="0 0 400 172" aria-hidden="true" focusable="false"><?php echo $grid; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<circle cx="200" cy="30" r="8" fill="#FFFFFF" stroke="#101820" stroke-width="2.4"/>
				<g stroke="#D9502B" stroke-width="2.2" stroke-linecap="round">
					<path d="M200 38 L126 96"/><path d="M200 38 L170 96"/>
					<path d="M200 38 L230 96"/><path d="M200 38 L274 96"/>
				</g>
				<rect x="112" y="96" width="176" height="42" rx="8" fill="#FFFFFF" stroke="#101820" stroke-width="2.4"/>
				<g stroke="#8A98A2" stroke-width="1.3">
					<path d="M112 110 H288 M112 124 H288"/>
					<path d="M140 96 V138 M168 96 V138 M196 96 V138 M224 96 V138 M252 96 V138"/>
				</g>
				<g stroke="#1B7FC4" stroke-width="1.8" fill="none" stroke-linecap="round">
					<circle cx="140" cy="117" r="8"/>
					<path d="M150 117 H246"/><path d="M246 117 l10 -6 M246 117 l10 6"/>
				</g>
				<g fill="#101820">
					<circle cx="126" cy="96" r="3.4"/><circle cx="170" cy="96" r="3.4"/>
					<circle cx="230" cy="96" r="3.4"/><circle cx="274" cy="96" r="3.4"/>
				</g>
				<g font-family="Source Sans 3, Segoe UI, sans-serif" letter-spacing="0.04em" font-size="9.5" font-weight="600" fill="#5A6873">
					<path d="M96 117 H70" stroke="#B0BDC6" stroke-width="1" stroke-dasharray="3 3"/>
					<text x="8" y="120">SKED / NEST</text>
					<path d="M304 117 H330" stroke="#B0BDC6" stroke-width="1" stroke-dasharray="3 3"/>
					<text x="336" y="120">4 PTS</text>
				</g>
			</svg>
			<?php
			break;
	}
}

/**
 * Fotografías de prácticas reales que vienen con el tema, agrupadas por
 * maniobra. Las claves coinciden con los valores del campo "Esquema técnico"
 * de las fichas, para que cada sistema encuentre sus fotos solo.
 *
 * @return array<string,array<int,array{0:string,1:string}>> grupo => [archivo, alt]
 */
function rv_campo_fotos() {
	return array(
		'equipo'        => array(
			array( 'equipo-1.jpg', __( 'Material de rescate vertical tendido sobre la lona para su revisión antes de la práctica', 'rescate-vertical' ) ),
			array( 'equipo-2.jpg', __( 'Mosquetones, poleas, bloqueadores y descensores ordenados por tipo antes de repartirlos', 'rescate-vertical' ) ),
		),
		'polipasto'     => array(
			array( 'polipasto-1.jpg', __( 'Polipasto montado sobre placa multianclaje, con poleas y mosquetones de seguro', 'rescate-vertical' ) ),
			array( 'polipasto-2.jpg', __( 'Detalle del reenvío de la cuerda en el sistema de poleas', 'rescate-vertical' ) ),
			array( 'polipasto-3.jpg', __( 'Conjunto de poleas y conectores del polipasto en carga', 'rescate-vertical' ) ),
			array( 'polipasto-4.jpg', __( 'Vista general del polipasto instalado en la zona de trabajo', 'rescate-vertical' ) ),
		),
		'anclaje'       => array(
			array( 'anclaje-1.jpg', __( 'Anclajes con cintas sobre la estructura metálica de la torre de prácticas', 'rescate-vertical' ) ),
			array( 'anclaje-2.jpg', __( 'Punto de anclaje montado con cinta y conector sobre perfil estructural', 'rescate-vertical' ) ),
			array( 'anclaje-3.jpg', __( 'Reparto de las líneas de trabajo y de seguridad desde los anclajes', 'rescate-vertical' ) ),
		),
		'rapel'         => array(
			array( 'rapel-1.jpg', __( 'Descenso controlado desde la torre de prácticas con el equipo asegurando desde abajo', 'rescate-vertical' ) ),
			array( 'rapel-2.jpg', __( 'Instructor montando el sistema en la cabecera antes de autorizar el descenso', 'rescate-vertical' ) ),
		),
		'camilla'       => array(
			array( 'camilla-1.jpg', __( 'Camilla de rescate suspendida con el paciente asegurado y el rescatista acompañando la maniobra', 'rescate-vertical' ) ),
		),
		'entrenamiento' => array(
			array( 'entrenamiento-1.jpg', __( 'Alumnos preparando cuerdas y conectores en la zona de trabajo', 'rescate-vertical' ) ),
			array( 'entrenamiento-2.jpg', __( 'Práctica de montaje de sistemas al pie de la torre', 'rescate-vertical' ) ),
			array( 'entrenamiento-3.jpg', __( 'El equipo sigue la maniobra desde la zona segura', 'rescate-vertical' ) ),
			array( 'entrenamiento-4.jpg', __( 'Manejo y ordenado de las cuerdas durante el ejercicio', 'rescate-vertical' ) ),
			array( 'entrenamiento-5.jpg', __( 'Revisión del montaje antes de cargar el sistema', 'rescate-vertical' ) ),
			array( 'entrenamiento-6.jpg', __( 'Alumnos trabajando por parejas en la instalación de anclajes', 'rescate-vertical' ) ),
		),
	);
}

/**
 * Foto de cabecera de una tarjeta.
 *
 * Sustituye a los esquemas dibujados en las tarjetas de portada y de "Qué es".
 * Si el archivo no estuviera en el tema, cae al esquema técnico para que la
 * tarjeta nunca se quede vacía.
 *
 * @param string $file    Archivo dentro de assets/images/campo.
 * @param string $alt     Texto alternativo.
 * @param string $esquema Esquema de reserva para rv_technique_diagram().
 * @param int    $foco    Altura del encuadre en porcentaje: qué parte de la
 *                        foto queda dentro de la franja recortada.
 */
function rv_card_foto( $file, $alt, $esquema = '', $foco = 32 ) {
	$ruta = get_template_directory() . '/assets/images/campo/' . $file;

	if ( ! file_exists( $ruta ) ) {
		if ( $esquema ) {
			rv_technique_diagram( $esquema );
		}
		return;
	}

	printf(
		'<img src="%1$s" alt="%2$s" style="object-position:center %3$d%%" loading="lazy" decoding="async" sizes="(max-width: 700px) 100vw, 380px">',
		esc_url( get_template_directory_uri() . '/assets/images/campo/' . $file ),
		esc_attr( $alt ),
		(int) $foco
	);
}

/**
 * Pinta una galería de fotos de prácticas.
 *
 * Solo muestra los archivos que existan, así que si alguno se quita del tema
 * la sección sigue funcionando.
 *
 * @param string $grupo Clave de rv_campo_fotos().
 * @param array  $args  titulo, texto y clase adicional.
 */
function rv_campo_galeria( $grupo, $args = array() ) {
	$grupos = rv_campo_fotos();
	if ( ! isset( $grupos[ $grupo ] ) ) {
		return;
	}

	$args = wp_parse_args(
		$args,
		array(
			'titulo' => '',
			'texto'  => '',
			'clase'  => '',
		)
	);

	$base  = get_template_directory() . '/assets/images/campo/';
	$fotos = array();
	foreach ( $grupos[ $grupo ] as $foto ) {
		if ( file_exists( $base . $foto[0] ) ) {
			$fotos[] = $foto;
		}
	}
	if ( ! $fotos ) {
		return;
	}
	?>
	<div class="rv-campo <?php echo esc_attr( $args['clase'] ); ?>">
		<?php if ( $args['titulo'] ) : ?>
			<h4 class="rv-campo-titulo"><?php echo esc_html( $args['titulo'] ); ?></h4>
		<?php endif; ?>
		<?php if ( $args['texto'] ) : ?>
			<p class="rv-campo-texto"><?php echo esc_html( $args['texto'] ); ?></p>
		<?php endif; ?>
		<div class="rv-campo-grid">
			<?php foreach ( $fotos as $foto ) : ?>
				<figure class="rv-campo-item">
					<a href="<?php echo esc_url( get_template_directory_uri() . '/assets/images/campo/' . $foto[0] ); ?>" target="_blank" rel="noopener">
						<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/campo/' . $foto[0] ); ?>"
							alt="<?php echo esc_attr( $foto[1] ); ?>" loading="lazy" decoding="async">
					</a>
					<figcaption><?php echo esc_html( $foto[1] ); ?></figcaption>
				</figure>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}

/**
 * Vídeos de prácticas incluidos en el tema.
 *
 * @return array<int,array{0:string,1:string}> archivo y pie.
 */
function rv_campo_videos() {
	return array(
		array( 'practica-1.mp4', __( 'Montaje del sistema y manejo de las cuerdas al pie de la torre de prácticas', 'rescate-vertical' ) ),
		array( 'practica-2.mp4', __( 'Izado de la camilla con el rescatista acompañando al paciente durante la maniobra', 'rescate-vertical' ) ),
	);
}

/**
 * Pinta los vídeos de prácticas que estén presentes en el tema.
 *
 * @param array $args titulo y texto de encabezado.
 */
function rv_campo_videos_html( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'titulo' => '',
			'texto'  => '',
		)
	);

	$base   = get_template_directory() . '/assets/video/';
	$videos = array();
	foreach ( rv_campo_videos() as $video ) {
		if ( file_exists( $base . $video[0] ) ) {
			$videos[] = $video;
		}
	}
	if ( ! $videos ) {
		return;
	}
	?>
	<div class="rv-campo-videos">
		<?php if ( $args['titulo'] ) : ?>
			<h4 class="rv-campo-titulo"><?php echo esc_html( $args['titulo'] ); ?></h4>
		<?php endif; ?>
		<?php if ( $args['texto'] ) : ?>
			<p class="rv-campo-texto"><?php echo esc_html( $args['texto'] ); ?></p>
		<?php endif; ?>
		<div class="rv-campo-videos-grid">
			<?php foreach ( $videos as $video ) : ?>
				<figure class="rv-campo-video">
					<video controls preload="metadata" playsinline>
						<source src="<?php echo esc_url( get_template_directory_uri() . '/assets/video/' . $video[0] ); ?>" type="video/mp4">
						<?php esc_html_e( 'Tu navegador no puede reproducir este vídeo.', 'rescate-vertical' ); ?>
					</video>
					<figcaption><?php echo esc_html( $video[1] ); ?></figcaption>
				</figure>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}

/**
 * Despiece dibujado de una pieza del equipo.
 *
 * Los números del dibujo se corresponden, en orden, con las líneas del campo
 * "Partes de la pieza" de la ficha. Son esquemas: buscan que se entienda qué
 * es cada parte y dónde está, no reproducir un modelo concreto.
 *
 * @param string $which mosqueton, arnes, cuerda, descensor, bloqueador, casco o camilla.
 */
function rv_equipo_partes( $which ) {
	$known = array( 'mosqueton', 'arnes', 'cuerda', 'descensor', 'bloqueador', 'casco', 'camilla' );
	if ( ! in_array( $which, $known, true ) ) {
		return;
	}

	/** Círculo azul con el número de la parte. */
	$badge = static function ( $x, $y, $n ) {
		printf(
			'<g><circle cx="%1$d" cy="%2$d" r="12" fill="#1B7FC4"/><text x="%1$d" y="%3$d" text-anchor="middle" font-family="Archivo, Segoe UI, sans-serif" font-size="13" font-weight="700" fill="#FFFFFF">%4$d</text></g>',
			(int) $x,
			(int) $y,
			(int) $y + 5,
			(int) $n
		);
	};

	/** Línea de puntos que une el número con la parte señalada. */
	$leader = static function ( $x1, $y1, $x2, $y2 ) {
		printf(
			'<path d="M%d %d L%d %d" stroke="#B0BDC6" stroke-width="1.3" stroke-dasharray="3 3" fill="none"/>',
			(int) $x1,
			(int) $y1,
			(int) $x2,
			(int) $y2
		);
	};

	echo '<svg class="rv-despiece-svg" viewBox="0 0 400 300" role="img" aria-label="' . esc_attr__( 'Esquema con las partes numeradas', 'rescate-vertical' ) . '">';

	switch ( $which ) {
		case 'mosqueton':
			?>
			<rect x="130" y="50" width="120" height="190" rx="58" fill="none" stroke="#101820" stroke-width="9"/>
			<path d="M130 112 V194" stroke="#D9502B" stroke-width="9" stroke-linecap="round"/>
			<rect x="121" y="120" width="18" height="42" rx="9" fill="#FFFFFF" stroke="#101820" stroke-width="2.4"/>
			<circle cx="130" cy="199" r="5" fill="#101820"/>
			<circle cx="130" cy="106" r="5" fill="#FFFFFF" stroke="#101820" stroke-width="2.4"/>
			<g stroke="#8A98A2" stroke-width="1.4">
				<path d="M228 158 h14 M228 168 h14 M228 178 h10"/>
			</g>
			<g stroke="#1B7FC4" stroke-width="1.6" fill="none">
				<path d="M276 58 V232" stroke-dasharray="5 4"/>
				<path d="M276 58 l-5 8 M276 58 l5 8 M276 232 l-5 -8 M276 232 l5 -8"/>
				<path d="M134 258 H246" stroke-dasharray="5 4"/>
				<path d="M134 258 l8 -5 M134 258 l8 5 M246 258 l-8 -5 M246 258 l-8 5"/>
			</g>
			<?php
			$leader( 320, 145, 288, 145 );
			$badge( 332, 145, 1 );
			$leader( 190, 278, 190, 266 );
			$badge( 190, 290, 2 );
			$leader( 72, 172, 124, 168 );
			$badge( 60, 172, 3 );
			$leader( 72, 128, 118, 138 );
			$badge( 60, 128, 4 );
			$leader( 72, 216, 124, 201 );
			$badge( 60, 216, 5 );
			$leader( 72, 84, 126, 104 );
			$badge( 60, 84, 6 );
			$leader( 320, 208, 246, 176 );
			$badge( 332, 208, 7 );
			break;

		case 'arnes':
			?>
			<g fill="none" stroke="#101820" stroke-width="2.6" stroke-linecap="round">
				<rect x="110" y="120" width="180" height="34" rx="17" fill="#FFFFFF"/>
				<ellipse cx="152" cy="206" rx="38" ry="44"/>
				<ellipse cx="248" cy="206" rx="38" ry="44"/>
				<path d="M152 154 V162 M248 154 V162"/>
				<path d="M142 120 C 152 74, 188 52, 200 48"/>
				<path d="M258 120 C 248 74, 212 52, 200 48"/>
			</g>
			<circle cx="200" cy="62" r="11" fill="none" stroke="#8A98A2" stroke-width="2" stroke-dasharray="4 3"/>
			<circle cx="200" cy="90" r="10" fill="#FFFFFF" stroke="#D9502B" stroke-width="3"/>
			<circle cx="200" cy="137" r="13" fill="#FFFFFF" stroke="#D9502B" stroke-width="3.4"/>
			<g fill="#FFFFFF" stroke="#101820" stroke-width="2">
				<rect x="126" y="126" width="17" height="22" rx="3"/>
				<rect x="257" y="126" width="17" height="22" rx="3"/>
				<rect x="134" y="238" width="15" height="19" rx="3"/>
				<rect x="251" y="238" width="15" height="19" rx="3"/>
			</g>
			<g fill="none" stroke="#8A98A2" stroke-width="2">
				<ellipse cx="105" cy="140" rx="9" ry="6"/>
				<ellipse cx="295" cy="140" rx="9" ry="6"/>
			</g>
			<g stroke="#8A98A2" stroke-width="1.3">
				<rect x="176" y="124" width="20" height="11" rx="2" fill="#FFFFFF"/>
				<path d="M179 128 h14 M179 132 h10"/>
			</g>
			<?php
			$leader( 66, 137, 106, 137 );
			$badge( 54, 137, 1 );
			$leader( 66, 232, 112, 220 );
			$badge( 54, 232, 2 );
			$leader( 334, 137, 216, 137 );
			$badge( 346, 137, 3 );
			$leader( 334, 90, 212, 90 );
			$badge( 346, 90, 4 );
			$leader( 334, 44, 208, 56 );
			$badge( 346, 44, 5 );
			$leader( 108, 278, 138, 258 );
			$badge( 108, 290, 6 );
			$leader( 334, 186, 300, 146 );
			$badge( 346, 186, 7 );
			$leader( 250, 282, 194, 142 );
			$badge( 250, 290, 8 );
			break;

		case 'cuerda':
			?>
			<rect x="26" y="94" width="234" height="84" rx="42" fill="#FFFFFF" stroke="#101820" stroke-width="3"/>
			<g stroke="#C3CED5" stroke-width="2">
				<path d="M52 96 l26 80 M80 95 l28 82 M108 94 l28 84 M136 94 l28 84 M164 94 l28 84 M192 94 l28 84 M218 96 l26 80"/>
			</g>
			<g>
				<rect x="132" y="94" width="20" height="84" fill="#D9502B" opacity="0.16"/>
				<path d="M132 94 V178 M152 94 V178" stroke="#D9502B" stroke-width="2.4"/>
			</g>
			<path d="M260 96 C 288 68, 320 68, 342 80" fill="none" stroke="#101820" stroke-width="2.8"/>
			<path d="M260 134 C 292 106, 322 104, 344 114" fill="none" stroke="#101820" stroke-width="2.8"/>
			<ellipse cx="260" cy="136" rx="9" ry="42" fill="#FFFFFF" stroke="#101820" stroke-width="2.8"/>
			<g stroke="#D9502B" stroke-width="2.6" fill="none" stroke-linecap="round">
				<path d="M268 112 C 300 104, 328 114, 352 108"/>
				<path d="M268 126 C 300 120, 328 130, 352 124"/>
				<path d="M268 140 C 300 134, 328 144, 352 138"/>
				<path d="M268 154 C 300 150, 328 158, 352 152"/>
				<path d="M268 168 C 300 164, 328 172, 352 166"/>
			</g>
			<rect x="356" y="100" width="28" height="72" rx="6" fill="#FFFFFF" stroke="#1B7FC4" stroke-width="2.2"/>
			<g stroke="#1B7FC4" stroke-width="1.5">
				<path d="M362 114 h16 M362 126 h16 M362 138 h11 M362 150 h16"/>
			</g>
			<?php
			$leader( 74, 56, 92, 92 );
			$badge( 68, 46, 1 );
			$leader( 306, 52, 306, 76 );
			$badge( 306, 40, 2 );
			$leader( 370, 216, 370, 174 );
			$badge( 370, 228, 3 );
			$leader( 142, 216, 142, 182 );
			$badge( 142, 228, 4 );
			$leader( 244, 252, 258, 182 );
			$badge( 244, 264, 5 );
			break;

		case 'descensor':
			?>
			<path d="M196 34 V104 C196 132 172 140 176 160 C 180 184 212 178 218 198 L218 262" fill="none" stroke="#D9502B" stroke-width="6" stroke-linecap="round"/>
			<rect x="140" y="70" width="112" height="132" rx="16" fill="#FFFFFF" fill-opacity="0.92" stroke="#101820" stroke-width="2.6"/>
			<path d="M140 86 V186 A 16 16 0 0 0 156 202 H192 V70 H156 A 16 16 0 0 0 140 86 Z" fill="none" stroke="#8A98A2" stroke-width="2" stroke-dasharray="5 4"/>
			<path d="M126 100 a 26 26 0 0 1 0 42" fill="none" stroke="#8A98A2" stroke-width="1.6"/>
			<path d="M126 142 l6 -6 M126 142 l7 2" stroke="#8A98A2" stroke-width="1.6" fill="none"/>
			<path d="M176 108 A 24 24 0 1 0 200 146" fill="none" stroke="#101820" stroke-width="4"/>
			<circle cx="192" cy="132" r="4" fill="#101820"/>
			<rect x="248" y="140" width="66" height="24" rx="12" fill="#FFFFFF" stroke="#101820" stroke-width="2.6"/>
			<circle cx="250" cy="152" r="5" fill="#101820"/>
			<g stroke="#8A98A2" stroke-width="1.4">
				<path d="M268 146 V158 M280 146 V158 M292 146 V158"/>
			</g>
			<circle cx="196" cy="216" r="13" fill="#FFFFFF" stroke="#101820" stroke-width="2.8"/>
			<rect x="216" y="80" width="26" height="26" rx="4" fill="#FFFFFF" stroke="#1B7FC4" stroke-width="1.8"/>
			<path d="M222 100 C 226 88, 234 96, 236 86" fill="none" stroke="#1B7FC4" stroke-width="1.8"/>
			<path d="M172 168 C 186 172, 196 178, 200 188" fill="none" stroke="#101820" stroke-width="3"/>
			<?php
			$leader( 74, 88, 132, 92 );
			$badge( 62, 88, 1 );
			$leader( 74, 132, 158, 128 );
			$badge( 62, 132, 2 );
			$leader( 340, 152, 322, 152 );
			$badge( 352, 152, 3 );
			$leader( 74, 180, 170, 176 );
			$badge( 62, 180, 4 );
			$leader( 196, 254, 196, 232 );
			$badge( 196, 266, 5 );
			$leader( 330, 92, 248, 92 );
			$badge( 342, 92, 6 );
			$leader( 296, 258, 224, 244 );
			$badge( 300, 268, 7 );
			break;

		case 'bloqueador':
			?>
			<path d="M160 26 V276" stroke="#D9502B" stroke-width="7" stroke-linecap="round"/>
			<rect x="150" y="58" width="82" height="126" rx="14" fill="#FFFFFF" fill-opacity="0.94" stroke="#101820" stroke-width="2.6"/>
			<rect x="226" y="92" width="48" height="102" rx="24" fill="#FFFFFF" stroke="#101820" stroke-width="2.6"/>
			<g stroke="#8A98A2" stroke-width="1.5">
				<path d="M236 116 h28 M236 130 h28 M236 144 h28 M236 158 h28"/>
			</g>
			<path d="M196 84 A 26 26 0 0 0 170 110 L170 124" fill="none" stroke="#101820" stroke-width="4"/>
			<g fill="#101820">
				<path d="M170 104 l-9 4 l9 4 Z"/>
				<path d="M170 114 l-9 4 l9 4 Z"/>
				<path d="M170 124 l-9 4 l9 4 Z"/>
			</g>
			<path d="M150 74 C 138 76, 134 88, 140 96" fill="none" stroke="#101820" stroke-width="3" stroke-linecap="round"/>
			<circle cx="150" cy="74" r="4" fill="#101820"/>
			<circle cx="196" cy="72" r="11" fill="#FFFFFF" stroke="#101820" stroke-width="2.8"/>
			<circle cx="196" cy="170" r="11" fill="#FFFFFF" stroke="#101820" stroke-width="2.8"/>
			<g stroke="#8A98A2" stroke-width="1.4" stroke-dasharray="4 4">
				<path d="M150 196 V246 M172 196 V246"/>
			</g>
			<?php
			$leader( 78, 112, 156, 114 );
			$badge( 66, 112, 1 );
			$leader( 78, 66, 132, 82 );
			$badge( 66, 66, 2 );
			$leader( 328, 142, 286, 142 );
			$badge( 340, 142, 3 );
			$leader( 328, 60, 210, 68 );
			$badge( 340, 60, 4 );
			$leader( 328, 186, 210, 172 );
			$badge( 340, 186, 5 );
			$leader( 106, 250, 146, 226 );
			$badge( 94, 250, 6 );
			break;

		case 'casco':
			?>
			<path d="M108 166 A 92 84 0 0 1 292 166" fill="#FFFFFF" stroke="#101820" stroke-width="3.2"/>
			<path d="M108 166 H292" stroke="#101820" stroke-width="3.2"/>
			<path d="M292 166 l24 7 l-24 9" fill="#FFFFFF" stroke="#101820" stroke-width="2.6" stroke-linejoin="round"/>
			<path d="M126 164 A 74 68 0 0 1 274 164" fill="none" stroke="#8A98A2" stroke-width="2" stroke-dasharray="5 4"/>
			<g fill="none" stroke="#101820" stroke-width="2.6" stroke-linecap="round">
				<path d="M132 168 C 148 206, 162 224, 176 234"/>
				<path d="M268 168 C 252 206, 238 224, 224 234"/>
			</g>
			<rect x="174" y="230" width="52" height="16" rx="5" fill="#FFFFFF" stroke="#101820" stroke-width="2.4"/>
			<path d="M200 230 V246" stroke="#101820" stroke-width="1.6"/>
			<circle cx="106" cy="152" r="13" fill="#FFFFFF" stroke="#101820" stroke-width="2.6"/>
			<g stroke="#101820" stroke-width="1.6">
				<path d="M106 143 V161 M97 152 H115"/>
			</g>
			<g fill="#FFFFFF" stroke="#101820" stroke-width="2">
				<rect x="146" y="116" width="14" height="20" rx="3"/>
				<rect x="242" y="116" width="14" height="20" rx="3"/>
			</g>
			<g fill="none" stroke="#8A98A2" stroke-width="3" stroke-linecap="round">
				<path d="M178 96 l10 -6 M200 88 l10 -4 M222 92 l10 -2"/>
			</g>
			<?php
			$leader( 100, 56, 146, 100 );
			$badge( 92, 46, 1 );
			$leader( 74, 196, 154, 156 );
			$badge( 62, 196, 2 );
			$leader( 204, 278, 204, 258 );
			$badge( 204, 290, 3 );
			$leader( 56, 122, 96, 142 );
			$badge( 46, 116, 4 );
			$leader( 332, 116, 262, 122 );
			$badge( 344, 116, 5 );
			$leader( 254, 48, 226, 84 );
			$badge( 258, 38, 6 );
			break;

		case 'camilla':
		default:
			?>
			<circle cx="200" cy="26" r="11" fill="#FFFFFF" stroke="#101820" stroke-width="2.6"/>
			<g stroke="#D9502B" stroke-width="2.2" fill="none" stroke-linecap="round">
				<path d="M200 37 L134 66"/><path d="M200 37 L172 60"/>
				<path d="M200 37 L228 60"/><path d="M200 37 L266 66"/>
			</g>
			<rect x="112" y="62" width="176" height="204" rx="44" fill="#FFFFFF" stroke="#101820" stroke-width="3.2"/>
			<rect x="126" y="76" width="148" height="176" rx="36" fill="none" stroke="#8A98A2" stroke-width="1.6" stroke-dasharray="5 4"/>
			<g fill="#1B7FC4" fill-opacity="0.16" stroke="#1B7FC4" stroke-width="1.6">
				<rect x="112" y="106" width="176" height="15" rx="3"/>
				<rect x="112" y="150" width="176" height="15" rx="3"/>
				<rect x="112" y="200" width="176" height="15" rx="3"/>
			</g>
			<rect x="144" y="238" width="112" height="18" rx="6" fill="#FFFFFF" stroke="#D9502B" stroke-width="2.4"/>
			<g fill="#101820">
				<circle cx="134" cy="66" r="4"/><circle cx="172" cy="60" r="4"/>
				<circle cx="228" cy="60" r="4"/><circle cx="266" cy="66" r="4"/>
			</g>
			<path d="M288 232 C 318 244, 334 252, 344 262" fill="none" stroke="#D9502B" stroke-width="2" stroke-dasharray="6 4"/>
			<?php
			$leader( 80, 78, 114, 88 );
			$badge( 68, 74, 1 );
			$leader( 74, 232, 132, 226 );
			$badge( 62, 232, 2 );
			$leader( 330, 158, 292, 158 );
			$badge( 342, 158, 3 );
			$leader( 106, 30, 148, 50 );
			$badge( 94, 26, 4 );
			$leader( 200, 286, 200, 260 );
			$badge( 200, 294, 5 );
			$leader( 322, 62, 272, 64 );
			$badge( 334, 62, 6 );
			$leader( 356, 288, 348, 268 );
			$badge( 360, 294, 7 );
			break;
	}

	echo '</svg>';
}

/**
 * Section page header: photo band with kicker, title and intro.
 *
 * @param array $args {
 *     @type string $kicker Small uppercase label.
 *     @type string $title  Title HTML (may contain <em> for the accent word).
 *     @type string $intro  Intro paragraph, plain text.
 *     @type string $image  Bundled image file name for the band background.
 *     @type string $alt    Alt text for that image.
 * }
 */
function rv_page_hero( $args ) {
	$args = wp_parse_args(
		$args,
		array(
			'kicker' => '',
			'title'  => '',
			'intro'  => '',
			'image'  => 'tecnicas.jpg',
			'alt'    => '',
		)
	);
	?>
	<section class="rv-band">
		<div class="rv-band-media">
			<img src="<?php echo esc_url( rv_img( $args['image'] ) ); ?>"
				alt="<?php echo esc_attr( $args['alt'] ); ?>"
				fetchpriority="high" decoding="async" sizes="100vw">
		</div>
		<div class="rv-band-inner">
			<?php if ( '' !== $args['kicker'] ) : ?>
				<span class="rv-kicker rv-reveal"><?php echo esc_html( $args['kicker'] ); ?></span>
			<?php endif; ?>
			<h1 class="rv-reveal d1" style="color:#FFFFFF;font-size:clamp(28px,4.2vw,44px);margin-bottom:14px;max-width:760px;">
				<?php echo wp_kses( $args['title'], array( 'em' => array() ) ); ?>
			</h1>
			<?php if ( '' !== $args['intro'] ) : ?>
				<p class="rv-reveal d2"><?php echo esc_html( $args['intro'] ); ?></p>
			<?php endif; ?>
		</div>
	</section>
	<?php
}

/**
 * Prints the page's editor content when the author added any, so section
 * templates stay editable without losing the coded design.
 */
function rv_editor_content() {
	if ( ! have_posts() ) {
		return;
	}
	while ( have_posts() ) {
		the_post();
		$content = trim( get_the_content() );
		if ( '' === $content ) {
			continue;
		}
		echo '<div class="rv-editor-content rv-reveal">';
		the_content();
		echo '</div>';
	}
	rewind_posts();
}
