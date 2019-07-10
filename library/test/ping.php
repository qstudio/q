<?php

namespace q\test;

class ping extends \q_test {
    
    // results ##
    public static
        $results = [
            'ip'        => 'MISSING',
            'status'    => '400',
            'output'    => false,
        ];


    public function __construct()
    {

        // check if any url passed ##
        if ( ! $ip = self::get() ) {

            self::log( 'No IP passed.' );

            return self::render();

        }

        // run request ##
        $request = self::ping( $ip );

        // format output ##
        self::$results = [
            'ip'        => $ip,
            'status'    => $request['status'],
            'output'    => $request['output'],
        ];

        // render output ##
        return self::render();

    }



    protected static function ping( $ip = null )
    {

        // sanity ##
        if ( is_null( $ip ) ) {

            return false;

        }  

        $curlHandle = curl_init();
        $timeout = 5;

        curl_setopt( $curlHandle, CURLINFO_HEADER_OUT, true ); // enable tracking
        curl_setopt( $curlHandle, CURLOPT_URL, $ip );
        curl_setopt( $curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt( $curlHandle, CURLOPT_FILETIME, true);
        curl_setopt( $curlHandle, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curlHandle, CURLOPT_HEADER, true );
        curl_setopt( $curlHandle, CURLOPT_CONNECTTIMEOUT, $timeout );
        curl_setopt( $curlHandle, CURLOPT_NOBODY, true );
        
        $data = curl_exec( $curlHandle );
        
        $status = curl_getinfo( $curlHandle, CURLINFO_HTTP_CODE );

        $sent = curl_getinfo( $curlHandle );

        curl_close( $curlHandle );

        // kick back results ##
        return [ 
            'status'    => $status,
            'output'    => $output, 
        ];

    }


    protected static function get_os()
    {

        return 'win';

    }



    protected static function render()
    {

?>
        <ul>
            <li><h1>URL: <?php echo self::$results['url']; ?></h1></li>
            <li><h4>Status: <?php echo self::$results['status']; ?></h4></li>
            <hr>
            <li>Output:</li>
            <li><?php var_dump( self::$results['output'] ); ?></li>
        </ul>
<?php

    }



    protected static function get()
    {

        $ip = isset( $_GET['ip'] ) ? $_GET['ip'] : false ;

        // kick it back sanitized ##
        return false === self::is_ip( $ip ) ? false : $ip;

    }



    protected static function is_ip( $string = null )
    {

        return 
            ( filter_var( $string, FILTER_VALIDATE_IP ) === FALSE ) ? 
            false : 
            true ;

    }


}

// run it ##
New ping();