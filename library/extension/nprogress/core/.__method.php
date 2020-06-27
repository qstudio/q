<?php

namespace q\theme\extension\nprogress;

use q\theme\core\helper as h;

class method extends \q_nprogress {

    /**
     * Check if this is a mobile/handheld device
     *
     * @since		0.2
     * @return		Boolean
     */
    public static function is_mobile()
    {

        if ( 
            'handheld' == h::device() 
            || 'tablet' == h::device() 
        ) {

            return true;

        }

        // negative ##
        return false;

    }


}
