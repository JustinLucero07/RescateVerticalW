<?php
/**
 * Template Name: Sección · Protocolos y normativas
 * Template Post Type: page
 *
 * Los protocolos se editan en Rescate Vertical → Protocolos. Cada uno puede
 * llevar su infografía como Imagen destacada.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

rv_page_hero(
	array(
		'kicker' => __( 'Sección 05 · 06', 'rescate-vertical' ),
		'title'  => sprintf(
			/* translators: %s: palabras destacadas. */
			esc_html__( 'Cuando la técnica se cruza con la %s', 'rescate-vertical' ),
			'<em>' . esc_html__( 'clínica', 'rescate-vertical' ) . '</em>'
		),
		'intro'  => __( 'Los protocolos médicos aplicados durante la extracción, y el marco normativo que respalda cada maniobra.', 'rescate-vertical' ),
		'image'  => 'protocolos.jpg',
		'alt'    => __( 'Equipo de rescate asegurando a un paciente en una camilla de extracción vertical', 'rescate-vertical' ),
	)
);

$rv_protocolos = class_exists( 'RV_Content' ) ? RV_Content::items( RV_Content::PROTOCOLO ) : array();

$rv_norms = array(
	array( 'NFPA 1983', __( 'cuerdas y accesorios', 'rescate-vertical' ) ),
	array( 'NFPA 2500', __( 'operación y entrenamiento', 'rescate-vertical' ) ),
	array( 'OSHA 1926.502', __( 'protección contra caídas', 'rescate-vertical' ) ),
	array( 'ANSI Z359', __( 'sistemas anticaídas', 'rescate-vertical' ) ),
	array( 'IRATA', __( 'certificación por niveles', 'rescate-vertical' ) ),
	array( 'SPRAT', __( 'acceso con cuerdas', 'rescate-vertical' ) ),
	array( 'OSHA 1910.146', __( 'espacios confinados', 'rescate-vertical' ) ),
);
?>

<section class="rv-section">
	<div class="rv-shell">
		<div class="rv-section-head rv-reveal">
			<span class="rv-kicker rv-kicker--med"><?php esc_html_e( 'Protocolos médicos', 'rescate-vertical' ); ?></span>
			<h2><?php esc_html_e( 'La atención no espera a la extracción', 'rescate-vertical' ); ?></h2>
			<p class="rv-section-intro"><?php esc_html_e( 'Marcos clínicos distintos, con un principio común: estabilizar al paciente sin detener la operación técnica que lo está sacando de ahí.', 'rescate-vertical' ); ?></p>
		</div>

		<?php if ( $rv_protocolos ) : ?>
			<div class="rv-protos">
				<?php
				foreach ( $rv_protocolos as $rv_p ) :
					$rv_sigla = get_post_meta( $rv_p->ID, 'rv_sigla', true );
					$rv_pasos = RV_Content::lines_to_list( get_post_meta( $rv_p->ID, 'rv_pasos', true ) );
					?>
					<article class="rv-proto-card rv-reveal">
						<header class="rv-proto-head">
							<h3><?php echo esc_html( get_the_title( $rv_p ) ); ?></h3>
							<?php if ( $rv_sigla ) : ?>
								<span class="rv-proto-sigla"><?php echo esc_html( $rv_sigla ); ?></span>
							<?php endif; ?>
						</header>

						<?php if ( trim( $rv_p->post_content ) !== '' ) : ?>
							<div class="rv-proto-desc"><?php echo wp_kses_post( wpautop( $rv_p->post_content ) ); ?></div>
						<?php endif; ?>

						<?php if ( $rv_pasos ) : ?>
							<div class="rv-proto-pasos">
								<h4><?php esc_html_e( 'Secuencia', 'rescate-vertical' ); ?></h4>
								<?php echo wp_kses_post( $rv_pasos ); ?>
							</div>
						<?php endif; ?>

						<?php if ( has_post_thumbnail( $rv_p ) ) : ?>
							<figure class="rv-proto-info">
								<a href="<?php echo esc_url( get_the_post_thumbnail_url( $rv_p, 'full' ) ); ?>" target="_blank" rel="noopener">
									<?php
									echo get_the_post_thumbnail(
										$rv_p,
										'large',
										array(
											'loading'  => 'lazy',
											'decoding' => 'async',
											'sizes'    => '(max-width: 860px) 100vw, 900px',
										)
									);
									?>
								</a>
								<figcaption><?php esc_html_e( 'Pulsa la infografía para verla a tamaño completo.', 'rescate-vertical' ); ?></figcaption>
							</figure>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<p class="rv-section-intro">
				<?php esc_html_e( 'Todavía no hay protocolos cargados. Añádelos en Rescate Vertical → Protocolos.', 'rescate-vertical' ); ?>
			</p>
		<?php endif; ?>
	</div>
</section>

<section class="rv-section rv-section--soft" id="normativas">
	<div class="rv-shell">
		<div class="rv-section-head rv-reveal">
			<span class="rv-kicker"><?php esc_html_e( 'Normativas', 'rescate-vertical' ); ?></span>
			<h2><?php esc_html_e( 'El marco que respalda cada maniobra', 'rescate-vertical' ); ?></h2>
			<p class="rv-section-intro"><?php esc_html_e( 'Estas referencias definen qué equipo es admisible, cómo debe operarse y qué nivel de certificación necesita quien lo usa.', 'rescate-vertical' ); ?></p>
		</div>

		<div class="rv-chips rv-reveal d1">
			<?php foreach ( $rv_norms as $norm ) : ?>
				<div class="rv-chip">
					<b><?php echo esc_html( $norm[0] ); ?></b>
					<span><?php echo esc_html( $norm[1] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>

		<?php rv_editor_content(); ?>
	</div>
</section>

<?php get_footer(); ?>
