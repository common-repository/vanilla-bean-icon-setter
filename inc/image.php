<?php


namespace VanillaBeans\Favicon;

if(!function_exists('\VanillaBeans\Favicon\vbean_image_bmp')){

    function vbean_image_bmp( $im ) {
                    $width = imagesx( $im );
                    $height = imagesy( $im );


                    $pixel_data = array();

                    $opacity_data = array();
                    $current_opacity_val = 0;

                    for ( $y = $height - 1; $y >= 0; $y-- ) {
                            for ( $x = 0; $x < $width; $x++ ) {
                                    $color = imagecolorat( $im, $x, $y );

                                    $alpha = ( $color & 0x7F000000 ) >> 24;
                                    $alpha = ( 1 - ( $alpha / 127 ) ) * 255;

                                    $color &= 0xFFFFFF;
                                    $color |= 0xFF000000 & ( $alpha << 24 );

                                    $pixel_data[] = $color;


                                    $opacity = ( $alpha <= 127 ) ? 1 : 0;

                                    $current_opacity_val = ( $current_opacity_val << 1 ) | $opacity;

                                    if ( ( ( $x + 1 ) % 32 ) == 0 ) {
                                            $opacity_data[] = $current_opacity_val;
                                            $current_opacity_val = 0;
                                    }
                            }

                            if ( ( $x % 32 ) > 0 ) {
                                    while ( ( $x++ % 32 ) > 0 )
                                            $current_opacity_val = $current_opacity_val << 1;

                                    $opacity_data[] = $current_opacity_val;
                                    $current_opacity_val = 0;
                            }
                    }

                    $image_header_size = 40;
                    $color_mask_size = $width * $height * 4;
                    $opacity_mask_size = ( ceil( $width / 32 ) * 4 ) * $height;


                    $data = pack( 'VVVvvVVVVVV', 40, $width, ( $height * 2 ), 1, 32, 0, 0, 0, 0, 0, 0 );

                    foreach ( $pixel_data as $color )
                            $data .= pack( 'V', $color );

                    foreach ( $opacity_data as $opacity )
                            $data .= pack( 'N', $opacity );


                    $image = array(
                            'width'                => $width,
                            'height'               => $height,
                            'color_palette_colors' => 0,
                            'bits_per_pixel'       => 32,
                            'size'                 => $image_header_size + $color_mask_size + $opacity_mask_size,
                            'data'                 => $data,
                    );

                    return $image;
            }
    
}

