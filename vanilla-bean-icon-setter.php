<?php

/*
  Plugin Name: Vanilla Bean - Icon Setter
  Plugin URI: http://www.velvary.com.au/vanilla-beans/wordpress/Iconifier/
  Description: Set your wordpress site icon
  Version: 2.81
  Author: Mark Pottie <mark@velvary.com.au>
  Author URI: http://www.velvary.com.au
  License: GPLv2
 */


// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if (!defined('VBEANFAVICON_PLUGIN_DIR')) {
    define('VBEANFAVICON_PLUGIN_DIR', plugin_dir_path(__FILE__));
}
if (!defined('VBEANFAVICON_PLUGIN_URL')) {
    define('VBEANFAVICON_PLUGIN_URL', plugin_dir_url(__FILE__));
}
if (!defined('VBEANFAVICON_PLUGIN_FILE')) {
    define('VBEANFAVICON_PLUGIN_FILE', __FILE__);
}
if (!defined('VBEANFAVICON_PLUGIN_VERSION')) {
    define('VBEANFAVICON_PLUGIN_VERSION', '2.81');
}

$vbean_apples = array(57, 114, 72, 144, 60, 120, 76, 152);
$vbean_favicons = array(16, 32, 96, 160, 196);
$vbean_msicons = array(70, 150, 310);
$vbean_favicon_message = '';

/* ===========================================
  Define Includes
  =========================================== */
$includes = array(
    'image.php',
    'functions.php'
);

$frontend_includes = array(
);


$adminincludes = array(
    'processors.php',
    'settings.php'
);

/* ===========================================
  Load Includes
  =========================================== */
// Common
foreach ($includes as $include) {
    require_once( dirname(__FILE__) . '/inc/' . $include );
}
if (is_admin()) {
    //load admin part
    foreach ($adminincludes as $admininclude) {
        require_once( dirname(__FILE__) . '/inc/admin/' . $admininclude );
    }
    add_action('admin_enqueue_scripts', 'vbean_load_colourpicker');
} else {
    //load front part
    foreach ($frontend_includes as $include) {
        require_once( dirname(__FILE__) . '/inc/' . $include );
    }
}

add_action('admin_menu', 'vbean_favicon_create_menu');

if (!function_exists('vbean_favicon_create_menu')) {






    function vbean_favicon_create_menu() {


        if (empty($GLOBALS['admin_page_hooks']['vanillabeans-settings'])) {
            //create new top-level menu
            add_menu_page('Vanilla Beans', 'Vanilla Beans', 'administrator', 'vanillabeans-settings', 'VanillaBeans\LiveSettings', VBEANFAVICON_PLUGIN_URL . 'vicon.png', 4);
        }
        $vbean_hook = add_submenu_page('vanillabeans-settings', 'Icon Setter', 'Icon Setter', 'administrator', __FILE__, 'VanillaBeans\Favicon\vbean_rendersettings');

        add_action('load-' . $vbean_hook, 'vbean_favicon_settings_save');





        //call register settings function
        add_action('admin_init', 'VanillaBeans\Favicon\RegisterSettings');
    }

}

if (!function_exists('vbean_favicon_testimagecompatible')) {

    function vbean_favicon_testimagecompatible() {
        $msg = '';
        $iscompatible = true;
        $iconfunctions = array(
            'getimagesize',
            'imagecreatetruecolor',
            'imagecolortransparent',
            'imagecolorallocatealpha',
            'imagealphablending',
            'imagesavealpha',
            'imagecopyresampled'
        );
        foreach ($iconfunctions as $function) {
            if (!function_exists($function)) {
                $msg.='Function missing: ' . $function . '<br />';
                $iscompatible = FALSE;
            }
        }
        return $iscompatible;
    }

}


if (!function_exists('vbean_favicon_settings_save')) {

    function vbean_favicon_settings_save() {
        if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
            //plugin settings have been saved. Here goes your code
            vbean_createicons();
        }
    }

}


