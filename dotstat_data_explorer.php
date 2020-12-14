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
	$de_page_title = get_post_meta($de->ID, 'de_page_title', true);
	$api_url = get_post_meta($de->ID, 'api_url', true);
	$backtype_radio_value = get_post_meta($de->ID, 'backtype_radio_value', true);
	$de_hierarchy_cfg = get_post_meta($de->ID, 'de_hierarchy_cfg', true);
	$de_forced_dims = get_post_meta($de->ID, 'de_forced_dims', true);
	$de_hide_total_labels = get_post_meta($de->ID, 'de_hide_total_labels', true);
?>
<div><a href="http://localhost/de-configurator/">Configurator</a></div>

	<table>
		<tr>
			<td>Page title</td>
			<td>​<input style="width: 500px" name="de_page_title" type="text" value="<?php echo $de_page_title; ?>"></input></td>
			<td>The page title</td>
		</tr>
		<tr>
			<td>API URL</td>
			<td>​<textarea name="de_api_url_name" rows="10" cols="80"><?php echo $api_url; ?></textarea></td>
			<td>e.g: {fusion: { url: "https://sdmx.data.unicef.org/ws/public/sdmxapi/rest",
				hasRangeHeader: !0,
				supportsReferencePartial: !1
				}}
			</td>
		</tr>
		<tr>
			<td>Backend</td>
			<td>
				<label>
					<input type="radio" name="de_backtype_radio_value" value="DOTSTAT" <?php checked($backtype_radio_value, 'DOTSTAT'); ?>>
					<?php esc_attr_e('DOTSTAT'); ?>
				</label>
				<br />
				<label>
					<input type="radio" name="de_backtype_radio_value" value="FUSION" <?php checked($backtype_radio_value, 'FUSION'); ?>>
					<?php esc_attr_e('FUSION'); ?>
				</label>
			</td>
			<td></td>
		</tr>
		<tr>
			<td>Hierarchy</td>
			<td>​<textarea name="de_hierarchy_cfg" rows="10" cols="80"><?php echo $de_hierarchy_cfg; ?></textarea></td>
			<td>e.g: {agencyId:"UNICEF", id:"REGIONS_HIERARCHY"}</td>
		</tr>
		<tr>
			<td>Forced dims</td>
			<td>​<textarea name="de_forced_dims" rows="10" cols="80"><?php echo $de_forced_dims; ?></textarea></td>
			<td>e.g: {REF_AREA:"AGF"}</td>
		</tr>
		<tr>
			<td>Hide Total label</td>
			<td><input name="de_hide_total_labels" type="checkbox" <?php if ($de_hide_total_labels=='on'){echo("checked");} ?>></td>
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

		// Store data in post meta table if present in post data
		if (isset($_POST['de_api_url_name']) && $_POST['de_api_url_name'] != '') {
			update_post_meta($de_id, 'api_url', $_POST['de_api_url_name']);
		}

		if (isset($_POST['de_backtype_radio_value'])) { // Input var okay.
			update_post_meta($de_id, 'backtype_radio_value', sanitize_text_field(wp_unslash($_POST['de_backtype_radio_value']))); // Input var okay.
		} else {
			update_post_meta($de_id, 'backtype_radio_value', 'DOTSTAT');
		}

		// Store data in post meta table if present in post data
		if (isset($_POST['de_hierarchy_cfg'])) {
			update_post_meta($de_id, 'de_hierarchy_cfg', $_POST['de_hierarchy_cfg']);
		}
		if (isset($_POST['de_forced_dims'])) {
			update_post_meta($de_id, 'de_forced_dims', $_POST['de_forced_dims']);
		} else {
			update_post_meta($de_id, 'de_forced_dims', "");
		}

		if (isset($_POST['de_hide_total_labels'])) { // Input var okay.
			update_post_meta($de_id, 'de_hide_total_labels', esc_html($_POST['de_hide_total_labels'])); // Input var okay.
		} else {
			update_post_meta($de_id, 'de_hide_total_labels', '');
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
	register_setting('de-settings-page', 'de_indicator_profile_url');
	register_setting('de-settings-page', 'de_help_url');
	//id of the settings field, title, callback function, page on which settings display, section on which to show settings
	add_settings_field('de_indicator_profile_url', 'Indicator profile URL', 'de_indicator_profile_url_callb', 'de-settings-page', 'de-settings-section');
	add_settings_field('de_help_url', 'Help URL', 'de_help_url_callb', 'de-settings-page', 'de-settings-section');
}

function de_indicator_profile_url_callb()
{
	$de_indicator_profile_url = esc_attr(get_option('de_indicator_profile_url', ''));
	echo ("<div><input id=\"de_indicator_profile_url\" type=\"text\" name=\"de_indicator_profile_url\" size=\"100\" value=\"" . $de_indicator_profile_url . "\"></div>");
}
function de_help_url_callb()
{
	$de_help_url = esc_attr(get_option('de_help_url', ''));
	echo ("<div><input id=\"de_help_url\" type=\"text\" name=\"de_help_url\" size=\"80\" value=\"" . $de_help_url . "\"></div>");
}
?>