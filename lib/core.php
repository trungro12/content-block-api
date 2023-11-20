<?php

class ContentBlockAPI
{
    static function init()
    {
        self::addRestApi();

        if (!is_admin()) return;

        self::addUrlItemInEditList();

        require_once(CONTENTBLOCK_API__PLUGIN_DIR . '/lib/menu.php');
        ContentBlockAPI_Menu_Admin::init();
        self::checkRedirect();
    }



    public static function pluginActivation(){

        update_option(CONTENTBLOCK_API_CLASS_NAME . '__redirected', 0);

    }
    public static function pluginDeactivation(){
        
    }

    static function checkRedirect(){
        $isRedirected = (int) get_option(CONTENTBLOCK_API_CLASS_NAME . '__redirected', 0);
        if(empty($isRedirected)){
            update_option(CONTENTBLOCK_API_CLASS_NAME . '__redirected', 1);
            wp_safe_redirect(admin_url( 'options-general.php?page=content-block-api'));
            die;
        }
    }

    static function addRestApi()
    {
        add_action('rest_api_init', 'content_block_api_endpoint');

        function content_block_api_endpoint()
        {

            register_rest_route('content-block-api', 'get', array(
                'methods' => 'GET',
                'callback' => 'content_block_api_endpoint_callback',
                'permission_callback' => function () {
                    return '';
                },
            ));
        }

        function content_block_api_endpoint_callback($data)
        {

            header('Content-Type: text/html');

            $apiKeyReal = trim(get_option(CONTENTBLOCK_API_CLASS_NAME . "_apiKey"));

            $apiKey = trim($data->get_header('apiKey'));

            if($apiKeyReal !== $apiKey){
                echo 'Api Key không đúng';
                exit;
            }


            $slug = $data->get_param('slug');
            $id = $data->get_param('id');

            $shortCode = '[content_block';
            if ($slug) $shortCode .= " slug=" . sanitize_text_field($slug);
            if ($id) $shortCode .= " id=" . (int) sanitize_text_field($id);
            $shortCode .= " ]";

            echo do_shortcode($shortCode);
            exit;
        }
    }


    static function addUrlItemInEditList()
    {
        add_action('admin_head-edit.php', function () {

            global $current_screen;
            if ('content_block' != $current_screen->post_type) {
                return;
            }

            $apiUrl = CONTENTBLOCK_API_URL;

?>
            <script type="text/javascript">
                (function($) {
                    $(function() {
                        const listContentBlock = $('.wp-list-table #the-list tr');
                        listContentBlock.each(function(index, e) {
                            console.log(1);
                            const row = $(this);
                            const id = parseInt(row.attr("id").replace("post-", ""));
                            const rowActions = $(this).find('.row-actions');
                            const editButton = rowActions.find('span.edit a');

                            const apiUrl = '<?php echo $apiUrl; ?>?slug=&id=' + id;
                            const apiUrlButton = '<span class="edit"><a target="_blank" href="' + apiUrl + '" aria-label="API URL">API URL</a> | </span>';
                            $(apiUrlButton).insertBefore(editButton);
                        });
                    });
                })(jQuery);
            </script>
<?php
        });
    }
}
