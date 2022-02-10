<?php

	function gmt_edd_custom_get_all_downloads () {

		// Get all downloads
		$downloads = get_posts(array(
			'post_type' => 'download',
			'post_status' => 'publish',
			'numberposts' => -1,
			'orderby' => 'title',
			'order' => 'ASC',
		));

		// Create array of options
		$options = array();
		foreach ($downloads as $download) {
			$options[$download->ID] = $download->post_title;
		}

		return $options;

	}


	/**
	 * Add settings section
	 * @param array $sections The current sections
	 */
	function gmt_edd_custom_settings_section ( $sections ) {
		$sections['gmt_edd_custom'] = __( 'GMT Customizations', 'gmt_edd' );
		return $sections;
	}
	add_filter( 'edd_settings_sections_extensions', 'gmt_edd_custom_settings_section' );


	/**
	 * Add settings
	 * @param  array $settings The existing settings
	 */
	function gmt_edd_custom_settings( $settings ) {

		$custom_settings = array(

			// Heading
			array(
				'id'    => 'gmt_edd_custom_settings_cart',
				'name'  => '<strong>' . __( 'Checkout Settings', 'gmt_edd' ) . '</strong>',
				'desc'  => __( 'Settings for the checkout page', 'gmt_edd' ),
				'type'  => 'header',
			),

			// Credit Card Label
			array(
				'id'      => 'gmt_edd_custom_credit_card_label',
				'name'    => __( 'Credit Card Label', 'gmt_edd' ),
				'desc'    => __( 'Label for the credit card field', 'gmt_edd' ),
				'type'    => 'text',
				'std'     => __( 'Credit Card (via Stripe)', 'gmt_edd' ),
			),

			// Product Link Text
			array(
				'id'      => 'gmt_edd_custom_product_link_text',
				'name'    => __( 'Product Link Text', 'gmt_edd' ),
				'desc'    => __( 'Text for the product links', 'gmt_edd' ),
				'type'    => 'text',
				'std'     => __( 'view item', 'gmt_edd' ),
			),

			// GDPR Message
			array(
				'id'      => 'gmt_edd_custom_gdpr_message',
				'name'    => __( 'GDPR Message', 'gmt_edd' ),
				'desc'    => __( 'GDPR message to display after the checkout button', 'gmt_edd' ),
				'type'    => 'textarea',
				'std'     => '',
			),

			// Recurring payment details
			array(
				'id'      => 'gmt_edd_custom_recurring_payments',
				'name'    => __( 'Recurring Payments', 'gmt_edd' ),
				'desc'    => __( 'Message to display when recurring payments are in the cart', 'gmt_edd' ),
				'type'    => 'textarea',
				'std'     => '',
			),

			// Heading
			array(
				'id'    => 'gmt_edd_custom_settings_bonuses',
				'name'  => '<strong>' . __( 'Bonus Gifts', 'gmt_edd' ) . '</strong>',
				'desc'  => __( 'Settings for bonus products with select purchases', 'gmt_edd' ),
				'type'  => 'header',
			),

			// Bonus Gift Activate
			array(
				'id'      => 'gmt_edd_custom_is_bonus_gift',
				'name'    => __( 'Active', 'gmt_edd' ),
				'desc'    => __( 'Offer bonus gifts', 'gmt_edd' ),
				'type'    => 'checkbox',
				'std'     => '',
			),

			// Start Date
			// @todo pass value into strtotime() later to calculate eligibility
			array(
				'id'      => 'gmt_edd_custom_bonus_start',
				'name'    => __( 'Start', 'gmt_edd' ),
				'desc'    => __( 'Start date for bonus gifts', 'gmt_edd' ),
				'type'    => 'text',
				'std'     => '',
				'field_class'   => 'edd_datepicker',
			),

			// End Date
			array(
				'id'      => 'gmt_edd_custom_bonus_end',
				'name'    => __( 'End', 'gmt_edd' ),
				'desc'    => __( 'End date for bonus gifts', 'gmt_edd' ),
				'type'    => 'text',
				'std'     => '',
				'field_class'   => 'edd_datepicker',
			),

			// Bonus Gift Message
			array(
				'id'      => 'gmt_edd_custom_bonus_gift_message',
				'name'    => __( 'Bonus Gift Message', 'gmt_edd' ),
				'desc'    => __( 'The message to display for bonus gift eligible purchases', 'gmt_edd' ),
				'type'    => 'textarea',
				'std'     => '',
			),

			// Bonus gift
			array(
				'id'      => 'gmt_edd_custom_bonus_gift',
				'name'    => __( 'Bonus Gift', 'gmt_edd' ),
				'desc'    => __( 'Product to add as a bonus gift', 'gmt_edd' ),
				'type'    => 'select',
				'std'     => 'none',
				'options' => gmt_edd_custom_get_all_downloads(),
			),

			// Bonus products
			array(
				'id'      => 'gmt_edd_custom_bonus_eligible_products',
				'name'    => __( 'Bonus Eligible Products', 'gmt_edd' ),
				'desc'    => __( 'Products that are eligible for bonus gifts', 'gmt_edd' ),
				'type'    => 'multicheck',
				'std'     => 'none',
				'options' => gmt_edd_custom_get_all_downloads(),
			),

		);

		if ( version_compare( EDD_VERSION, 2.5, '>=' ) ) {
			$custom_settings = array( 'gmt_edd_custom' => $custom_settings );
		}

		return array_merge( $settings, $custom_settings );

	}
	add_filter( 'edd_settings_extensions', 'gmt_edd_custom_settings', 999, 1 );