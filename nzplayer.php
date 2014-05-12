<?php
/*
 * Plugin Name: NZPlayer
 * Plugin URI: https://github.com/oxnz/NZPlayer
 * Description: A beautiful music player
 * Author: Oxnz
 * Version: 1.0
 * Author URI: http://xinyi.sourceforge.net
 * License: GPLv2 or later
 */
/*
 * Copyright (c) 2014 oxnz, All Rights Reserved.
 */

define('NZPLYAER_VERSION', '1.0.1');
define('NZPLAYER_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once dirname(__FILE__) . '/widget.php';

if (is_admin())
	require_once dirname(__FILE__) . '/admin.php';

function nzplayer_init() {
	// do init
}
add_action('init', 'nzplayer_init');

function nzplayer_activate() {
}
function nzplayer_deactivate() {
}

register_activation_hook(__FILE__, 'nzplayer_activate');
register_deactivation_hook(__FILE__, 'nzplayer_deactivate');
