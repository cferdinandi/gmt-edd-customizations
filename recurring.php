<?php

	/**
	 * Update the payment status when a subscription status changes
	 * @param  String $old_status   The old status
	 * @param  String $new_status   The new status
	 * @param  Object $subscription The subscription data
	 */
	function gmt_edd_custom_update_payment_on_subscription_status_change ( $old_status, $new_status, $subscription ) {

		// Get the payment associated with the subscription
		$payment = new EDD_Payment( $subscription->parent_payment_id );
		if (empty($payment)) return;

		// Define the $status
		$status = ($new_status == 'cancelled' || $new_status == 'expired') ? 'cancelled' : 'complete';

		// Update the payment status
		$payment->update_status( $status );
		$payment->save();

	}
	add_action( 'edd_subscription_status_change', 'gmt_edd_custom_update_payment_on_subscription_status_change', 10, 3 );



	/**
	 * Check if cart has a recurring payment
	 * @return Boolean Returns true if recurring payment is in cart
	 */
	function gmt_edd_custom_is_recurring_in_cart () {
		$cart = edd_get_cart_contents();
		if ( is_array( $cart ) ) {
			foreach ( $cart as $download ) {
				if ( isset( $download['options'] ) && isset( $download['options']['recurring'] ) ) return true;
			}
		}
		return false;
	}



	/**
	 * Show payment details for payment installments
	 */
	function gmt_edd_custom_subscription_message () {
		if (!gmt_edd_custom_is_recurring_in_cart()) return;
		echo stripslashes( edd_get_option( 'gmt_edd_custom_recurring_payments' ) );
	}
	add_action('edd_before_purchase_form', 'gmt_edd_custom_subscription_message');