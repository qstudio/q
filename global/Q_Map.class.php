<?php

/**
 * Google Maps Class
 *
 * @since 1.1.0
 */

if ( ! class_exists( 'Q_Map' ) )
{

    // declare Class
    class Q_Map extends Q
    {

        // set-up some properties ##
        var $api_key = '';
        var $suffix;

        public function __construct(){

            // add maps stylesheet ##
            add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts'), 997 );

        }

        public function set( $key ){

            if ( $key ) $this->api_key = $key;

        }

        public function async () {

    ?>
    <script type="text/javascript">

        // load JS async style - https://developers.google.com/maps/documentation/javascript/examples/map-simple-async ##
        function loadScript() {

            var script = document.createElement('script');
            script.type = 'text/javascript';
            script.src = '//maps.googleapis.com/maps/api/js?key=<?php echo $this->api_key; ?>&v=3.exp&'+'callback=initialize';
            document.body.appendChild(script);

        }

        window.onload = loadScript;

    </script>
    <?php

        }

        public function wp_enqueue_scripts() {

            wp_register_style( 'q-map', q_locate_template( "css/q.map.css", false ), '', '0.1', 'all' );
            wp_enqueue_style( 'q-map' );

            wp_enqueue_script( 'q-map', q_locate_template( "javascript/q.map.js", false ), false, '1.0', true );
            wp_enqueue_script( 'q-map' );

        }

    }

}