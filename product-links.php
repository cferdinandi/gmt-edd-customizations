<?php


	/**
	 * Adds a link to the product description page in checkout cart
	 * @param  array  $item The item
	 * @return string       A link back to the item description
	 */
	function gmt_edd_custom_add_links_to_cart_items( $item ) {

		// Get the link
		$link = get_post_meta( $item['id'], 'gmt_edd_product_page_link', true );
		if ( empty( $link ) ) return;

		// Set the display text
		$text = edd_get_option('gmt_edd_custom_product_link_text');
		$text = apply_filters( 'gmt_edd_get_link_to_product_text', $text );

		echo ' <span class="gmt-edd-link-to-product-in-cart">(<a href="' . esc_url_raw( $link ) . '">' . $text . '</a>)</span>';
	}
	add_action( 'edd_checkout_cart_item_title_after', 'gmt_edd_custom_add_links_to_cart_items' );



	/**
	 * Create the product page link metabox
	 */
	function gmt_edd_custom_create_product_page_link_metabox() {
		add_meta_box( 'gmt_edd_product_page_link_metabox', __( 'Download Page Link', 'gmt_edd' ), 'gmt_edd_render_product_page_link_metabox', 'download', 'side', 'default');
	}
	add_action( 'add_meta_boxes', 'gmt_edd_custom_create_product_page_link_metabox' );



	/**
	 * Render the product page link metabox
	 */
	function gmt_edd_custom_render_product_page_link_metabox() {

		// Variables
		global $post;

		?>

			<fieldset>

				<input type="url" name="gmt_edd_product_page_link" class="widefat" id="gmt_edd_product_page_link" value="<?php echo esc_attr( get_post_meta( $post->ID, 'gmt_edd_product_page_link', true ) ); ?>">
				<label for="gmt_edd_product_page_link"><?php _e( 'A link for the download description page', 'gmt_edd' ); ?></label>

			</fieldset>

		<?php
		wp_nonce_field( 'gmt_edd_custom_product_page_link_metabox_nonce', 'gmt_edd_custom_product_page_link_metabox_process' );

	}



	/**
	 * Save the product page link metabox
	 * @param  Number $post_id The post ID
	 * @param  Array  $post    The post data
	 */
	function gmt_edd_custom_save_product_page_link_metabox( $post_id, $post ) {

		if ( !isset( $_POST['gmt_edd_custom_product_page_link_metabox_process'] ) ) return;

		// Verify data came from edit screen
		if ( !wp_verify_nonce( $_POST['gmt_edd_custom_product_page_link_metabox_process'], 'gmt_edd_custom_product_page_link_metabox_nonce' ) ) {
			return $post->ID;
		}

		// Verify user has permission to edit post
		if ( !current_user_can( 'edit_post', $post->ID )) {
			return $post->ID;
		}

		// Make sure data was provided
		if ( !isset( $_POST['gmt_edd_product_page_link'] ) ) return;

		// Update the data
		update_post_meta( $post->ID, 'gmt_edd_product_page_link', wp_filter_nohtml_kses( $_POST['gmt_edd_product_page_link'] ) );

	}
	add_action( 'save_post', 'gmt_edd_custom_save_product_page_link_metabox', 1, 2 );



	/**
	 * Redirect away from the product if a custom sales page for it exists
	 */
	function gmt_edd_custom_product_link_redirect() {

		// Don't run on admin or if not a download page
		if ( is_admin() || get_post_type() !== 'download' ) return;

		// Variables
		global $post;
		$link = get_post_meta( $post->ID, 'gmt_edd_product_page_link', true );

		// If no link, bail
		if ( empty($link) ) return;

		// Otherwise, redirect
		wp_redirect( $link, 301 );

	}
	add_action( 'wp', 'gmt_edd_custom_product_link_redirect' );