<?php
/**
 * Plugin Name: Audiolize Article
 * Plugin URI: http://www.audiolize.net
 * Description: Automatically injects audiolize functionality in wordpress articles.
 * Version: 0.1
 * Author: audiolize
 * Author URI: http://www.audiolize.net
 */

if( !defined( 'ABSPATH' ) )
	exit;

/* 
 * adds audiolize_widget function to tamplates scope
 */

function audiolize_widget(){
	do_action('audiolize_widget');
}

class AudiolizeWidget {
	private $theme_choices = array(
		'full' => 'full',
		'wide' => 'wide',
		'minimal' => 'minimal',
	);
	
	function settings_menu(){
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		add_submenu_page('options-general.php', 'Audiolize widget', 'Audiolize widget', 'manage_options', 'audiolize_widget_options', array($this, 'settings_page' ));
	} 
	
	function __construct(){
		add_option('audiolize_widget_theme', 'full', '', 'yes');
		add_action('audiolize_widget', array( $this, 'inject_audiolize_widget'));
		add_action('admin_menu', array( $this, 'settings_menu' ) );
	}
	
	function inject_audiolize_widget(){
		$theme = get_option('audiolize_widget_theme', 'full');
		$article_url = get_permalink();
		$widget_html = <<<HTML
		<script id="audiolize-widgets-loader" type="text/javascript" src="https://dev.audiolize.net/static/js/widgets/loader.js" data-audiolize-theme="$theme" data-audiolize-domain="https://dev.audiolize.net"></script>
	<div data-audiolize-article_canonical_url="$article_url" data-audiolize-article_version="1.0"></div>
HTML;
		echo $widget_html;
	}

	function settings_page(){
		if ( isset( $_POST['submit'] ) ) {
			update_option('audiolize_widget_theme', $_POST['audiolize_widget_theme']);
		}
		$theme = get_option('audiolize_widget_theme', 'full');
		$html = <<<HTML
		<form method="post" action="" id="audiolize_widget_options_form">
			<table>
					<tr>
						<td>
							<label for="audiolize_widget_theme">Audiolize widget theme (minimal, wide, full)</label>			
						</td>
						<td>
							<input name="audiolize_widget_theme" type="text" id="audiolize_widget_theme" value="$theme" class="text">
							<!--
							<select name="audiolize_widget_theme" id="audiolize_widget_theme">
								<option value='minimal'>minimal</option>
								<option value='wide'>wide</option>
								<option value='full'>full</option>
							</select>
							-->
						</td>
					</tr>
			</table>
			<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
		</form>
HTML;
		echo $html;
	}
}
new AudiolizeWidget();
?>