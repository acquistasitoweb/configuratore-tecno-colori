<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://acquistasitoweb.com
 * @since      1.0.0
 *
 * @package    Configuratore_Vernici
 * @subpackage Configuratore_Vernici/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Configuratore_Vernici
 * @subpackage Configuratore_Vernici/admin
 * @author     Acquistasitoweb <info@acquistasitoweb.com>
 */
class Configuratore_Vernici_Admin {

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
	 * The admin page hook suffix.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $page_hooks    The plugin admin page hooks.
	 */
	private $page_hooks = array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles( $hook_suffix = '' ) {

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

		if ( ! in_array( $hook_suffix, $this->page_hooks, true ) ) {
			return;
		}

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/configuratore-vernici-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook_suffix = '' ) {

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

		if ( ! in_array( $hook_suffix, $this->page_hooks, true ) ) {
			return;
		}

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/configuratore-vernici-admin.js', array(), $this->version, true );

		wp_localize_script(
			$this->plugin_name,
			'configuratoreVerniciAdmin',
			array(
				'quoteChart' => $this->get_quote_chart_data( 14 ),
			)
		);

	}

	/**
	 * Add the plugin settings page to the WordPress admin menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
		$this->page_hooks[] = add_menu_page(
			__( 'Configuratore Vernici', 'configuratore-vernici' ),
			__( 'Configuratore Vernici', 'configuratore-vernici' ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_admin_page' ),
			'dashicons-admin-customizer',
			56
		);

		$this->page_hooks[] = add_submenu_page(
			$this->plugin_name,
			__( 'Vernici candidabili', 'configuratore-vernici' ),
			__( 'Vernici', 'configuratore-vernici' ),
			'manage_options',
			$this->plugin_name . '-vernici',
			array( $this, 'display_paints_page' )
		);

		$this->page_hooks[] = add_submenu_page(
			$this->plugin_name,
			__( 'Richieste preventivo', 'configuratore-vernici' ),
			__( 'Richieste', 'configuratore-vernici' ),
			'manage_options',
			$this->plugin_name . '-richieste',
			array( $this, 'display_requests_page' )
		);

		add_submenu_page(
			$this->plugin_name,
			__( 'Superfici', 'configuratore-vernici' ),
			__( 'Superfici', 'configuratore-vernici' ),
			'manage_categories',
			'edit-tags.php?taxonomy=cv_superficie&post_type=product'
		);

		add_submenu_page(
			$this->plugin_name,
			__( 'Strumenti di applicazione', 'configuratore-vernici' ),
			__( 'Strumenti', 'configuratore-vernici' ),
			'edit_posts',
			'edit.php?post_type=cv_strumento'
		);
	}

	/**
	 * Register the plugin settings.
	 *
	 * @since    1.0.0
	 */
	public function register_settings() {
		register_setting(
			'configuratore_vernici_settings',
			'configuratore_vernici_recipient_emails',
			array(
				'type'              => 'string',
				'sanitize_callback' => array( $this, 'sanitize_recipient_emails' ),
				'default'           => get_option( 'admin_email' ),
			)
		);

		register_setting(
			'configuratore_vernici_settings',
			'configuratore_vernici_email_subject',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => __( 'Nuova richiesta preventivo vernici', 'configuratore-vernici' ),
			)
		);

		register_setting(
			'configuratore_vernici_settings',
			'configuratore_vernici_safety_margin',
			array(
				'type'              => 'number',
				'sanitize_callback' => array( $this, 'sanitize_safety_margin' ),
				'default'           => 10,
			)
		);

		register_setting(
			'configuratore_vernici_settings',
			'configuratore_vernici_free_delivery_threshold',
			array(
				'type'              => 'number',
				'sanitize_callback' => array( $this, 'sanitize_positive_number' ),
				'default'           => 30,
			)
		);

