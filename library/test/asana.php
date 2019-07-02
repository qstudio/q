<?php

/**
 * Asana modules and wrappers
 * 
 * 
 */

namespace q\test;

use q\core\core as core; 
use q\core\helper as helper;
use q\test\log as log;

// \q\test\asana::run();

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
        ) {

            helper::log( 'Error in passed araguments...' );

            return false;

        }

        // content ##
        $content = $args['response'];

        // headers --- CLUCKY.. ##
        $headers =  'MIME-Version: 1.0' . "\r\n"; 
        $headers .= 'From: Web Team<mgurner@greenheart.org>' . "\r\n";
        $headers .= 'Content-Type: text/plain; charset=\"utf-8\"\r\n' . "\r\n"; 
        $headers .= "Reply-To: wordpress@greenheart.org\r\n";
        $headers .= 'Cc: btoth@greenheart.org' . "\r\n";

        // Create Asana task via email ##
        $email = mail(
            
            'x+310727860574480@mail.asana.com',
            'Email Delivery Error',
            $content,
            $headers

        );

        // log ##
        helper::log( 'Email sent to Asana: '.$email );

        // kick back ##
        return true;

    }

}