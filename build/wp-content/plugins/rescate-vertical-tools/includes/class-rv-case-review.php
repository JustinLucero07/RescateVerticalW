<?php
/**
 * Shortcode [rv_presenta_caso] — a student describes a rescue case and
 * receives AI-generated educational feedback via the Gemini API.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RV_Case_Review {

	const NONCE_ACTION = 'rv_presenta_caso';
	const AJAX_ACTION   = 'rv_revisar_caso';
	const MAX_CASE_LEN  = 6000;

	public static function register() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_shortcode( 'rv_presenta_caso', array( __CLASS__, 'render' ) );
		add_action( 'wp_ajax_' . self::AJAX_ACTION, array( __CLASS__, 'handle_ajax' ) );
		add_action( 'wp_ajax_nopriv_' . self::AJAX_ACTION, array( __CLASS__, 'handle_ajax' ) );
	}

	public static function enqueue_assets() {
		wp_enqueue_style(
			'rv-tools-case',
			RV_TOOLS_URL . 'assets/css/rv-case.css',
			array(),
			RV_TOOLS_VERSION
		);

		wp_enqueue_script(
			'rv-tools-case',
			RV_TOOLS_URL . 'assets/js/rv-case.js',
			array(),
			RV_TOOLS_VERSION,
			true
		);

		wp_localize_script(
			'rv-tools-case',
			'rvCaseData',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'action'  => self::AJAX_ACTION,
				'nonce'   => wp_create_nonce( self::NONCE_ACTION ),
				'i18n'    => array(
					'loading' => __( 'Analizando el caso con IA…', 'rescate-vertical-tools' ),
					'empty'   => __( 'Describe el caso antes de enviarlo.', 'rescate-vertical-tools' ),
					'generic' => __( 'Ocurrió un error al analizar el caso. Inténtalo de nuevo.', 'rescate-vertical-tools' ),
				),
			)
		);
	}

	public static function render( $atts ) {
		ob_start();
		?>
		<div class="rv-case">
			<form class="rv-case-form" id="rv-case-form">
				<div class="rv-case-row">
					<label for="rv_caso_texto"><?php esc_html_e( 'Describe el caso (terreno, altura, estado del paciente, equipo disponible…)', 'rescate-vertical-tools' ); ?></label>
					<textarea id="rv_caso_texto" name="rv_caso_texto" maxlength="<?php echo esc_attr( self::MAX_CASE_LEN ); ?>" required></textarea>
				</div>
				<button type="submit" class="rv-case-submit"><?php esc_html_e( 'Analizar caso con IA', 'rescate-vertical-tools' ); ?></button>
				<p class="rv-case-disclaimer"><?php esc_html_e( 'Retroalimentación generada por IA con fines educativos. No sustituye la supervisión de un instructor certificado.', 'rescate-vertical-tools' ); ?></p>
			</form>
			<div class="rv-case-result" id="rv-case-result" role="status" aria-live="polite" hidden></div>
		</div>
		<?php
		return ob_get_clean();
	}

	public static function handle_ajax() {
		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		$caso = isset( $_POST['rv_caso_texto'] ) ? sanitize_textarea_field( wp_unslash( $_POST['rv_caso_texto'] ) ) : '';

		if ( '' === trim( $caso ) ) {
			wp_send_json_error( array( 'message' => __( 'Describe el caso antes de enviarlo.', 'rescate-vertical-tools' ) ) );
		}

		if ( function_exists( 'mb_substr' ) ) {
			$caso = mb_substr( $caso, 0, self::MAX_CASE_LEN );
		} else {
			$caso = substr( $caso, 0, self::MAX_CASE_LEN );
		}

		$settings = rv_get_settings();
		$api_key  = trim( $settings['gemini_api_key'] );

		if ( '' === $api_key ) {
			wp_send_json_error( array( 'message' => __( 'El sitio aún no tiene configurada la clave de la API de Gemini. Ve a Rescate Vertical → Ajustes.', 'rescate-vertical-tools' ) ) );
		}

		$result = self::call_gemini( $api_key, $settings['gemini_model'], $settings['gemini_prompt'], $caso );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		wp_send_json_success( array( 'texto' => $result ) );
	}

	/**
	 * Calls the Gemini generateContent REST endpoint.
	 * Publica: la reutiliza RV_Cases para corregir ejercicios.
	 *
	 * @param string $api_key       Gemini API key.
	 * @param string $model         Gemini model name (e.g. gemini-2.0-flash).
	 * @param string $system_prompt System instruction configured in settings.
	 * @param string $caso          Sanitized case description from the student.
	 * @return string|WP_Error Generated feedback text, or a WP_Error on failure.
	 */
	public static function call_gemini( $api_key, $model, $system_prompt, $caso ) {
		$endpoint = sprintf(
			'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent',
			rawurlencode( $model )
		);

		$body = array(
			'systemInstruction' => array(
				'parts' => array( array( 'text' => $system_prompt ) ),
			),
			'contents'          => array(
				array(
					'parts' => array( array( 'text' => $caso ) ),
				),
			),
			'generationConfig'  => array(
				'temperature'     => 0.4,
				'maxOutputTokens' => 8192,
			),
		);

		$response = wp_remote_post(
			$endpoint,
			array(
				'timeout' => 60,
				'headers' => array(
					'Content-Type'   => 'application/json',
					'x-goog-api-key' => $api_key,
				),
				'body'    => wp_json_encode( $body ),
			)
		);

		if ( is_wp_error( $response ) ) {
			return new WP_Error( 'rv_gemini_http', __( 'No se pudo contactar con el servicio de IA. Inténtalo de nuevo en unos minutos.', 'rescate-vertical-tools' ) );
		}

		$code = wp_remote_retrieve_response_code( $response );
		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( 200 !== (int) $code ) {
			$api_message = isset( $data['error']['message'] ) ? $data['error']['message'] : '';
			return new WP_Error(
				'rv_gemini_api',
				$api_message
					? sprintf(
						/* translators: %s: error message returned by the Gemini API */
						__( 'El servicio de IA devolvió un error: %s', 'rescate-vertical-tools' ),
						$api_message
					)
					: __( 'El servicio de IA devolvió un error. Revisa la clave de la API en Ajustes.', 'rescate-vertical-tools' )
			);
		}

		/*
		 * Gemini devuelve "parts" como ARRAY y con frecuencia reparte la
		 * respuesta en varios trozos. Leer solo parts[0] entrega nada más el
		 * primer fragmento (típicamente la frase de entrada), que es como se
		 * veía cortada la retroalimentación. Hay que concatenarlos todos.
		 */
		$parts  = isset( $data['candidates'][0]['content']['parts'] ) && is_array( $data['candidates'][0]['content']['parts'] )
			? $data['candidates'][0]['content']['parts']
			: array();
		$finish = isset( $data['candidates'][0]['finishReason'] ) ? $data['candidates'][0]['finishReason'] : '';

		$chunks = array();
		foreach ( $parts as $part ) {
			if ( isset( $part['text'] ) && is_string( $part['text'] ) ) {
				$chunks[] = $part['text'];
			}
		}
		$text = trim( implode( '', $chunks ) );

		if ( '' === $text ) {
			if ( 'SAFETY' === $finish || 'PROHIBITED_CONTENT' === $finish ) {
				return new WP_Error(
					'rv_gemini_safety',
					__( 'Los filtros de contenido del servicio de IA bloquearon este caso. Reformúlalo en términos clínicos y técnicos.', 'rescate-vertical-tools' )
				);
			}
			return new WP_Error(
				'rv_gemini_empty',
				__( 'La IA no pudo generar una respuesta para este caso. Intenta reformularlo con más detalle.', 'rescate-vertical-tools' )
			);
		}

		// Si el modelo agotó el techo de tokens, avisarlo en vez de cortar en seco.
		if ( 'MAX_TOKENS' === $finish ) {
			$text .= "\n\n" . __( '[La respuesta se cortó por longitud: resume el caso o envíalo por partes para recibir el análisis completo.]', 'rescate-vertical-tools' );
		}

		return $text;
	}
}
