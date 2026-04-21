<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://acquistasitoweb.com
 * @since      1.0.0
 *
 * @package    Configuratore_Vernici
 * @subpackage Configuratore_Vernici/admin/partials
 */
?>

<div class="wrap configuratore-vernici-admin">
	<h1><?php esc_html_e( 'Configuratore Vernici', 'configuratore-vernici' ); ?></h1>
	<?php settings_errors(); ?>

	<div class="configuratore-vernici-admin__layout">
		<div class="configuratore-vernici-admin__main">
			<form method="post" action="options.php">
				<?php settings_fields( 'configuratore_vernici_settings' ); ?>

				<table class="form-table" role="presentation">
					<tbody>
						<tr>
							<th scope="row">
								<label for="configuratore_vernici_recipient_emails"><?php esc_html_e( 'Email destinatari CRM', 'configuratore-vernici' ); ?></label>
							</th>
							<td>
								<textarea
									class="large-text"
									rows="4"
									id="configuratore_vernici_recipient_emails"
									name="configuratore_vernici_recipient_emails"
								><?php echo esc_textarea( get_option( 'configuratore_vernici_recipient_emails', get_option( 'configuratore_vernici_recipient_email', get_option( 'admin_email' ) ) ) ); ?></textarea>
								<p class="description"><?php esc_html_e( 'Inserisci una o più email, una per riga. Riceveranno le notifiche interne delle nuove richieste.', 'configuratore-vernici' ); ?></p>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="configuratore_vernici_email_subject"><?php esc_html_e( 'Oggetto email', 'configuratore-vernici' ); ?></label>
							</th>
							<td>
								<input
									type="text"
									class="regular-text"
									id="configuratore_vernici_email_subject"
									name="configuratore_vernici_email_subject"
									value="<?php echo esc_attr( get_option( 'configuratore_vernici_email_subject', __( 'Nuova richiesta preventivo vernici', 'configuratore-vernici' ) ) ); ?>"
								>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="configuratore_vernici_safety_margin"><?php esc_html_e( 'Margine di sicurezza (%)', 'configuratore-vernici' ); ?></label>
							</th>
							<td>
								<input
									type="number"
									class="small-text"
									id="configuratore_vernici_safety_margin"
									name="configuratore_vernici_safety_margin"
									min="0"
									max="100"
									step="0.1"
									value="<?php echo esc_attr( get_option( 'configuratore_vernici_safety_margin', 10 ) ); ?>"
								>
								<p class="description"><?php esc_html_e( 'Valore predefinito: 10%. Formula: (m2 / resa_litro) * (1 + margine).', 'configuratore-vernici' ); ?></p>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="configuratore_vernici_free_delivery_threshold"><?php esc_html_e( 'Soglia consegna gratuita (L)', 'configuratore-vernici' ); ?></label>
							</th>
							<td>
								<input
									type="number"
									class="small-text"
									id="configuratore_vernici_free_delivery_threshold"
									name="configuratore_vernici_free_delivery_threshold"
									min="0"
									step="0.1"
									value="<?php echo esc_attr( get_option( 'configuratore_vernici_free_delivery_threshold', 30 ) ); ?>"
								>
								<p class="description"><?php esc_html_e( 'Default: 30 L. Sotto questa soglia il configuratore suggerisce di aumentare la richiesta per ottenere la consegna gratuita in cantiere/officina.', 'configuratore-vernici' ); ?></p>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="configuratore_vernici_urgent_phone"><?php esc_html_e( 'Telefono ordini urgenti', 'configuratore-vernici' ); ?></label>
							</th>
							<td>
								<input
									type="tel"
									class="regular-text"
									id="configuratore_vernici_urgent_phone"
									name="configuratore_vernici_urgent_phone"
									value="<?php echo esc_attr( get_option( 'configuratore_vernici_urgent_phone', '' ) ); ?>"
									placeholder="<?php esc_attr_e( 'Es. +39 0123 456789', 'configuratore-vernici' ); ?>"
								>
								<p class="description"><?php esc_html_e( 'Se compilato, nel risultato appare un pulsante Chiama per ordini urgenti.', 'configuratore-vernici' ); ?></p>
							</td>
						</tr>
					</tbody>
				</table>

				<?php submit_button( __( 'Salva impostazioni', 'configuratore-vernici' ) ); ?>
			</form>

			<div class="configuratore-vernici-admin__chart-panel">
				<div class="configuratore-vernici-admin__chart-header">
					<h2><?php esc_html_e( 'Richieste preventivo giornaliere', 'configuratore-vernici' ); ?></h2>
					<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=configuratore-vernici-richieste' ) ); ?>">
						<?php esc_html_e( 'Apri CRM', 'configuratore-vernici' ); ?>
					</a>
				</div>
				<canvas class="configuratore-vernici-admin__chart" width="900" height="280" data-configuratore-chart></canvas>
			</div>
		</div>

		<aside class="configuratore-vernici-admin__side">
			<div class="configuratore-vernici-admin__box">
				<h2><?php esc_html_e( 'Richieste CRM', 'configuratore-vernici' ); ?></h2>
				<p class="configuratore-vernici-admin__count"><?php echo esc_html( $quotes_count ); ?></p>
				<p>
					<?php
					printf(
						esc_html__( '%d nuove da lavorare.', 'configuratore-vernici' ),
						(int) $new_quotes_count
					);
					?>
				</p>
				<ul class="configuratore-vernici-admin__list">
					<?php foreach ( $quote_status_counts as $status_count ) : ?>
						<li><?php echo esc_html( $status_count['label'] . ': ' . $status_count['count'] ); ?></li>
					<?php endforeach; ?>
				</ul>
				<p>
					<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=configuratore-vernici-richieste' ) ); ?>">
						<?php esc_html_e( 'Gestisci richieste', 'configuratore-vernici' ); ?>
					</a>
				</p>
			</div>

			<div class="configuratore-vernici-admin__box">
				<h2><?php esc_html_e( 'Shortcode', 'configuratore-vernici' ); ?></h2>
				<code>[configuratore_vernici]</code>
				<p><?php esc_html_e( 'Inseriscilo in una pagina o in un blocco testo di Flatsome.', 'configuratore-vernici' ); ?></p>
			</div>

			<div class="configuratore-vernici-admin__box">
				<h2><?php esc_html_e( 'Categorie padre', 'configuratore-vernici' ); ?></h2>
				<ul class="configuratore-vernici-admin__list">
					<?php foreach ( $paint_parent_categories as $category ) : ?>
						<li><?php echo esc_html( $category['name'] ); ?></li>
					<?php endforeach; ?>
				</ul>
				<p><?php esc_html_e( 'Il plugin crea queste categorie WooCommerce se non esistono. Le sottocategorie ereditano la candidabilità automatica.', 'configuratore-vernici' ); ?></p>
			</div>

			<div class="configuratore-vernici-admin__box">
				<h2><?php esc_html_e( 'Prodotti configurati', 'configuratore-vernici' ); ?></h2>
				<p class="configuratore-vernici-admin__count"><?php echo esc_html( $configured_products ); ?></p>
				<p><?php esc_html_e( 'Sono conteggiati i prodotti pubblicati con custom field resa_litro.', 'configuratore-vernici' ); ?></p>
				<p>
					<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=configuratore-vernici-vernici' ) ); ?>">
						<?php esc_html_e( 'Gestisci lista vernici', 'configuratore-vernici' ); ?>
					</a>
				</p>
			</div>

			<div class="configuratore-vernici-admin__box">
				<h2><?php esc_html_e( 'Superfici', 'configuratore-vernici' ); ?></h2>
				<p class="configuratore-vernici-admin__count"><?php echo esc_html( $surfaces_count ); ?></p>
				<p><?php esc_html_e( 'Usale come tag sui prodotti WooCommerce per indicare i materiali o supporti compatibili.', 'configuratore-vernici' ); ?></p>
				<p>
					<a class="button" href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=cv_superficie&post_type=product' ) ); ?>">
						<?php esc_html_e( 'Gestisci superfici', 'configuratore-vernici' ); ?>
					</a>
				</p>
			</div>

			<div class="configuratore-vernici-admin__box">
				<h2><?php esc_html_e( 'Strumenti', 'configuratore-vernici' ); ?></h2>
				<p class="configuratore-vernici-admin__count"><?php echo esc_html( $tools_count ); ?></p>
				<p>
					<a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=cv_strumento' ) ); ?>">
						<?php esc_html_e( 'Gestisci strumenti', 'configuratore-vernici' ); ?>
					</a>
				</p>
			</div>
		</aside>
	</div>
</div>
