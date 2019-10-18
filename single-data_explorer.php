<?php
/*
Template Name: data_explorer
*/
?>
<?php get_header(); ?>

<main id="mainUpdateBrowser" role="main" style="display:none">
    <?php echo ("global" . $is_IE); ?>
    <div id="browserError">
        <div class="innerBrowserError">
            <h3>Your browser is not supported</h3>
            <p><strong>Please update your browser and try again</strong></p>
            <i>ES script is needed</i>
        </div>
    <div>
</main>
<main id="main" class="data-explorer" role="main">
    <script>
        //Check if ES6 features are available
        ES6_Error = false;

        function check() {
            "use strict";

            if (typeof Symbol == "undefined") return false;
            try {
                eval("class A {}");
                eval("var B = (x) => x+1");
            } catch (e) {
                return false;
            }

            return true;
        }
        // The engine supports ES6 features you want to use
        if (!check()) {
            ES6_Error = true;
            document.getElementById('main').style.display = "none";
            document.getElementById('mainUpdateBrowser').style.display = "";
        }
    </script>

    <?php
    //We're in single page, jsut one post at the time, fix this while loop?
    while (have_posts()) {
        the_post();
        $api_url =  get_post_meta(get_the_ID(), 'api_url', true);
        echo ('<script>SETTINGS_override = ' . $api_url . '</script>');
    }

    $qs_agency = sanitize_text_field(get_query_var('ag'));
    $qs_dataflow = sanitize_text_field(get_query_var('df'));
    $qs_version = sanitize_text_field(get_query_var('ver'));
    $dataquery = sanitize_text_field(get_query_var('dq'));

    $dataquery = $dataquery . "/";

    $startPeriod = sanitize_text_field(get_query_var('startPeriod'));
    $endPeriod = sanitize_text_field(get_query_var('endPeriod'));

    $backendId =  get_post_meta(get_the_ID(), 'backtype_radio_value', true);

    $script_dataflow = '<script>var DATAFLOW = {datasourceId:"fusion", agencyId:"$agency_id", dataflowId:"$dataflow", version:"$version", dataquery:"$dataquery", period: [$startPeriod, $endPeriod], backendId:"$backendId"}</script>';

    $dataflow_vars = array(
        '$agency_id' => $qs_agency,
        '$dataflow' => $qs_dataflow,
        '$version' =>  $qs_version,
        '$dataquery' => $dataquery,
        '$startPeriod' => $startPeriod,
        '$endPeriod' => $endPeriod,
        '$backendId' => $backendId
    );

    $script_dataflow = strtr($script_dataflow, $dataflow_vars);

    echo ($script_dataflow);
    ?>

    <div id="root">
    </div>
</main>

<?php 
wp_reset_query(); 
get_footer();