<?php

/**
 * Plugin Name: GMT EDD Customizations
 * Plugin URI: https://github.com/cferdinandi/gmt-edd-customizations/
 * GitHub Plugin URI: https://github.com/cferdinandi/gmt-edd-customizations/
 * Description: Customizations to Easy Digital Downloads for Go Make Things.
 * Version: 1.4.5
 * Author: Chris Ferdinandi
 * Author URI: http://gomakethings.com
 * License: GPLv3
 */

// Security
if (!defined('ABSPATH')) exit;

// Require files
require_once('settings.php');
require_once('cart.php');
require_once('recurring.php');
require_once('product-links.php');
require_once('bonus-gifts.php');
require_once('email-tags.php');