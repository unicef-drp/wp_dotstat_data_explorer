<?php

/**
 * Plugin Name: Data Explorer
 * Description: A dotstat data explorer wrapper.
 * Version: 1.0.1
 * Author: Daniele Olivotti
 */


/* ------------------------------------------------------------------------- *
*   CUSTOM POST TYPE DataExplorer
/* ------------------------------------------------------------------------- */

//Create custom post type/endpoints
add_action('init', 'de_create_data_explorer');

function de_create_data_explorer()
{
	$labels = array(
		'name'               => __('Data Explorers', 'data_explorer'),
		'singular_name'      => __('Data Explorer', 'data_explorer'),
		'add_new'            => __('Add New', 'data_explorer'),
		'add_new_item'       => __('Add New Endpoint', 'data_explorer'),
		'edit_item'          => __('Edit Endpoint', 'data_explorer'),
		'new_item'           => __('New Endpoint', 'data_explorer'),
		'all_items'          => __('All Endpoints', 'data_explorer'),
		'view_item'          => __('View Endpoint', 'data_explorer'),
		'search_items'       => __('Search Endpoints', 'data_explorer'),
		'not_found'          => __('Endpoint not found', 'data_explorer'),
		'not_found_in_trash' => __('Endpoint not found in the trash', 'data_explorer'),
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'menu_position' => 22,
		'supports' => array('title'),
		'taxonomies' => array(''),
		'menu_icon' => 'dashicons-chart-bar',
		'has_archive' => false
	);

	register_post_type('data_explorer', $args);
}

//Add settings
add_action('admin_init', 'de_add_metabox');

function de_add_metabox()
{
	add_meta_box(
		'data_explorer_meta_box',
		'Data explorer details',
		'de_display_meta_box',
		'data_explorer'
	);
}

function de_display_meta_box($de)
{
	$de_config_id=get_post_meta($de->ID, 'de_config_id', true);
	$de_page_title=get_post_meta($de->ID, 'de_page_title', true);
?>
<!--<div><a href="http://localhost/de-configurator/">Configurator</a></div>-->

	<table>
		<tr>
			<td>Page title</td>
			<td>​<input style="width: 300px" name="de_page_title" type="text" value="<?php echo $de_page_title; ?>"></input></td>
			<td>The title of the page</td>
		</tr>
		<tr>
			<td>Configuration ID</td>
			<td>​<input style="width: 300px" name="de_config_id" type="text" value="<?php echo $de_config_id; ?>"></input></td>
			<td>The configuration ID</td>
		</tr>
	</table>
<?php
}

//Save from settings fields
add_action('save_post', 'de_add_fields', 10, 2);

function de_add_fields($de_id, $de_fields)
{
	// Check post type for data explorers
	if ($de_fields->post_type == 'data_explorer') {
		// Store data in post meta table if present in post data
		if (isset($_POST['de_page_title']) && $_POST['de_page_title'] != '') {
			update_post_meta($de_id, 'de_page_title', $_POST['de_page_title']);
		}
		if (isset($_POST['de_config_id']) && $_POST['de_config_id'] != '') {
			update_post_meta($de_id, 'de_config_id', $_POST['de_config_id']);
		}
	}
}

//Adds the page template from the plugin's path
add_filter('single_template', 'de_data_explorer_template');

function de_data_explorer_template($single)
{
	global $post;

	$deSinglePath = plugin_dir_path(__FILE__) . 'single-data_explorer.php';
	//Add if post type is the right one
	if ($post->post_type == 'data_explorer') {
		if (file_exists($deSinglePath)) {
			return $deSinglePath;
		}
	}
	return $single;
}

//Gets the query params
add_filter('query_vars', 'de_custom_query_vars_filter');

function de_custom_query_vars_filter($vars)
{
	$vars[] .= 'ag';
	$vars[] .= 'df';
	$vars[] .= 'ver';
	$vars[] .= 'dq';
	$vars[] .= 'startPeriod';
	$vars[] .= 'endPeriod';
	$vars[] .= 'lastnobservations';

	return $vars;
}



/*Plugin's settings*/
function de_settings_page()
{
	add_submenu_page(
		'options-general.php', // top level menu page
		'Data Explorer Settings Page', // title of the settings page
		'Data Explorer', // title of the submenu
		'manage_options', // capability of the user to see this page
		'de-settings-page', // slug of the settings page
		'de_settings_page_html' // callback function when rendering the page
	);
	add_action('admin_init', 'de_settings_init');
}
add_action('admin_menu', 'de_settings_page');

function de_settings_page_html()
{
	// check user capabilities
	/*if (!current_user_can('manage_options')) {
		return;
	} */ ?>

	<div class="wrap">
		<?php settings_errors(); ?>
		<form method="POST" action="options.php">
			<?php settings_fields('de-settings-page'); ?>
			<?php do_settings_sections('de-settings-page') ?>
			<?php submit_button(); ?>
		</form>
	</div>
<?php
}


/*Groups together options, I need just one group for this plugin*/
function de_settings_init()
{
	add_settings_section(
		'de-settings-section', // id of the section
		'Data Explorer Settings', // title to be displayed
		'', // callback function to be called when opening section, currently empty
		'de-settings-page' // page on which to display the section
	);


	// register the setting (option group, setting id)
	register_setting('de-settings-page', 'de_remote_files_url');
	//id of the settings field, title, callback function, page on which settings display, section on which to show settings
	add_settings_field('de_remote_files_url', 'Remote files URL', 'de_remote_files_url_callb', 'de-settings-page', 'de-settings-section');
}

function de_remote_files_url_callb()
{
	$de_remote_files_url = esc_attr(get_option('de_remote_files_url', ''));
	echo ("<div><input id=\"de_remote_files_url\" type=\"text\" name=\"de_remote_files_url\" size=\"80\" value=\"" . $de_remote_files_url . "\"></div>");
}
?>