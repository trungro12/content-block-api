<?php

class ContentBlockAPI_Menu_Admin
{
    static function init()
    {
        self::addMenuOptions();
    }

    static function addMenuOptions()
    {
        add_action("admin_menu", function () {
            add_options_page(CONTENTBLOCK_API_NAME, CONTENTBLOCK_API_NAME, 'administrator', 'content-block-api', 'ContentBlockAPI_Menu_Admin::__htmlMenu');
        });
    }

    static function __htmlMenu()
    {
        $apiKey = get_option(CONTENTBLOCK_API_CLASS_NAME . "_apiKey", "");;
        if (
            !empty($_POST['api_key']) &&
            !empty($_POST['action']) &&
            !empty($_POST['_wpnonce']) &&
            wp_verify_nonce($_POST['_wpnonce'], $_POST['action'])
        ) {
            $apiKey = trim($_POST['api_key']);
            $options = [
                'cost' => 12,
            ];
            $apiKey = password_hash($apiKey, PASSWORD_BCRYPT, $options);
            update_option(CONTENTBLOCK_API_CLASS_NAME . "_apiKey", $apiKey);
        }

?>
        <h1><?php echo CONTENTBLOCK_API_NAME; ?></h1>
        <p><b>Url API có dạng : <?php echo CONTENTBLOCK_API_URL;?>?slug={slug}&id={id}</b>, với {slug} và {id} là tham số Slug, ID của Content Block (chỉ cần có 1 trong 2)</p>
        <p><b>Header apiKey: {apiKey}</b> với {apiKey} là Api Key sau khi đã mã hoá</p>
        <p>Ví dụ (CURL) :</p>
        <p><code>curl --location '<?php echo CONTENTBLOCK_API_URL;?>?slug=asdasdsada' --header 'apiKey: <?php echo $apiKey;?>'</code></p>
        <div class="wrap">
            <div id="showInfo" <?php echo isset($_POST['api_key']) ? '' : 'style="display:none"'; ?>>
                <div class="notice notice-success">
                    <p>Thành công.</p>
                </div>
            </div>

            <form action="" method="post" action="options.php">
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="blogname">Api Key</label></th>
                            <td><input name="api_key" type="text" id="api_key" value="" class="regular-text"><br>
                                <label for="">Tạo api key cho API (api này sẽ tự động mã hoá sang bcrypt khi lưu)</label>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><label for="blogname">Api Key sau khi đã mã hoá</label></th>
                            <td><input disabled type="text" id="apiKeyFinal" value="<?php echo $apiKey; ?>" class="regular-text">
                                <button type="button" class="button" id="copyApiKey">Copy</button>
                                <br>
                                <label for="">Hãy dùng api key này để lấy data từ content block</label>
                            </td>
                        </tr>

                    </tbody>

                    <input type="hidden" name="action" value="saveApiKey">
                    <?php wp_nonce_field('saveApiKey');  ?>

                </table>
                <?php submit_button('Save') ?>
            </form>
        </div>
        <script>
            (function($) {
                const btnCopy = $('#copyApiKey');
                btnCopy.click(function() {
                    const apiKey = $('#apiKeyFinal').val();
                    copyToClipboard(apiKey, btnCopy);
                });
            })(jQuery);

            function copyToClipboard(str, btnCopy) {
                var copyText = str;
                navigator.clipboard.writeText(copyText).then(() => {
                    btnCopy.text("Copied");
                });
            }
        </script>
<?php
    }
}
