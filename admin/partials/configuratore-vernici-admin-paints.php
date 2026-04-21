<?php

/**
 * Provide the paint management view for the plugin.
 *
 * @link       https://acquistasitoweb.com
 * @since      1.0.0
 *
 * @package    Configuratore_Vernici
 * @subpackage Configuratore_Vernici/admin/partials
 */
?>

<div class="wrap configuratore-vernici-admin">
	<h1><?php esc_html_e( 'Vernici candidabili', 'configuratore-vernici' ); ?></h1>

	<?php if ( isset( $_GET['updated'] ) && 'true' === $_GET['updated'] ) : ?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Lista vernici aggiornata.', 'configuratore-vernici' ); ?></p>
		</div>
	<?php endif; ?>

	<p><?php esc_html_e( 'Da qui puoi decidere quali prodotti WooCommerce entrano nella lista del configuratore. In automatico entrano solo i prodotti pubblicati con resa_litro maggiore di zero e assegnati a una delle 4 categorie B2B, incluse le sottocategorie. La stellina WooCommerce porta il prodotto in cima ai suggerimenti.', 'configuratore-vernici' ); ?></p>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="configuratore_vernici_save_paints">
		<?php wp_nonce_field( 'configuratore_vernici_save_paints', 'configuratore_vernici_paints_nonce' ); ?>

		<table class="wp-list-table widefat fixed striped configuratore-vernici-admin__paints-table">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Vernice', 'configuratore-vernici' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Categorie', 'configuratore-vernici' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Superfici', 'configuratore-vernici' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Resa m2/L', 'configuratore-vernici' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Stato', 'configuratore-vernici' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Nel configuratore', 'configuratore-vernici' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $products ) ) : ?>
					<tr>
						<td colspan="6"><?php esc_html_e( 'Nessuna vernice trovata. Assegna prodotti alle categorie B2B del configuratore, oppure imposta una resa_litro su un prodotto.', 'configuratore-vernici' ); ?></td>
					</tr>
				<?php endif; ?>

				<?php foreach ( $products as $product ) : ?>
					<tr>
						<td>
							<strong>
								<a href="<?php echo esc_url( $product['edit_link'] ); ?>">
									<?php echo esc_html( $product['title'] ); ?>
								</a>
							</strong>
						</td>
						<td>
							<?php echo esc_html( $product['categories'] ? $product['categories'] : __( 'Nessuna categoria', 'configuratore-vernici' ) ); ?>
							<?php if ( ! $product['is_in_configurator_category'] ) : ?>
								<br><span class="configuratore-vernici-admin__muted"><?php esc_html_e( 'Fuori dalle categorie padre configuratore', 'configuratore-vernici' ); ?></span>
							<?php endif; ?>
						</td>
						<td>
							<?php echo esc_html( $product['surfaces'] ? $product['surfaces'] : __( 'Nessuna superficie', 'configuratore-vernici' ) ); ?>
							<?php if ( $product['is_featured'] ) : ?>
								<br><span class="configuratore-vernici-admin__muted"><?php esc_html_e( 'Consigliato con stellina WooCommerce', 'configuratore-vernici' ); ?></span>
							<?php endif; ?>
						</td>
						<td>
							<input
								type="number"
								name="configuratore_vernici_product_yield[<?php echo esc_attr( $product['id'] ); ?>]"
								value="<?php echo esc_attr( $product['yield'] ); ?>"
								min="0"
								step="0.01"
								class="small-text"
							>
						</td>
						<td>
							<select name="configuratore_vernici_product_status[<?php echo esc_attr( $product['id'] ); ?>]">
								<?php foreach ( $statuses as $status => $label ) : ?>
									<option value="<?php echo esc_attr( $status ); ?>" <?php selected( $product['status'], $status ); ?>>
										<?php echo esc_html( $label ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</td>
						<td>
							<?php if ( $product['is_candidate'] ) : ?>
								<span class="configuratore-vernici-admin__status is-active"><?php esc_html_e( 'Si', 'configuratore-vernici' ); ?></span>
							<?php else : ?>
								<span class="configuratore-vernici-admin__status is-inactive"><?php esc_html_e( 'No', 'configuratore-vernici' ); ?></span>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<?php submit_button( __( 'Aggiorna lista vernici', 'configuratore-vernici' ) ); ?>
	</form>
</div>
