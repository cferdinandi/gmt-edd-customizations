<?php


	/**
	 * Check if cart has a recurring payment
	 * @param  Array   $purchases The user's purchases
	 * @return Boolean            Returns true if purchase is bonus gift eligible
	 */
	function gmt_edd_custom_is_bonus_eligible ( $purchases ) {

		// Check if bonus gifts are active
		if (empty(edd_get_option('gmt_edd_custom_is_bonus_gift'))) return false;

		// Check if there are eligible products and a bonus gift
		$products = edd_get_option('gmt_edd_custom_bonus_eligible_products');
		if (empty($products) || empty(edd_get_option('gmt_edd_custom_bonus_gift'))) return false;

		// If there's a start date, make sure we're at or past it
		$start = edd_get_option('gmt_edd_custom_bonus_start');
		if (!empty($start) && time() < strtotime($start)) return false;

		// If there's an end date, make sure we're before it
		$end = edd_get_option('gmt_edd_custom_bonus_end');
		if (!empty($end) && time() > (strtotime($end) + 86400)) return false;

		// Get an array of purchase IDs
		$purchase_ids = array();
		if ( is_array($purchases) ) {
			foreach ( $purchases as $download ) {
				$purchase_ids[] = $download['id'];
			}
		}

		// Check if the user has eligible purchases
		foreach ($products as $product_id => $value) {
			if (in_array($product_id, $purchase_ids)) return true;
		}

		// Otherwise, return false
		return false;

	}



	/**
	 * Show payment details for payment installments
	 */
	function gmt_edd_custom_get_bonus_gift_message () {
		$cart = edd_get_cart_contents();
		if (!gmt_edd_custom_is_bonus_eligible($cart)) return;
		echo stripslashes( edd_get_option( 'gmt_edd_custom_bonus_gift_message' ) );
	}
	add_action('edd_before_purchase_form', 'gmt_edd_custom_get_bonus_gift_message');



	/**
	 * Add bonus gift to buyer's purchases
	 * @param  Integer $payment_id ID for the purchase
	 */
	function gmt_edd_custom_add_bonus_gift_to_purchase ( $payment_id ) {

		// Check if bonus gift eligible
		$purchase = edd_get_payment_meta( $payment_id );
		if (empty(gmt_edd_custom_is_bonus_eligible($purchase['downloads']))) return;

		// Add the bonus content
		$bonus_gift_id = edd_get_option('gmt_edd_custom_bonus_gift');
		$payment = new EDD_Payment();
		$payment->add_download( $bonus_gift_id, array(
			'item_price' => 0.00,
		));
		$payment->email = $purchase['user_info']['email'];
		$payment->save();

		// Flip from pending to complete
		$payment->status = 'complete';
		$payment->save();

	}
	add_action( 'edd_after_payment_actions', 'gmt_edd_custom_add_bonus_gift_to_purchase', 10, 2 );