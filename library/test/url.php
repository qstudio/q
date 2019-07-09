<?php

namespace q\test;

class url extends \q_test {
    
    // results ##
    public static
        $results = [
            'url'       => 'MISSING',
            'status'    => '400',
            'sent'      => false,
            'received'  => false,
            'body'      => false,
        ];


    public function __construct()
    {

        // check if any url passed ##
        if ( ! $url = self::get() ) {

            self::log( 'No URL passed.' );

            return self::render();

        }

        // run request ##
        $request = self::request( $url );

        // format output ##
        self::$results = [
            'url'       => $url,
            'status'    => $request['status'],
            'sent'      => $request['sent'],
            'received'  => $request['received'],
            'body'      => $request['body'],
        ];

        // render output ##
        return self::render();

    }



    protected static function request( $url = null )
    {

        // sanity ##
        if ( is_null( $url ) ) {

            return false;

        }  

        $curlHandle = curl_init();
        $timeout = 5;

        curl_setopt( $curlHandle, CURLINFO_HEADER_OUT, true ); // enable tracking
        curl_setopt( $curlHandle, CURLOPT_URL, $url );
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
            'sent'      => $sent['request_header'], 
            'received'  => $data, 
            'body'      => false
        ];

    }



    protected static function render()
    {

?>
        <ul>
            <li><h1>URL: <?php echo self::$results['url']; ?></h1></li>
            <li><h4>Status: <?php echo self::$results['status']; ?></h4></li>
            <li>Sent:</li>
            <li><?php var_dump( self::$results['sent'] ); ?></li>
            <hr>
            <li>Received:</li>
            <li><?php var_dump( self::$results['received'] ); ?></li>
        </ul>
<?php

    }



    protected static function get()
    {

        #$url = "http://www.example.com/";
        $url = isset( $_GET['url'] ) ? $_GET['url'] : false ;

        // kick it back sanitized ##
        return false === self::is_url( $url ) ? false : $url;

    }



    protected static function is_url( $url = null )
    {

        return 
            ( filter_var( $url, FILTER_VALIDATE_URL) === FALSE ) ? 
            false : 
            true ;

    }


}

// run it ##
New url();