<?php

/**
 * Plugin Name: data_explorer
 * Description: A dotstat data explorer wrapper.
 * Version: 1.0
 * Author: Daniele Olivotti
 */


/* ------------------------------------------------------------------------- *
*   CUSTOM POST TYPE DataExplorer
/* ------------------------------------------------------------------------- */

add_action('init', 'create_data_explorer');

function create_data_explorer()
{
	$labels = array(
		'name'               => __('Data explorers', 'data_explorer'),
		'singular_name'      => __('Data explorer', 'data_explorer'),
		'add_new'            => __('Add new', 'data_explorer'),
		'add_new_item'       => __('Add New de', 'data_explorer'),
		'edit_item'          => __('Edit de', 'data_explorer'),
		'new_item'           => __('New de', 'data_explorer'),
		'all_items'          => __('All de', 'data_explorer'),
		'view_item'          => __('View de', 'data_explorer'),
		'search_items'       => __('Search de', 'data_explorer'),
		'not_found'          => __('De not found', 'data_explorer'),
		'not_found_in_trash' => __('De not found in the trash', 'data_explorer'),
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

add_action('admin_init', 'dotstat_data_explorer_add_metabox');
function dotstat_data_explorer_add_metabox()
{
	add_meta_box(
		'data_explorer_meta_box',
		'Data explorer details',
		'display_de_meta_box',
		'data_explorer'
	);
}

?>

<?php
function display_de_meta_box($de)
{
	$api_url = get_post_meta($de->ID, 'api_url', true);
	$backtype_radio_value = get_post_meta($de->ID, 'backtype_radio_value', true);
	?>

	<table>
		<tr>
			<td>API URL</td>
			<td>​<textarea name="api_url_name" rows="10" cols="80"><?php echo $api_url; ?></textarea></td>
		</tr>

		<tr>
			<td>Backend</td>
			<td>
				<label>
					<input type="radio" name="backtype_radio_value" value="DOTSTAT" <?php checked($backtype_radio_value, 'DOTSTAT'); ?>>
					<?php esc_attr_e('DOTSTAT'); ?>
				</label>
				<br />
				<label>
					<input type="radio" name="backtype_radio_value" value="FUSION" <?php checked($backtype_radio_value, 'FUSION'); ?>>
					<?php esc_attr_e('FUSION'); ?>
				</label>
			</td>
		</tr>
	</table>
<?php
}
?>


<?php
add_action('save_post', 'add_de_fields', 10, 2);

function add_de_fields($de_id, $de_fields)
{
	// Check post type for movie reviews
	if ($de_fields->post_type == 'data_explorer') {
		// Store data in post meta table if present in post data
		if (isset($_POST['api_url_name']) && $_POST['api_url_name'] != '') {
			update_post_meta($de_id, 'api_url', $_POST['api_url_name']);
		}

		if (isset($_POST['backtype_radio_value'])) { // Input var okay.
			update_post_meta($de_id, 'backtype_radio_value', sanitize_text_field(wp_unslash($_POST['backtype_radio_value']))); // Input var okay.
		} else {
			update_post_meta($de_id, 'backtype_radio_value', 'DOTSTAT');
		}


		if (isset($_POST['algolia_search_enabled'])) {
			update_post_meta($de_id, 'algolia_search_enabled', "1");
		} else {
			update_post_meta($de_id, 'algolia_search_enabled', "0");
		}

		update_post_meta($de_id, 'algolia_app_name', $_POST['algolia_app_name']);
		update_post_meta($de_id, 'algolia_public_key', $_POST['algolia_public_key']);
		update_post_meta($de_id, 'algolia_index_name', $_POST['algolia_index_name']);
		update_post_meta($de_id, 'algolia_max_results', $_POST['algolia_max_results']);
	}
}
?>

<?php
//Adds the page template from the plugin's path
add_filter('single_template', 'data_explorer_template');

function data_explorer_template($single)
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
?>


<?php
//gets the query params
function custom_query_vars_filter($vars)
{
	$vars[] .= 'ag';
	$vars[] .= 'df';
	$vars[] .= 'ver';
	$vars[] .= 'dq';
	$vars[] .= 'startPeriod';
	$vars[] .= 'endPeriod';

	return $vars;
}
add_filter('query_vars', 'custom_query_vars_filter');
?>


<?php
//Adds the Data explorer javascripts and css
function add_dataexplorer()
{
	if (is_single() && get_post_type() == 'data_explorer') {

		$js_url = plugins_url('js/', __FILE__);
		wp_enqueue_script('de_settings', $js_url . 'de_settings/settings.js', NULL, 1.02, true);

		$static_path = plugin_dir_path(__FILE__) . 'de/static/';

		$css_url = plugins_url('de/static/css/', __FILE__);
		$js_url = plugins_url('de/static/js/', __FILE__);

		$css_list = glob($static_path . "css/*.css");
		$js_list = glob($static_path . "js/*.js");
		//att the styles
		for ($i = 0; $i < count($css_list); $i++) {
			//remove the main as it seems to contain body elements already present in the howsting Wordpress page
			if (strpos(basename($css_list[$i]), "main") === false) {
				wp_enqueue_style('de_style' . $i, $css_url . basename($css_list[$i]));
			}
		}

		wp_enqueue_style('de_style2' . $i, 'C:\xampp\htdocs\wp-content\plugins\wp_dotstat_data_explorer/de/static/css/2.736207e5.chunk.css.map');

		//add the js files in the same order they're added by react in the main page
		$js_load_order = array();
		$pos_count = 1;

		//sort them
		for ($i = 0; $i < count($js_list); $i++) {
			$baseN = basename($js_list[$i]);
			if (strpos($baseN, 'runtime~') !== false) {
				$js_load_order[0] = $baseN;
			} elseif (strpos($baseN, 'main.') !== false) {
				$js_load_order[count($js_list) - 1] = $baseN;
			} else {
				$js_load_order[$pos_count] = $baseN;
				$pos_count++;
			}
		}

		//add the first one, jquery is a dependency
		wp_enqueue_script('de_script0', $js_url . $js_load_order[0], array('jquery', 'de_settings',), NULL, true);
		//add the remaining ones(each one depends on the first one)
		for ($i = 1; $i < count($js_list); $i++) {
			wp_enqueue_script('de_script' . $i, $js_url . $js_load_order[$i], array('de_script0'), NULL, true);
		}
	}
}

//Adds the resources needed by the wp page
function add_de()
{
	if (is_single() && get_post_type() == 'data_explorer') {

		$css_url = plugins_url('css/', __FILE__);
		wp_enqueue_style('data_expl_css', $css_url . 'data_explorer.css?v=2');
		
		// $js_url = plugins_url('js/', __FILE__);
		// wp_enqueue_script('related_search', $js_url . 'related_search.js', array('jquery', 'algoliasearchLite', 'algolia_instantsearch'), NULL, true);
	}
}


//add the scripts and css only if the post type is data_explorer
add_action('wp_enqueue_scripts', 'add_de');
add_action('wp_enqueue_scripts', 'add_dataexplorer');
?>