<?php
/**
 * @package NZPlayer
 */

require_once(dirname(__FILE__) . '/nzutils.php');

class NZPlayer_Widget extends WP_Widget {
	function __construct() {
		parent::__construct('nzplayer', __('NZPlayer', 'wpbootstrap'), array(
			'classname' => 'nzplayer',
			'description' => __('A beautiful player widget', 'wpbootstrap'),
		));
		$this->title = __('NZPlayer', 'wpbootstrap');
		$upload_dir = wp_upload_dir();
		if (! $upload_dir['error']) {
			$this->playlists = $this->findmedia($upload_dir['basedir'],
				$upload_dir['baseurl']);
			$this->index = 0;
		}

		if (is_active_widget(false, false, $this->id_base)) {
			add_action('wp_enqueue_scripts', array($this, 'scripts'));
			add_action('wp_ajax_nopriv_nzplayer_ctl', array($this, 'controll'));
			add_action('wp_ajax_nzplayer_ctl', array($this, 'controll'));
		}
	}

	function scripts() {
		wp_enqueue_style( 'nzplayer-widget', NZPLAYER_PLUGIN_URL . 'widget.css');
		wp_enqueue_script( 'nzplayer-widget', NZPLAYER_PLUGIN_URL . 'widget.js', array('jquery'), '1.0.0' );
		wp_localize_script( 'nzplayer-widget', 'NZPlayerAjax', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'playlist' => $this->playlist(0),
	   	));
	}

	function findmedia($path, $url) {
		$flist = nzfind(array('path' => $path, 'suffix' => 'mp3',
			'recursive' => true));
		$flist = nzlist(array('ftree' => $flist, 'depth' => 2,
			'ignore' => false));
		$playlists = array();
		$pathlen = strlen($path);
		foreach ($flist as $ykey => $yval) {
			foreach ($yval as $mkey => $mval) {
				foreach ($mval as $k => $m) { // transform abs path to url
					$minfo = mediainfo($m);
					//print_r($minfo);
					$mval[$k] = array(
						'source' => $url . substr($m, $pathlen),
						'cover'	=> $url . substr($minfo['cover'], $pathlen),
						'title' => $minfo['title'],
						'artist' => $minfo['artist'],
						'album' => $minfo['album'],
						'id'	=> $minfo['trackid'],
					);
					//$mval[$k] = $url . substr($m, $pathlen);
				}
				array_push($playlists, $mval);
			}
		}
		//print_r($playlists);
		return $playlists;
	}

	function playlist($index) {
		$this->index += $index;
		if (0 > $this->index) { // -1, go back to the last song
			$this->index += count($this->playlists);
		} else if (count($this->playlists) == $this->index) { // last, rewind
			$this->index = 0;
		}
		return $this->playlists[$this->index];
	}

	function controll() {
		switch ($_REQUEST['option']) {
		case "prev":
			echo json_encode($this->playlist(-1));
			break;
		case "next":
			echo json_encode($this->playlist(1));
			break;
		default:
			echo "default";
			break;
		}
		die(0);
	}

	function form($instance) {
		if ($instance) {
			$title = esc_attr($instance['title']);
		} else {
			$title = __('NZPlayer');
		}
		$libdir = "use the wp_upload_dir";
?>
<p>
<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?>
</label>
	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
</label>
</p>
<p>
<label for="<?php echo $this->get_field_id('libdir'); ?>"><?php _e('Library Path:'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('libdir'); ?>" name="<?php echo $this->get_field_name('libdir'); ?>" type="text" value="<?php echo esc_attr($libdir); ?>" />
</p>
<label for="<?php echo $this->get_field_id('default-cover'); ?>"><?php _e('Default Cover File Path:'); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('default-cover'); ?>" name="<?php echo $this->get_field_name('default-cover'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
<p>
</p>
<p>
<input class="checkbox" type="checkbox" id="" name="">
<label for="<?php echo $this->get_field_id('autoplay'); ?>"><?php _e('Autoplay:'); ?></label>
</p>
<?php
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['playlists'] = $new_instance['playlists'];
		$instance['index'] = $new_instance['index'];
		return $instance;
	}

	function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', empty($instance['title']) ?
			__('NZPlayer', 'wpbootstrap') : $instance['title'], $instance,
				$this->id_base);
		echo $before_widget . $before_title . $title . $after_title;
		$volume = array(
			'id'	=> 'volume',
			'class'	=> 'barval volume',
		);
		$volbar = '<div id="' . $volume['id']
			. '" class="' . $volume['class'] . '"></div>';
		$buttons = array(
			'backward'	=> '<span class="fa fa-backward fa-lg fa-fw"></span>',
			'play'		=> '<span class="fa fa-pause fa-lg fa-fw"></span>',
			'forward'	=> '<span class="fa fa-forward fa-lg fa-fw"></span>',
			'mode'		=> '<span class="fa fa-repeat fa-lg fa-fw"></span>',
			'mute'		=> '<span class="fa fa-volume-up fa-lg fa-fw"></span>',
			'volbar'	=> $volbar,
			'heart'		=> '<span class="fa fa-heart fa-lg fa-fw"></span>'
		);
		foreach ($buttons as $class => $content) {
			$toolbar .= '<div id="' . $class . '" class="button ' . $class . '">' . $content . '</div>';
		}
		$toolbar = '<div class="toolbar" id="toolbar">' . $toolbar . '</div>';
?>
		<div id="nzplayer" class="original">
			<div class="track">
				<div class="cover"><img id="cover" class="cover-pic"></div>
				<div class="info">
					<ul>
						<li id="track-title">No music available</li>
						<li><span class="nzlabel">Artist: </span>
							<span id="track-artist">Nobody</span>
						</li>
						<li>
							<span class="nzlabel">Album: </span>
							<span id="track-album">Nil collection</span>
						</li>
						<li><span class="nzlabel">No. </span>
							<span id="track-id">007</span>
						<li id="times">
							<span id="curtime">loading:</span>
							<span id="timesep"></span>
							<span id="duration"><span class="fa fa-spinner fa-spin"></span></span></li>
						</li>
					</ul>
				</div><!--.info-->
			</div><!--.track-->
			<div id="nzplayer-controller-wrapper">
				<div id="nzplayer-controller" class="controller">
					<?php echo $toolbar; ?>
					<div id="seekbar" class="seekbar progbar">
						<div id="position" class="barval position"></div>
					</div>
				</div><!--.controller-->
			</div><!-- #controller-wrapper -->
		</div><!--.player-->
<?php
		echo $after_widget;
	}
}

function nzplayer_register_widgets() {
	register_widget('NZPlayer_Widget');
}

add_action('widgets_init', 'nzplayer_register_widgets');
