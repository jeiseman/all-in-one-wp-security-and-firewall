<?php

class AIOWPSecurity_Settings_Menu extends AIOWPSecurity_Admin_Menu
{
    var $menu_page_slug = AIOWPSEC_SETTINGS_MENU_SLUG;
    
    /* Specify all the tabs of this menu in the following array */
    var $menu_tabs;

    var $menu_tabs_handler = array(
        'tab1' => 'render_tab1', 
        'tab2' => 'render_tab2',
        'tab3' => 'render_tab3',
        'tab4' => 'render_tab4',
        );

    function __construct() 
    {
        $this->render_menu_page();
    }

    function set_menu_tabs() 
    {
        $this->menu_tabs = array(
        'tab1' => __('General Settings', 'aiowpsecurity'), 
        'tab2' => '.htaccess '.__('File', 'aiowpsecurity'),
        'tab3' => 'wp-config.php '.__('File', 'aiowpsecurity'),
        'tab4' => __('WP Meta Info', 'aiowpsecurity'),
        );
    }

    function get_current_tab() 
    {
        $tab_keys = array_keys($this->menu_tabs);
        $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $tab_keys[0];
        return $tab;
    }

    /*
     * Renders our tabs of this menu as nav items
     */
    function render_menu_tabs() 
    {
        $current_tab = $this->get_current_tab();

        echo '<h2 class="nav-tab-wrapper">';
        foreach ( $this->menu_tabs as $tab_key => $tab_caption ) 
        {
            $active = $current_tab == $tab_key ? 'nav-tab-active' : '';
            echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->menu_page_slug . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';	
        }
        echo '</h2>';
    }
    
    /*
     * The menu rendering goes here
     */
    function render_menu_page() 
    {
        $this->set_menu_tabs();
        $tab = $this->get_current_tab();
        ?>
        <div class="wrap">
        <div id="poststuff"><div id="post-body">
        <?php 
        $this->render_menu_tabs();
        //$tab_keys = array_keys($this->menu_tabs);
        call_user_func(array(&$this, $this->menu_tabs_handler[$tab]));
        ?>
        </div></div>
        </div><!-- end of wrap -->
        <?php
    }
    
    function render_tab1()
    {
        global $aio_wp_security;
        if(isset($_POST['aiowpsec_disable_all_features']))//Do form submission tasks
        {
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-disable-all-features'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed on disable all security features!",4);
                die("Nonce check failed on disable all security features!");
            }
            AIOWPSecurity_Configure_Settings::turn_off_all_security_features();
            //Now let's clear the applicable rules from the .htaccess file
            $res = AIOWPSecurity_Utility_Htaccess::write_to_htaccess();
            
            //Now let's revert the disable editing setting in the wp-config.php file if necessary
            $res2 = AIOWPSecurity_Utility::enable_file_edits();

            if ($res)
            {
                $this->show_msg_updated(__('All the security features have been disabled successfully!', 'aiowpsecurity'));
            }
            else if($res == -1)
            {
                $this->show_msg_error(__('Could not write to the .htaccess file. Please restore your .htaccess file manually using the restore functionality in the ".htaccess File".', 'aiowpsecurity'));
            }

            if(!$res2)
            {
                $this->show_msg_error(__('Could not write to the wp-config.php. Please restore your wp-config.php file manually using the restore functionality in the "wp-config.php File".', 'aiowpsecurity'));
            }
        }

        if(isset($_POST['aiowpsec_disable_all_firewall_rules']))//Do form submission tasks
        {
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-disable-all-firewall-rules'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed on disable all firewall rules!",4);
                die("Nonce check failed on disable all firewall rules!");
            }
            AIOWPSecurity_Configure_Settings::turn_off_all_firewall_rules();
            //Now let's clear the applicable rules from the .htaccess file
            $res = AIOWPSecurity_Utility_Htaccess::write_to_htaccess();
            
