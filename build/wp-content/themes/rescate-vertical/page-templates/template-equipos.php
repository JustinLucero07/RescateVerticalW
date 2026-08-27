<?php
/**
 * Template Name: Sección · Equipos
 * Template Post Type: page
 *
 * Las fichas se editan en Rescate Vertical → Equipos. Si el plugin no está
 * activo, la sección muestra un aviso en lugar de romperse.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

rv_page_hero(
	array(
		'kicker' => __( 'Sección 04', 'rescate-vertical' ),
		'title'  => sprintf(
			/* translators: %s: palabras destacadas. */
			esc_html__( 'El equipo, %s', 'rescate-vertical' ),
			'<em>' . esc_html__( 'pieza por pieza', 'rescate-vertical' ) . '</em>'
		),
		'intro'  => __( 'Cada elemento tiene una norma que lo respalda, una resistencia verificable y un contexto de uso. Saber cuándo NO usar una pieza importa tanto como saber cuándo sí.', 'rescate-vertical' ),
		'image'  => 'equipos.jpg',
		'alt'    => __( 'Primer plano de un mosquetón de seguro conectado a una línea de vida', 'rescate-vertical' ),
	)
);

$rv_equipos = class_exists( 'RV_Content' ) ? RV_Content::items( RV_Content::EQUIPO ) : array();
?>

<section class="rv-section">
	<div class="rv-shell">
		<div class="rv-section-head rv-reveal">
			<span class="rv-kicker"><?php esc_html_e( 'Fichas técnicas', 'rescate-vertical' ); ?></span>
			<h2><?php esc_html_e( 'Cada pieza, con su norma y su contexto', 'rescate-vertical' ); ?></h2>
			<p class="rv-section-intro"><?php esc_html_e( 'La norma europea EN define el ensayo que cada pieza debe superar. Conocerla permite verificar en campo si el equipo disponible sirve para la maniobra que se va a ejecutar.', 'rescate-vertical' ); ?></p>
		</div>

		<?php if ( $rv_equipos ) : ?>
			<div class="rv-fichas">
				<?php
				$rv_i = 0;
				foreach ( $rv_equipos as $rv_post ) :
					setup_postdata( $GLOBALS['post'] = $rv_post ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					$rv_norma  = get_post_meta( $rv_post->ID, 'rv_norma', true );
					$rv_dato   = get_post_meta( $rv_post->ID, 'rv_dato', true );
					$rv_dato_l = get_post_meta( $rv_post->ID, 'rv_dato_label', true );
					$rv_si     = RV_Content::lines_to_list( get_post_meta( $rv_post->ID, 'rv_cuando_si', true ) );
					$rv_no     = RV_Content::lines_to_list( get_post_meta( $rv_post->ID, 'rv_cuando_no', true ) );
					$rv_cls    = ( $rv_i % 2 ) > 0 ? ' d1' : '';
					$rv_i++;
					?>
					<article class="rv-ficha rv-reveal<?php echo esc_attr( $rv_cls ); ?><?php echo has_post_thumbnail( $rv_post ) ? '' : ' rv-ficha--sinfoto'; ?>">
						<?php if ( has_post_thumbnail( $rv_post ) ) : ?>
							<div class="rv-ficha-media">
								<?php echo get_the_post_thumbnail( $rv_post, 'large', array( 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
							</div>
						<?php endif; ?>

						<div class="rv-ficha-body">
							<span class="rv-badge"><?php esc_html_e( 'Certificado', 'rescate-vertical' ); ?></span>
							<h3><?php echo esc_html( get_the_title( $rv_post ) ); ?></h3>

							<?php if ( trim( $rv_post->post_content ) !== '' ) : ?>
								<div class="rv-ficha-desc"><?php echo wp_kses_post( wpautop( $rv_post->post_content ) ); ?></div>
							<?php endif; ?>

							<?php if ( $rv_norma || $rv_dato ) : ?>
								<div class="rv-ficha-specs">
									<?php if ( $rv_norma ) : ?>
										<div class="rv-spec"><span><?php esc_html_e( 'Norma', 'rescate-vertical' ); ?></span><span><?php echo esc_html( $rv_norma ); ?></span></div>
									<?php endif; ?>
									<?php if ( $rv_dato ) : ?>
										<div class="rv-spec">
											<span><?php echo esc_html( $rv_dato_l ? $rv_dato_l : __( 'Dato', 'rescate-vertical' ) ); ?></span>
											<span><?php echo esc_html( $rv_dato ); ?></span>
										</div>
									<?php endif; ?>
								</div>
							<?php endif; ?>

							<?php if ( $rv_si || $rv_no ) : ?>
								<div class="rv-uso">
									<?php if ( $rv_si ) : ?>
										<div class="rv-uso-col rv-uso-col--si">
											<h4><?php esc_html_e( 'Cuándo se usa', 'rescate-vertical' ); ?></h4>
											<?php echo wp_kses_post( $rv_si ); ?>
										</div>
									<?php endif; ?>
									<?php if ( $rv_no ) : ?>
										<div class="rv-uso-col rv-uso-col--no">
											<h4><?php esc_html_e( 'Cuándo NO se usa', 'rescate-vertical' ); ?></h4>
											<?php echo wp_kses_post( $rv_no ); ?>
										</div>
									<?php endif; ?>
								</div>
							<?php endif; ?>

							<?php $rv_gal = RV_Content::gallery_ids( $rv_post->ID ); ?>
							<?php if ( $rv_gal ) : ?>
								<div class="rv-galeria rv-galeria--mini">
									<?php foreach ( $rv_gal as $rv_gid ) : ?>
										<a href="<?php echo esc_url( wp_get_attachment_image_url( $rv_gid, 'full' ) ); ?>" target="_blank" rel="noopener">
											<?php echo wp_get_attachment_image( $rv_gid, 'medium', false, array( 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
										</a>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
					</article>
				<?php endforeach; wp_reset_postdata(); ?>
			</div>
		<?php else : ?>
			<p class="rv-section-intro">
				<?php esc_html_e( 'Todavía no hay fichas de equipo. Añádelas en Rescate Vertical → Equipos.', 'rescate-vertical' ); ?>
			</p>
		<?php endif; ?>

		<?php rv_editor_content(); ?>
	</div>
</section>

<?php get_footer(); ?>
