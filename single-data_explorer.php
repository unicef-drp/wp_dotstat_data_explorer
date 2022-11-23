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
    <article class="site-main status-publish hentry">
        <div class="site-main__content blocks">
            <?php
            $page_title =  get_post_meta(get_the_ID(), 'de_page_title', true);
            $remote_files_path = esc_attr(get_option('de_remote_files_url', ''));
            $de_config_id =  get_post_meta(get_the_ID(), 'de_config_id', true);

            $remote_files_path="https://data.unicef.org/wp-content/plugins/wp_dotstat_data_explorer_cfg";
            //$remote_files_path = "http://localhost/wp-content/plugins/wp_dotstat_data_explorer_cfg";
            //$remote_files_path="http://seotest.buzz/wp-content/plugins/dataexplorer_maps";


            //We're in single page, jsut one post at the time, fix this while loop?
            while (have_posts()) {
                the_post();
            }

            if ($startPeriod == "") {
                $startPeriod = date("Y")-10;
            } ?>

            <section style="--block--margin-bottom: 0" class="wp-block-flag flag has-background has-breadcrumbs flag--reverse flag--compact alignfull">
                <div class="flag__inner">
                    <div class="flag__content">
                        <h1 class="flag__heading">
                            <?= wp_kses_post($page_title); ?>
                        </h1>
                        <div class="breadcrumbs flag__breadcrumbs">
                            <p class="breadcrumbs__inner">
                                <span>
                                    <span>
                                        <a href="<?= home_url(); ?>"><?= __('Home', 'granola'); ?></a> / 
                                        <span class="breadcrumb_last" aria-current="page"><?= wp_kses_post($page_title); ?></span>
                                    </span>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </section>
            <section id="root" class="wp-block alignfull">
            </section> 
        </div>
    </article>
</main>

<?php $date = date('YmdH', time());?>
<script>
    var remote_files_path = "<?php echo ($remote_files_path); ?>"
    //var json_config = "PCO_subnat";
    //var json_config = "GLOBAL_DATAFLOW";
    var json_config = "<?php echo ($de_config_id); ?>";
</script>
<script src="<?php echo ($remote_files_path .'/js/bundle.js?v=' . $date) ?>"></script>
<style>
.pt-slider-label { width: 40px; text-align: center; }
</style>
<?php
wp_reset_query();
get_footer();
