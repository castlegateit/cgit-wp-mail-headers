<?php

/**
 * Plugin Name: Castlegate IT WP Mail Headers
 * Plugin URI:  https://github.com/castlegateit/cgit-wp-mail-headers
 * Description: Set default WordPress mail headers.
 * Version:     1.0.0
 * Author:      Castlegate IT
 * Author URI:  https://www.castlegateit.co.uk/
 * License:     MIT
 * Update URI:  https://github.com/castlegateit/cgit-wp-mail-headers
 */

use Castlegate\MailHeaders\Plugin;

if (!defined('ABSPATH')) {
    wp_die('Access denied');
}

define('CGIT_WP_MAIL_HEADERS_VERSION', '1.0.0');
define('CGIT_WP_MAIL_HEADERS_PLUGIN_FILE', __FILE__);
define('CGIT_WP_MAIL_HEADERS_PLUGIN_DIR', __DIR__);

require_once __DIR__ . '/vendor/autoload.php';

Plugin::init();
