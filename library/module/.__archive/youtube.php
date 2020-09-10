<?php

namespace q\extension;

use q\core;
use q\core\helper as h;
use q\asset;

// load it up ##
\q\extension\youtube::run();

class youtube extends \Q {
    
    static $args = array();

    public static function run()
    {

        // add JS ##
        \add_action( 'wp_footer', [ get_class(), 'wp_footer' ], 5 ); 

        // add css ##
        // \add_action( 'wp_head', [ get_class(), 'wp_head' ], 5 ); 

    }



    public static function wp_footer()
    {

        asset\javascript::ob_get([
            'view'      => get_class(), 
            'method'    => 'javascript',
            // 'priority'  => 10,
            // 'handle'    => 'YouTube'
        ]);

    }



    public static function args( $args = false )
    {

        #helper::log( 'passed args to class' );
        #helper::log( $args );

        // update passed args ##
        self::$args = \wp_parse_args( $args, self::$args );

    }



    /**
    * JS
    *
    * @since    2.0.0
    * @return   String HTML
    */
    public static function javascript()
    {

?>
<script>
// jQuery ##
if ( typeof jQuery !== 'undefined' ) {

    jQuery(document).ready(function() {

         //console.log( 'Youtube script added....' );

        // convert youtube oembeds into autoplay iframe ##
        jQuery( document ).on( 'click', '[data-youtube]', function(e){

            // console.log( 'Youtube play button clicked.' );

            q_load_youtube( jQuery( this ) );

        });

    });

}


function q_load_youtube( e ){

    $youtube = e.attr( 'data-youtube' );

    if ( ! $youtube ) {

         console.log( 'No youtube selector found.' );

        return false;

    }

    // width + height ##
    $height = e.attr( 'data-youtube-height' ) ? e.attr( 'data-youtube-height' ) : '560' ;
    $width = e.attr( 'data-youtube-width' ) ? e.attr( 'data-youtube-width' ) : '315' ;

    //console.log( 'Youtube ID: '+$youtube );

    // convert youtube clicks into playing videos ##
    var youtube_id = q_get_youtube_id( $youtube );
    //console.log( 'video id: '+youtube_id );

    // create markup ##
    var iframeMarkup = '<iframe width="'+ $width +'" height="'+ $height +'" src="//www.youtube.com/embed/'+ youtube_id +'?rel=0&showinfo=0&autoplay=1" frameborder="0" allowfullscreen></iframe>';
    
    // add to parent element ##
    e.parent(".q-youtube").html( iframeMarkup );

}


function q_get_youtube_id( url ) {

    url = url || false ;

    if ( ! url ) {

        // console.log( 'No youtube URL passed.' );

        return false;

    }

    var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
    var match = String(url).match(regExp);

    if (match && match[2].length == 11) {
        return match[2];
    } else {
        return false;
    }

}
</script>
<?php

    }



    public static function wp_head()
    {

        asset\css::ob_get([
            'view'      => get_class(), 
            'method'    => 'css',
            // 'priority'  => 10,
            // 'handle'    => 'YouTube'
        ]);

    }



    public static function css()
    {

?>
<style>
div.q-youtube {
    background-color: #000;
    margin-bottom: 30px;
    position: relative;
    padding-top: 56.25%;
    overflow: hidden;
    cursor: pointer;
    background-size: cover;
}
div.q-youtube img {
    width: 100%;
    top: -16.84%;
    left: 0;
    opacity: 0.7;
}
div.q-youtube .play-button {
    width: 90px;
    height: 60px;
    background-color: #333;
    box-shadow: 0 0 30px rgba( 0,0,0,0.6 );
    z-index: 1;
    opacity: 0.8;
    border-radius: 6px;
}
div.q-youtube .play-button:before {
    content: "";
    border-style: solid;
    border-width: 15px 0 15px 26.0px;
    border-color: transparent transparent transparent #fff;
}
div.q-youtube img,
div.q-youtube .play-button {
    cursor: pointer;
}
div.q-youtube img,
div.q-youtube iframe,
div.q-youtube .play-button,
div.q-youtube .play-button:before {
    position: absolute;
}
div.q-youtube .play-button,
div.q-youtube .play-button:before {
    top: 50%;
    left: 50%;
    transform: translate3d( -50%, -50%, 0 );
}
div.q-youtube iframe {
    height: 100%;
    width: 100%;
    top: 0;
    left: 0;
}
</style>
<?php

    }


}