            if ($res)
            {
                $this->show_msg_updated(__('All firewall rules have been disabled successfully!', 'aiowpsecurity'));
            }
            else if($res == -1)
            {
                $this->show_msg_error(__('Could not write to the .htaccess file. Please restore your .htaccess file manually using the restore functionality in the ".htaccess File".', 'aiowpsecurity'));
            }
        }
        ?>
        <div class="aio_grey_box">
 	<p>For information, updates and documentation, please visit the <a href="http://www.tipsandtricks-hq.com/wordpress-security-and-firewall-plugin" target="_blank">AIO WP Security & Firewall Plugin</a> Page.</p>
        <p><a href="http://www.tipsandtricks-hq.com/development-center" target="_blank">Follow us</a> on Twitter, Google+ or via Email to stay upto date about the new security features of this plugin.</p>
        </div>
        
        <div class="postbox">
        <h3><label for="title"><?php _e('WP Security Plugin', 'aiowpsecurity'); ?></label></h3>
        <div class="inside">
        <p><?php _e('Thank you for using our WordPress security plugin. There are a lot of security features in this plugin.', 'aiowpsecurity'); ?></p>
        <p><?php _e('Go through each menu items and enable the security options to add more security to your site. Start by activating the basic features first.', 'aiowpsecurity'); ?></p>
        <p><?php _e('It is a good practice to take a backup of your .htaccess file, database and wp-config.php file before activating the security features. This plugin has options that you can use to backup those resources easily.', 'aiowpsecurity'); ?></p>
        <p>
        <ul class="aiowps_admin_ul_grp1">
            <li><a href="admin.php?page=aiowpsec_database&tab=tab2" target="_blank"><?php _e('Backup your database', 'aiowpsecurity'); ?></a></li>
            <li><a href="admin.php?page=aiowpsec_settings&tab=tab2" target="_blank"><?php _e('Backup .htaccess file', 'aiowpsecurity'); ?></a></li>
            <li><a href="admin.php?page=aiowpsec_settings&tab=tab3" target="_blank"><?php _e('Backup wp-config.php file', 'aiowpsecurity'); ?></a></li>
        </ul>
        </p>
        </div></div>
        
        <div class="postbox">
        <h3><label for="title"><?php _e('Disable Security Features', 'aiowpsecurity'); ?></label></h3>
        <div class="inside">
        <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
        <?php wp_nonce_field('aiowpsec-disable-all-features'); ?>
        <div class="aio_blue_box">
            <?php
            echo '<p>'.__('If you think that some plugin functionality on your site is broken due to a security feature you enabled in this plugin, then use the following option to turn off all the security features of this plugin.', 'aiowpsecurity').'</p>';
            ?>
        </div>      
        <div class="submit">
            <input type="submit" class="button" name="aiowpsec_disable_all_features" value="<?php _e('Disable All Security Features'); ?>" />
        </div>
        </form>   
        </div></div>

        <div class="postbox">
        <h3><label for="title"><?php _e('Disable All Firewall Rules', 'aiowpsecurity'); ?></label></h3>
        <div class="inside">
        <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
        <?php wp_nonce_field('aiowpsec-disable-all-firewall-rules'); ?>
        <div class="aio_blue_box">
            <?php
            echo '<p>'.__('This feature will disable all firewall rules which are currently active in this plugin and it will also delete these rules from your .htacess file. Use it if you think one of the firewall rules is causing an issue on your site.', 'aiowpsecurity').'</p>';
            ?>
        </div>      
        <div class="submit">
            <input type="submit" class="button" name="aiowpsec_disable_all_firewall_rules" value="<?php _e('Disable All Firewall Rules'); ?>" />
        </div>
        </form>   
        </div></div>
        <?php
    }
    
    function render_tab2()
    {
        global $aio_wp_security;

        if(isset($_POST['aiowps_save_htaccess']))//Do form submission tasks
        {
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-save-htaccess-nonce'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed on htaccess file save!",4);
                die("Nonce check failed on htaccess file save!");
            }
            $htaccess_path = ABSPATH . '.htaccess';
            $result = AIOWPSecurity_Utility_File::backup_a_file($htaccess_path); //Backup the htaccess file
            
            if ($result)
            {
                $random_prefix = AIOWPSecurity_Utility::generate_alpha_numeric_random_string(10);
                if (rename(ABSPATH.'.htaccess.backup', AIO_WP_SECURITY_BACKUPS_PATH.'/'.$random_prefix.'_htaccess_backup.txt'))
                {
//                    $backup_file_url = AIOWPSEC_WP_URL . '/htaccess_backup.txt';
                    echo '<div id="message" class="updated fade"><p>';
                    _e('Your .htaccess file was successfully backed up! Using an FTP program go to the "backups" directory of this plugin to save a copy of the file to your computer.','aiowpsecurity');
//                    echo '<p>';
//                    _e('Your .htaccess File: ');
//                    echo '<a href="'.$backup_file_url.'" target="_blank">'.$backup_file_url.'</a>';
//                    echo '</p>';
                    echo '</p></div>';
                }
                else
                {
                    $aio_wp_security->debug_logger->log_debug("htaccess file rename failed during backup!",4);
                    $this->show_msg_error(__('htaccess file rename failed during backup. Please check your root directory for the backup file using FTP.','aiowpsecurity'));
                }
            } 
            else
            {
                $aio_wp_security->debug_logger->log_debug("htaccess - Backup operation failed!",4);
                $this->show_msg_error(__('htaccess backup failed.','aiowpsecurity'));
            }
        }
        
        if(isset($_POST['aiowps_restore_htaccess_button']))//Do form submission tasks
        {
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-restore-htaccess-nonce'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed on htaccess file restore!",4);
                die("Nonce check failed on htaccess file restore!");
            }
            
            if (empty($_POST['aiowps_htaccess_file']))
            {
                $this->show_msg_error(__('Please choose a .htaccess to restore from.', 'aiowpsecurity'));
            }
            else
            {
                //Let's copy the uploaded .htaccess file into the active root file
                $new_htaccess_file_path = trim($_POST['aiowps_htaccess_file']);
                //TODO
                //Verify that file chosen has contents which are relevant to .htaccess file
                $is_htaccess = AIOWPSecurity_Utility_Htaccess::check_if_htaccess_contents($new_htaccess_file_path);
                if ($is_htaccess == 1)
                {
                    $active_root_htaccess = ABSPATH.'.htaccess';
                    if (!copy($new_htaccess_file_path, $active_root_htaccess)) 
                    {
                        //Failed to make a backup copy
                        $aio_wp_security->debug_logger->log_debug("htaccess - Restore from .htaccess operation failed!",4);
                        $this->show_msg_error(__('htaccess file restore failed. Please attempt to restore the .htaccess manually using FTP.','aiowpsecurity'));
                    }
                    else
                    {
                        $this->show_msg_updated(__('Your .htaccess file has successfully been restored!', 'aiowpsecurity'));
                    }
                }
                else
                {
                    $aio_wp_security->debug_logger->log_debug("htaccess restore failed - Contents of restore file appear invalid!",4);
                    $this->show_msg_error(__('htaccess Restore operation failed! Please check the contents of the file you are trying to restore from.','aiowpsecurity'));
                }
            }
        }
        
        ?>
        <h2><?php _e('.htaccess File Operations', 'aiowpsecurity')?></h2>
        <div class="aio_blue_box">
            <?php
            echo '<p>'.__('Your ".htaccess" file is a key component of your website\'s security and it can be modified to implement various levels of protection mechanisms.', 'aiowpsecurity').'
            <br />'.__('This feature allows you to backup and save your currently active .htaccess file should you need to re-use the the backed up file in the future.', 'aiowpsecurity').'
            <br />'.__('You can also restore your site\'s .htaccess settings using a backed up .htaccess file.', 'aiowpsecurity').'    
            </p>';
            ?>
        </div>
        <?php 
        if (AIOWPSecurity_Utility::is_multisite_install() && get_current_blog_id() != 1)
        {
           //Hide config settings if MS and not main site
           AIOWPSecurity_Utility::display_multisite_message();
        }
        else
        {
        ?>
        <div class="postbox">
        <h3><label for="title"><?php _e('Save the current .htaccess file', 'aiowpsecurity'); ?></label></h3>
        <div class="inside">
        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-save-htaccess-nonce'); ?>
            <p class="description"><?php _e('Click the button below to backup and save the currently active .htaccess file.', 'aiowpsecurity'); ?></p>
            <input type="submit" name="aiowps_save_htaccess" value="<?php _e('Backup .htaccess File', 'aiowpsecurity')?>" class="button-primary" />
        </form>
        </div></div>
        <div class="postbox">
        <h3><label for="title"><?php _e('Restore from a backed up .htaccess file', 'aiowpsecurity'); ?></label></h3>
        <div class="inside">
        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-restore-htaccess-nonce'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('.htaccess file to restore from', 'aiowpsecurity')?>:</th>
                <td>
                    <input type="button" id="aiowps_htaccess_file_button" name="aiowps_htaccess_file_button" class="button rbutton" value="Select Your htaccess File" />
                    <input name="aiowps_htaccess_file" type="text" id="aiowps_htaccess_file" value="" size="80" />
                    <p class="description">
                        <?php
                        _e('After selecting your file, click the button below to restore your site using the backed up htaccess file (htaccess_backup.txt).', 'aiowpsecurity');
                        ?>
                    </p>
                </td>
            </tr>            
        </table>
        <input type="submit" name="aiowps_restore_htaccess_button" value="<?php _e('Restore .htaccess File', 'aiowpsecurity')?>" class="button-primary" />
        </form>
        </div></div>
        <div class="postbox">
        <h3><label for="title"><?php _e('View Contents of the currently active .htaccess file', 'aiowpsecurity'); ?></label></h3>
        <div class="inside">
            <?php
            $ht_file = ABSPATH . '.htaccess';
            $ht_contents = AIOWPSecurity_Utility_File::get_file_contents($ht_file);
            //echo $ht_contents;
            ?>
            <textarea class="aio_text_area_file_output aio_half_width aio_spacer_10_tb" rows="15" readonly><?php echo $ht_contents; ?></textarea>
        </div></div>

        <?php
        } // End if statement
    }

    function render_tab3()
    {
        global $aio_wp_security;
        
        if(isset($_POST['aiowps_restore_wp_config_button']))//Do form submission tasks
        {
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-restore-wp-config-nonce'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed on wp-config file restore!",4);
                die("Nonce check failed on wp-config file restore!");
            }
            
            if (empty($_POST['aiowps_wp_config_file']))
            {
                $this->show_msg_error(__('Please choose a wp-config.php file to restore from.', 'aiowpsecurity'));
            }
            else
            {
                //Let's copy the uploaded wp-config.php file into the active root file
                $new_wp_config_file_path = trim($_POST['aiowps_wp_config_file']);
                //TODO
                //Verify that file chosen has contents which are relevant to .htaccess file
                $is_wp_config = $this->check_if_wp_config_contents($new_wp_config_file_path); //TODO - write the function
                if ($is_wp_config == 1)
                {
                    $active_root_wp_config = ABSPATH.'wp-config.php';
                    if (!copy($new_wp_config_file_path, $active_root_wp_config)) 
                    {
                        //Failed to make a backup copy
                        $aio_wp_security->debug_logger->log_debug("wp-config.php - Restore from backed up wp-config operation failed!",4);
                        $this->show_msg_error(__('wp-config.php file restore failed. Please attempt to restore this file manually using FTP.','aiowpsecurity'));
                    }
                    else
                    {
                        $this->show_msg_updated(__('Your wp-config.php file has successfully been restored!', 'aiowpsecurity'));
                    }
                }
                else
                {
                    $aio_wp_security->debug_logger->log_debug("wp-config.php restore failed - Contents of restore file appear invalid!",4);
                    $this->show_msg_error(__('wp-config.php Restore operation failed! Please check the contents of the file you are trying to restore from.','aiowpsecurity'));
                }
            }
        }
        
        ?>
        <h2><?php _e('wp-config.php File Operations', 'aiowpsecurity')?></h2>
        <div class="aio_blue_box">
            <?php
            echo '<p>'.__('Your "wp-config.php" file is one of the most important in your WordPress installation. It is a primary configuration file and contains crucial things such as details of your database and other critical components.', 'aiowpsecurity').'
            <br />'.__('This feature allows you to backup and save your currently active wp-config.php file should you need to re-use the the backed up file in the future.', 'aiowpsecurity').'
            <br />'.__('You can also restore your site\'s wp-config.php settings using a backed up wp-config.php file.', 'aiowpsecurity').'    
            </p>';
            ?>
        </div>
        <?php 
        if (AIOWPSecurity_Utility::is_multisite_install() && get_current_blog_id() != 1)
        {
           //Hide config settings if MS and not main site
           AIOWPSecurity_Utility::display_multisite_message();
        }
        else
        {
        ?>
        <div class="postbox">
        <h3><label for="title"><?php _e('Save the current wp-config.php file', 'aiowpsecurity'); ?></label></h3>
        <div class="inside">
        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-save-wp-config-nonce'); ?>
            <p class="description"><?php _e('Click the button below to backup and download the contents of the currently active wp-config.php file.', 'aiowpsecurity'); ?></p>
            <input type="submit" name="aiowps_save_wp_config" value="<?php _e('Backup wp-config.php File', 'aiowpsecurity')?>" class="button-primary" />

        </form>
        </div></div>
        <div class="postbox">
        <h3><label for="title"><?php _e('Restore from a backed up wp-config file', 'aiowpsecurity'); ?></label></h3>
        <div class="inside">
        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-restore-wp-config-nonce'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('wp-config file to restore from', 'aiowpsecurity')?>:</th>
                <td>
                    <input type="button" id="aiowps_wp_config_file_button" name="aiowps_wp_config_file_button" class="button rbutton" value="Select Your wp-config File" />
                    <input name="aiowps_wp_config_file" type="text" id="aiowps_wp_config_file" value="" size="80" />                    
                    <p class="description">
                        <?php
                        _e('After selecting your file click the button below to restore your site using the backed up wp-config file (wp-config.php.backup.txt).', 'aiowpsecurity'); 
                        ?>
                    </p>
                </td>
            </tr>            
        </table>
        <input type="submit" name="aiowps_restore_wp_config_button" value="<?php _e('Restore wp-config File', 'aiowpsecurity')?>" class="button-primary" />
        </form>
        </div></div>
        <div class="postbox">
        <h3><label for="title"><?php _e('View Contents of the currently active wp-config.php file', 'aiowpsecurity'); ?></label></h3>
        <div class="inside">
            <?php
            $wp_config_file = ABSPATH . 'wp-config.php';
            $wp_config_contents = AIOWPSecurity_Utility_File::get_file_contents($wp_config_file); 
            ?>
            <textarea class="aio_text_area_file_output aio_width_80 aio_spacer_10_tb" rows="20" readonly><?php echo $wp_config_contents; ?></textarea>
        </div></div>

        <?php
        } //End if statement
    }
    
    function render_tab4()
    {
        global $aio_wp_security;
        global $aiowps_feature_mgr;
        
        if(isset($_POST['aiowps_save_remove_wp_meta_info']))//Do form submission tasks
        {
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-remove-wp-meta-info-nonce'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed on remove wp meta info options save!",4);
                die("Nonce check failed on remove wp meta info options save!");
            }
            $aio_wp_security->configs->set_value('aiowps_remove_wp_generator_meta_info',isset($_POST["aiowps_remove_wp_generator_meta_info"])?'1':'');
            $aio_wp_security->configs->save_config();
            
            //Recalculate points after the feature status/options have been altered
            $aiowps_feature_mgr->check_feature_status_and_recalculate_points();
            
            $this->show_msg_settings_updated();
    }
        ?>
        <h2><?php _e('WP Generator Meta Tag', 'aiowpsecurity')?></h2>
        <div class="aio_blue_box">
            <?php
            echo '<p>'.__('Wordpress generator automatically adds some meta information inside the "head" tags of every page on your site\'s front end. Below is an example of this:', 'aiowpsecurity');
            echo '<br /><strong>&lt;meta name="generator" content="WordPress 3.5.1" /&gt;</strong>';
            echo '<br />'.__('The above meta information shows which version of WordPress your site is currently running and thus can help hackers or crawlers scan your site to see if you have an older version of WordPress or one with a known exploit.', 'aiowpsecurity').'
            <br />'.__('This feature will allow you to remove the WP generator meta info from your site\'s pages.', 'aiowpsecurity').'    
            </p>';
            ?>
        </div>

        <div class="postbox">
        <h3><label for="title"><?php _e('WP Generator Meta Info', 'aiowpsecurity'); ?></label></h3>
        <div class="inside">
        <?php
        //Display security info badge
        global $aiowps_feature_mgr;
        $aiowps_feature_mgr->output_feature_details_badge("wp-generator-meta-tag");
        ?>

        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-remove-wp-meta-info-nonce'); ?>            
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Remove WP Generator Meta Info', 'aiowpsecurity')?>:</th>                
                <td>
                <input name="aiowps_remove_wp_generator_meta_info" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_remove_wp_generator_meta_info')=='1') echo ' checked="checked"'; ?> value="1"/>
                <span class="description"><?php _e('Check this if you want to remove the meta info produced by WP Generator from all pages', 'aiowpsecurity'); ?></span>
                </td>
            </tr>            
        </table>
        <input type="submit" name="aiowps_save_remove_wp_meta_info" value="<?php _e('Save Settings', 'aiowpsecurity')?>" class="button-primary" />
        </form>
        </div></div>
    <?php
    }

    
    function check_if_wp_config_contents($wp_file)
    {
        $is_wp_config = false;
        
        $file_contents = file($wp_file);

        if ($file_contents == '' || $file_contents == NULL || $file_contents == false)
        {
            return -1;
        }
        foreach ($file_contents as $line)
        {
            if ((strpos($line, "define('DB_NAME'") !== false))
            {
                $is_wp_config = true; //It appears that we have some sort of wp-config.php file
                break;
            }
            else
            { 
                //see if we're at the end of the section
                $is_wp_config = false;
            }
        }
        if ($is_wp_config)
        {
            return 1;
        }
        else
        {
            return -1;
        }

    }

} //end class