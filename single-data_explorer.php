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

        $indicator_profile_url = esc_attr(get_option('de_indicator_profile_url', ''));
        $help_url = esc_attr(get_option('de_help_url', ''));

        $unicef_settings = '{"indicatorProfileUrl": "' . $indicator_profile_url . '", "helpUrl": "' . $help_url . '" }';

        echo ('<script>unicef_settings = ' . $unicef_settings . '</script>');

        $hierarchy = get_post_meta(get_the_ID(), 'de_hierarchy_cfg', true);
        if ($hierarchy != null && trim($hierarchy) != "") {
            echo ('<script>HIERARCHY_override=' . $hierarchy . '</script>');
        } else {
            echo ('<script>HIERARCHY_override={}</script>');
        }
    }

    $qs_agency = sanitize_text_field(get_query_var('ag'));
    $qs_dataflow = sanitize_text_field(get_query_var('df'));
    $qs_version = sanitize_text_field(get_query_var('ver'));
    $dataquery = sanitize_text_field(get_query_var('dq'));

    $dataquery = $dataquery . "/";

    $startPeriod = sanitize_text_field(get_query_var('startPeriod'));
    $endPeriod = sanitize_text_field(get_query_var('endPeriod'));
    if ($endPeriod == "") {
        $endPeriod = date("Y");
    }

    $backendId =  get_post_meta(get_the_ID(), 'backtype_radio_value', true);

    $script_dataflow = '<script>var DATAFLOW = {datasourceId:"fusion", agencyId:"$agency_id", dataflowId:"$dataflow", version:"$version", dataquery:"$dataquery", period: [$startPeriod, $endPeriod], backendId:"$backendId"}</script>';

    $dataflow_vars = array(
        '$agency_id' => $qs_agency,
        '$dataflow' => $qs_dataflow,
        '$version' =>  $qs_version,
        '$dataquery' => str_replace(" ", "+", $dataquery),
        '$startPeriod' => $startPeriod,
        '$endPeriod' => $endPeriod,
        '$backendId' => $backendId
    );

    $script_dataflow = strtr($script_dataflow, $dataflow_vars);

    echo ($script_dataflow);
    //echo('<script> var HIERARCHY = {"url": "https://sdmx.data.unicef.org/ws/public/sdmxapi/rest/hierarchicalcodelist/UNICEF/REGIONS_HIERARCHY/latest/"  }</script>');
    ?>

    <!--Page title-->
    <div class="row">
        <div class="col-xs-12">
            <div class="block block--heading resource-heading resource-heading--archive">
                <div class="block__background block__background--small" style="
            background-color:#f1f1f1;">
                </div>
                <div class="block__content">
                    <div class="row center-xs">
                        <div class="col-xs-12 col-lg-8">
                            <div class="block--heading__content box">
                                <div class="block--heading__card card">
                                    <div class="block--heading__tags block--heading__card--middle">
                                        <h1 class="no-margin"><?php echo (get_post_meta(get_the_ID(), 'de_page_title', true)); ?></h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--End page title-->

    <div id="root">
    </div>


    <?php if ($help_url != null && $help_url != "") { ?>
        <div id="div_de_help" class="pull-right">
            <div class="closebtn" onclick="document.getElementById('div_de_help').style.display='none';"><i class="material-icons md-18">clear</i></div>
            <a href="<?php echo ($help_url) ?>"><i class="material-icons">help</i></a><a href="<?php echo ($help_url) ?>"><span class="help_text">Need help using this tool?</span><span class="help_text_small">Help</span>
            </a>
        </div>
    <?php } ?>
</main>

<?php
wp_reset_query();
get_footer();
