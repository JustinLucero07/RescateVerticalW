<?php
/**
 * Casos prácticos: el alumno recibe un caso al azar, lo resuelve y obtiene
 * la corrección.
 *
 * Los casos viven en un CPT propio para que el profesor pueda añadirlos y
 * editarlos desde el escritorio. El caso se sirve por AJAX (no incrustado en
 * el HTML) por dos motivos: así sigue siendo aleatorio aunque la página esté
 * cacheada, y la respuesta modelo no queda expuesta en el código fuente.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RV_Cases {

	const CPT          = 'rv_caso';
	const NONCE_ACTION = 'rv_caso_practico';
	const AJAX_GET     = 'rv_caso_obtener';
	const AJAX_CHECK   = 'rv_caso_corregir';

	public static function register() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ) );
		add_action( 'save_post_' . self::CPT, array( __CLASS__, 'save_meta' ), 10, 2 );
		add_filter( 'manage_' . self::CPT . '_posts_columns', array( __CLASS__, 'columns' ) );
		add_action( 'manage_' . self::CPT . '_posts_custom_column', array( __CLASS__, 'render_column' ), 10, 2 );

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_shortcode( 'rv_caso_practico', array( __CLASS__, 'render' ) );

		foreach ( array( self::AJAX_GET, self::AJAX_CHECK ) as $action ) {
			add_action( 'wp_ajax_' . $action, array( __CLASS__, 'handle_' . ( self::AJAX_GET === $action ? 'get' : 'check' ) ) );
			add_action( 'wp_ajax_nopriv_' . $action, array( __CLASS__, 'handle_' . ( self::AJAX_GET === $action ? 'get' : 'check' ) ) );
		}
	}

	/* ------------------------------------------------------------------
	 * CPT
	 * ------------------------------------------------------------------ */

	public static function register_post_type() {
		register_post_type(
			self::CPT,
			array(
				'labels'          => array(
					'name'          => __( 'Casos prácticos', 'rescate-vertical-tools' ),
					'singular_name' => __( 'Caso práctico', 'rescate-vertical-tools' ),
					'menu_name'     => __( 'Casos prácticos', 'rescate-vertical-tools' ),
					'add_new'       => __( 'Añadir caso', 'rescate-vertical-tools' ),
					'add_new_item'  => __( 'Añadir caso práctico', 'rescate-vertical-tools' ),
					'edit_item'     => __( 'Editar caso práctico', 'rescate-vertical-tools' ),
					'not_found'     => __( 'Todavía no hay casos.', 'rescate-vertical-tools' ),
				),
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => RV_TOOLS_MENU_SLUG,
				'capability_type' => 'post',
				'map_meta_cap'    => true,
				'supports'        => array( 'title', 'editor' ),
				'has_archive'     => false,
				'rewrite'         => false,
				'show_in_rest'    => false,
				'menu_icon'       => 'dashicons-clipboard',
			)
		);
	}

	public static function add_meta_box() {
		add_meta_box(
			'rv_caso_datos',
			__( 'Datos del caso y respuesta modelo', 'rescate-vertical-tools' ),
			array( __CLASS__, 'render_meta_box' ),
			self::CPT,
			'normal',
			'high'
		);
	}

	public static function render_meta_box( $post ) {
		wp_nonce_field( 'rv_caso_meta', 'rv_caso_meta_nonce' );
		$caida  = get_post_meta( $post->ID, 'rv_caida', true );
		$cuerda = get_post_meta( $post->ID, 'rv_cuerda', true );
		$modelo = get_post_meta( $post->ID, 'rv_modelo', true );
		?>
		<p><?php esc_html_e( 'El enunciado del caso se escribe en el editor de arriba. Aquí van los datos que permiten corregir automáticamente y la respuesta modelo que verá el alumno.', 'rescate-vertical-tools' ); ?></p>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><label for="rv_caida"><?php esc_html_e( 'Distancia de caída (m)', 'rescate-vertical-tools' ); ?></label></th>
				<td>
					<input type="number" step="0.1" min="0" id="rv_caida" name="rv_caida" value="<?php echo esc_attr( $caida ); ?>" class="small-text">
					<p class="description"><?php esc_html_e( 'Se usa para corregir el factor de caída. Déjalo vacío si el caso no lo plantea.', 'rescate-vertical-tools' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="rv_cuerda"><?php esc_html_e( 'Cuerda disponible (m)', 'rescate-vertical-tools' ); ?></label></th>
				<td><input type="number" step="0.1" min="0" id="rv_cuerda" name="rv_cuerda" value="<?php echo esc_attr( $cuerda ); ?>" class="small-text"></td>
			</tr>
			<tr>
				<th scope="row"><label for="rv_modelo"><?php esc_html_e( 'Respuesta modelo', 'rescate-vertical-tools' ); ?></label></th>
				<td>
					<textarea id="rv_modelo" name="rv_modelo" rows="10" class="large-text"><?php echo esc_textarea( $modelo ); ?></textarea>
					<p class="description"><?php esc_html_e( 'Lo que el alumno debería haber contestado. Admite viñetas con guion y títulos con ##.', 'rescate-vertical-tools' ); ?></p>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Guarda los campos del caso.
	 *
	 * @param int     $post_id ID del caso.
	 * @param WP_Post $post    Objeto del caso.
	 */
	public static function save_meta( $post_id, $post ) {
		if ( ! isset( $_POST['rv_caso_meta_nonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['rv_caso_meta_nonce'] ) ), 'rv_caso_meta' )
		) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		update_post_meta( $post_id, 'rv_caida', isset( $_POST['rv_caida'] ) ? (float) $_POST['rv_caida'] : '' );
		update_post_meta( $post_id, 'rv_cuerda', isset( $_POST['rv_cuerda'] ) ? (float) $_POST['rv_cuerda'] : '' );
		update_post_meta(
			$post_id,
			'rv_modelo',
			isset( $_POST['rv_modelo'] ) ? sanitize_textarea_field( wp_unslash( $_POST['rv_modelo'] ) ) : ''
		);
	}

	public static function columns( $columns ) {
		$new = array();
		foreach ( $columns as $k => $v ) {
			$new[ $k ] = $v;
			if ( 'title' === $k ) {
				$new['rv_fc'] = __( 'Factor de caída', 'rescate-vertical-tools' );
			}
		}
		return $new;
	}

	public static function render_column( $column, $post_id ) {
		if ( 'rv_fc' !== $column ) {
			return;
		}
		$fc = self::fall_factor( $post_id );
		echo null === $fc ? '—' : esc_html( number_format_i18n( $fc, 2 ) );
	}

	/**
	 * Factor de caída real del caso, o null si no tiene datos suficientes.
	 *
	 * @param int $post_id ID del caso.
	 * @return float|null
	 */
	private static function fall_factor( $post_id ) {
		$caida  = (float) get_post_meta( $post_id, 'rv_caida', true );
		$cuerda = (float) get_post_meta( $post_id, 'rv_cuerda', true );
		if ( $cuerda <= 0 ) {
			return null;
		}
		return min( $caida / $cuerda, 2 );
	}

	/* ------------------------------------------------------------------
	 * Front
	 * ------------------------------------------------------------------ */

	public static function enqueue_assets() {
		wp_enqueue_style( 'rv-tools-cases', RV_TOOLS_URL . 'assets/css/rv-cases.css', array(), RV_TOOLS_VERSION );
		wp_enqueue_script( 'rv-tools-cases', RV_TOOLS_URL . 'assets/js/rv-cases.js', array(), RV_TOOLS_VERSION, true );

		wp_localize_script(
			'rv-tools-cases',
			'rvCasesData',
			array(
				'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
				'actionGet'   => self::AJAX_GET,
				'actionCheck' => self::AJAX_CHECK,
				'nonce'       => wp_create_nonce( self::NONCE_ACTION ),
				'i18n'        => array(
					'loading'   => __( 'Cargando caso…', 'rescate-vertical-tools' ),
					'checking'  => __( 'Corrigiendo tu respuesta…', 'rescate-vertical-tools' ),
					'noCases'   => __( 'Todavía no hay casos cargados. Añádelos desde Rescate Vertical → Casos prácticos.', 'rescate-vertical-tools' ),
					'generic'   => __( 'Ocurrió un error. Inténtalo de nuevo.', 'rescate-vertical-tools' ),
					'needAnswer'=> __( 'Escribe al menos una respuesta antes de pedir la corrección.', 'rescate-vertical-tools' ),
				),
			)
		);
	}

	public static function render( $atts ) {
		ob_start();
		?>
		<div class="rv-caso" id="rv-caso">
			<div class="rv-caso-head">
				<span class="rv-caso-badge"><?php esc_html_e( 'Caso práctico', 'rescate-vertical-tools' ); ?></span>
				<button type="button" class="rv-caso-nuevo" id="rv-caso-nuevo">
					<?php esc_html_e( 'Otro caso', 'rescate-vertical-tools' ); ?>
				</button>
			</div>

			<h3 class="rv-caso-titulo" id="rv-caso-titulo"></h3>
			<div class="rv-caso-enunciado" id="rv-caso-enunciado">
				<p><?php esc_html_e( 'Cargando caso…', 'rescate-vertical-tools' ); ?></p>
			</div>

			<form class="rv-caso-form" id="rv-caso-form" hidden>
				<div class="rv-caso-row rv-caso-row--fc" id="rv-caso-fc-row">
					<label for="rv-caso-fc"><?php esc_html_e( '1. ¿Cuál es el factor de caída?', 'rescate-vertical-tools' ); ?></label>
					<input type="number" step="0.01" min="0" max="2" id="rv-caso-fc" placeholder="0.00">
				</div>
				<div class="rv-caso-row">
					<label for="rv-caso-riesgos"><?php esc_html_e( '2. Riesgos técnicos que identificas', 'rescate-vertical-tools' ); ?></label>
					<textarea id="rv-caso-riesgos" rows="3"></textarea>
				</div>
				<div class="rv-caso-row">
					<label for="rv-caso-medico"><?php esc_html_e( '3. Prioridades médicas', 'rescate-vertical-tools' ); ?></label>
					<textarea id="rv-caso-medico" rows="3"></textarea>
				</div>
				<div class="rv-caso-row">
					<label for="rv-caso-accion"><?php esc_html_e( '4. ¿Qué harías primero?', 'rescate-vertical-tools' ); ?></label>
					<textarea id="rv-caso-accion" rows="3"></textarea>
				</div>
				<button type="submit" class="rv-caso-enviar"><?php esc_html_e( 'Ver corrección', 'rescate-vertical-tools' ); ?></button>
			</form>

			<div class="rv-caso-correccion" id="rv-caso-correccion" role="status" aria-live="polite" hidden></div>
		</div>
		<?php
		return ob_get_clean();
	}

	/* ------------------------------------------------------------------
	 * AJAX
	 * ------------------------------------------------------------------ */

	/** Devuelve un caso al azar (sin la respuesta modelo). */
	public static function handle_get() {
		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		$exclude = isset( $_POST['exclude'] ) ? absint( $_POST['exclude'] ) : 0;

		$args = array(
			'post_type'      => self::CPT,
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'orderby'        => 'rand',
			'no_found_rows'  => true,
		);
		if ( $exclude ) {
			$args['post__not_in'] = array( $exclude );
		}

		$posts = get_posts( $args );

		// Si al excluir el anterior no queda ninguno, se repite el mismo.
		if ( ! $posts && $exclude ) {
			unset( $args['post__not_in'] );
			$posts = get_posts( $args );
		}

		if ( ! $posts ) {
			wp_send_json_error( array( 'message' => __( 'Todavía no hay casos cargados.', 'rescate-vertical-tools' ) ) );
		}

		$post = $posts[0];

		wp_send_json_success(
			array(
				'id'         => $post->ID,
				'titulo'     => get_the_title( $post ),
				'enunciado'  => wpautop( wp_kses_post( $post->post_content ) ),
				'preguntaFc' => null !== self::fall_factor( $post->ID ),
			)
		);
	}

	/** Corrige la respuesta del alumno. */
	public static function handle_check() {
		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		$id = isset( $_POST['caso'] ) ? absint( $_POST['caso'] ) : 0;
		if ( ! $id || self::CPT !== get_post_type( $id ) || 'publish' !== get_post_status( $id ) ) {
			wp_send_json_error( array( 'message' => __( 'El caso ya no está disponible. Pide otro.', 'rescate-vertical-tools' ) ) );
		}

		$fc_alumno = isset( $_POST['fc'] ) && '' !== $_POST['fc'] ? (float) $_POST['fc'] : null;
		$riesgos   = isset( $_POST['riesgos'] ) ? sanitize_textarea_field( wp_unslash( $_POST['riesgos'] ) ) : '';
		$medico    = isset( $_POST['medico'] ) ? sanitize_textarea_field( wp_unslash( $_POST['medico'] ) ) : '';
		$accion    = isset( $_POST['accion'] ) ? sanitize_textarea_field( wp_unslash( $_POST['accion'] ) ) : '';

		$respuesta = array(
			'fc'     => null,
			// Texto plano: el cliente lo renderiza con el mismo markdown que la IA.
			'modelo' => (string) get_post_meta( $id, 'rv_modelo', true ),
			'ia'     => '',
		);

		// --- Corrección objetiva del factor de caída ---
		$fc_real = self::fall_factor( $id );
		if ( null !== $fc_real ) {
			$caida   = (float) get_post_meta( $id, 'rv_caida', true );
			$cuerda  = (float) get_post_meta( $id, 'rv_cuerda', true );
			$acierto = ( null !== $fc_alumno ) && ( abs( $fc_alumno - $fc_real ) <= 0.1 );

			$respuesta['fc'] = array(
				'correcto' => $acierto,
				'tuyo'     => null === $fc_alumno ? '' : number_format_i18n( $fc_alumno, 2 ),
				'real'     => number_format_i18n( $fc_real, 2 ),
				'calculo'  => sprintf(
					/* translators: 1: caída en metros, 2: cuerda en metros, 3: resultado */
					__( '%1$s m de caída ÷ %2$s m de cuerda = %3$s', 'rescate-vertical-tools' ),
					number_format_i18n( $caida, 1 ),
					number_format_i18n( $cuerda, 1 ),
					number_format_i18n( $fc_real, 2 )
				),
			);
		}

		// --- Evaluación con IA, solo si hay clave configurada ---
		$settings = rv_get_settings();
		$api_key  = trim( $settings['gemini_api_key'] );

		if ( '' !== $api_key && ( '' !== $riesgos || '' !== $medico || '' !== $accion ) ) {
			$prompt = "Eres un instructor de rescate vertical corrigiendo el ejercicio de un alumno. "
				. "Compara sus respuestas con la respuesta modelo y responde en español, en menos de 200 palabras, con esta estructura:\n\n"
				. "## Lo que acertaste\nMáximo 3 viñetas.\n\n"
				. "## Lo que faltó\nMáximo 3 viñetas.\n\n"
				. "Sé concreto y no repitas el enunciado. No uses viñetas anidadas.";

			$entrada = "CASO:\n" . wp_strip_all_tags( get_post_field( 'post_content', $id ) ) . "\n\n"
				. "RESPUESTA MODELO:\n" . (string) get_post_meta( $id, 'rv_modelo', true ) . "\n\n"
				. "RESPUESTAS DEL ALUMNO:\n"
				. "1. Riesgos técnicos: " . ( '' !== $riesgos ? $riesgos : '(sin responder)' ) . "\n"
				. "2. Prioridades médicas: " . ( '' !== $medico ? $medico : '(sin responder)' ) . "\n"
				. "3. Primera acción: " . ( '' !== $accion ? $accion : '(sin responder)' ) . "\n";

			$ia = RV_Case_Review::call_gemini( $api_key, $settings['gemini_model'], $prompt, $entrada );
			if ( ! is_wp_error( $ia ) ) {
				$respuesta['ia'] = $ia;
			}
		}

		wp_send_json_success( $respuesta );
	}

	/* ------------------------------------------------------------------
	 * Casos de ejemplo
	 * ------------------------------------------------------------------ */

	/**
	 * Crea los casos de ejemplo la primera vez. No duplica si ya existen.
	 */
	public static function seed() {
		$existing = get_posts(
			array(
				'post_type'      => self::CPT,
				'post_status'    => array( 'publish', 'draft' ),
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);
		if ( $existing ) {
			return;
		}

		foreach ( self::seed_data() as $caso ) {
			$id = wp_insert_post(
				array(
					'post_type'    => self::CPT,
					'post_status'  => 'publish',
					'post_title'   => $caso['titulo'],
					'post_content' => $caso['enunciado'],
				),
				true
			);
			if ( is_wp_error( $id ) ) {
				continue;
			}
			update_post_meta( $id, 'rv_caida', $caso['caida'] );
			update_post_meta( $id, 'rv_cuerda', $caso['cuerda'] );
			update_post_meta( $id, 'rv_modelo', $caso['modelo'] );
		}
	}

	/**
	 * @return array<int,array<string,mixed>>
	 */
	private static function seed_data() {
		return array(
			array(
				'titulo'    => 'Escalador con fractura en pared',
				'caida'     => 8,
				'cuerda'    => 10,
				'enunciado' => "Una persona practicaba escalada y sufrió una caída de 8 metros. Usaba una cuerda dinámica de 10 metros desde su último punto de anclaje.\n\nAl llegar, la víctima está consciente y orientada, pero presenta una deformidad visible en la pierna izquierda compatible con fractura de tibia. Son las 18:00 y la temperatura ambiente es de 10 °C. Quedan unas dos horas de luz.",
				'modelo'    => "## Factor de caída\n8 m ÷ 10 m = 0,80. Riesgo moderado: el sistema absorbió el impacto sin llegar al límite de 12 kN, pero un anclaje intermedio lo habría reducido bastante.\n\n## Riesgos técnicos\n- Revisar la cuerda y el anclaje antes de volver a cargarlos: ya absorbieron una caída real.\n- Montar un sistema de descenso con anclaje redundante y ángulo por debajo de 60°.\n- Proteger los bordes de roca para evitar abrasión durante la evacuación.\n\n## Prioridades médicas\n- Evaluación XABCDE según PHTLS antes de mover a la víctima.\n- Inmovilizar la fractura de tibia con férula antes del traslado; valorar pulso distal antes y después.\n- Prevenir la hipotermia: a 10 °C y con inmovilidad, la pérdida de calor es un riesgo real. Aislar del suelo y cubrir.\n\n## Qué harías primero\nAsegurar la escena y tu propio anclaje antes de acercarte. Después XABCDE, control del dolor, inmovilización y solo entonces montar la extracción. La luz restante condiciona el ritmo: prepara iluminación antes de que anochezca.",
			),
			array(
				'titulo'    => 'Operario suspendido en torre de telecomunicaciones',
				'caida'     => 2,
				'cuerda'    => 6,
				'enunciado' => "Un operario cae desde una plataforma de una torre de telecomunicaciones y queda suspendido de su arnés integral a 25 metros del suelo. Cayó 2 metros y tenía 6 metros de cuerda en juego.\n\nEstá consciente, responde, pero refiere hormigueo en las piernas y sensación de mareo. Lleva unos 12 minutos suspendido. Son las 14:00 y hace 32 °C.",
				'modelo'    => "## Factor de caída\n2 m ÷ 6 m = 0,33. Factor bajo: el impacto quedó lejos del límite del sistema.\n\n## Riesgos técnicos\n- El acceso vertical debe montarse sobre un anclaje independiente del que sostiene a la víctima.\n- Vigilar el rozamiento de las cuerdas contra la estructura metálica.\n- El calor a 32 °C afecta también al rescatista: turnos cortos e hidratación.\n\n## Prioridades médicas\n- Sospecha de síndrome de suspensión: es la urgencia real del caso, no la caída.\n- Reducir el tiempo suspendido lo antes posible; si no se puede bajar de inmediato, ayudar a que apoye los pies o eleve las piernas.\n- Tras el rescate, no tumbar bruscamente en horizontal: retorno venoso controlado y monitorización.\n\n## Qué harías primero\nEl reloj manda: llevar 12 minutos suspendido con hormigueo y mareo convierte esto en una extracción urgente. Prioriza bajarlo sobre cualquier otra maniobra.",
			),
			array(
				'titulo'    => 'Senderista en quebrada, caída larga',
				'caida'     => 12,
				'cuerda'    => 15,
				'enunciado' => "Un senderista cae por una quebrada de unos 12 metros. El equipo de rescate desciende con 15 metros de cuerda desde el borde.\n\nLa víctima está inconsciente pero respira. Se aprecia sangrado activo en el cuero cabelludo y no se puede descartar lesión de columna. Son las 16:30 y la temperatura es de 5 °C. El terreno del fondo es irregular y hay agua corriendo.",
				'modelo'    => "## Factor de caída\n12 m ÷ 15 m = 0,80. Riesgo moderado, pero la altura absoluta hace prever lesiones graves por el impacto.\n\n## Riesgos técnicos\n- Caída de piedras sobre el equipo y la víctima al descender: casco obligatorio y control del borde.\n- Anclajes en roca de calidad dudosa: montar redundancia y evaluar cada punto.\n- Agua corriendo: riesgo de hipotermia acelerada y de perder tracción.\n\n## Prioridades médicas\n- Inconsciente que respira: vía aérea con control cervical, según PHTLS y TECC.\n- Control del sangrado del cuero cabelludo por presión directa; sangra mucho y contribuye a la hipotermia.\n- Restricción de movimiento espinal e inmovilización en camilla tipo Stokes para la extracción vertical.\n- A 5 °C y con la ropa mojada, aislar del suelo es tan urgente como el resto.\n\n## Qué harías primero\nSeguridad de la escena y control del borde antes de bajar a nadie. Al llegar a la víctima: vía aérea con control cervical, hemorragia y prevención de hipotermia, en ese orden, mientras el resto del equipo monta el sistema de izado.",
			),
			array(
				'titulo'    => 'Rescate en pozo de espacio confinado',
				'caida'     => 3,
				'cuerda'    => 4,
				'enunciado' => "Un trabajador cae al fondo de un pozo de 9 metros de profundidad mientras realizaba una inspección. Cayó 3 metros con 4 metros de cuerda en juego antes de que su sistema lo detuviera.\n\nEstá consciente y refiere dolor lumbar intenso. El pozo es estrecho y no se conoce la calidad del aire en el fondo. Son las 22:00 y la temperatura en el interior es de 8 °C.",
				'modelo'    => "## Factor de caída\n3 m ÷ 4 m = 0,75. Riesgo moderado, cerca del umbral que obliga a revisar el montaje.\n\n## Riesgos técnicos\n- Espacio confinado: no entra nadie sin medir atmósfera (oxígeno, gases tóxicos e inflamables) y sin ventilación forzada.\n- Trípode o pórtico sobre la boca del pozo con sistema de izado y línea de vida independiente.\n- Pozo estrecho: la camilla debe poder izarse en vertical sin quedar atrapada.\n\n## Prioridades médicas\n- Dolor lumbar tras caída: restricción de movimiento espinal hasta descartar lesión.\n- Valoración XABCDE y control del dolor antes de la transición horizontal a vertical.\n- Vigilar hipotermia: 8 °C, de noche y con inmovilidad.\n\n## Qué harías primero\nMedir la atmósfera. Es lo que diferencia este caso de cualquier otro rescate vertical: entrar sin medir convierte a los rescatistas en víctimas. Solo después montar el izado y atender al paciente.",
			),
		);
	}
}
