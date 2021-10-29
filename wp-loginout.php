<?php
/**
 * @package WP-LogInOut
 * @version 0.1.5
 */
/*
Plugin Name: WP LogInOut
Plugin URI: http://wordpress.org/plugins/wp-loginout/
Description: Goto: Appearance > WP LoginOut. Add login / out buttons in selected menu automatically depending upon users login status.
Author: Nabtron
Tested up to: 5.8.1
Version: 0.1.5
Author URI: https://nabtron.com/
*/

//add submenu item to themes menu in admin panel for WP LoginOut
add_action('admin_menu', 'wp_loginout_menu');
function wp_loginout_menu() {
	add_theme_page('WP LogInOut', 'WP LoginOut', 'edit_theme_options', 'wp_loginout_options', 'wp_loginout_options');
}

function wp_loginout_options(){
    //must check that the user has the required capability 
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }
		
		$hidden_field_name = 'nab_wp_loginout_h';

		//get options value if saved
		$nab_menu_location = get_option('nab_menu_location');
		$nab_ll_before = get_option('nab_ll_before');
		$nab_ll_after = get_option('nab_ll_after');

		//update options value if submitted
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == '1' ) {
			$nab_menu_location = esc_html($_POST['nab_menu_location']);
			$nab_ll_before = esc_html($_POST['nab_ll_before']);
			$nab_ll_after = esc_html($_POST['nab_ll_after']);
			update_option( 'nab_menu_location', $nab_menu_location); 
			update_option( 'nab_ll_before', $nab_ll_before); 
			update_option( 'nab_ll_after', $nab_ll_after); 
			
			//echo if settings are saved
			echo '<div class="updated"><p><strong>settings saved</strong></p></div>';
		}
		
	  //displaying settings on admin page	
?>

		<div class="wrap">
    	<h2>WP LogInOut Settings</h2>
      
			<form name="wp_loginout_form" method="post" action="">
				<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="1">
        <ul class="nab_ll_ul">
				<li><label class="nab_ll_class" for="nab_menu_location">Menu theme location:</label>
					<input type="text" id="nab_menu_location" name="nab_menu_location" value="<?php echo stripslashes($nab_menu_location); ?>" size="20">
          <span class="description">name of the menu which you want to be extended by this plugin</span>
				</li>
				<li><label class="nab_ll_class" for="nab_ll_before">Code Before link:</label>
					<input type="text" id="nab_ll_before" name="nab_ll_before" value="<?php echo stripslashes($nab_ll_before); ?>" size="20">
          <span class="description">e.g. &lt;li class="someclass"&gt;</span>
				</li>
				<li><label class="nab_ll_class" for="nab_ll_after">Code After link :</label>
					<input type="text" id="nab_ll_after" name="nab_ll_after" value="<?php echo stripslashes($nab_ll_after); ?>" size="20">
          <span class="description">e.g &lt;/li&gt;</span>
				</li>
        </ul>
        <hr />
				<p class="submit"><input type="submit" name="Submit" class="button-primary" value="Save Changes" /></p>
			</form>
		</div>
    <style type="text/css">
		label.nab_ll_class{width:200px;display:inline-block;font-weight:bold;font-size:1.2em;}
		.nab_ll_ul li{padding:10px;}
		.nab_ll_ul input{width: 400px;padding:5px;font-family:"Courier New", Courier, monospace;}
		</style>

    <hr />
    <center>Developed by <a href="http://nabtron.com/" target="_blank">Nabtron</a></center>  
<?php
}

//function to get custom location chosen menu name (to hook it)
function get_nab_menu_location_function() {
		$nab_menu_location = get_option('nab_menu_location');
		$nab_menu_location_function = 'wp_nav_menu_'.$nab_menu_location.'_items';
		return $nab_menu_location_function;
}

//add_filter( 'wp_nav_menu', 'your_custom_menu_item', 10, 2 );
function your_custom_menu_item ( $items, $args ) {
	print_r($args);
    if ($args->theme_location == 'secondary') {
        $items .= '<li>Show whatever</li>';
    }
    return $items;
}

//get the chosen menu location and then add custom code to it
//gets something like wp_nav_menu_secondary_items where secondary is the theme location for custom menu
add_filter( get_nab_menu_location_function(),'wpsites_loginout_menu_link' );

function wpsites_loginout_menu_link( $menu ) {
    $loginout = stripslashes(htmlspecialchars_decode(get_option('nab_ll_before'))) . wp_loginout($_SERVER['REQUEST_URI'], false ) . stripslashes(htmlspecialchars_decode(get_option('nab_ll_after')));
    $menu .= $loginout;
    return $menu;
}

// get all menu items created in the wp admin panel menus tab
function wp_loginout_get_all_menus(){
    return get_terms( 'nav_menu', array( 'hide_empty' => true ) ); 
}

// add settings link
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'wploginout_add_plugin_page_settings_link');
function wploginout_add_plugin_page_settings_link( $links ) {
	$links[] = '<a href="' .
		admin_url( 'themes.php?page=wp_loginout_options' ) .
		'">' . __('Settings') . '</a>';
	return $links;
}
