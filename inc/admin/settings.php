<?php

/* 
 * Copyright (C) 2014 Velvary Pty Ltd
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace VanillaBeans\Favicon;
            // If this file is called directly, abort.
            if ( ! defined( 'WPINC' ) ) {
                    die;
            }

function vbean_favicon_admin_scripts() {
    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
    wp_enqueue_script('jquery');
}

function vbean_favicon_admin_styles() {
    wp_enqueue_style('thickbox');
}


    add_action('admin_print_scripts', '\VanillaBeans\Favicon\vbean_favicon_admin_scripts');
    add_action('admin_print_styles', '\VanillaBeans\Favicon\vbean_favicon_admin_styles');    



function RegisterSettings(){
	register_setting( 'vbean-favicon-settings', 'vbean_favicon_metrobgcolour' );
	register_setting( 'vbean-favicon-settings', 'vbean_favicon_iconimage' );
	register_setting( 'vbean-favicon-settings', 'vbean_favicon_iconlandscape' );
	register_setting( 'vbean-favicon-settings', 'vbean_favicon_faviconoverridetheme' );
}



function vbean_rendersettings(){
    global $vbean_favicon_message;
    $vbean_bgc=  get_option('vbean_favicon_metrobgcolour');
    $vbean_square=  get_option('vbean_favicon_iconimage');
    $vbean_oblong=  get_option('vbean_favicon_iconlandscape');
    
    
    
    ?>
<style>
    .statuscolour{
        
    }
    .colourbox{
        position:relative;
        display:inline-block;
        width:100px;
    }
    .colourboxes{
        position:relative;
        display:inline-block;
    }
        .pixelplug{display:none;}

</style>
<script language="javascript" type="text/javascript" >
    window.onload = function(e){

    };
    

</script>


        <div class="wrap">
        <h2>Vanilla Bean Icon Settings</h2>
        <div class="vbean_message"><?php echo $vbean_favicon_message ?></div>
        <p>This is a simplistic bean. Choose one square and one oblong logo, and it will be created in 16 sizes from 310 pixels down to 16 pixels thereby supporting all devices and browsers.</p>
            <form method="post" action="options.php">

    <?php settings_fields( 'vbean-favicon-settings' ); ?>
    <?php do_settings_sections( 'vbean-favicon-settings' ); ?>
                <table class="form-table">


                    <tr valign="top">
                            <td>Square Logo</td>
                            <td><label for="vbean_favicon_iconimage">
                                    <input id="vbean_favicon_iconimage" type="text" size="36" name="vbean_favicon_iconimage" value="<?php echo  \VanillaBeans\vbean_setting('vbean_favicon_iconimage',''); ?>" />
                                    <input id="upload_icon_button" type="button" value="Choose Logo" />
                                    <br />Enter an URL or upload an image for the branding and favicon (recommended 310px by 310px).
                                    </label>
                            </td>
                            <td>
                                <div id="vbean_favicon_iconimage_preview" style="max-width:310px;max-height:310px;overflow: hidden;">
                                    <img src="<?php echo $vbean_square ?>" style="width:100%" onerror="this.style.display='none';" />
                                </div>
                            </td>
                    </tr>                
                
                    <tr valign="top">
                            <td>Landscape Logo</td>
                            <td><label for="vbean_favicon_iconlandscape">
                                    <input id="vbean_favicon_iconlandscape" type="text" size="36" name="vbean_favicon_iconlandscape" value="<?php echo  \VanillaBeans\vbean_setting('vbean_favicon_iconlandscape',''); ?>" />
                                    <input id="upload_landscape_button" type="button" value="Choose Image" />
                                    <br />Enter an URL or upload an image for the branding and favicon used in windows metro style OS's (recommended 310px by 150px).
                                    </label>
                            </td>
                            <td>
                                <div id="vbean_favicon_iconlandscape_preview" style="max-width:310px;max-height:150px;overflow:hidden;">
                                    <img src="<?php echo $vbean_oblong ?>" style="width:100%"  onerror="this.style.display='none';"  />
                                </div>
                            </td>
                    </tr>                
                

                
                    <tr valign="top">
                        <th scope="row">Override Theme</th>
                        <td colspan="2"><input type="checkbox" class="checkbox" name="vbean_favicon_faviconoverridetheme"  id="vbean_favicon_faviconoverridetheme" value="1" <?php echo checked(1, get_option('vbean_favicon_faviconoverridetheme'), false)   ?>/>Override
                            <div class="description">Check this to override any theme favicons.</div>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">Metro Background</th>
                        <td colspan="2">
                            
                                <input type="text" name="vbean_favicon_metrobgcolour" id="vbean_favicon_metrobgcolour" value="<?php echo esc_attr(get_option('vbean_favicon_metrobgcolour') ); ?>" class="statuscolour" />
                            <div class="description">This is the background colour of the windows tile.</div>
                        </td>

                    </tr>  



                </table>



                

            <?php submit_button(); ?>
            </form>
        </div>    
<script language="JavaScript">
    var vbean_currentpreview;
jQuery(document).ready(function() {

    jQuery('#upload_icon_button').click(function() {
        vbean_currentpreview = 'vbean_favicon_iconimage';
        formfield = jQuery('#vbean_favicon_iconimage').attr('name');
        tb_show('', 'media-upload.php?type=image&TB_iframe=true');
        return false;
    });

    jQuery('#upload_landscape_button').click(function() {
        vbean_currentpreview = 'vbean_favicon_iconlandscape';
        formfield = jQuery('#vbean_favicon_iconlandscape').attr('name');
        tb_show('', 'media-upload.php?type=image&TB_iframe=true');
        return false;
    });


    window.send_to_editor = function(html) {
            console.log('send to editor');
            console.log(html);
            var div = document.createElement('div');
            div.innerHTML=html;
            var thisimg = div.firstChild;
            imgurl = jQuery(thisimg).attr('src');
            div= null;
            jQuery('#'+vbean_currentpreview).val(imgurl);
            var s = '<img src="'+imgurl+'" width="310" style="100%';
                    s+='" />';
            jQuery('#'+vbean_currentpreview+'_preview').html(s);
            tb_remove();
    };        
        
        

    
});
(function( $ ) {
	// Add Color Picker to all inputs that have 'color-field' class
	$(function() {
	$('.statuscolour').wpColorPicker();
	});
})( jQuery );
</script>
    <?php
    
    
}