		register_setting(
			'configuratore_vernici_settings',
			'configuratore_vernici_urgent_phone',
			array(
				'type'              => 'string',
				'sanitize_callback' => array( $this, 'sanitize_phone_number' ),
				'default'           => '',
			)
		);
	}

	/**
	 * Render the plugin settings page.
	 *
	 * @since    1.0.0
	 */
	public function display_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Non hai i permessi per accedere a questa pagina.', 'configuratore-vernici' ) );
		}

		$configured_products = $this->count_configured_products();
		$surfaces_count = $this->count_terms( 'cv_superficie' );
		$tools_count = $this->count_published_posts( 'cv_strumento' );
		$quotes_count = $this->count_quote_requests();
		$new_quotes_count = $this->count_quote_requests( 'new' );
		$quote_status_counts = $this->get_quote_status_counts();
		$paint_parent_categories = Configuratore_Vernici_Content_Types::get_paint_parent_categories();
		require plugin_dir_path( __FILE__ ) . 'partials/configuratore-vernici-admin-display.php';
	}

	/**
	 * Render the paint management page.
	 *
	 * @since    1.0.0
	 */
	public function display_paints_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Non hai i permessi per accedere a questa pagina.', 'configuratore-vernici' ) );
		}

		$products = $this->get_paint_products_overview();
		$statuses = Configuratore_Vernici_Content_Types::get_product_statuses();
		require plugin_dir_path( __FILE__ ) . 'partials/configuratore-vernici-admin-paints.php';
	}

	/**
	 * Render the quote requests CRM page.
	 *
	 * @since    1.0.0
	 */
	public function display_requests_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Non hai i permessi per accedere a questa pagina.', 'configuratore-vernici' ) );
		}

		$requests = $this->get_quote_requests();
		$statuses = Configuratore_Vernici_Content_Types::get_quote_statuses();
		require plugin_dir_path( __FILE__ ) . 'partials/configuratore-vernici-admin-requests.php';
	}

	/**
	 * Save a quote request status.
	 *
	 * @since    1.0.0
	 */
	public function save_quote_request_status() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Non hai i permessi per modificare queste richieste.', 'configuratore-vernici' ) );
		}

		check_admin_referer( 'configuratore_vernici_save_request_status', 'configuratore_vernici_request_nonce' );

		$request_id = isset( $_POST['request_id'] ) ? absint( $_POST['request_id'] ) : 0;
		$status = isset( $_POST['request_status'] ) && is_scalar( $_POST['request_status'] ) ? sanitize_key( wp_unslash( $_POST['request_status'] ) ) : 'new';
		$valid_statuses = array_keys( Configuratore_Vernici_Content_Types::get_quote_statuses() );

		if ( $request_id > 0 && in_array( $status, $valid_statuses, true ) && 'cv_preventivo' === get_post_type( $request_id ) ) {
			update_post_meta( $request_id, Configuratore_Vernici_Content_Types::QUOTE_META_STATUS, $status );
		}

		$this->redirect_to_requests_page( array( 'updated' => 'true' ) );
	}

	/**
	 * Send a quote email to a customer.
	 *
	 * @since    1.0.0
	 */
	public function send_quote_to_customer() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Non hai i permessi per inviare preventivi.', 'configuratore-vernici' ) );
		}

		check_admin_referer( 'configuratore_vernici_send_quote', 'configuratore_vernici_quote_nonce' );

		$request_id = isset( $_POST['request_id'] ) ? absint( $_POST['request_id'] ) : 0;
		$subject = isset( $_POST['quote_subject'] ) && is_scalar( $_POST['quote_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['quote_subject'] ) ) : '';
		$message = isset( $_POST['quote_message'] ) && is_scalar( $_POST['quote_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['quote_message'] ) ) : '';

		if ( $request_id <= 0 || 'cv_preventivo' !== get_post_type( $request_id ) ) {
			$this->redirect_to_requests_page( array( 'sent' => 'invalid' ) );
		}

		$email = get_post_meta( $request_id, '_cv_customer_email', true );

		if ( ! is_email( $email ) || '' === $subject || '' === $message ) {
			$this->redirect_to_requests_page( array( 'sent' => 'invalid' ) );
		}

		$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
		$sent = wp_mail( $email, $subject, $message, $headers );

		if ( ! $sent ) {
			$this->redirect_to_requests_page( array( 'sent' => 'false' ) );
		}

		update_post_meta( $request_id, Configuratore_Vernici_Content_Types::QUOTE_META_STATUS, 'quote_sent' );
		update_post_meta( $request_id, '_cv_last_quote_subject', $subject );
		update_post_meta( $request_id, '_cv_last_quote_message', $message );
		update_post_meta( $request_id, '_cv_last_quote_sent_at', current_time( 'mysql' ) );

		$this->redirect_to_requests_page( array( 'sent' => 'true' ) );
	}

	/**
	 * Save products inclusion settings from the paint management page.
	 *
	 * @since    1.0.0
	 */
	public function save_paints_list() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Non hai i permessi per modificare queste impostazioni.', 'configuratore-vernici' ) );
		}

		check_admin_referer( 'configuratore_vernici_save_paints', 'configuratore_vernici_paints_nonce' );

		$statuses = isset( $_POST['configuratore_vernici_product_status'] ) && is_array( $_POST['configuratore_vernici_product_status'] ) ? wp_unslash( $_POST['configuratore_vernici_product_status'] ) : array();
		$yields   = isset( $_POST['configuratore_vernici_product_yield'] ) && is_array( $_POST['configuratore_vernici_product_yield'] ) ? wp_unslash( $_POST['configuratore_vernici_product_yield'] ) : array();
		$valid_statuses = array_keys( Configuratore_Vernici_Content_Types::get_product_statuses() );

		foreach ( $statuses as $product_id => $status ) {
			$product_id = absint( $product_id );

			if ( ! is_scalar( $status ) ) {
				continue;
			}

			$status = sanitize_key( $status );

			if ( $product_id <= 0 || ! in_array( $status, $valid_statuses, true ) || ! current_user_can( 'edit_post', $product_id ) ) {
				continue;
			}

			if ( 'auto' === $status ) {
				delete_post_meta( $product_id, Configuratore_Vernici_Content_Types::PRODUCT_META_STATUS );
			} else {
				update_post_meta( $product_id, Configuratore_Vernici_Content_Types::PRODUCT_META_STATUS, $status );
			}

			if ( ! array_key_exists( $product_id, $yields ) || ! is_scalar( $yields[ $product_id ] ) ) {
				continue;
			}

			$raw_yield = trim( (string) $yields[ $product_id ] );

			if ( '' === $raw_yield ) {
				delete_post_meta( $product_id, 'resa_litro' );
				continue;
			}

			$yield = (float) str_replace( ',', '.', $raw_yield );

			if ( $yield <= 0 ) {
				delete_post_meta( $product_id, 'resa_litro' );
				continue;
			}

			update_post_meta( $product_id, 'resa_litro', $yield );
		}

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'    => $this->plugin_name . '-vernici',
					'updated' => 'true',
				),
				admin_url( 'admin.php' )
			)
		);
		exit;
	}

	/**
	 * Add the configurator meta box to WooCommerce products.
	 *
	 * @since    1.0.0
	 */
	public function add_product_meta_box() {
		add_meta_box(
			'configuratore-vernici-product-yield',
			__( 'Configuratore Vernici', 'configuratore-vernici' ),
			array( $this, 'render_product_meta_box' ),
			'product',
			'side',
			'default'
		);
	}

	/**
	 * Render the product yield meta box.
	 *
	 * @since    1.0.0
	 * @param    WP_Post $post The current product post.
	 */
	public function render_product_meta_box( $post ) {
		$yield = get_post_meta( $post->ID, 'resa_litro', true );
		$yield = '' !== $yield ? (float) str_replace( ',', '.', $yield ) : '';

		wp_nonce_field( 'configuratore_vernici_save_product_yield', 'configuratore_vernici_product_yield_nonce' );
		?>
		<p>
			<label for="configuratore_vernici_resa_litro"><?php esc_html_e( 'Resa per litro (m2/L)', 'configuratore-vernici' ); ?></label>
			<input
				type="number"
				id="configuratore_vernici_resa_litro"
				name="configuratore_vernici_resa_litro"
				class="widefat"
				min="0"
				step="0.01"
				value="<?php echo esc_attr( $yield ); ?>"
			>
		</p>
		<p>
			<label for="configuratore_vernici_product_status"><?php esc_html_e( 'Disponibilità nel configuratore', 'configuratore-vernici' ); ?></label>
			<select id="configuratore_vernici_product_status" name="configuratore_vernici_product_status" class="widefat">
				<?php
				$current_status = Configuratore_Vernici_Content_Types::get_product_status( $post->ID );
				foreach ( Configuratore_Vernici_Content_Types::get_product_statuses() as $status => $label ) :
					?>
					<option value="<?php echo esc_attr( $status ); ?>" <?php selected( $current_status, $status ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>
		<p class="description"><?php esc_html_e( 'In automatico il prodotto entra solo se ha resa_litro ed è dentro una delle 4 categorie B2B del configuratore. Puoi forzare inclusione o esclusione.', 'configuratore-vernici' ); ?></p>
		<?php
	}

	/**
	 * Save the product yield meta.
	 *
	 * @since    1.0.0
	 * @param    int     $post_id The product post ID.
	 * @param    WP_Post $post    The product post object.
	 * @param    bool    $update  Whether this is an existing post being updated.
	 */
	public function save_product_yield_meta( $post_id, $post, $update ) {
		if ( ! isset( $_POST['configuratore_vernici_product_yield_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['configuratore_vernici_product_yield_nonce'] ) ), 'configuratore_vernici_save_product_yield' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$status = isset( $_POST['configuratore_vernici_product_status'] ) && is_scalar( $_POST['configuratore_vernici_product_status'] ) ? sanitize_key( wp_unslash( $_POST['configuratore_vernici_product_status'] ) ) : 'auto';
		$valid_statuses = array_keys( Configuratore_Vernici_Content_Types::get_product_statuses() );

		if ( ! in_array( $status, $valid_statuses, true ) || 'auto' === $status ) {
			delete_post_meta( $post_id, Configuratore_Vernici_Content_Types::PRODUCT_META_STATUS );
		} else {
			update_post_meta( $post_id, Configuratore_Vernici_Content_Types::PRODUCT_META_STATUS, $status );
		}

		$raw_yield = isset( $_POST['configuratore_vernici_resa_litro'] ) ? wp_unslash( $_POST['configuratore_vernici_resa_litro'] ) : '';

		if ( ! is_scalar( $raw_yield ) ) {
			return;
		}

		$raw_yield = trim( (string) $raw_yield );

		if ( '' === $raw_yield ) {
			delete_post_meta( $post_id, 'resa_litro' );
			return;
		}

		$yield = (float) str_replace( ',', '.', $raw_yield );

		if ( $yield <= 0 ) {
			delete_post_meta( $post_id, 'resa_litro' );
		} else {
			update_post_meta( $post_id, 'resa_litro', $yield );
		}
	}

	/**
	 * Sanitize the recipient email list setting.
	 *
	 * @since    1.0.0
	 * @param    string $emails Raw email list value.
	 * @return   string
	 */
	public function sanitize_recipient_emails( $emails ) {
		$emails = is_scalar( $emails ) ? (string) $emails : '';
		$raw_emails = preg_split( '/[\s,;]+/', $emails );
		$valid_emails = array();

		foreach ( $raw_emails as $email ) {
			$email = sanitize_email( $email );

			if ( is_email( $email ) ) {
				$valid_emails[] = $email;
			}
		}

		$valid_emails = array_values( array_unique( $valid_emails ) );

		if ( empty( $valid_emails ) ) {
			add_settings_error(
				'configuratore_vernici_recipient_emails',
				'configuratore_vernici_recipient_emails_invalid',
				__( 'Inserisci almeno un indirizzo email valido per ricevere le richieste.', 'configuratore-vernici' )
			);

			return get_option( 'configuratore_vernici_recipient_emails', get_option( 'admin_email' ) );
		}

		return implode( "\n", $valid_emails );
	}

	/**
	 * Backward-compatible single email sanitizer.
	 *
	 * @since    1.0.0
	 * @param    string $email Raw email value.
	 * @return   string
	 */
	public function sanitize_recipient_email( $email ) {
		$emails = $this->sanitize_recipient_emails( $email );
		$emails = preg_split( '/[\r\n]+/', $emails );

		return isset( $emails[0] ) ? $emails[0] : get_option( 'admin_email' );
	}

	/**
	 * Sanitize the safety margin setting.
	 *
	 * @since    1.0.0
	 * @param    mixed $margin Raw margin value.
	 * @return   float
	 */
	public function sanitize_safety_margin( $margin ) {
		$margin = (float) str_replace( ',', '.', $margin );

		if ( $margin < 0 ) {
			$margin = 0;
		}

		if ( $margin > 100 ) {
			$margin = 100;
		}

		return $margin;
	}

	/**
	 * Sanitize a positive numeric setting.
	 *
	 * @since    1.0.0
	 * @param    mixed $number Raw number value.
	 * @return   float
	 */
	public function sanitize_positive_number( $number ) {
		$number = (float) str_replace( ',', '.', $number );

		return $number > 0 ? $number : 0;
	}

	/**
	 * Sanitize a phone number setting.
	 *
	 * @since    1.0.0
	 * @param    string $phone Raw phone value.
	 * @return   string
	 */
	public function sanitize_phone_number( $phone ) {
		$phone = is_scalar( $phone ) ? (string) $phone : '';

		return trim( preg_replace( '/[^0-9+\s().-]/', '', $phone ) );
	}

	/**
	 * Count products that can be used by the configurator.
	 *
	 * @since    1.0.0
	 * @return   int
	 */
	private function count_configured_products() {
		return count( array_filter( $this->get_paint_products_overview(), array( $this, 'filter_candidate_product' ) ) );
	}

	/**
	 * Filter helper for candidate products.
	 *
	 * @since    1.0.0
	 * @param    array $product Product overview row.
	 * @return   bool
	 */
	private function filter_candidate_product( $product ) {
		return ! empty( $product['is_candidate'] );
	}

	/**
	 * Retrieve products that should be visible in the paint management page.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	private function get_paint_products_overview() {
		$query = new WP_Query(
			array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'no_found_rows'  => true,
			)
		);

		$products = array();

		if ( ! $query->have_posts() ) {
			return $products;
		}

		foreach ( $query->posts as $product ) {
			$status = Configuratore_Vernici_Content_Types::get_product_status( $product->ID );
			$yield  = get_post_meta( $product->ID, 'resa_litro', true );
			$yield_value = (float) str_replace( ',', '.', $yield );
			$is_in_configurator_category = Configuratore_Vernici_Content_Types::product_is_in_configurator_category( $product->ID );
			$is_candidate = Configuratore_Vernici_Content_Types::product_is_candidate( $product->ID );

			if ( $yield_value <= 0 && 'auto' === $status && ! $is_in_configurator_category ) {
				continue;
			}

			$products[] = array(
				'id'                          => $product->ID,
				'title'                       => get_the_title( $product ),
				'edit_link'                   => get_edit_post_link( $product->ID ),
				'yield'                       => $yield,
				'status'                      => $status,
				'is_in_configurator_category' => $is_in_configurator_category,
				'is_candidate'                => $is_candidate,
				'categories'                  => $this->get_product_category_names( $product->ID ),
				'surfaces'                    => $this->get_product_surface_names( $product->ID ),
				'is_featured'                 => $this->product_is_featured( $product->ID ),
			);
		}

		return $products;
	}

	/**
	 * Get product category names.
	 *
	 * @since    1.0.0
	 * @param    int $product_id Product ID.
	 * @return   string
	 */
	private function get_product_category_names( $product_id ) {
		$terms = get_the_terms( $product_id, 'product_cat' );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return '';
		}

		return implode( ', ', wp_list_pluck( $terms, 'name' ) );
	}

	/**
	 * Get product surface names.
	 *
	 * @since    1.0.0
	 * @param    int $product_id Product ID.
	 * @return   string
	 */
	private function get_product_surface_names( $product_id ) {
		$terms = get_the_terms( $product_id, 'cv_superficie' );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return '';
		}

		return implode( ', ', wp_list_pluck( $terms, 'name' ) );
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
	 * Retrieve quote requests for the CRM page.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	private function get_quote_requests() {
		$query = new WP_Query(
			array(
				'post_type'      => 'cv_preventivo',
				'post_status'    => 'publish',
				'posts_per_page' => 50,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'no_found_rows'  => true,
			)
		);

		$requests = array();

		foreach ( $query->posts as $request ) {
			$meta = $this->get_quote_request_meta( $request->ID );

			$requests[] = array(
				'id'                    => $request->ID,
				'title'                 => get_the_title( $request ),
				'date'                  => get_the_date( 'd/m/Y H:i', $request ),
				'status'                => Configuratore_Vernici_Content_Types::get_quote_status( $request->ID ),
				'email'                 => $meta['email'],
				'product'               => $meta['product'],
				'category'              => $meta['category'],
				'surface'               => $meta['surface'],
				'notes'                 => $meta['notes'],
				'mq'                    => $meta['mq'],
				'liters'                => $meta['liters'],
				'last_quote_sent_at'    => $meta['last_quote_sent_at'],
				'default_quote_subject' => sprintf( '%s - %s', __( 'Preventivo', 'configuratore-vernici' ), $meta['product'] ),
				'default_quote_message' => $this->build_default_quote_message( $meta ),
			);
		}

		return $requests;
	}

	/**
	 * Retrieve normalized quote request meta.
	 *
	 * @since    1.0.0
	 * @param    int $request_id Request ID.
	 * @return   array
	 */
	private function get_quote_request_meta( $request_id ) {
		$surface = get_post_meta( $request_id, '_cv_surface', true );
		$category = get_post_meta( $request_id, '_cv_category', true );

		return array(
			'email'              => get_post_meta( $request_id, '_cv_customer_email', true ),
			'product'            => get_post_meta( $request_id, '_cv_product', true ),
			'category'           => '' !== $category ? $category : get_post_meta( $request_id, '_cv_tool', true ),
			'surface'            => '' !== $surface ? $surface : get_post_meta( $request_id, '_cv_material', true ),
			'notes'              => get_post_meta( $request_id, '_cv_notes', true ),
			'mq'                 => get_post_meta( $request_id, '_cv_mq', true ),
			'liters'             => get_post_meta( $request_id, '_cv_liters', true ),
			'yield'              => get_post_meta( $request_id, '_cv_yield', true ),
			'page'               => get_post_meta( $request_id, '_cv_page', true ),
			'last_quote_sent_at' => get_post_meta( $request_id, '_cv_last_quote_sent_at', true ),
		);
	}

	/**
	 * Build a default plain text quote email.
	 *
	 * @since    1.0.0
	 * @param    array $meta Request meta.
	 * @return   string
	 */
	private function build_default_quote_message( $meta ) {
		return implode(
			"\n",
			array(
				__( 'Buongiorno,', 'configuratore-vernici' ),
				'',
				__( 'in riferimento alla richiesta inviata tramite il configuratore, riportiamo il riepilogo tecnico:', 'configuratore-vernici' ),
				'',
				sprintf( '%s: %s', __( 'Prodotto', 'configuratore-vernici' ), $meta['product'] ),
				sprintf( '%s: %s', __( 'Settore', 'configuratore-vernici' ), '' !== $meta['category'] ? $meta['category'] : __( 'Non indicato', 'configuratore-vernici' ) ),
				sprintf( '%s: %s', __( 'Materiale o supporto', 'configuratore-vernici' ), '' !== $meta['surface'] ? $meta['surface'] : __( 'Non indicato', 'configuratore-vernici' ) ),
				sprintf( '%s: %s', __( 'Metri quadri', 'configuratore-vernici' ), $meta['mq'] ),
				sprintf( '%s: %s L', __( 'Litri stimati', 'configuratore-vernici' ), $meta['liters'] ),
				sprintf( '%s: %s', __( 'Note cliente', 'configuratore-vernici' ), '' !== $meta['notes'] ? $meta['notes'] : __( 'Nessuna nota', 'configuratore-vernici' ) ),
				'',
				__( 'Inserire qui condizioni commerciali, disponibilità, prezzo e tempi di consegna.', 'configuratore-vernici' ),
				'',
				__( 'Cordiali saluti', 'configuratore-vernici' ),
			)
		);
	}

	/**
	 * Count quote requests, optionally by status.
	 *
	 * @since    1.0.0
	 * @param    string $status Optional status.
	 * @return   int
	 */
	private function count_quote_requests( $status = '' ) {
		$args = array(
			'post_type'      => 'cv_preventivo',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		);

		if ( '' !== $status ) {
			$args['meta_query'] = array(
				array(
					'key'   => Configuratore_Vernici_Content_Types::QUOTE_META_STATUS,
					'value' => $status,
				),
			);
		}

		$query = new WP_Query( $args );

		return (int) $query->found_posts;
	}

	/**
	 * Count quote requests grouped by CRM status.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	private function get_quote_status_counts() {
		$counts = array();

		foreach ( Configuratore_Vernici_Content_Types::get_quote_statuses() as $status => $label ) {
			$counts[ $status ] = array(
				'label' => $label,
				'count' => $this->count_quote_requests( $status ),
			);
		}

		return $counts;
	}

	/**
	 * Build daily chart data for quote requests.
	 *
	 * @since    1.0.0
	 * @param    int $days Number of days.
	 * @return   array
	 */
	private function get_quote_chart_data( $days = 14 ) {
		$days = max( 1, absint( $days ) );
		$counts_by_day = array();
		$labels = array();
		$counts = array();
		$today = current_time( 'timestamp' );

		for ( $i = $days - 1; $i >= 0; $i-- ) {
			$timestamp = $today - ( $i * DAY_IN_SECONDS );
			$key = date_i18n( 'Y-m-d', $timestamp );
			$counts_by_day[ $key ] = 0;
			$labels[] = date_i18n( 'd/m', $timestamp );
		}

		$query = new WP_Query(
			array(
				'post_type'      => 'cv_preventivo',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'no_found_rows'  => true,
				'date_query'     => array(
					array(
						'after'     => date_i18n( 'Y-m-d 00:00:00', $today - ( ( $days - 1 ) * DAY_IN_SECONDS ) ),
						'inclusive' => true,
					),
				),
			)
		);

		foreach ( $query->posts as $request ) {
			$key = mysql2date( 'Y-m-d', $request->post_date );

			if ( isset( $counts_by_day[ $key ] ) ) {
				$counts_by_day[ $key ]++;
			}
		}

		foreach ( $counts_by_day as $count ) {
			$counts[] = $count;
		}

		return array(
			'labels' => $labels,
			'counts' => $counts,
		);
	}

	/**
	 * Redirect back to the CRM requests page.
	 *
	 * @since    1.0.0
	 * @param    array $args Query arguments.
	 */
	private function redirect_to_requests_page( $args = array() ) {
		wp_safe_redirect(
			add_query_arg(
				array_merge(
					array( 'page' => $this->plugin_name . '-richieste' ),
					$args
				),
				admin_url( 'admin.php' )
			)
		);
		exit;
	}

	/**
	 * Count published posts for a configurator post type.
	 *
	 * @since    1.0.0
	 * @param    string $post_type Post type.
	 * @return   int
	 */
	private function count_published_posts( $post_type ) {
		$counts = wp_count_posts( $post_type );

		if ( ! $counts || ! isset( $counts->publish ) ) {
			return 0;
		}

		return (int) $counts->publish;
	}

	/**
	 * Count terms for a taxonomy.
	 *
	 * @since    1.0.0
	 * @param    string $taxonomy Taxonomy name.
	 * @return   int
	 */
	private function count_terms( $taxonomy ) {
		if ( ! taxonomy_exists( $taxonomy ) ) {
			return 0;
		}

		$count = wp_count_terms(
			$taxonomy,
			array(
				'hide_empty' => false,
			)
		);

		return is_wp_error( $count ) ? 0 : (int) $count;
	}

}
