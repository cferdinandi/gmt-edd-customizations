<?php

	/**
	 * Get a list of download names for the email
	 * @param  Number $payment_id The payment ID
	 * @return String             A comma-separated list of download names
	 */
	function gmt_edd_custom_get_download_list_names ( $payment_id ) {

		$cart   = edd_get_payment_meta_cart_details( $payment_id, true );
		$files  = array();

		if ( $cart ) {
			foreach ( $cart as $key => $item ) {
				if ( empty( $item['in_bundle'] ) ) {
					$variable_prices = edd_has_variable_prices( $item['id'] );
					$option = '';
					if ($variable_prices && isset( $item['item_number']['options']['price_id'] )) {
						$option = ' - ' . edd_get_price_option_name( $item['id'], $item['item_number']['options']['price_id'], 32 );
					}
					$files[] = gmt_edd_custom_update_cart_item_name($item['name'] . $option);
				}
			}
		}

		return implode(', ', $files);

	}



	/**
	 * Get the pricing parity discount
	 * @param  integer $payment_id The payment ID
	 */
	function gmt_edd_custom_get_pricing_parity ( $payment_id = 0 ) {
		$payment = edd_get_payment($payment_id);
		$payment_meta = $payment->get_meta();
		$discount = $payment_meta['pricing_parity'];
		if (empty($discount)) return '';
		return $discount['country'] . ' - ' . $discount['amount'] . '%';
	}



	/**
	 * Add tag to email templates
	 * @param  Number $payment_id The payment ID
	 * @return Array  The download all tag
	 */
	function gmt_edd_custom_setup_email_tags( $payment_id ) {
		edd_add_email_tag( 'download_list_names', __( 'Adds a comma-separated listed of purchased product names', 'edd' ), 'gmt_edd_custom_get_download_list_names' );
		edd_add_email_tag( 'pricing_parity', __( 'The pricing parity discount.', 'edd' ), 'gmt_edd_custom_get_pricing_parity' );
	}
	add_action( 'edd_add_email_tags', 'gmt_edd_custom_setup_email_tags' );