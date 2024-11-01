=== Plugin Name ===
Contributors: vsmash
Donate link: https://www.velvary.com.au/vanilla-beans/wordpress/iconifier/
Tags: icon, favicon, iconify, branding, site icon, icon setter, custom icon
Requires at least: 4.0
Tested up to: 5.4
Stable tag: 2.81
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


== Description ==
Icon Setter (Iconifier) is a simple set-site-icon plugin for all devices.
 
Simply visit settings in admin, upload or choose your logo from the media 
library and it will set:
*   All Apple device icons and tiles
*   All windows device icons and tiles
*   All Android and smartphone device icons and tiles
*   ALl desktop website icons

Designed to solve your wordpress website branding setup in moments without 
dependency on themes or jetpack. 

Vanilla Beans are published separately so that you can choose your beans to suit
your needs.

See your Vanilla Bean page in admin for other beans available.

Tested with php v5.4 to v7.4
PHP v5.5+ supports cropping of Microsoft wide tile image version.

== Installation ==
Automatic install through wordpress plugins page 
OR
1. Upload zip file to the `/wp-content/plugins/` directory and uncompress it
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Visit Vanilla Beans -> icon setter in wordpress admin to upload your logo

== Changelog ==
= 2.81 =
- tested against wp 5.42

= 2.80 =
- added icon to login page

= 2.75 =
- tested against wp 4.5

= 2.74 =
- updated javascript to avoid jquery version dependency

= 2.71 =
- hotfix to icon setter


= 2.70 =
- fixed issue with url test not working all the time
- updated common function to use is_404 test
- re-factored iconmaking process

= 2.66 =
Bugfix to T_STRING error during install


= 2.62 =
Updated common functions
Added missing icon

= 2.61 =
Changed code to support new imagecrop variables
Created intelligent cropping so that squares are cropped from image centre.

= 2.43 =
Tested against wp 4.4
Added instructional code

= 2.42 =
Tested against wp 4.23

= 2.41 =
Added Vanilla product list. No functional changes.

= 2.40 =
Added windows tile background colour option

= 2.23 =
Updated image manipulation functions

= 2.22 =
Copy change
Added exception handling missing files
Added favicon to admin section

= 2.0 =
Added favicon multilayer icon creation

= 1.4 =
Changed Favicon.ico to create transparency

= 1.3 =
Changed Favicon.ico to enable transparent background

= 1.22 =
Copy change

= 1.21 =
Removed javascript alert from settings

= 1.2 =
Added Microsoft wide tile support

= 1.12 =
Added php 5.5 crop support

= 1.01 =
Release tweak

= 1.0 =
Initial Release