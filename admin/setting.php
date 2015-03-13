<div class="wrap">
    <h2><?php _e('Wurmfarm Klima Monitor Einstellungen','wormstation');?></h2>
    <?php ws_save_setting(); ?>
    <form method="post" action="">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><?php _e('Wurmfarm Klima Monitor Einstellungen','wormstation');?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text">
                                <span>
                                    <?php _e('Datenbanktabelle','wormstation');?>
                                </span>
                            </legend>
                            <label for="Datenbanktabelle">
                                <input name="Datenbanktabelle löschen" type="checkbox" id="Datenbanktabelle" value="1" <?php if (defined('ws_db_delete') && ws_db_delete == true) { ?>checked="checked"<?php } ?>>
                                <?php _e('Datenbanktabelle löschen','wormstation');?> {define('ws_db_delete',true);}
                            </label>
                            <br>
                            <label for="error_log">
                                <input name="error_log" type="checkbox" id="error_log" value="1" <?php if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG == true) { ?>checked="checked"<?php } ?>>
                                <?php _e('Create Error Log in File','debug');?> /wp-content/debug.log {define('WP_DEBUG_LOG',true);}
                            </label>
                            <br>
                            <label for="display_error">
                                <input name="display_error" type="checkbox" id="display_error" value="1" <?php if (defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY == true) { ?>checked="checked"<?php } ?>>
                                <?php _e('Enable Display Errors at on all website','debug');?> {define('WP_DEBUG_DISPLAY',true);}
                            </label>
                            <br>
                            <label for="error_script">
                                <input name="error_script" type="checkbox" id="error_script" value="1" <?php if (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG == true) { ?>checked="checked"<?php } ?>>
                                <?php _e('Enable Script Debug','debug');?> {define('SCRIPT_DEBUG',true);}
                            </label>
                            <br>
                            <label for="error_savequery">
                                <input name="error_savequery" type="checkbox" id="error_savequery" value="1" <?php if (defined('SAVEQUERIES') && SAVEQUERIES == true) { ?>checked="checked"<?php } ?>>
                                <?php _e('Enable Save Queries','debug');?> {define('SAVEQUERIES',true);}
                            </label>
                            <br>
                            <p class="description">
                                <?php _e('(These settings overridden in wp-config.php file so get backup first.)','debug');?>
                            </p>
                        </fieldset>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" name="debugsetting" id="submit" class="button button-primary" value="<?php _e('Save Changes','debug');?>">
        </p>
    </form>
    <?php echo debug_footer_link();
    ?>
</div>