if (!function_exists('vbean_favicon_folder')) {

    function vbean_favicon_folder() {
        $uploads = trailingslashit(ABSPATH . 'wp-content/uploads');
        $uploads = trailingslashit($uploads . 'favicons');
        if (!file_exists($uploads)) {
            mkdir($uploads, 0777, true);
        }
        return $uploads;
    }

}



/* -------------------------------------------
 *            Validate uploaded file
 * ------------------------------------------ */
if (!function_exists('vbean_favicon_validfile')) {

    function vbean_favicon_validfile($filetype) {
        $allowed_mime_types = array('jpg' => 'image/jpg', 'jpeg' => 'image/jpeg', 'jpe' => 'image/jpe', 'gif' => 'image/gif', 'png' => 'image/png');
        return in_array($filetype, $allowed_mime_types);
    }

}



if (!function_exists('vbean_makeico')) {

    function vbean_makeico($sq, $width, $height, $uploads) {
        global $vbean_favicons;
        $filename = $uploads . 'favicon';
        $faviconname = $uploads . 'favicon.ico';
        $ficon = array();
        $favicons = $vbean_favicons;
        foreach ($favicons as $favicon) {
            $tmp = imagecreatetruecolor($favicon, $favicon);
            imagecolortransparent($tmp, imagecolorallocatealpha($tmp, 0, 0, 0, 127));
            imagealphablending($tmp, false);
            imagesavealpha($tmp, true);
            imagecopyresampled($tmp, $sq, 0, 0, 0, 0, $favicon, $favicon, $width, $height);
            $ficon[] = \VanillaBeans\Favicon\vbean_image_bmp($tmp);
            $filename = $uploads . 'favicon-' . $favicon . 'x' . $favicon . '.png';
            imagepng($tmp, $filename, 5);
        }

        // build icon from ficon
        $icondata = pack('vvv', 0, 1, count($ficon));
        $pixel_data = '';

        $offset = 6 + ( 16 * count($ficon) );
        foreach ($ficon as $iconimage) {
            $icondata .= pack('CCCCvvVV', $iconimage['width'], $iconimage['height'], $iconimage['color_palette_colors'], 0, 1, $iconimage['bits_per_pixel'], $iconimage['size'], $offset);
            $pixel_data .= $iconimage['data'];

            $offset += $iconimage['size'];
        }

        $icondata .= $pixel_data;
        unset($pixel_data);
        if (FALSE !== ($fw = fopen($faviconname, 'w'))) {
            fwrite($fw, $icondata);
            fclose($fw);
        }
    }

}

if (!function_exists('vbean_make_apples')) {

    function vbean_make_apples($sq, $width, $height, $uploads) {
        global $vbean_apples;
        $apples = $vbean_apples;
        foreach ($apples as $apple) {
            $tmp = imagecreatetruecolor($apple, $apple);
            imagecolortransparent($tmp, imagecolorallocatealpha($tmp, 0, 0, 0, 127));
            imagealphablending($tmp, false);
            imagesavealpha($tmp, true);
            imagecopyresampled($tmp, $sq, 0, 0, 0, 0, $apple, $apple, $width, $height);
            $filename = $uploads . 'apple-touch-icon-' . $apple . 'x' . $apple . '.png';
            imagepng($tmp, $filename, 5);
        }
    }

}




