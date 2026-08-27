<?php
/**
 * Siembra el contenido que hasta ahora vivía dentro de las plantillas del
 * tema, para que quede editable desde el escritorio sin perder nada.
 *
 * La siembra completa solo se ejecuta si el tipo está vacío. Cuando ya hay
 * fichas creadas se usa el relleno (backfill), que únicamente añade los
 * campos nuevos que estén vacíos: nunca pisa lo que el profesor escribió.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RV_Content_Seed {

	public static function run() {
		self::seed( RV_Content::EQUIPO, self::equipos() );
		self::seed( RV_Content::TECNICA, self::tecnicas() );
		self::seed( RV_Content::PROTOCOLO, self::protocolos() );

		// Para instalaciones que ya tenían fichas creadas antes de esta versión.
		self::backfill( RV_Content::EQUIPO, self::equipos() );
		self::backfill( RV_Content::TECNICA, self::tecnicas() );
		self::backfill( RV_Content::PROTOCOLO, self::protocolos() );
	}

	/**
	 * @param string $type  Tipo de contenido.
	 * @param array  $items Fichas a crear.
	 */
	private static function seed( $type, $items ) {
		$existing = get_posts(
			array(
				'post_type'      => $type,
				'post_status'    => array( 'publish', 'draft', 'trash' ),
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);
		if ( $existing ) {
			return;
		}

		$order = 0;
		foreach ( $items as $item ) {
			$order += 10;
			self::crear( $type, $item, isset( $item['orden'] ) ? $item['orden'] : $order );
		}
	}

	/**
	 * Completa fichas ya existentes con los campos que se han ido añadiendo.
	 *
	 * Empareja por título normalizado (sin acentos ni mayúsculas) o por el
	 * alias de la ficha. Solo escribe un campo si está vacío, así que una
	 * ficha editada a mano queda intacta. Las fichas marcadas como nuevas se
	 * crean si no existe ninguna que se les parezca.
	 *
	 * @param string $type  Tipo de contenido.
	 * @param array  $items Fichas de referencia.
	 */
	private static function backfill( $type, $items ) {
		$posts = get_posts(
			array(
				'post_type'      => $type,
				'post_status'    => array( 'publish', 'draft' ),
				'posts_per_page' => -1,
				'no_found_rows'  => true,
			)
		);
		if ( ! $posts ) {
			return;
		}

		$orden_max = 0;
		$indice    = array();
		foreach ( $posts as $p ) {
			$indice[ self::normaliza( $p->post_title ) ] = $p;
			$orden_max                                   = max( $orden_max, (int) $p->menu_order );
		}

		foreach ( $items as $item ) {
			$post = self::buscar( $item, $indice );

			if ( ! $post ) {
				if ( ! empty( $item['nuevo'] ) ) {
					$orden_max += 10;
					self::crear( $type, $item, isset( $item['orden'] ) ? $item['orden'] : $orden_max );
				}
				continue;
			}

			foreach ( $item as $k => $v ) {
				if ( 0 !== strpos( $k, 'rv_' ) ) {
					continue;
				}
				$actual = get_post_meta( $post->ID, $k, true );
				if ( '' === trim( (string) $actual ) && '' !== trim( (string) $v ) ) {
					update_post_meta( $post->ID, $k, $v );
				}
			}
		}
	}

	/**
	 * Busca la ficha equivalente por título normalizado o por alias.
	 *
	 * @param array $item   Ficha de referencia.
	 * @param array $indice Fichas existentes indexadas por título normalizado.
	 * @return WP_Post|null
	 */
	private static function buscar( $item, $indice ) {
		$titulo = self::normaliza( $item['titulo'] );
		if ( isset( $indice[ $titulo ] ) ) {
			return $indice[ $titulo ];
		}

		$alias = isset( $item['alias'] ) ? self::normaliza( $item['alias'] ) : '';
		if ( '' === $alias ) {
			return null;
		}
		foreach ( $indice as $clave => $post ) {
			if ( false !== strpos( $clave, $alias ) ) {
				return $post;
			}
		}
		return null;
	}

	/**
	 * Título en minúsculas, sin acentos ni espacios de más.
	 *
	 * @param string $texto Título original.
	 * @return string
	 */
	private static function normaliza( $texto ) {
		$texto = remove_accents( (string) $texto );
		$texto = strtolower( trim( $texto ) );
		return preg_replace( '/\s+/', ' ', $texto );
	}

	/**
	 * Crea una ficha con sus metadatos.
	 *
	 * @param string $type  Tipo de contenido.
	 * @param array  $item  Datos de la ficha.
	 * @param int    $orden Posición en la página.
	 * @return void
	 */
	private static function crear( $type, $item, $orden ) {
		$id = wp_insert_post(
			array(
				'post_type'    => $type,
				'post_status'  => 'publish',
				'post_title'   => $item['titulo'],
				'post_content' => isset( $item['texto'] ) ? $item['texto'] : '',
				'menu_order'   => $orden,
			),
			true
		);
		if ( is_wp_error( $id ) ) {
			return;
		}
		foreach ( $item as $k => $v ) {
			if ( 0 === strpos( $k, 'rv_' ) ) {
				update_post_meta( $id, $k, $v );
			}
		}
	}

	/* ------------------------------------------------------------------ */

	private static function equipos() {
		return array(
			array(
				'titulo'        => 'Cuerda dinámica',
				'alias'         => 'cuerda dinamica',
				'texto'         => 'Absorbe energía de caída mediante elongación controlada.',
				'rv_norma'      => 'EN 892',
				'rv_dato_label' => 'Resistencia mín.',
				'rv_dato'       => '22 kN',
				'rv_esquema'    => 'cuerda',
				'rv_partes'     => "- Camisa o funda — trenzado exterior que aguanta la abrasión y el roce contra la roca. Es lo único que se ve al inspeccionar.\n- Alma o núcleo — haz de fibras paralelas del interior: soporta en torno al 85 % de la carga.\n- Marcado de extremo — cinta o funda termorretráctil con la norma, el diámetro y el año de fabricación.\n- Marca de punto medio — señal central que permite igualar los dos cabos antes de un rápel.\n- Extremo rematado — cosido o termosellado para que la camisa no se deshilache.",
				'rv_cuando_si'  => "- Progresión y aseguramiento donde puede haber caída con factor mayor que cero.\n- Escalada de aproximación al punto del paciente.\n- Cualquier tramo donde el rescatista quede por encima de su anclaje.",
				'rv_cuando_no'  => "- Izado y descenso de cargas: la elongación hace el sistema esponjoso e impreciso. Ahí va cuerda estática o semiestática.\n- Líneas de vida horizontales tensadas.\n- Después de haber frenado una caída dura: se retira hasta revisarla.",
			),
			array(
				'titulo'        => 'Cuerda estática / semiestática',
				'alias'         => 'cuerda estatica',
				'texto'         => 'Elongación mínima: mantiene la posición y la precisión bajo carga.',
				'rv_norma'      => 'EN 1891',
				'rv_dato_label' => 'Elongación',
				'rv_dato'       => 'menos del 5 %',
				'rv_esquema'    => 'cuerda',
				'rv_partes'     => "- Camisa o funda — trenzado más cerrado y rígido que en la dinámica: resiste mejor el rozamiento continuado del descensor.\n- Alma o núcleo — fibras casi sin rizado, por eso apenas se estira bajo carga.\n- Marcado de extremo — norma EN 1891 y tipo A o B. El tipo A es el de rescate.\n- Marca de punto medio — imprescindible para repartir la cuerda en maniobras largas.\n- Zona de trabajo — el tramo que roza siempre en el mismo punto; se rota periódicamente.",
				'rv_cuando_si'  => "- Descenso y izado de paciente y camilla.\n- Líneas de trabajo y de seguridad en acceso con cuerda.\n- Sistemas de polipasto, donde la elongación restaría eficiencia.",
				'rv_cuando_no'  => "- Situaciones con riesgo de caída por encima del anclaje: no absorbe energía y la fuerza de choque se dispara.\n- Como sustituto de cuerda dinámica en progresión de escalada.",
			),
			array(
				'titulo'        => 'Arnés integral',
				'alias'         => 'arnes',
				'texto'         => 'Distribuye la carga entre tronco, hombros y muslos.',
				'rv_norma'      => 'EN 361',
				'rv_dato_label' => 'Reduce',
				'rv_dato'       => 'síndrome de suspensión',
				'rv_esquema'    => 'arnes',
				'rv_partes'     => "- Cinturón — apoya sobre la cresta ilíaca, nunca sobre el abdomen. Es lo que sostiene el peso en suspensión.\n- Perneras — rodean los muslos e impiden que el cuerpo vuelque hacia atrás.\n- Punto ventral — anilla frontal a la altura del ombligo: el punto de trabajo y de suspensión.\n- Punto esternal — anilla del pecho, para líneas anticaídas y para izar en vertical.\n- Punto dorsal — anilla de la espalda: caída libre e izado de una persona inconsciente.\n- Hebillas de regulación — ajustan cinturón y perneras. Se comprueban realimentadas antes de cargar.\n- Portamateriales — anillas de plástico para colgar equipo. No soportan carga: se rompen a pocos kilos.\n- Etiqueta interior — norma, talla, fabricante y fecha; si no se lee, el arnés se retira.",
				'rv_cuando_si'  => "- Suspensión prolongada del paciente o del rescatista.\n- Trabajos con riesgo de caída libre, donde hace falta punto dorsal.\n- Evacuación de personas inconscientes.",
				'rv_cuando_no'  => "- No sustituye a la camilla en pacientes con sospecha de lesión espinal.\n- No se usa si presenta cortes, quemaduras o costuras dañadas: se retira del servicio.",
			),
			array(
				'titulo'        => 'Mosquetón de seguro',
				'alias'         => 'mosqueton',
				'texto'         => 'Cierre de seguro automático o de rosca en anclajes críticos.',
				'rv_norma'      => 'EN 362',
				'rv_dato_label' => 'Eje mayor',
				'rv_dato'       => '20 kN',
				'rv_esquema'    => 'mosqueton',
				'rv_partes'     => "- Eje mayor — el lado largo y macizo del cuerpo. Es donde el mosquetón da los 20 a 25 kN marcados.\n- Eje menor — el lado corto, de lado a lado. Ahí resiste apenas 7 kN: nunca se carga en esta dirección.\n- Gatillo — la parte móvil que se abre para conectar. Con el gatillo abierto la resistencia cae también a unos 7 kN.\n- Casquillo de seguro — la rosca o el cierre automático que bloquea el gatillo. Debe quedar totalmente cerrado.\n- Eje del gatillo — el remache sobre el que gira. Si tiene holgura, el mosquetón se retira.\n- Nariz — el encaje donde apoya el gatillo cerrado. Es el punto que se engancha en cintas y anillas.\n- Marcado — CE, la norma EN 362 y los kN de los tres casos: eje mayor, eje menor y gatillo abierto.",
				'rv_cuando_si'  => "- Todo punto crítico del sistema: anclajes, punto maestro, conexión al arnés.\n- Donde la conexión vaya a quedar sin vigilancia durante la maniobra.\n- Con carga siempre en el eje mayor y el gatillo cerrado.",
				'rv_cuando_no'  => "- Nunca cargado en el eje menor ni sobre el gatillo: ahí resiste apenas 7 kN.\n- No se usa un mosquetón sin seguro en puntos vitales.\n- Se retira tras una caída fuerte o un golpe seco contra roca.",
			),
			array(
				'titulo'        => 'Descensor',
				'alias'         => 'descensor',
				'texto'         => 'Fricciona la cuerda para controlar la velocidad de bajada.',
				'rv_norma'      => 'EN 341',
				'rv_dato_label' => 'Tipo',
				'rv_dato'       => 'ID / RIG / ocho',
				'rv_esquema'    => 'descensor',
				'rv_partes'     => "- Placa lateral móvil — se abre para colocar la cuerda sin desconectar el mosquetón del arnés.\n- Leva de frenado — pieza que pinza la cuerda contra el cuerpo del aparato cuando se suelta la empuñadura.\n- Empuñadura — regula la velocidad. Si se suelta, o si se aprieta a tope, el descensor bloquea.\n- Patín o zapata — guía la cuerda y añade la fricción que hace controlable el descenso.\n- Orificio de conexión — donde va el mosquetón de seguro que une el aparato al punto ventral.\n- Pictograma grabado — el dibujo del propio aparato que indica en qué sentido entra la cuerda.\n- Cabo de freno — el lado de la cuerda que la mano de control no suelta nunca.",
				'rv_cuando_si'  => "- Descenso controlado de rescatista o de camilla.\n- Con dispositivos autoblocantes cuando haya que soltar las manos.\n- Siempre con la mano de freno en la cuerda.",
				'rv_cuando_no'  => "- El descensor en ocho retuerce la cuerda: se evita en descensos largos con carga.\n- No se usa con diámetro de cuerda fuera del rango que marca el fabricante.",
			),
			array(
				'titulo'        => 'Bloqueador de puño',
				'alias'         => 'bloqueador',
				'nuevo'         => true,
				'orden'         => 55,
				'texto'         => 'Desliza hacia arriba y muerde la cuerda al cargarlo hacia abajo. Es la pieza que permite ascender por cuerda y la que retiene la carga en un polipasto.',
				'rv_norma'      => 'EN 567',
				'rv_dato_label' => 'Cuerda',
				'rv_dato'       => 'de 8 a 13 mm',
				'rv_esquema'    => 'bloqueador',
				'rv_partes'     => "- Leva dentada — muerde la camisa de la cuerda y detiene el deslizamiento hacia abajo. Los dientes se limpian de barro y hielo.\n- Gatillo de seguridad — pestillo que impide que la leva se abra sola al rozar con la roca.\n- Empuñadura — permite tirar con la mano completa y transmitir el peso del cuerpo.\n- Orificio superior — conexión de la cinta que va al arnés, o de la polea en un polipasto.\n- Orificio inferior — para el pedal de pie o para desmultiplicar el sistema.\n- Canal de la cuerda — la ranura por donde pasa. Hay modelo derecho e izquierdo, y no son intercambiables.",
				'rv_cuando_si'  => "- Ascenso por cuerda fija, combinado con un segundo bloqueador o con pedal.\n- Como bloqueador de retención en un polipasto: sostiene la carga en cada pausa del izado.\n- Como bloqueador de tracción, para desplazar el punto de tiro sobre la cuerda.",
				'rv_cuando_no'  => "- Nunca como elemento anticaídas: con una caída de factor alto los dientes cortan la camisa.\n- No se usa sobre cuerda helada, embarrada o con la camisa dañada: pierde la mordida.\n- No se carga en dirección lateral ni sobre el gatillo de seguridad.",
			),
			array(
				'titulo'        => 'Casco',
				'alias'         => 'casco',
				'texto'         => 'Protege contra impacto de objetos en caída durante la maniobra.',
				'rv_norma'      => 'EN 397',
				'rv_dato_label' => 'Uso',
				'rv_dato'       => 'obligatorio',
				'rv_esquema'    => 'casco',
				'rv_partes'     => "- Calota — la carcasa exterior. Reparte por toda la superficie la energía de un impacto puntual.\n- Absorbedor interno — espuma o cintas que amortiguan y evitan que el golpe llegue al cráneo.\n- Barboquejo — la cinta de la barbilla. Sin abrochar, el casco sale despedido en la caída.\n- Ajuste occipital — rueda o cinta trasera que fija el casco a la nuca para que no baile.\n- Anclajes de linterna — clips laterales para el frontal, imprescindibles en maniobra nocturna.\n- Ventilaciones — orificios que disipan el calor en trabajos largos.",
				'rv_cuando_si'  => "- Siempre, desde que se entra a la zona de trabajo.\n- También sobre el paciente durante el izado o descenso.",
				'rv_cuando_no'  => "- Nunca sin barboquejo abrochado: en una caída se sale.\n- Se retira del servicio tras cualquier impacto fuerte, aunque no se vea fisura.",
			),
			array(
				'titulo'        => 'Camilla de rescate (tipo Stokes)',
				'alias'         => 'camilla',
				'texto'         => 'Inmoviliza al paciente en posición horizontal o inclinada.',
				'rv_norma'      => 'Enganches certificados',
				'rv_dato_label' => 'Prioriza',
				'rv_dato'       => 'restricción de movimiento',
				'rv_esquema'    => 'camilla',
				'rv_partes'     => "- Bastidor perimetral — el tubo estructural que da rigidez al conjunto y sirve de asa para el porteo.\n- Cesta o plataforma — la superficie sobre la que apoya el paciente, normalmente con acolchado.\n- Correas de sujeción — fijan tórax, pelvis y piernas. Son las que impiden el desplazamiento.\n- Bridas de suspensión o araña — las ramas que unen la camilla al punto de izado.\n- Apoyo de pies — evita que el paciente se deslice hacia abajo cuando el izado es vertical.\n- Puntos de izado certificados — los únicos lugares por los que se puede colgar la camilla.\n- Cabo guía — cuerda auxiliar que maneja alguien desde abajo para que la camilla no gire ni golpee la pared.",
				'rv_cuando_si'  => "- Sospecha de lesión espinal o pélvica.\n- Paciente inconsciente o con lesiones múltiples.\n- Extracción larga donde la suspensión en arnés sería intolerable.",
				'rv_cuando_no'  => "- En pozos muy estrechos donde no pueda izarse en vertical sin quedar atrapada: hay que valorar otro sistema.\n- Nunca se iza sin bridas de suspensión certificadas y con el paciente bien fijado.",
			),
		);
	}

	private static function tecnicas() {
		return array(
			array(
				'titulo'      => 'Nudo ocho',
				'alias'       => 'ocho',
				'texto'       => 'Terminal de alta resistencia, fácil de inspeccionar a simple vista. Es el nudo de encordamiento por defecto en rescate porque su silueta se reconoce de un vistazo, incluso con poca luz.',
				'rv_grupo'    => 'nudo',
				'rv_etiqueta' => 'Encordamiento',
				'rv_material' => "- Cuerda semiestática o dinámica.\n- Mosquetón de seguro, si el nudo va conectado a un anclaje.",
				'rv_pasos'    => "- Mide un cabo de trabajo de unos 80 cm desde el extremo de la cuerda.\n- Dobla la cuerda sobre sí misma para formar una gaza: es el ocho por seno, el que se usa cuando el extremo queda libre.\n- Pasa la gaza por detrás del cabo principal dando una vuelta completa.\n- Introduce la gaza por el ojo que acaba de formarse. Ya se distingue la silueta del número ocho.\n- Aprieta tirando de los cuatro cabos por igual, uno a uno, sin dejar ningún cruce montado sobre otro.\n- Deja un rabo de seguridad de al menos diez veces el diámetro de la cuerda: unos 10 cm en cuerda de 11 mm.\n- Inspección final: deben verse dos curvas paralelas limpias que se siguen con la vista sin perderse.",
				'rv_errores'  => "- Rabo de seguridad corto: bajo carga cíclica el nudo puede correrse y deshacerse.\n- Cabos cruzados dentro del nudo: pierde resistencia y después cuesta muchísimo deshacerlo.\n- Apretar tirando de un solo cabo: el nudo queda deformado y no se puede inspeccionar.\n- Hacerlo con prisa y no revisarlo: todo nudo crítico lo comprueba una segunda persona.",
			),
			array(
				'titulo'      => 'Ballestrinque',
				'alias'       => 'ballestrinque',
				'texto'       => 'Fijación rápida a estructuras tubulares, ajustable bajo carga. Su ventaja es que permite regular la longitud sin deshacerlo, algo que ahorra tiempo montando un anclaje.',
				'rv_grupo'    => 'nudo',
				'rv_etiqueta' => 'Anclaje',
				'rv_material' => "- Cuerda o cordino.\n- Estructura tubular, barandilla o mosquetón donde montarlo.",
				'rv_pasos'    => "- Da una vuelta completa a la estructura con la cuerda, dejando el cabo de trabajo hacia ti.\n- Da una segunda vuelta por encima de la primera, en el mismo sentido de giro.\n- Cruza el cabo de la segunda vuelta por debajo de sí mismo: ahí se forma la mordida que bloquea el nudo.\n- Introduce el extremo bajo esa última vuelta y tira para apretar.\n- Comprueba que las dos vueltas quedan paralelas y en contacto, sin hueco entre ellas.\n- Ajusta la longitud tirando del cabo correspondiente: el ballestrinque se regula sin deshacerlo.\n- Remátalo con un nudo de tope si va a quedar como punto fijo de la maniobra.",
				'rv_errores'  => "- Montarlo sobre una superficie muy lisa o de diámetro grande: puede deslizar bajo carga.\n- Dejarlo como único punto de un anclaje crítico y sin nudo de remate.\n- Equivocar el sentido de la segunda vuelta: el nudo no muerde y se suelta al cargarlo.\n- Apretarlo tan poco que las vueltas queden separadas.",
			),
			array(
				'titulo'      => 'Prusik',
				'alias'       => 'prusik',
				'texto'       => 'Bloqueo por fricción para ascenso controlado o seguro de emergencia. Desliza mientras se empuja y muerde en cuanto se carga: por eso funciona como autoseguro en un rápel.',
				'rv_grupo'    => 'nudo',
				'rv_etiqueta' => 'Autoseguro',
				'rv_material' => "- Cordino cerrado en anillo, de 6 a 8 mm.\n- Cuerda principal de diámetro claramente mayor.",
				'rv_pasos'    => "- Elige un cordino de entre el 60 y el 80 % del diámetro de la cuerda principal. Si son parecidos, no morderá.\n- Pasa el anillo de cordino por detrás de la cuerda y mete un extremo del anillo por el otro: primera vuelta.\n- Repite la operación dos veces más, hasta tener tres vueltas completas.\n- Peina las vueltas con los dedos para que queden ordenadas y paralelas, sin que ninguna monte sobre otra.\n- Tira del cordino en la dirección en que va a ir la carga: debe morder y detenerse en seco.\n- Para desplazarlo, empuja el cuerpo del nudo con los dedos. Nunca se agarra el nudo entero con la mano.\n- Si desliza, añade una cuarta vuelta o pasa a un cordino de menor diámetro.",
				'rv_errores'  => "- Cordino del mismo diámetro que la cuerda: no muerde y desliza sin avisar.\n- Vueltas cruzadas o desordenadas: el agarre es irregular y poco fiable.\n- Agarrar el nudo con la mano entera bajo carga: se abre y desliza. Es el error que más accidentes causa en rápel.\n- Colocarlo por encima del descensor: al bloquearse queda fuera de alcance.",
			),
			array(
				'titulo'      => 'Mariposa',
				'alias'       => 'mariposa',
				'texto'       => 'Crea un anclaje intermedio sin usar los extremos de la cuerda. Aguanta tracción en los tres sentidos y se deshace bien después de haber estado cargado.',
				'rv_grupo'    => 'nudo',
				'rv_etiqueta' => 'Punto intermedio',
				'rv_material' => "- Cuerda de trabajo.\n- Mosquetón de seguro para conectar la gaza resultante.",
				'rv_pasos'    => "- Da dos vueltas de cuerda alrededor de la mano abierta, dejando los dos cabos por fuera.\n- Coge la vuelta más cercana a los dedos y crúzala por encima de las otras dos.\n- Pasa esa misma vuelta ahora por debajo de las dos restantes y sácala hacia fuera de la mano.\n- Saca la mano del conjunto y aprieta tirando a la vez de la gaza central y de los dos cabos.\n- Comprueba la simetría: la gaza debe salir centrada y el nudo se lee igual por las dos caras.\n- Conecta el mosquetón a la gaza, nunca a una de las vueltas del cuerpo del nudo.",
				'rv_errores'  => "- Confundirlo con un nudo mal cerrado que se le parece: hay que revisar siempre la silueta final.\n- Cargar la gaza de forma continuada en dirección lateral extrema.\n- Apretarlo sin igualar los cabos: queda torcido y pierde parte de su resistencia.",
			),
			array(
				'titulo'      => 'Rapel y descenso controlado',
				'alias'       => 'rapel',
				'nuevo'       => true,
				'orden'       => 110,
				'texto'       => 'Progresión vertical descendente con freno de fricción. Es la maniobra más repetida y, precisamente por eso, donde más accidentes se producen por confianza.',
				'rv_grupo'    => 'sistema',
				'rv_etiqueta' => 'Descenso',
				'rv_esquema'  => 'rapel',
				'rv_material' => "- Cuerda semiestática del largo suficiente, con nudo de tope en el extremo.\n- Descensor autofrenante compatible con el diámetro de la cuerda.\n- Dos mosquetones de seguro.\n- Cordino para el autoseguro, o dispositivo específico.\n- Casco, arnés y guantes.",
				'rv_pasos'    => "- Verifica el anclaje: dos puntos independientes, ecualizados, con el ángulo entre ramas por debajo de 60°.\n- Haz un nudo de tope en el extremo de la cuerda, para no salirte por abajo si el tramo es más largo de lo previsto.\n- Monta el descensor siguiendo el pictograma grabado en su propia placa lateral: es la comprobación que evita montarlo del revés.\n- Conéctalo al punto ventral del arnés con un mosquetón de seguro, cerrado y con la rosca hecha del todo.\n- Instala el autoseguro por debajo del descensor, nunca por encima.\n- Prueba de carga: carga el sistema progresivamente estando todavía en zona segura, antes de comprometerte al vacío.\n- Desciende con la mano de freno siempre en la cuerda, mirando hacia el punto de llegada y con el cuerpo perpendicular a la pared.\n- Avisa al llegar, y desconéctate solo cuando estés asegurado en el nuevo punto.",
				'rv_errores'  => "- Colocar el autoseguro por encima del descensor: si bloquea, queda fuera de alcance y no se puede liberar.\n- Descender sin nudo de tope en el extremo de la cuerda.\n- Soltar la mano de freno para hablar por radio o para recolocar material.\n- No hacer la prueba de carga en zona segura.",
			),
			array(
				'titulo'      => 'Sistema de polipasto 3:1',
				'alias'       => 'polipasto',
				'nuevo'       => true,
				'orden'       => 120,
				'texto'       => 'Ventaja mecánica para izar a un paciente cuando la extracción hacia arriba es la única salida. La relación 3:1 es teórica: el rozamiento real la deja en torno a 2,5:1.',
				'rv_grupo'    => 'sistema',
				'rv_etiqueta' => 'Izado',
				'rv_esquema'  => 'polipasto',
				'rv_material' => "- Cuerda semiestática.\n- Dos poleas de diámetro adecuado a la cuerda.\n- Dos bloqueadores, o dos prusiks si no hay bloqueadores.\n- Mosquetones de seguro.\n- Anclaje ecualizado y verificado.",
				'rv_pasos'    => "- Monta el anclaje principal y comprueba que resiste con margen la carga prevista: paciente, camilla y acompañante.\n- Fija el bloqueador de retención en la cuerda, junto al anclaje. Es el que sostiene la carga cada vez que el equipo para a recolocarse.\n- Coloca la polea de retorno en el anclaje y pasa la cuerda por ella.\n- Sitúa el bloqueador de tracción sobre la cuerda, del lado de la carga.\n- Conecta la segunda polea al bloqueador de tracción y pasa por ella el cabo de tiro.\n- Tira del cabo de tiro: por cada tres metros recorridos, la carga sube aproximadamente uno.\n- Cuando el bloqueador de tracción llegue arriba, deja que el de retención sujete la carga y devuelve el de tracción hacia abajo para repetir el ciclo.\n- Mantén siempre una persona vigilando el bloqueador de retención y otra la comunicación con el paciente.",
				'rv_errores'  => "- Montar el sistema sin bloqueador de retención: si el equipo suelta, la carga se va abajo.\n- Usar poleas de diámetro pequeño para la cuerda empleada: el rozamiento se dispara y se pierde la ventaja.\n- Tirar entre varios sin coordinar: se pierde el control de la velocidad y se dan tirones al paciente.\n- Dejar que el bloqueador de tracción llegue a tocar la polea de retorno.",
			),
			array(
				'titulo'      => 'Anclaje ecualizado (SRENE)',
				'alias'       => 'anclaje',
				'nuevo'       => true,
				'orden'       => 130,
				'texto'       => 'Reparto de la carga entre varios puntos para que ninguno trabaje solo. Todo el sistema de cuerdas cuelga de aquí: es la parte que no admite atajos.',
				'rv_grupo'    => 'sistema',
				'rv_etiqueta' => 'Anclaje',
				'rv_esquema'  => 'anclaje',
				'rv_material' => "- Dos o más puntos sólidos e independientes entre sí.\n- Cinta cosida o cordino para unirlos.\n- Mosquetones de seguro, uno por punto y uno para el punto maestro.\n- Protectores de canto si la cinta roza roca viva.",
				'rv_pasos'    => "- Elige puntos realmente independientes: si falla uno, el otro no debe verse comprometido.\n- Conecta cada punto con su mosquetón de seguro.\n- Une los puntos con la cinta y localiza dónde se juntan las ramas: ese es el punto maestro.\n- Ecualiza ajustando las ramas para que la carga se reparta por igual en la dirección de tiro prevista.\n- Mantén el ángulo entre ramas por debajo de 60°. A 120° cada punto ya soporta el 100 % de la carga total.\n- Añade nudos limitadores en la cinta: si un punto cede, el punto maestro cae unos centímetros y no medio metro.\n- Repasa la regla SRENE: Sólido, Redundante, Ecualizado y sin Extensión.\n- Protege cualquier tramo de cinta que roce un canto vivo.",
				'rv_errores'  => "- Abrir el ángulo por encima de 90° para ganar altura en el punto maestro.\n- Usar dos puntos del mismo elemento estructural: si cede el elemento, ceden los dos a la vez.\n- Ecualizar para una dirección de tiro y luego cargar en otra distinta.\n- Dejar cinta rozando un canto sin protección.",
			),
			array(
				'titulo'      => 'Empaquetamiento del paciente',
				'alias'       => 'empaquetamiento',
				'nuevo'       => true,
				'orden'       => 140,
				'texto'       => 'Fijación del paciente a la camilla para que la extracción no agrave la lesión. Un empaquetamiento flojo convierte cada movimiento del izado en un nuevo mecanismo lesional.',
				'rv_grupo'    => 'sistema',
				'rv_etiqueta' => 'Camilla',
				'rv_esquema'  => 'camilla',
				'rv_material' => "- Camilla tipo Stokes o SKED.\n- Correas de sujeción.\n- Bridas de suspensión certificadas.\n- Collarín e inmovilizadores laterales de cabeza.\n- Material de acolchado para rellenar huecos.\n- Manta térmica.",
				'rv_pasos'    => "- Inmoviliza la columna cervical antes de mover al paciente.\n- Traslada al paciente a la camilla en bloque, con la maniobra cantada por quien controla la cabeza.\n- Acolcha los huecos: cintura, rodillas y hombros, para que el cuerpo no se desplace dentro de la cesta.\n- Fija las correas en este orden: tórax, pelvis, piernas y por último los pies.\n- Comprueba que ninguna correa comprime el abdomen ni el cuello, y que ninguna bloquea el acceso a la vía aérea.\n- Coloca el apoyo de pies si el izado va a hacerse en vertical.\n- Monta las bridas de suspensión en los puntos certificados y ecualízalas.\n- Prueba de carga a 20 cm del suelo: comprueba el equilibrio y que nada se mueve antes de comprometer el izado.\n- Cubre al paciente con manta térmica: la hipotermia aparece rápido en una extracción larga.",
				'rv_errores'  => "- Pasar correas sobre el abdomen: dificultan la ventilación en una suspensión larga.\n- Izar sin haber probado el equilibrio a poca altura.\n- Dejar los brazos del paciente fuera de las correas.\n- Olvidar el cabo guía: la camilla gira y golpea la pared.",
			),
		);
	}

	private static function protocolos() {
		return array(
			array(
				'titulo'   => 'PHTLS',
				'alias'    => 'phtls',
				'texto'    => 'Evaluación XABCDE del paciente politraumatizado: la X de hemorragia exanguinante se atiende antes que la vía aérea. Se ejecuta mientras avanza la extracción técnica, no después de ella.',
				'rv_sigla' => 'XABCDE',
				'rv_pasos' => "- X — Exanguinante: control inmediato de hemorragias masivas.\n- A — Vía aérea con control cervical.\n- B — Ventilación y oxigenación.\n- C — Circulación y control de hemorragias no visibles.\n- D — Discapacidad neurológica (AVPU / GCS).\n- E — Exposición y control ambiental, previniendo hipotermia.",
			),
			array(
				'titulo'   => 'TECC',
				'alias'    => 'tecc',
				'texto'    => 'Control de hemorragias y vía aérea cuando el entorno restringe el acceso normal del equipo médico.',
				'rv_sigla' => 'MARCH PAWS',
				'rv_pasos' => "- M — Hemorragia masiva.\n- A — Vía aérea.\n- R — Respiración.\n- C — Circulación.\n- H — Hipotermia y lesión craneal.\n- P — Dolor.\n- A — Antibióticos.\n- W — Heridas.\n- S — Inmovilización.",
			),
			array(
				'titulo'   => 'TCCC',
				'alias'    => 'tccc',
				'texto'    => 'Equilibra la atención clínica con la seguridad operativa cuando la evacuación inmediata no es posible.',
				'rv_sigla' => 'MARCH PAWS',
				'rv_pasos' => "- Tratar primero lo que mata primero.\n- Evacuar lo antes posible.\n- Comunicar todo al equipo.\n- Mantener al paciente abrigado.\n- Reevaluar constantemente.",
			),
			array(
				'titulo'   => 'Síndrome compartimental y aplastamiento',
				'alias'    => 'compartimental',
				'texto'    => 'Dos complicaciones propias del rescate vertical: la presión dentro de un compartimento muscular cerrado, y el daño por compresión prolongada de tejidos.',
				'rv_sigla' => 'Las 6 P',
				'rv_pasos' => "- Dolor desproporcionado que aumenta al estirar el músculo.\n- Presión: tensión profunda en el compartimento.\n- Parestesias: hormigueo o adormecimiento.\n- Palidez de la piel.\n- Parálisis o debilidad de la extremidad.\n- Pulsos distales débiles o ausentes (signo tardío).",
			),
			array(
				'titulo'   => 'Integración con el equipo técnico',
				'alias'    => 'integracion',
				'texto'    => 'El equipo técnico y el médico actúan en simultáneo, no en secuencia: reduce el tiempo total de exposición al riesgo.',
				'rv_sigla' => '',
				'rv_pasos' => "- Asegurar al paciente antes de cualquier intervención clínica.\n- Reevaluar tras cada cambio de posición y cada fase de izado o descenso.\n- Comunicación constante entre el equipo de cuerdas y el sanitario.",
			),
		);
	}
}
