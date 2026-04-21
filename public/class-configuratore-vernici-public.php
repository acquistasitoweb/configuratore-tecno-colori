<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://acquistasitoweb.com
 * @since      1.0.0
 *
 * @package    Configuratore_Vernici
 * @subpackage Configuratore_Vernici/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Configuratore_Vernici
 * @subpackage Configuratore_Vernici/public
 * @author     Acquistasitoweb <info@acquistasitoweb.com>
 */
class Configuratore_Vernici_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Configuratore_Vernici_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Configuratore_Vernici_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/configuratore-vernici-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Configuratore_Vernici_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Configuratore_Vernici_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/configuratore-vernici-public.js', array(), $this->version, true );

		wp_localize_script(
			$this->plugin_name,
			'configuratoreVernici',
			array(
				'ajaxUrl'               => admin_url( 'admin-ajax.php' ),
				'calculationNonce'      => wp_create_nonce( 'configuratore_vernici_calcolo' ),
				'quoteNonce'            => wp_create_nonce( 'configuratore_vernici_preventivo' ),
				'nonce'                 => wp_create_nonce( 'configuratore_vernici_preventivo' ),
				'safetyMargin'          => $this->get_safety_margin_multiplier(),
				'freeDeliveryThreshold' => $this->get_free_delivery_threshold(),
				'texts'                 => array(
					'selectCategory' => __( 'Seleziona il settore.', 'configuratore-vernici' ),
					'selectSurface'  => __( 'Seleziona il materiale o supporto da verniciare.', 'configuratore-vernici' ),
					'selectProduct'  => __( 'Seleziona un prodotto valido.', 'configuratore-vernici' ),
					'enterMq'        => __( 'Inserisci i metri quadri da verniciare.', 'configuratore-vernici' ),
					'enterEmail'     => __( 'Inserisci un indirizzo email valido.', 'configuratore-vernici' ),
					'genericError'   => __( 'Si è verificato un errore. Riprova più tardi.', 'configuratore-vernici' ),
					'loading'        => __( 'Calcolo in corso...', 'configuratore-vernici' ),
					'quoteLoading'   => __( 'Invio in corso...', 'configuratore-vernici' ),
					'featured'       => __( 'Consigliato', 'configuratore-vernici' ),
				),
			)
		);

	}

	/**
	 * Register the public shortcode.
	 *
	 * @since    1.0.0
	 */
	public function register_shortcode() {
		add_shortcode( 'configuratore_vernici', array( $this, 'render_configurator_shortcode' ) );
	}

	/**
	 * Render the paint configurator form.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	public function render_configurator_shortcode() {
		$categories = $this->get_configurator_categories();
		$surfaces = $this->get_configurator_surfaces();
		$urgent_phone = get_option( 'configuratore_vernici_urgent_phone', '' );
		$urgent_phone_href = $this->get_phone_href( $urgent_phone );

		ob_start();
		?>
		<div class="configuratore-vernici" data-configuratore-vernici>
			<form class="configuratore-vernici__form" data-configuratore-calcolo>
				<div class="configuratore-vernici__field">
					<label for="configuratore-vernici-categoria"><?php esc_html_e( 'Settore', 'configuratore-vernici' ); ?></label>
					<select id="configuratore-vernici-categoria" name="categoria" required data-configuratore-categoria>
						<option value=""><?php esc_html_e( 'Scegli il settore', 'configuratore-vernici' ); ?></option>
						<?php foreach ( $categories as $category ) : ?>
							<option value="<?php echo esc_attr( $category['id'] ); ?>"><?php echo esc_html( $category['name'] ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="configuratore-vernici__field">
					<label for="configuratore-vernici-superficie"><?php esc_html_e( 'Materiale o supporto da verniciare', 'configuratore-vernici' ); ?></label>
					<select id="configuratore-vernici-superficie" name="superficie" required data-configuratore-superficie>
						<option value=""><?php esc_html_e( 'Scegli il materiale', 'configuratore-vernici' ); ?></option>
						<?php foreach ( $surfaces as $surface ) : ?>
							<option value="<?php echo esc_attr( $surface['id'] ); ?>"><?php echo esc_html( $surface['name'] ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="configuratore-vernici__field">
					<label for="configuratore-vernici-mq"><?php esc_html_e( 'Metri quadri da verniciare', 'configuratore-vernici' ); ?></label>
					<input id="configuratore-vernici-mq" type="number" name="mq" min="0.1" step="0.1" placeholder="<?php esc_attr_e( 'Es. 120', 'configuratore-vernici' ); ?>" required data-configuratore-mq>
				</div>

				<button type="submit" class="button primary"><?php esc_html_e( 'Calcola Fabbisogno', 'configuratore-vernici' ); ?></button>
			</form>

			<div class="configuratore-vernici__result" hidden data-configuratore-risultato>
				<h3><?php esc_html_e( 'Prodotti consigliati', 'configuratore-vernici' ); ?></h3>
				<div class="configuratore-vernici__products" data-configuratore-prodotti></div>
				<p class="configuratore-vernici__delivery-message" data-configuratore-consegna></p>
				<?php if ( '' !== $urgent_phone_href ) : ?>
					<p class="configuratore-vernici__urgent">
						<a class="button secondary" href="<?php echo esc_url( $urgent_phone_href ); ?>">
							<?php esc_html_e( 'Contatta il negozio', 'configuratore-vernici' ); ?>
						</a>
					</p>
				<?php endif; ?>
			</div>

			<form class="configuratore-vernici__email-form" hidden data-configuratore-preventivo>
				<div class="configuratore-vernici__field">
					<label for="configuratore-vernici-email"><?php esc_html_e( 'Email per ricevere il preventivo', 'configuratore-vernici' ); ?></label>
					<input id="configuratore-vernici-email" type="email" name="email" placeholder="<?php esc_attr_e( 'nome@azienda.it', 'configuratore-vernici' ); ?>" required data-configuratore-email>
				</div>

				<div class="configuratore-vernici__field">
					<label for="configuratore-vernici-note"><?php esc_html_e( 'Note/Aggiunte opzionali', 'configuratore-vernici' ); ?></label>
					<textarea id="configuratore-vernici-note" name="note" rows="3" placeholder="<?php esc_attr_e( 'Es. colore, urgenza, consegna in cantiere', 'configuratore-vernici' ); ?>" data-configuratore-note></textarea>
				</div>

				<button type="submit" class="button primary"><?php esc_html_e( 'Richiedi preventivo', 'configuratore-vernici' ); ?></button>
			</form>

			<div class="configuratore-vernici__message" aria-live="polite" data-configuratore-messaggio></div>
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Calculate recommended products from category, surface and square meters.
	 *
	 * @since    1.0.0
	 */
	public function process_product_calculation() {
		check_ajax_referer( 'configuratore_vernici_calcolo', 'nonce' );

		$category_id = isset( $_POST['categoria'] ) ? absint( $_POST['categoria'] ) : 0;
		$surface_id  = isset( $_POST['superficie'] ) ? absint( $_POST['superficie'] ) : 0;
		$mq          = $this->get_posted_float( 'mq' );

		if ( $category_id <= 0 || ! term_exists( $category_id, 'product_cat' ) ) {
			wp_send_json_error( array( 'message' => __( 'Seleziona un settore valido.', 'configuratore-vernici' ) ), 400 );
		}

		if ( $surface_id <= 0 || ! term_exists( $surface_id, 'cv_superficie' ) ) {
			wp_send_json_error( array( 'message' => __( 'Seleziona una superficie valida.', 'configuratore-vernici' ) ), 400 );
		}

		if ( $mq <= 0 ) {
			wp_send_json_error( array( 'message' => __( 'Inserisci i metri quadri da verniciare.', 'configuratore-vernici' ) ), 400 );
		}

		$products = $this->get_recommended_products( $category_id, $surface_id, $mq );

		if ( empty( $products ) ) {
			wp_send_json_error( array( 'message' => __( 'Non abbiamo trovato prodotti compatibili con questi dati. Contatta il negozio per una verifica tecnica.', 'configuratore-vernici' ) ), 404 );
		}

		wp_send_json_success(
			array(
				'products' => $products,
			)
		);
	}

	/**
	 * Process the quote request sent from the frontend.
	 *
	 * @since    1.0.0
	 */
	public function process_quote_request() {
		check_ajax_referer( 'configuratore_vernici_preventivo', 'nonce' );

		$email      = isset( $_POST['email'] ) && is_scalar( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
		$category_id = isset( $_POST['category_id'] ) ? absint( $_POST['category_id'] ) : 0;
		$surface_id  = isset( $_POST['surface_id'] ) ? absint( $_POST['surface_id'] ) : 0;
		$category    = $this->get_term_name_by_id( $category_id, 'product_cat' );
		$surface     = $this->get_term_name_by_id( $surface_id, 'cv_superficie' );
		$notes       = isset( $_POST['notes'] ) && is_scalar( $_POST['notes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['notes'] ) ) : '';
		$mq         = $this->get_posted_float( 'mq' );
		$litri      = $this->get_posted_float( 'litri' );

		if ( ! is_email( $email ) ) {
			wp_send_json_error( array( 'message' => __( 'Indirizzo email non valido.', 'configuratore-vernici' ) ), 400 );
		}

		if ( $product_id <= 0 || $mq <= 0 || $litri <= 0 ) {
			wp_send_json_error( array( 'message' => __( 'Dati del preventivo non validi.', 'configuratore-vernici' ) ), 400 );
		}

		$post_product = get_post( $product_id );

		if ( ! $post_product || 'product' !== $post_product->post_type || 'publish' !== $post_product->post_status ) {
			wp_send_json_error( array( 'message' => __( 'Prodotto non valido.', 'configuratore-vernici' ) ), 400 );
		}

		if ( ! Configuratore_Vernici_Content_Types::product_is_candidate( $product_id ) ) {
			wp_send_json_error( array( 'message' => __( 'Questo prodotto non è disponibile nel configuratore.', 'configuratore-vernici' ) ), 400 );
		}

		$yield = (float) str_replace( ',', '.', get_post_meta( $product_id, 'resa_litro', true ) );

		if ( $yield <= 0 ) {
			wp_send_json_error( array( 'message' => __( 'Resa del prodotto non configurata.', 'configuratore-vernici' ) ), 400 );
		}

		$calculated_liters = round( ( $mq / $yield ) * $this->get_safety_margin_multiplier(), 2 );

		if ( abs( $calculated_liters - $litri ) > 0.05 ) {
			wp_send_json_error( array( 'message' => __( 'Il calcolo inviato non corrisponde ai dati del prodotto.', 'configuratore-vernici' ) ), 400 );
		}

		$product = get_the_title( $product_id );

		$request_id = $this->store_quote_request(
			array(
				'email'      => $email,
				'product_id' => $product_id,
				'product'    => $product,
				'category'   => $category,
				'surface'    => $surface,
				'notes'      => $notes,
				'mq'         => $mq,
				'liters'     => $calculated_liters,
				'yield'      => $yield,
				'page'       => wp_get_referer() ? esc_url_raw( wp_get_referer() ) : home_url(),
			)
		);

		if ( $request_id <= 0 ) {
			wp_send_json_error( array( 'message' => __( 'Non è stato possibile registrare la richiesta. Riprova più tardi.', 'configuratore-vernici' ) ), 500 );
		}

		$recipients = $this->get_notification_recipients();
		$subject   = get_option( 'configuratore_vernici_email_subject', __( 'Nuova richiesta preventivo vernici', 'configuratore-vernici' ) );

		$message = implode(
			"\n",
			array(
				__( 'Nuova richiesta preventivo dal configuratore vernici.', 'configuratore-vernici' ),
				'',
				sprintf( '%s: %s', __( 'Email cliente', 'configuratore-vernici' ), $email ),
				sprintf( '%s: %s', __( 'Prodotto', 'configuratore-vernici' ), $product ),
				sprintf( '%s: %s', __( 'Settore', 'configuratore-vernici' ), '' !== $category ? $category : __( 'Non indicato', 'configuratore-vernici' ) ),
				sprintf( '%s: %s', __( 'Materiale o supporto', 'configuratore-vernici' ), '' !== $surface ? $surface : __( 'Non indicato', 'configuratore-vernici' ) ),
				sprintf( '%s: %.2f', __( 'Metri quadri', 'configuratore-vernici' ), $mq ),
				sprintf( '%s: %.2f L', __( 'Litri calcolati', 'configuratore-vernici' ), $calculated_liters ),
				sprintf( '%s: %.2f m2/L', __( 'Resa prodotto', 'configuratore-vernici' ), $yield ),
				sprintf( '%s: %s', __( 'Note cliente', 'configuratore-vernici' ), '' !== $notes ? $notes : __( 'Nessuna nota', 'configuratore-vernici' ) ),
				'',
				sprintf( '%s: #%d', __( 'ID richiesta CRM', 'configuratore-vernici' ), $request_id ),
				sprintf( '%s: %s', __( 'Apri richieste', 'configuratore-vernici' ), admin_url( 'admin.php?page=configuratore-vernici-richieste' ) ),
				sprintf( '%s: %s', __( 'Pagina di provenienza', 'configuratore-vernici' ), wp_get_referer() ? esc_url_raw( wp_get_referer() ) : home_url() ),
			)
		);

		$headers = array(
			'Content-Type: text/plain; charset=UTF-8',
			'Reply-To: ' . $email,
		);

		$sent = wp_mail( $recipients, sanitize_text_field( $subject ), $message, $headers );

		if ( ! $sent ) {
			wp_send_json_error( array( 'message' => __( 'Non è stato possibile inviare la richiesta. Riprova più tardi.', 'configuratore-vernici' ) ), 500 );
		}

		wp_send_json_success( array( 'message' => __( 'Richiesta inviata correttamente. Ti contatteremo al più presto.', 'configuratore-vernici' ) ) );
	}

	/**
	 * Retrieve recommended WooCommerce products for the chosen use case.
	 *
	 * @since    1.0.0
	 * @param    int   $category_id Product category term ID.
	 * @param    int   $surface_id Surface term ID.
	 * @param    float $mq Square meters.
	 * @return   array
	 */
	private function get_recommended_products( $category_id, $surface_id, $mq ) {
		$query = new WP_Query(
			array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => 24,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'no_found_rows'  => true,
				'meta_query'     => array(
					array(
						'key'     => 'resa_litro',
						'value'   => 0,
						'compare' => '>',
						'type'    => 'NUMERIC',
					),
				),
				'tax_query'      => array(
					'relation' => 'AND',
					array(
						'taxonomy'         => 'product_cat',
						'field'            => 'term_id',
						'terms'            => array( $category_id ),
						'include_children' => true,
					),
					array(
						'taxonomy' => 'cv_superficie',
						'field'    => 'term_id',
						'terms'    => array( $surface_id ),
					),
				),
			)
		);

		$products = array();

		if ( $query->have_posts() ) {
			foreach ( $query->posts as $product ) {
				$yield = (float) str_replace( ',', '.', get_post_meta( $product->ID, 'resa_litro', true ) );

				if ( $yield <= 0 || ! Configuratore_Vernici_Content_Types::product_is_candidate( $product->ID ) ) {
					continue;
				}

				$liters = round( ( $mq / $yield ) * $this->get_safety_margin_multiplier(), 2 );
				$is_featured = $this->product_is_featured( $product->ID );

				$products[] = array(
					'id'               => $product->ID,
					'name'             => get_the_title( $product ),
					'resa_litro'       => $yield,
					'litri'            => $liters,
					'litri_formattati' => $this->format_liters( $liters ),
					'upsell_message'   => $this->get_delivery_message( $liters ),
					'is_featured'      => $is_featured,
				);
			}
		}

		wp_reset_postdata();

		usort(
			$products,
			function ( $a, $b ) {
				if ( $a['is_featured'] !== $b['is_featured'] ) {
					return $a['is_featured'] ? -1 : 1;
				}

				return strcasecmp( $a['name'], $b['name'] );
			}
		);

		return array_slice( $products, 0, 3 );
	}

	/**
	 * Retrieve configured WooCommerce categories for the selector.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	private function get_configurator_categories() {
		if ( ! taxonomy_exists( 'product_cat' ) ) {
			return array();
		}

		$items = array();

		foreach ( Configuratore_Vernici_Content_Types::get_paint_parent_categories() as $category ) {
			$term = get_term_by( 'slug', $category['slug'], 'product_cat' );

			if ( ! $term || is_wp_error( $term ) ) {
				continue;
			}

			$items[] = array(
				'id'   => (int) $term->term_id,
				'name' => $term->name,
			);
		}

		return $items;
	}

	/**
	 * Retrieve configured surfaces for the selector.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	private function get_configurator_surfaces() {
		if ( ! taxonomy_exists( 'cv_superficie' ) ) {
			return array();
		}

		$terms = get_terms(
			array(
				'taxonomy'   => 'cv_superficie',
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return array();
		}

		$items = array();

		foreach ( $terms as $term ) {
			$items[] = array(
				'id'   => (int) $term->term_id,
				'name' => $term->name,
			);
		}

		return $items;
	}

	/**
	 * Store a quote request for CRM management.
	 *
	 * @since    1.0.0
	 * @param    array $data Request data.
	 * @return   int
	 */
	private function store_quote_request( $data ) {
		$title = sprintf(
			'%s - %s - %s',
			__( 'Richiesta preventivo', 'configuratore-vernici' ),
			$data['email'],
			current_time( 'd/m/Y H:i' )
		);

		$request_id = wp_insert_post(
			array(
				'post_type'   => 'cv_preventivo',
				'post_status' => 'publish',
				'post_title'  => sanitize_text_field( $title ),
				'meta_input'  => array(
					Configuratore_Vernici_Content_Types::QUOTE_META_STATUS => 'new',
					'_cv_customer_email' => sanitize_email( $data['email'] ),
					'_cv_product_id'     => isset( $data['product_id'] ) ? absint( $data['product_id'] ) : 0,
					'_cv_product'        => sanitize_text_field( $data['product'] ),
					'_cv_category'       => sanitize_text_field( isset( $data['category'] ) ? $data['category'] : '' ),
					'_cv_surface'        => sanitize_text_field( isset( $data['surface'] ) ? $data['surface'] : '' ),
					'_cv_material'       => sanitize_text_field( isset( $data['surface'] ) ? $data['surface'] : '' ),
					'_cv_tool'           => '',
					'_cv_notes'          => sanitize_textarea_field( isset( $data['notes'] ) ? $data['notes'] : '' ),
					'_cv_mq'             => (float) $data['mq'],
					'_cv_liters'         => (float) $data['liters'],
					'_cv_yield'          => (float) $data['yield'],
					'_cv_page'           => esc_url_raw( $data['page'] ),
				),
			),
			true
		);

		if ( is_wp_error( $request_id ) ) {
			return 0;
		}

		return (int) $request_id;
	}

	/**
	 * Get notification recipients.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	private function get_notification_recipients() {
		$raw_recipients = get_option( 'configuratore_vernici_recipient_emails', '' );

		if ( '' === $raw_recipients ) {
			$raw_recipients = get_option( 'configuratore_vernici_recipient_email', get_option( 'admin_email' ) );
		}

		$emails = preg_split( '/[\s,;]+/', (string) $raw_recipients );
		$recipients = array();

		foreach ( $emails as $email ) {
			$email = sanitize_email( $email );

			if ( is_email( $email ) ) {
				$recipients[] = $email;
			}
		}

		if ( empty( $recipients ) ) {
			$recipients[] = get_option( 'admin_email' );
		}

		return array_values( array_unique( $recipients ) );
	}

	/**
	 * Retrieve the configured safety margin as a multiplier.
	 *
	 * @since    1.0.0
	 * @return   float
	 */
	private function get_safety_margin_multiplier() {
		$margin = (float) get_option( 'configuratore_vernici_safety_margin', 10 );

		if ( $margin < 0 ) {
			$margin = 0;
		}

		return 1 + ( $margin / 100 );
	}

	/**
	 * Retrieve the free delivery threshold.
	 *
	 * @since    1.0.0
	 * @return   float
	 */
	private function get_free_delivery_threshold() {
		$threshold = (float) get_option( 'configuratore_vernici_free_delivery_threshold', 30 );

		return $threshold > 0 ? $threshold : 30;
	}

	/**
	 * Build the delivery upsell message for a liters estimate.
	 *
	 * @since    1.0.0
	 * @param    float $liters Calculated liters.
	 * @return   string
	 */
	private function get_delivery_message( $liters ) {
		$threshold = $this->get_free_delivery_threshold();

		if ( $liters >= $threshold ) {
			return __( 'Questo quantitativo ti garantisce la consegna gratuita in loco.', 'configuratore-vernici' );
		}

		$missing_liters = max( 0, round( $threshold - $liters, 2 ) );

		return sprintf(
			/* translators: %s: missing liters. */
			__( 'Aggiungendo circa %s L puoi raggiungere la soglia per la consegna gratuita.', 'configuratore-vernici' ),
			$this->format_liters( $missing_liters )
		);
	}

	/**
	 * Check if a WooCommerce product is marked as featured.
	 *
	 * @since    1.0.0
	 * @param    int $product_id Product ID.
	 * @return   bool
	 */
	private function product_is_featured( $product_id ) {
		if ( function_exists( 'wc_get_product' ) ) {
			$product = wc_get_product( $product_id );

			if ( $product && method_exists( $product, 'is_featured' ) ) {
				return (bool) $product->is_featured();
			}
		}

		return has_term( 'featured', 'product_visibility', $product_id ) || 'yes' === get_post_meta( $product_id, '_featured', true );
	}

	/**
	 * Format liters for the Italian frontend.
	 *
	 * @since    1.0.0
	 * @param    float $liters Liters.
	 * @return   string
	 */
	private function format_liters( $liters ) {
		return function_exists( 'number_format_i18n' ) ? number_format_i18n( $liters, 2 ) : number_format( $liters, 2, ',', '.' );
	}

	/**
	 * Get a term name from a term ID.
	 *
	 * @since    1.0.0
	 * @param    int    $term_id Term ID.
	 * @param    string $taxonomy Taxonomy name.
	 * @return   string
	 */
	private function get_term_name_by_id( $term_id, $taxonomy ) {
		if ( $term_id <= 0 || ! taxonomy_exists( $taxonomy ) ) {
			return '';
		}

		$term = get_term( $term_id, $taxonomy );

		if ( ! $term || is_wp_error( $term ) ) {
			return '';
		}

		return $term->name;
	}

	/**
	 * Build a tel: URL from a configured phone number.
	 *
	 * @since    1.0.0
	 * @param    string $phone Phone number.
	 * @return   string
	 */
	private function get_phone_href( $phone ) {
		$phone = preg_replace( '/[^0-9+]/', '', (string) $phone );

		if ( '' === $phone ) {
			return '';
		}

		return 'tel:' . $phone;
	}

	/**
	 * Retrieve a float value from POST data.
	 *
	 * @since    1.0.0
	 * @param    string $key POST key.
	 * @return   float
	 */
	private function get_posted_float( $key ) {
		if ( ! isset( $_POST[ $key ] ) || ! is_scalar( $_POST[ $key ] ) ) {
			return 0;
		}

		return (float) str_replace( ',', '.', wp_unslash( $_POST[ $key ] ) );
	}

}