if (!function_exists('vbean_make_ms')) {

    function vbean_make_ms($sq, $width, $height, $uploads, $image) {
        global $vbean_msicons;
        $mss = $vbean_msicons; // not the wide one
        foreach ($mss as $ms) {
            if ($ms == 70) {
                $thisname = 'tiny';
            } elseif ($ms == 150) {
                $thisname = 'square';
            } else {
                $thisname = 'large';
            }
            $tmp = imagecreatetruecolor($ms, $ms);
            imagecopyresampled($tmp, $sq, 0, 0, 0, 0, $ms, $ms, $width, $height);
            $filename = $uploads . 'msapplication-' . $thisname . '.png';
            imagepng($tmp, $filename, 5);
        }

        if (function_exists('imagecrop')) {
            $ratio = 310 / $width;
            $newwidth = 310;
            $newheight = $height * $ratio;
            $tmp = imagecreatetruecolor($newwidth, $newheight);
            imagecopyresampled($tmp, $image, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            if ($newheight > 150) {
                // we need to crop it
                $to_crop_array = array('x' => 0, 'y' => 0, 'width' => 310, 'height' => 150);
                $tmp = imagecrop($tmp, $to_crop_array);
            }
        } else {
            $newwidth = 310;
            $newheight = 150;
            $tmp = imagecreatetruecolor($newwidth, $newheight);
            imagecopyresampled($tmp, $image, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        }

        $filename = $uploads . 'msapplication-wide.png';
        imagepng($tmp, $filename, 5); //        

        $imageurl = get_option('vbean_favicon_iconlandscape');
        if (!is_404($imageurl)) {
            $xurl = parse_url($imageurl);
            $path = $xurl['path'];
            $filepath = $_SERVER['DOCUMENT_ROOT'] . $path;
            $path_parts = pathinfo($filepath);
            $ext = strtolower($path_parts['extension']);
            if ($ext == 'jpg' || $ext == 'jpe' || $ext == 'jpeg') {
                $image = imagecreatefromjpeg($filepath);
            } else if ($ext == 'png') {
                $image = imagecreatefrompng($filepath);
            } else if ($ext == "gif") {
                $image = imagecreatefromgif($filepath);
            }
            if ($image) {
                list($width, $height) = getimagesize($filepath);
                // MS wide
                if (function_exists('imagecrop')) {
                    $ratio = 310 / $width;
                    $newwidth = 310;
                    $newheight = $height * $ratio;
                    $tmp = imagecreatetruecolor($newwidth, $newheight);
                    imagecopyresampled($tmp, $image, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                    if ($newheight > 150) {
                        // we need to crop it
                        $to_crop_array = array('x' => 0, 'y' => 0, 'width' => 310, 'height' => 150);
                        $tmp = imagecrop($tmp, $to_crop_array);
                    }
                } else {
                    $newwidth = 310;
                    $newheight = 150;
                    $tmp = imagecreatetruecolor($newwidth, $newheight);
                    imagecopyresampled($tmp, $image, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                }

                $filename = $uploads . 'msapplication-wide.png';
                imagepng($tmp, $filename, 5); //
            }
        }
    }

}




if (!function_exists('vbean_createicons')) {

    function vbean_createicons() {
        global $vbean_favicon_message;
        global $vbean_apples;
        global $vbean_favicons;
        global $vbean_msicons;
        $msg = '';
        $imageurl = '' . get_option('vbean_favicon_iconimage');
        $uploads = \vbean_favicon_folder();

        if (!is_404($imageurl)) {
            $canfavicon = vbean_favicon_testimagecompatible();
            if (!$canfavicon) {
                return('<b>Cannot create icons</b> - Server configuration does not support icon setting.<br />');
            }

            // get the file path components;
            $xurl = parse_url($imageurl);
            $path = $xurl['path'];
            $filepath = $_SERVER['DOCUMENT_ROOT'] . $path;
            $path_parts = pathinfo($filepath);

            // create the image
            $ext = strtolower($path_parts['extension']);
            if ($ext == 'jpg' || $ext == 'jpe' || $ext == 'jpeg') {
                $image = imagecreatefromjpeg($filepath);
            } else if ($ext == 'png') {
                $image = imagecreatefrompng($filepath);
            } else if ($ext == "gif") {
                $image = imagecreatefromgif($filepath);
            }

            if ($image && $canfavicon) {

                // resize stuff
                list($width, $height) = getimagesize($filepath);
                list($w, $h) = getimagesize($filepath);
                $crop_x = 0;
                $crop_y = 0;

                // test for width/height
                if ($w > $h) {
                    $new_height = $h;
                    $new_width = $h;
                    // $new_width  =   floor($w * ($new_height / $h));
                    $crop_x = ceil(($w - $h) / 2);
                    $crop_y = 0;
                } elseif ($h > $w) {
                    $new_width = $w;
                    $new_height = $w;
                    // $new_height =   floor( $h * ( $new_width / $w ));
                    $crop_x = 0;
                    $crop_y = ceil(($h - $w) / 2);
                } else {
                    $new_height = $h;
                    $new_width = $w;
                }
                $width = $new_width;
                $height = $new_height;
                $square = $uploads . 'square.png';
                // crop automatically or manually
                // create a square image
                if ($w != $h && function_exists('imagecrop')) {
                    // we can crop
                    $to_crop_array = array('x' => $crop_x, 'y' => $crop_y, 'width' => $new_width, 'height' => $new_height);
                    $sq = imagecrop($image, $to_crop_array);
                    $squareimg = imagepng($sq, $square, 5);
                } else {
                    $sq = imagecreatetruecolor($new_width, $new_height);
                    imagecolortransparent($sq, imagecolorallocatealpha($image, 0, 0, 0, 127));
                    imagealphablending($sq, false);
                    imagesavealpha($sq, true);
                    imagecopyresampled($sq, $image, 0, 0, 0, 0, $new_width, $new_height, $w, $h);
                    $squareimg = imagepng($sq, $square, 5);
                }

                // initialize Favicon
                $newwidth = 32;
                $newheight = 32;

                $tmp = imagecreatetruecolor($newwidth, $newheight);
                imagecolortransparent($tmp, imagecolorallocatealpha($tmp, 0, 0, 0, 127));
                imagealphablending($tmp, false);
                imagesavealpha($tmp, true);
                imagecopyresampled($tmp, $sq, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);


                vbean_makeico($sq, $width, $height, $uploads);
                vbean_make_apples($sq, $width, $height, $uploads);
                vbean_make_ms($sq, $width, $height, $uploads, $image);
            }
        }
    }

}

if (!function_exists('vbean_setfavicons')) {

    function vbean_setfavicons() {
        global $vbean_apples;
        global $vbean_favicons;
        global $vbean_msicons;
        $upload_dir = wp_upload_dir();
        $upload_base = $upload_dir['baseurl'] . '/favicons/';

        $fileurl = trailingslashit($upload_base) . 'favicon.ico';
        echo('<link rel="icon" href="' . $fileurl . '" />' . PHP_EOL);


        $favicons = $vbean_favicons;
        foreach ($favicons as $favicon) {
            $fileurl = trailingslashit($upload_base) . 'favicon-' . $favicon . 'x' . $favicon . '.png';
            echo('<link rel="icon" type="image/png" href="' . $fileurl . '" sizes="' . $favicon . 'x' . $favicon . '" />' . PHP_EOL);
        }
        foreach ($vbean_apples as $apple) {
            $fileurl = trailingslashit($upload_base) . 'apple-touch-icon-' . $apple . 'x' . $apple . '.png';
            echo('<link rel="apple-touch-icon" sizes="' . $apple . 'x' . $apple . '" href="' . $fileurl . '" />' . PHP_EOL);
        }

        foreach ($vbean_msicons as $ms) {
            if ($ms == 70) {
                $thisname = 'tiny';
            } elseif ($ms == 150) {
                $thisname = 'square';
            } else {
                $thisname = 'large';
            }
            $fileurl = trailingslashit($upload_base) . 'msapplication-' . $thisname . '.png';

            echo('<meta name="msapplication-' . $thisname . $ms . 'x' . $ms . 'logo" content="' . $fileurl . '" />' . PHP_EOL);
        }
        $fileurl = trailingslashit($upload_base) . 'msapplication-wide.png';

        echo('<meta name="msapplication-wide310x150logo" content="' . $fileurl . '" />' . PHP_EOL);
        echo('<meta name="msapplication-TileColor" content="' . get_option('vbean_favicon_metrobgcolour') . '"/>');
    }

}
add_action('wp_head', 'vbean_setfavicons');
add_action('admin_head', 'vbean_setfavicons');
add_action('login_head', 'vbean_setfavicons');


if (!function_exists('vbean_load_colourpicker')) {

    function vbean_load_colourpicker() {

        wp_enqueue_style('wp-color-picker');
        //
        wp_enqueue_script('wp-color-picker');
    }

}        

