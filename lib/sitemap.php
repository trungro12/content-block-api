<?php

class ContentBlockAPI_Sitemap
{


    static function init()
    {
        define('CONTENTBLOCK_API_URL_SITEMAP', home_url('wp-json/content-block-api/sitemap'));
        add_shortcode('content_block_api_sitemap', 'ContentBlockAPI_Sitemap::exportSitemap');
        self::addRestApi();
    }

    static function exportSitemap()
    {
        $posts = get_posts([
            'post_type' => 'content_block',
            'post_status' => 'publish',
            'numberposts' => -1
            // 'order'    => 'ASC'
        ]);
        self::__sitemapTemplate($posts);
    }

    /**
     * @param WP_Post $posts
     */
    static function __sitemapTemplate($posts)
    {

        echo `<?xml version="1.0" encoding="UTF-8"?>`;
?>
        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
            <?php foreach ($posts as $post) : ?>
                <url>
                    <loc>
                        <![CDATA[ <?php echo $post->post_name; ?> ]]>
                    </loc>
                    <lastmod><?php echo date("Y-m-d", get_post_timestamp($post)); ?></lastmod>
                    <changefreq>weekly</changefreq>
                </url>
            <?php endforeach; ?>
        </urlset>
<?php
    }


    static function addRestApi()
    {
        add_action('rest_api_init', 'content_block_api_sitemap_endpoint');

        function content_block_api_sitemap_endpoint()
        {

            register_rest_route('content-block-api', 'sitemap', array(
                'methods' => 'GET',
                'callback' => 'content_block_api_sitemap_endpoint_callback',
                'permission_callback' => function () {
                    return '';
                },
            ));
        }

        function content_block_api_sitemap_endpoint_callback($data)
        {

            header('Content-Type: application/xml; charset=utf-8');
            echo do_shortcode('[content_block_api_sitemap]');
            exit;
        }
    }
}
