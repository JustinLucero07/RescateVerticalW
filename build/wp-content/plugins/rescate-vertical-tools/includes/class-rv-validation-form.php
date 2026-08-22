<?php
/**
 * Shortcode [rv_formulario_validacion] — expert validation form.
 * Saves each response as an "rv_validacion" custom post type entry.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RV_Validation_Form {

	const NONCE_ACTION = 'rv_validacion_submit';
	const NONCE_FIELD  = 'rv_validacion_nonce';

	public static function register() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_shortcode( 'rv_formulario_validacion', array( __CLASS__, 'render' ) );
	}

	public static function enqueue_assets() {
		wp_enqueue_style(
			'rv-tools-form',
			RV_TOOLS_URL . 'assets/css/rv-form.css',
			array(),
			RV_TOOLS_VERSION
		);
	}

	/**
	 * Validates and stores a submission.
	 *
	 * @return array{0: bool, 1: string} Tuple of (success, error message).
	 */
	private static function process_submission() {
		if ( ! isset( $_POST[ self::NONCE_FIELD ] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE_FIELD ] ) ), self::NONCE_ACTION )
		) {
			return array( false, __( 'No se pudo verificar el formulario. Actualiza la página e inténtalo de nuevo.', 'rescate-vertical-tools' ) );
		}

		$experto       = isset( $_POST['rv_experto'] ) ? sanitize_text_field( wp_unslash( $_POST['rv_experto'] ) ) : '';
		$claridad      = isset( $_POST['rv_claridad'] ) ? absint( $_POST['rv_claridad'] ) : 0;
		$utilidad      = isset( $_POST['rv_utilidad'] ) ? absint( $_POST['rv_utilidad'] ) : 0;
		$diseno        = isset( $_POST['rv_diseno'] ) ? absint( $_POST['rv_diseno'] ) : 0;
		$observaciones = isset( $_POST['rv_observaciones'] ) ? sanitize_textarea_field( wp_unslash( $_POST['rv_observaciones'] ) ) : '';

		if ( '' === $experto ) {
			return array( false, __( 'Indica tu nombre para poder registrar la validación.', 'rescate-vertical-tools' ) );
		}

		foreach ( array( $claridad, $utilidad, $diseno ) as $scale_value ) {
			if ( $scale_value < 1 || $scale_value > 5 ) {
				return array( false, __( 'Completa las tres escalas (claridad, utilidad, diseño) con un valor entre 1 y 5.', 'rescate-vertical-tools' ) );
			}
		}

		$post_id = wp_insert_post(
			array(
				'post_type'   => RV_TOOLS_CPT,
				'post_status' => 'publish',
				'post_title'  => sprintf(
					/* translators: 1: expert name, 2: submission date/time */
					__( '%1$s — %2$s', 'rescate-vertical-tools' ),
					$experto,
					current_time( 'Y-m-d H:i' )
				),
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			return array( false, __( 'Ocurrió un error al guardar tu validación. Inténtalo de nuevo.', 'rescate-vertical-tools' ) );
		}

		update_post_meta( $post_id, 'rv_experto', $experto );
		update_post_meta( $post_id, 'rv_claridad', $claridad );
		update_post_meta( $post_id, 'rv_utilidad', $utilidad );
		update_post_meta( $post_id, 'rv_diseno', $diseno );
		update_post_meta( $post_id, 'rv_observaciones', $observaciones );

		return array( true, '' );
	}

	public static function render( $atts ) {
		$success = false;
		$error   = '';

		if ( isset( $_POST['rv_validacion_submitted'] ) ) {
			list( $success, $error ) = self::process_submission();
		}

		ob_start();
		?>
		<div class="rv-vf">
			<?php if ( $success ) : ?>
				<div class="rv-vf-success" role="status">
					<?php esc_html_e( '¡Gracias! Tu validación fue registrada correctamente.', 'rescate-vertical-tools' ); ?>
				</div>
			<?php else : ?>
				<?php if ( '' !== $error ) : ?>
					<div class="rv-vf-error" role="alert"><?php echo esc_html( $error ); ?></div>
				<?php endif; ?>
				<form class="rv-vf-form" method="post" action="">
					<?php wp_nonce_field( self::NONCE_ACTION, self::NONCE_FIELD ); ?>
					<input type="hidden" name="rv_validacion_submitted" value="1">

					<div class="rv-vf-row">
						<label for="rv_experto"><?php esc_html_e( 'Nombre del experto', 'rescate-vertical-tools' ); ?></label>
						<input type="text" id="rv_experto" name="rv_experto" required
							value="<?php echo isset( $_POST['rv_experto'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_POST['rv_experto'] ) ) ) : ''; ?>">
					</div>

					<?php
					self::render_scale_row( 'rv_claridad', __( 'Claridad del contenido', 'rescate-vertical-tools' ) );
					self::render_scale_row( 'rv_utilidad', __( 'Utilidad del recurso', 'rescate-vertical-tools' ) );
					self::render_scale_row( 'rv_diseno', __( 'Diseño y usabilidad', 'rescate-vertical-tools' ) );
					?>

					<div class="rv-vf-row">
						<label for="rv_observaciones"><?php esc_html_e( 'Observaciones', 'rescate-vertical-tools' ); ?></label>
						<textarea id="rv_observaciones" name="rv_observaciones"><?php echo isset( $_POST['rv_observaciones'] ) ? esc_textarea( sanitize_textarea_field( wp_unslash( $_POST['rv_observaciones'] ) ) ) : ''; ?></textarea>
					</div>

					<button type="submit" class="rv-vf-submit"><?php esc_html_e( 'Enviar validación', 'rescate-vertical-tools' ); ?></button>
				</form>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Renders a 1–5 rating scale row.
	 *
	 * @param string $name  Field name/id.
	 * @param string $label Visible label.
	 */
	private static function render_scale_row( $name, $label ) {
		$current = isset( $_POST[ $name ] ) ? absint( $_POST[ $name ] ) : 0;
		?>
		<div class="rv-vf-row">
			<label id="<?php echo esc_attr( $name ); ?>-label"><?php echo esc_html( $label ); ?></label>
			<div class="rv-vf-scale" role="radiogroup" aria-labelledby="<?php echo esc_attr( $name ); ?>-label">
				<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
					<label>
						<input type="radio" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $i ); ?>" <?php checked( $current, $i ); ?> required>
						<?php echo esc_html( (string) $i ); ?>
					</label>
				<?php endfor; ?>
			</div>
		</div>
		<?php
	}
}
