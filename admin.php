<?php
add_action('admin_menu', 'nzplayer_admin_menu');

function nzplayer_admin_page() {
?>
<div class="wrap">
	<?php screen_icon(); ?>
	<h2>NZPlayer</h2>
		<p><?php _e('NZPlayer is a music player plugin which supplies a music player widget. You need to setup music library before using.'); ?></p>
	<form name="nzplayer" action="" method="POST" id="nzplayer-form">
<label for="music">Music:</label>
<input type="text" name="music" />
<input type="submit" class="button button-primary" value="<?php esc_attr_e('Save'); ?>" />
	</form>
</div>
<?php
}

function nzplayer_admin_menu() {
	add_submenu_page('plugins.php', 'NZPlyaer', 'NZPlayer', 'manage_options',
		'nzplayer', 'nzplayer_admin_page');
}
