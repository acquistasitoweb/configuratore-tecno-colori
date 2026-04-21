<?php

/**
 * Provide the quote requests CRM view for the plugin.
 *
 * @link       https://acquistasitoweb.com
 * @since      1.0.0
 *
 * @package    Configuratore_Vernici
 * @subpackage Configuratore_Vernici/admin/partials
 */
?>

<div class="wrap configuratore-vernici-admin">
	<h1><?php esc_html_e( 'Richieste preventivo', 'configuratore-vernici' ); ?></h1>

	<?php if ( isset( $_GET['updated'] ) && 'true' === $_GET['updated'] ) : ?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Stato richiesta aggiornato.', 'configuratore-vernici' ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( isset( $_GET['sent'] ) ) : ?>
		<?php if ( 'true' === $_GET['sent'] ) : ?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e( 'Preventivo inviato al cliente.', 'configuratore-vernici' ); ?></p>
			</div>
		<?php elseif ( 'false' === $_GET['sent'] ) : ?>
			<div class="notice notice-error is-dismissible">
				<p><?php esc_html_e( 'Invio email non riuscito. Verifica la configurazione SMTP del sito.', 'configuratore-vernici' ); ?></p>
			</div>
		<?php else : ?>
			<div class="notice notice-error is-dismissible">
				<p><?php esc_html_e( 'Dati preventivo non validi.', 'configuratore-vernici' ); ?></p>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<p><?php esc_html_e( 'Questa lista raccoglie automaticamente le richieste generate dallo shortcode. Puoi cambiare stato e inviare un preventivo testuale al cliente senza uscire dal backoffice.', 'configuratore-vernici' ); ?></p>

	<table class="wp-list-table widefat striped configuratore-vernici-admin__requests-table">
		<thead>
			<tr>
				<th scope="col"><?php esc_html_e( 'Cliente', 'configuratore-vernici' ); ?></th>
				<th scope="col"><?php esc_html_e( 'Richiesta', 'configuratore-vernici' ); ?></th>
				<th scope="col"><?php esc_html_e( 'Stato', 'configuratore-vernici' ); ?></th>
				<th scope="col"><?php esc_html_e( 'Preventivo', 'configuratore-vernici' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $requests ) ) : ?>
				<tr>
					<td colspan="4"><?php esc_html_e( 'Nessuna richiesta preventivo registrata.', 'configuratore-vernici' ); ?></td>
				</tr>
			<?php endif; ?>

			<?php foreach ( $requests as $request ) : ?>
				<tr>
					<td>
						<strong><?php echo esc_html( $request['email'] ); ?></strong>
						<br>
						<span class="configuratore-vernici-admin__muted">
							<?php echo esc_html( $request['date'] ); ?> · #<?php echo esc_html( $request['id'] ); ?>
						</span>
						<?php if ( $request['last_quote_sent_at'] ) : ?>
							<br>
							<span class="configuratore-vernici-admin__muted">
								<?php
								printf(
									esc_html__( 'Ultimo invio: %s', 'configuratore-vernici' ),
									esc_html( $request['last_quote_sent_at'] )
								);
								?>
							</span>
						<?php endif; ?>
					</td>
					<td>
						<strong><?php echo esc_html( $request['product'] ); ?></strong>
						<br>
						<?php
						printf(
							esc_html__( '%1$s m2 · %2$s L', 'configuratore-vernici' ),
							esc_html( $request['mq'] ),
							esc_html( $request['liters'] )
						);
						?>
						<br>
						<span class="configuratore-vernici-admin__muted">
							<?php
							printf(
								esc_html__( 'Settore: %1$s · Superficie: %2$s', 'configuratore-vernici' ),
								esc_html( $request['category'] ? $request['category'] : __( 'Non indicato', 'configuratore-vernici' ) ),
								esc_html( $request['surface'] ? $request['surface'] : __( 'Non indicato', 'configuratore-vernici' ) )
							);
							?>
						</span>
						<?php if ( $request['notes'] ) : ?>
							<br>
							<span class="configuratore-vernici-admin__muted">
								<?php
								printf(
									esc_html__( 'Note: %s', 'configuratore-vernici' ),
									esc_html( $request['notes'] )
								);
								?>
							</span>
						<?php endif; ?>
					</td>
					<td>
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
							<input type="hidden" name="action" value="configuratore_vernici_save_request_status">
							<input type="hidden" name="request_id" value="<?php echo esc_attr( $request['id'] ); ?>">
							<?php wp_nonce_field( 'configuratore_vernici_save_request_status', 'configuratore_vernici_request_nonce' ); ?>
							<select name="request_status">
								<?php foreach ( $statuses as $status => $label ) : ?>
									<option value="<?php echo esc_attr( $status ); ?>" <?php selected( $request['status'], $status ); ?>>
										<?php echo esc_html( $label ); ?>
									</option>
								<?php endforeach; ?>
							</select>
							<?php submit_button( __( 'Aggiorna', 'configuratore-vernici' ), 'secondary small', 'submit', false ); ?>
						</form>
					</td>
					<td>
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="configuratore-vernici-admin__quote-form">
							<input type="hidden" name="action" value="configuratore_vernici_send_quote">
							<input type="hidden" name="request_id" value="<?php echo esc_attr( $request['id'] ); ?>">
							<?php wp_nonce_field( 'configuratore_vernici_send_quote', 'configuratore_vernici_quote_nonce' ); ?>
							<label>
								<span class="screen-reader-text"><?php esc_html_e( 'Oggetto preventivo', 'configuratore-vernici' ); ?></span>
								<input type="text" name="quote_subject" class="regular-text" value="<?php echo esc_attr( $request['default_quote_subject'] ); ?>">
							</label>
							<label>
								<span class="screen-reader-text"><?php esc_html_e( 'Messaggio preventivo', 'configuratore-vernici' ); ?></span>
								<textarea name="quote_message" rows="7" class="large-text"><?php echo esc_textarea( $request['default_quote_message'] ); ?></textarea>
							</label>
							<?php submit_button( __( 'Invia preventivo', 'configuratore-vernici' ), 'primary small', 'submit', false ); ?>
						</form>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
