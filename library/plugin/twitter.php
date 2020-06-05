<?php

namespace q\plugin;

use q\core;
use q\core\helper as h;
use q\ui;
use q\get;

// load it up ##
\q\plugin\twitter::run();

class twitter extends \Q {

    public static function run()
    {

        // if on a single post screen, generate and insert twitter:OG tags ##
        \add_action( 'wp_head', [ get_class(), 'meta' ], 12 );

    }

    
    /**
    * generate and insert meta tags for twitter sharing control
    * 
    * @since       2.1.5
    * @return      mixed   boolean or HTML
    */
    public static function meta()
    {

        // check we can get a post object ##
        if ( ! $the_post = get\wp::the_post() ) { 
        
            // h::log( 'No post object' );

            return false; 
        
        }

        // check we are on a single post or page, if not bulk ##
        if ( 
            ! \is_single()
            && ! \is_page()
        ) { 
        
            // h::log( 'Not a single post or page query' );

            return false; 
        
        }

        // get all the data we need ##
        $array = [];
        // $array['title'] = $the_post->post_title;
        // $array['description'] = wp_post::excerpt_from_id( $the_post->post_title, 200 );
        // $array['image'] = \wp_get_attachment_image_src( \get_post_thumbnail_id( $the_post->ID ), 'large' );
        $array['card'] = 'summary_large_image';

?>
        <meta name="twitter:card" content="<?php echo $array['card']; ?>">
<?php

    }



}