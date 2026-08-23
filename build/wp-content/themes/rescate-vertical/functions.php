<?php
/**
 * Rescate Vertical theme functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RV_THEME_VERSION', '2.3.0' );

/**
 * Theme setup.
 */
function rv_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );

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
		'https://fonts.googleapis.com/css2?family=Archivo:wght@600;700;800&family=Source+Sans+3:wght@400;500;600;700&display=swap',
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
		esc_url( get_template_directory_uri() . '/assets/images/hero.jpg' )
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
	return get_template_directory_uri() . '/assets/images/' . ltrim( $file, '/' );
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
				<g stroke="#0E1A24" stroke-width="2" fill="none" stroke-linecap="round">
					<path d="M158 26 H242"/><path d="M170 26 V16 M230 26 V16"/>
				</g>
				<circle cx="200" cy="36" r="6" fill="#FFFFFF" stroke="#0E1A24" stroke-width="2"/>
				<path d="M200 42 V150" stroke="#E4572E" stroke-width="2.6" stroke-linecap="round"/>
				<path d="M205 42 V150" stroke="#E4572E" stroke-width="2.4" stroke-dasharray="1 8" opacity="0.5" stroke-linecap="round"/>
				<rect x="188" y="80" width="24" height="30" rx="4" fill="#FFFFFF" stroke="#0E1A24" stroke-width="2"/>
				<circle cx="200" cy="89" r="4.2" fill="none" stroke="#0E1A24" stroke-width="1.7"/>
				<circle cx="200" cy="101" r="3.2" fill="none" stroke="#0E1A24" stroke-width="1.7"/>
				<g stroke="#2C5468" stroke-width="1.5" fill="none" stroke-linecap="round">
					<path d="M228 88 h16 M240 84 l4 4 -4 4"/><path d="M228 104 h16 M240 100 l4 4 -4 4"/>
				</g>
				<path d="M200 150 l-10 12 M200 150 l10 12" stroke="#0E1A24" stroke-width="2" stroke-linecap="round"/>
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
				<g stroke="#0E1A24" stroke-width="2" fill="none" stroke-linecap="round">
					<path d="M120 24 H280"/><path d="M132 24 V14 M268 24 V14"/>
				</g>
				<circle cx="164" cy="40" r="11" fill="#FFFFFF" stroke="#0E1A24" stroke-width="2"/>
				<circle cx="164" cy="40" r="3" fill="#0E1A24"/>
				<circle cx="236" cy="40" r="11" fill="#FFFFFF" stroke="#0E1A24" stroke-width="2"/>
				<circle cx="236" cy="40" r="3" fill="#0E1A24"/>
				<circle cx="200" cy="118" r="12" fill="#FFFFFF" stroke="#0E1A24" stroke-width="2"/>
				<circle cx="200" cy="118" r="3" fill="#0E1A24"/>
				<g stroke="#E4572E" stroke-width="2.4" fill="none" stroke-linecap="round">
					<path d="M153 40 V112 A12 12 0 0 0 200 130"/>
					<path d="M200 106 A12 12 0 0 1 247 40"/>
					<path d="M247 40 V22"/>
				</g>
				<g stroke="#2C5468" stroke-width="1.6" fill="none" stroke-linecap="round">
					<path d="M268 60 V34 M264 42 l4 -6 4 6"/>
				</g>
				<path d="M200 130 V150" stroke="#0E1A24" stroke-width="2" stroke-linecap="round"/>
				<rect x="184" y="150" width="32" height="14" rx="2" fill="#FFFFFF" stroke="#0E1A24" stroke-width="2"/>
				<text x="292" y="64" font-family="Source Sans 3, Segoe UI, sans-serif" letter-spacing="0.04em" font-size="11" font-weight="700" fill="#E4572E">3:1</text>
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
				<path d="M96 30 H304" stroke="#0E1A24" stroke-width="2.5" stroke-linecap="round"/>
				<g stroke="#8A98A2" stroke-width="1.4">
					<path d="M108 30 l-8 -10 M138 30 l-8 -10 M168 30 l-8 -10 M198 30 l-8 -10 M228 30 l-8 -10 M258 30 l-8 -10 M288 30 l-8 -10"/>
				</g>
				<circle cx="150" cy="42" r="7" fill="#FFFFFF" stroke="#0E1A24" stroke-width="2"/>
				<circle cx="250" cy="42" r="7" fill="#FFFFFF" stroke="#0E1A24" stroke-width="2"/>
				<path d="M150 49 L200 108" stroke="#E4572E" stroke-width="2.5" stroke-linecap="round"/>
				<path d="M250 49 L200 108" stroke="#E4572E" stroke-width="2.5" stroke-linecap="round"/>
				<path d="M170 72 A 42 42 0 0 0 230 72" fill="none" stroke="#2C5468" stroke-width="1.6" stroke-dasharray="4 3"/>
				<text x="181" y="90" font-family="Source Sans 3, Segoe UI, sans-serif" letter-spacing="0.04em" font-size="10.5" font-weight="700" fill="#2C5468">≤60°</text>
				<circle cx="200" cy="114" r="9" fill="#FFFFFF" stroke="#0E1A24" stroke-width="2.4"/>
				<path d="M200 123 V148" stroke="#0E1A24" stroke-width="2" stroke-linecap="round"/>
				<path d="M192 148 h16" stroke="#0E1A24" stroke-width="2" stroke-linecap="round"/>
				<g stroke="#0E1A24" stroke-width="1.8" fill="none" stroke-linecap="round">
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
				<circle cx="200" cy="30" r="8" fill="#FFFFFF" stroke="#0E1A24" stroke-width="2.4"/>
				<g stroke="#E4572E" stroke-width="2.2" stroke-linecap="round">
					<path d="M200 38 L126 96"/><path d="M200 38 L170 96"/>
					<path d="M200 38 L230 96"/><path d="M200 38 L274 96"/>
				</g>
				<rect x="112" y="96" width="176" height="42" rx="8" fill="#FFFFFF" stroke="#0E1A24" stroke-width="2.4"/>
				<g stroke="#8A98A2" stroke-width="1.3">
					<path d="M112 110 H288 M112 124 H288"/>
					<path d="M140 96 V138 M168 96 V138 M196 96 V138 M224 96 V138 M252 96 V138"/>
				</g>
				<g stroke="#2C5468" stroke-width="1.8" fill="none" stroke-linecap="round">
					<circle cx="140" cy="117" r="8"/>
					<path d="M150 117 H246"/><path d="M246 117 l10 -6 M246 117 l10 6"/>
				</g>
				<g fill="#0E1A24">
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
