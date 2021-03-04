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


    <?php
    $remote_files_path = esc_attr(get_option('de_remote_files_url', ''));
    $de_config_id =  get_post_meta(get_the_ID(), 'de_config_id', true);
    $remote_files_path = "http://localhost/wp-content/plugins/wp_dotstat_data_explorer_cfg";
    //$remote_files_path="https://data.unicef.org/wp-content/plugins/wp_dotstat_data_explorer_cfg";
    //$remote_files_path="http://seotest.buzz/wp-content/plugins/dataexplorer_maps";







    //We're in single page, jsut one post at the time, fix this while loop?
    while (have_posts()) {
        the_post();
    }


    echo ("<script>var remote_files_path=\"" . $remote_files_path . "\" </script>");

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
</main>




<script>
    var remote_files_path = "<?php echo ($remote_files_path); ?>"
    //var json_config = "PCO_subnat";
    //var json_config = "GLOBAL_DATAFLOW";
    var json_config = "<?php echo ($de_config_id); ?>";
    var version = "1.09";
</script>

<script src="<?php echo ($remote_files_path); ?>/js/bundle.js?v=" +version></script>



<?php
wp_reset_query();
get_footer();
