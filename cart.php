<?php


	//
	// Cart Items
	//

	/**
	 * Remove unneeded dash (-) from cart items with no bundle names
	 */
	function gmt_edd_custom_update_cart_item_name ( $item_name ) {
		return str_replace(' - _', '', str_replace(' — _', '', $item_name));
	}
	add_filter( 'edd_get_cart_item_name', 'gmt_edd_custom_update_cart_item_name' );


	/**
	 * Only allow one item in cart at a time
	 */
	// add_filter( 'edd_pre_add_to_cart_contents', '__return_false' );



	//
	// Stripe Label
	//

	/**
	 * Add stripe language to credit card field
	 */
	function gmt_edd_custom_add_via_stripe ( $gateways ) {
		if (array_key_exists( 'stripe', $gateways )) {
			if (array_key_exists( 'checkout_label', $gateways['stripe'] ))
			$gateways['stripe']['checkout_label'] = edd_get_option( 'gmt_edd_custom_credit_card_label' );
		}
		return $gateways;
	}
	add_filter( 'edd_payment_gateways', 'gmt_edd_custom_add_via_stripe', 20 );



	//
	// Name Fields
	//

	/**
	 * Unset first and last name as required fields in checkout
	 * @param  Array $required_fields Required fields
	 */
	function gmt_edd_custom_purchase_form_remove_required_fields( $required_fields ) {
		unset( $required_fields['edd_first'] );
		unset( $required_fields['edd_last'] );
		return $required_fields;
	}
	add_filter( 'edd_purchase_form_required_fields', 'gmt_edd_custom_purchase_form_remove_required_fields' );


	/**
	 * Remove default name fields from checkout
	 */
	function gmt_edd_custom_remove_names() {
		remove_action( 'edd_purchase_form_after_user_info', 'edd_user_info_fields' );
	}
	add_action( 'init', 'gmt_edd_custom_remove_names' );


	/**
	 * Remove name fields from checkout form
	 */
	function gmt_edd_custom_user_info_fields() {
		if( is_user_logged_in() ) :
			$user_data = get_userdata( get_current_user_id() );
		endif;
		$desc = edd_get_option('gmt_edd_custom_email_description');
		?>
		<fieldset id="edd_checkout_user_info">
			<?php do_action( 'edd_purchase_form_before_email' ); ?>
			<p id="edd-email-wrap">
				<label class="edd-label<?php if (!empty($desc)) : ?> no-margin-bottom<?php endif; ?>" for="edd-email"><strong><?php _e('Email Address', 'edd'); ?></strong></label>
				<?php if (!empty($desc)) : ?>
				<div class="margin-bottom-small"><em id="edd-email-desc" class="text-small text-muted"><?php echo esc_html($desc); ?></em></div>
				<?php endif; ?>
				<input class="edd-input required" type="email" name="edd_email" placeholder="<?php _e('Email address', 'edd'); ?>" id="edd-email" <?php if (!empty($desc)) : ?>aria-describedby="edd-email-desc"<?php endif; ?> value="<?php echo is_user_logged_in() ? $user_data->user_email : ''; ?>"/>
			</p>
			<?php do_action( 'edd_purchase_form_after_email' ); ?>
			<?php do_action( 'edd_purchase_form_user_info' ); ?>
		</fieldset>
		<?php
	}
	add_action( 'edd_purchase_form_after_user_info', 'gmt_edd_custom_user_info_fields' );



	//
	// GDPR Message
	//

	/**
	 * GDPR Message
	 */
	function gmt_edd_custom_add_gdpr_message() {
		echo stripslashes( edd_get_option('gmt_edd_custom_gdpr_message') );
	}
	add_action( 'edd_purchase_form_after_submit', 'gmt_edd_custom_add_gdpr_message' );



	//
	// Disable Things
	//

	/**
	 * Disable EDD verification emails
	 */
	function gmt_edd_custom_disable_verification_email() {
		remove_action( 'edd_send_verification_email', 'edd_process_user_verification_request' );
	}
	add_action('init', 'gmt_edd_custom_disable_verification_email');


	/**
	 * Remove default credit card validator
	 */
	function gmt_edd_custom_remove_credit_card_validator() {
		wp_dequeue_script( 'creditCardValidator' );
	}
	add_action( 'wp_enqueue_scripts', 'gmt_edd_custom_remove_credit_card_validator' );



	//
	// Cart Icon
	//

	/**
	 * Add cart icon to the navigation menu if items are in cart
	 */
	function gmt_edd_custom_add_cart_link_to_nav( $items, $args ) {
		if ( $args->theme_location !== 'primary' ) return $items;
		if ( !function_exists( 'edd_get_cart_quantity' ) ) return $items;
		$cart_quantity = edd_get_cart_quantity();
		$items .=
			'<li id="primary-nav-edd-cart" data-edd-cart-quantity="' . $cart_quantity . '">' .
				'<a href="' . edd_get_checkout_uri() . '">' .
					'<svg xmlns="http://www.w3.org/2000/svg" height="1em" width="1em" viewBox="0 0 17 17" aria-hidden="true"><path fill="currentColor" d="M6.375 15.406a1.594 1.594 0 1 1-3.189 0 1.594 1.594 0 0 1 3.189 0zM17 15.406a1.594 1.594 0 1 1-3.189 0 1.594 1.594 0 0 1 3.189 0zM17 8.5V2.125H4.25c0-.587-.476-1.063-1.063-1.063H-.001v1.063h2.125l.798 6.841a2.124 2.124 0 0 0 1.327 3.784h12.75v-1.063H4.249a1.063 1.063 0 0 1-1.063-1.063v-.011l13.812-2.114z"/></svg> ' . __( 'Cart', 'keel' ) . ' (' . $cart_quantity . ')' .
				'</a>' .
			'</li>';
		return $items;
	}
	// add_filter( 'wp_nav_menu_items', 'gmt_edd_custom_add_cart_link_to_nav', 10, 2);

	/**
	 * Add back button to navigation menu
	 */
	function gmt_edd_custom_add_back_to_course_link_to_nav ( $items, $args ) {
		if ( $args->theme_location !== 'primary' ) return $items;
		if ( !function_exists( 'edd_get_cart_contents' ) ) return $items;
		$cart_contents = edd_get_cart_contents();
		$url = 'https://gomakethings.com/resources/';
		if (!empty($cart_contents)) {
			foreach ( $cart_contents as $item ) {
				$link = get_post_meta( $item['id'], 'gmt_edd_product_page_link', true );
				if (!empty($link)) {
					$url = $link;
				}
			}
		}
		$items .=
			'<li id="primary-nav-edd-back">' .
				'<a href="' . $url . '">' .
					'&larr; Back' .
				'</a>' .
			'</li>';
		return $items;
	}
	add_filter( 'wp_nav_menu_items', 'gmt_edd_custom_add_back_to_course_link_to_nav', 10, 2);



	//
	// Checkout Success
	//

	/**
	 * Redirect the success page to a custom URL.
	 * We use this instead of the success page redirect filter because of Stripe, which does not use that filter.
	 */
	function gmt_edd_custom_redirect_success_page() {

		// If there's no success page, bail
		if ( ! edd_is_success_page() ) return;

		// Get the purchase session
		$purchase_session = edd_get_purchase_session();
		if ( empty( $purchase_session ) || empty( $purchase_session['user_info']['email'] ) ) return;

		// Get the student portal URL
		$url = edd_get_option('gmt_edd_custom_student_portal_url');
		if (empty($url)) return;

		// Get the user email
		$email = $purchase_session['user_info']['email'];
		if (empty($email)) return;

		// Redirect to student portal
		header('Location: ' . $url . '?email=' . urlencode($url));
		exit();

	}
	add_action( 'template_redirect', 'gmt_edd_custom_redirect_success_page' );