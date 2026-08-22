<?php
/**
 * Header template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#rv-content"><?php esc_html_e( 'Saltar al contenido', 'rescate-vertical' ); ?></a>

<header class="rv-header">
	<div class="rv-header-inner">
		<a class="rv-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
				<circle cx="12" cy="6" r="3.2" stroke="#E4572E" stroke-width="2"/>
				<path d="M12 9.2v8.8M12 18l-4.4 4.2M12 18l4.4 4.2" stroke="#0E1A24" stroke-width="2" stroke-linecap="round"/>
			</svg>
			<span class="rv-brand-name"><?php bloginfo( 'name' ); ?></span>
		</a>

		<button type="button" class="rv-nav-toggle" id="rv-nav-toggle" aria-expanded="false" aria-controls="rv-nav">
			<?php esc_html_e( 'Menú', 'rescate-vertical' ); ?>
		</button>

		<nav class="rv-nav" id="rv-nav" aria-label="<?php esc_attr_e( 'Navegación principal', 'rescate-vertical' ); ?>">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => '',
						'depth'          => 1,
					)
				);
			} else {
				rv_fallback_menu();
			}
			?>
			<a class="rv-nav-cta" href="<?php echo esc_url( rv_section_url( 'fisica' ) ); ?>">
				<?php esc_html_e( 'Simulador', 'rescate-vertical' ); ?>
			</a>
		</nav>
	</div>
</header>

<main id="rv-content">
