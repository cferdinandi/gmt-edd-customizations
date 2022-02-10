<?php

	/**
	 * @section Add company field to checkout form
	 * @deprecated
	 */

	/**
	 * Display company name field at checkout
	 */
	function gmt_edd_custom_display_company_name_field() {
	?>
		<p id="edd-company-wrap">
			<label class="edd-label" for="edd-company"><strong><?php _e('Company Name', 'edd'); ?></strong> <span class="text-muted text-normal">(<?php _e('Optional', 'edd'); ?>)</span></label>
			<input class="edd-input" type="text" name="edd_company" id="edd-company" placeholder="<?php _e('Company name', 'edd'); ?>" />
		</p>
		<?php
	}
	add_action( 'edd_purchase_form_user_info', 'gmt_edd_custom_display_company_name_field' );



	/**
	 * Store the custom field data into EDD's payment meta
	 */
	function gmt_edd_custom_store_company_name_field( $payment_meta ) {

		if ( 0 !== did_action('edd_pre_process_purchase') ) {
			$payment_meta['company'] = isset( $_POST['edd_company'] ) ? sanitize_text_field( $_POST['edd_company'] ) : '';
		}

		return $payment_meta;
	}
	add_filter( 'edd_payment_meta', 'gmt_edd_custom_store_company_name_field');



	/**
	 * Add the company name to the "View Order Details" page
	 */
	function gmt_edd_custom_add_company_to_view_order_details( $payment_meta, $user_info ) {
		$company = isset( $payment_meta['company'] ) ? $payment_meta['company'] : 'none';
	?>
		<div class="column-container">
			<div class="column">
				<strong>Company Name: </strong>
				 <?php echo $company; ?>
			</div>
		</div>
	<?php
	}
	add_action( 'edd_payment_personal_details_list', 'gmt_edd_custom_add_company_to_view_order_details', 10, 2 );



	/**
	 * The {company} email tag
	 */
	function gmt_edd_custom_email_tag_company( $payment_id ) {
		$payment_data = edd_get_payment_meta( $payment_id );
		return $payment_data['company'];
	}



	/**
	 * Add a {company} tag for use in either the purchase receipt email or admin notification emails
	 */
	function gmt_edd_custom_add_company_name_email_tag() {
		edd_add_email_tag( 'company', 'Company name', 'gmt_edd_custom_email_tag_company' );
	}
	add_action( 'edd_add_email_tags', 'gmt_edd_custom_add_company_name_email_tag' );