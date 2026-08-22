<?php
/**
 * Default index template (blog listing / generic fallback).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="rv-page-content">
	<div class="rv-shell">
		<?php if ( have_posts() ) : ?>
			<div class="rv-section-head rv-reveal">
				<h1 style="font-size:clamp(28px,4.2vw,42px);">
					<?php
					if ( is_search() ) {
						printf(
							/* translators: %s: search query. */
							esc_html__( 'Resultados para «%s»', 'rescate-vertical' ),
							esc_html( get_search_query() )
						);
					} elseif ( is_archive() ) {
						the_archive_title();
					} else {
						bloginfo( 'name' );
					}
					?>
				</h1>
			</div>

			<div class="rv-cards">
				<?php
				while ( have_posts() ) {
					the_post();
					?>
					<article <?php post_class( 'rv-card rv-reveal' ); ?>>
						<div class="rv-card-body">
							<div class="rv-card-top">
								<h3><a href="<?php the_permalink(); ?>" style="text-decoration:none;color:inherit;"><?php the_title(); ?></a></h3>
							</div>
							<p class="rv-mono" style="font-size:11.5px;color:var(--rv-ink-3);margin-bottom:10px;"><?php echo esc_html( get_the_date() ); ?></p>
							<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 28 ) ); ?></p>
							<a class="rv-card-foot" href="<?php the_permalink(); ?>" style="text-decoration:none;">
								<?php esc_html_e( 'Leer', 'rescate-vertical' ); ?>
								<svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
							</a>
						</div>
					</article>
					<?php
				}
				?>
			</div>

			<div style="margin-top:36px;"><?php the_posts_pagination(); ?></div>

		<?php else : ?>
			<div class="rv-section-head">
				<h1 style="font-size:clamp(28px,4.2vw,42px);"><?php esc_html_e( 'No se encontró contenido', 'rescate-vertical' ); ?></h1>
				<p class="rv-section-intro"><?php esc_html_e( 'Prueba con otra búsqueda o vuelve al inicio.', 'rescate-vertical' ); ?></p>
			</div>
			<a class="rv-btn rv-btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Volver al inicio', 'rescate-vertical' ); ?></a>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>
