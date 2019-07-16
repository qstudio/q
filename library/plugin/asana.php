<?php

/**
 * Asana modules and wrappers
 * 
 * 
 */

namespace q\plugin;

use q\core\core as core; 
use q\core\helper as helper;
// use q\test\log as log;

// \q\plugin\asana::run();

class asana extends \Q {
    

    public static function run()
    {

        

    }



    /**
     * Create task in Asana
     * 
     * @param   $args   Array
     * @return  Mixed
     */
    public static function create_task( Array $args = null )
    {

        // sanity ##
        if (
            is_null( $args )
            || ! isset( $args['method'] )
        ){

            helper::log( 'Error in passed arguments' );

            return false;

        }

        // switch over methods ##
        switch ( $args['method'] ) {

            case "api" :

                helper::log( 'No API endpoints developered yet.' );

                return false;

            break ;

            default :
            case "email" :

                self::create_task_email( $args );

            break ;

        }

    }



    /**
     * Create task in Asana via API
     * 
     * https://asana.com/developers/api-reference/tasks#create
     * https://github.com/Asana/php-asana
     */
    public static function create_task_api()
    {}



    /**
     * Create task in Asana via Email
     * 
    Add tasks by email
    You can add a task to this list by sending an email to:
    x+310727860574480@mail.asana.com
    The subject will be the task name
    The body will be the task description
    All email attachments will be attached to the task
    You can cc teammates to add them as task followers
    Learn more from our Asana Guide article.
     */
    public static function create_task_email( Array $args = null )
    {

        // sanity ##
        if ( 
            is_null( $args )
            || ! is_array( $args )
            || ! isset( $args['response'] )
            || ! isset( $args['email'] )
        ) {

            helper::log( 'Error in passed araguments...' );

            return false;

        }

        // helper::log( $args );

        // work out subject ##
        $subject = isset( $args['subject'] ) ? $args['subject'] : 'Q Tracking Error' ; 

        // content ##
        $content = $args['response'];

        // do we have a source to link back to ? ##
        if ( isset( $args['source'] ) ) {

$content .= '

Source: '.$args['source'];

        }

        // allow for faking ##
        if ( 
            isset( $args['fake'] ) 
            && TRUE === $args['fake']
        ) {

            // log ##
            helper::log( 'To: '.$args['email'].' --> Subject: '.$subject.' --> '.$content );

            // return ##
            return true;

        }

        // headers --- CLUCKY.. ##
        $headers =  'MIME-Version: 1.0' . "\r\n"; 
        $headers .= 'From: Web Team<btoth@greenheart.org>' . "\r\n";
        $headers .= 'Content-Type: text/plain; charset=\"utf-8\"\r\n' . "\r\n"; 
        $headers .= "Reply-To: wordpress@greenheart.org\r\n";
        $headers .= 'Cc: ksmithy@greenheart.org' . "\r\n";

        // Create Asana task via email ##
        $email = mail(
            
            $args['email'], // 'x+310727860574480@mail.asana.com',
            $subject,
            $content,
            $headers

        );

        // log ##
        helper::log( 'Email sent to Asana: '.$email );

        // kick back ##
        return true;

    }

}