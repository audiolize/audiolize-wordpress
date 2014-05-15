<?php
/**
 * Plugin Name:     audiolize.net
 * Plugin URI:      https://github.com/audiolize/audiolize-wordpress
 * Description:     Automatically injects audiolize.net functionality in wordpress articles.
 * Version:         1.0
 * Author:          audiolize GmbH
 * Author URI:      http://www.audiolize.net
 */

if( !defined( 'ABSPATH' ) )
	exit;

/* 
 * adds audiolize_widget function to templates scope
 */

function audiolize_widget() {
	do_action('audiolize_widget');
}

class AudiolizeWidget {
	private $themes = array(
            "minimal" => "Minimal theme",
            "wide" => "Wide theme",
            "full" => "Full theme",
            "tiny" => "Tiny theme"
    );
    
    private $widget_host = 'https://dev.audiolize.net';
	
	function settings_menu() {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		add_submenu_page(
		    'options-general.php',
		    'audiolize.net',
		    'audiolize.net',
		    'manage_options',
		    'audiolize_widget_options',
		    array($this, 'settings_page' )
        );
	}
	
	function __construct() {
		add_option('audiolize_widget_theme', 'tiny', '', 'yes');
		add_action('audiolize_widget', array( $this, 'inject_audiolize_widget'));
		add_action('admin_menu', array( $this, 'settings_menu' ) );
	}
	
	function inject_audiolize_widget() {
		$theme = get_option('audiolize_widget_theme', 'tiny');
		$article_url = get_permalink();
		$widget_html = <<<HTML
    		<script id="audiolize-widgets-loader" type="text/javascript" src="$widget_host/static/js/widgets/loader.js" data-audiolize-theme="$theme" data-audiolize-domain="$widget_host"></script>
	        <div data-audiolize-article_canonical_url="$article_url" data-audiolize-article_version="1.0"></div>
HTML;
		echo $widget_html;
	}

	function settings_page() {
		if ( isset( $_POST['submit'] ) ) {
			update_option('audiolize_widget_theme', $_POST['audiolize_widget_theme']);
		}
		
        $options = "";

        foreach ($themes as $key => $value) {
    		$selected = ($key == get_option('audiolize_widget_theme', 'tiny') ? ' selected="selected"' : '');
    		$options .= "<option value='$key' $selected>$value</option>";
        }
		
		$html = <<<HTML
		<form method="post" action="" id="audiolize_widget_options_form">
			<table>
				<tr>
					<td><label for="audiolize_widget_theme">Audiolize widget theme</label>:</td>
					<td><select name="audiolize_widget_theme" id="audiolize_widget_theme">$options</select></td>
				</tr>
			</table>
			<p class="submit">
			    <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
			</p>
		</form>
HTML;
		echo $html;
	}
}

new AudiolizeWidget();

?>

