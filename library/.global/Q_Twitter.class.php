<?php

/**
 * Twitter Functions
 *
 * @since 1.0.1
 * @author Q Studio
 * @url http://qstudio.us
 */

if ( ! class_exists ( "Q_Twitter" ) )
{
    
    
    class Q_Twitter extends Q 
    {
        
        /**
         * Class Constructor
         */
        public function __construct(){
        
            

        }
        
        
        /**
         * get tweets
         * 
         * @param       Array   $args
         * @return      Mixed   Array of tweets on success or boolean false
         */
        public function get( $args )
        {
            
            if ( ! $args || ! is_array( $args ) ) { return false; }
            
            // default arguments ##
            $defaults = array(
                'mode'          => 'search',
                'debug'         => false,
                'count'         => 20,
                'retweet'       => false,
                'cache'         => true,
                'cache_length'  => 'three_hours',
                'cacheid'       => ''
            );

            // merge args - WordPress function #
            $args = wp_parse_args( $args, $defaults );

            // convert "args" array to object - Custom function ##
            $args = q_array_to_object( $args );

            // instatiate transient object ##
            $q_transients = new Q_Transients();

            // should we get this from cache ? ##
            if ( $args->cache === true && $args->cacheid ) {

                $twitter_data = $q_transients->get( $args->cacheid );
                
                // test data ##
                #Q_Control::log( $twitter_data );

            }

            if ( false === $twitter_data || '' === $twitter_data ) { // nothing in transients ##

                // check for required oAuth details ##
                if ( ! $args->consumer_key || ! $args->consumer_secret || ! $args->oauth_access_token || ! $args->oauth_access_token_secret ) { 
                    if ( $args->debug === true ) {
                        echo 'Error - missing oAuth details'; // echo an error, if debugging ##
                    }
                    return false; // return nothing ##
                }

                // mode ##
                $twitter_mode = $args->mode; // convert to normal varaible for switch - not sure why that's required? ##
                switch ( $twitter_mode ) {

                case "search": // search API ##
                    $args->url = "https://api.twitter.com/1.1/search/tweets.json"; // url ##
                    break;

                case "home_timeline": // home_timeline API ##
                    $args->url = "https://api.twitter.com/1.1/statuses/home_timeline.json"; // url ##
                    break;

                case "sitestream": // sitestream API ##
                    $args->url = "https://sitestream.twitter.com/1.1/site.json"; // url ##
                    break;    

                case "user_timeline": // user_timeline API - default ##
                default:
                    $args->url = "https://api.twitter.com/1.1/statuses/user_timeline.json"; // url ##
                    break;

                break;

                }

                // compile querystring ##
                $args->query = '?screen_name='.$args->username.'&follow='.$args->follow.'&include_rts='.$args->retweet.'&q='.urlencode($args->search).'&count='.$args->count;

                // prepare oAuth args ##
                $oauth = array(
                    'screen_name'               => $args->username,
                    'count'                     => $args->count,
                    'include_rts'               => $args->retweet,
                    'q'                         => ($args->search),
                    'follow'                    => ($args->follow),
                    //'include_entities'        => true,
                    'oauth_consumer_key'        => $args->consumer_key,
                    'oauth_nonce'               => time(),
                    'oauth_signature_method'    => 'HMAC-SHA1',
                    'oauth_token'               => $args->oauth_access_token,
                    'oauth_timestamp'           => time(),
                    'oauth_version'             => '1.0'
                    );

                $base_info = $this->build_base_string( $args->url, 'GET', $oauth );
                $composite_key = rawurlencode($args->consumer_secret) . '&' . rawurlencode($args->oauth_access_token_secret);
                $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
                $oauth['oauth_signature'] = $oauth_signature;

                // Make CURL Request ##
                $header = array( $this->build_authorization_header( $oauth ), 'Expect:' );
                $options = array(
                    CURLOPT_HTTPHEADER => $header,
                    CURLOPT_HEADER => false,
                    CURLOPT_URL => $args->url.$args->query, 
                    CURLOPT_RETURNTRANSFER => true, 
                    CURLOPT_SSL_VERIFYPEER => false
                    );

                $feed = curl_init();
                curl_setopt_array($feed, $options);
                $json = curl_exec($feed);
                curl_close($feed);

                // abort on error ##
                if ( $json === false ) { 
                    if ( $args->debug === true ) {
                        echo 'CURL Error'; // echo an error, if debugging ##
                    }
                    return false; 
                }

                // JSON decode returned data ##
                $twitter_data = json_decode( $json );

                // optionally debug returned data ##
                if ( $args->debug === true ) $this->pr( $twitter_data );

                // search fix - object returned is nested deeper ##
                if ( $twitter_mode === 'search' ) { // array shift ##
                    $twitter_data = $twitter_data->statuses;
                }

                // TODO -- some extra error checking here ##

                // cache ##
                if ( $args->cache === true ) {

                    // check transient cache ##
                    $q_transients->set( $args->cacheid, $twitter_data );

                }

            }
            
            // last test ##
            #wp_die( $this->pr( $twitter_data ) );

            // return array of data ##
            return $twitter_data;
            
        }
        
        
        /**
         * Build base string
         * 
         * @since       1.0
         * @link        http://stackoverflow.com/a/12939923/591486
         * @return      String
         */
        public function build_base_string( $baseURI, $method, $params ) 
        {
            
            $r = array();
            ksort($params);
            foreach($params as $key=>$value){
                $r[] = "$key=" . rawurlencode($value);
            }
            
            return $method."&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
            
        }

        
        /**
         * Build authorizion header
         * 
         * @since       1.0
         * @link        http://stackoverflow.com/a/12939923/591486
         * @param       String    $oauth
         * @return      String
         */
        function build_authorization_header( $oauth ) {
            
            $r = 'Authorization: OAuth ';
            $values = array();
            
            foreach($oauth as $key=>$value)
                $values[] = "$key=\"" . rawurlencode($value) . "\"";
            $r .= implode(', ', $values);
            
            return $r;
            
        }

        
        /**
         * Add href link to text string ##
         *  
         * @since       1.0
         * @link        http://saturnboy.com/2010/02/parsing-twitter-with-regexp/
         * @param       String        $text
         * @return      String
         */
        function add_href ( $text ) {

            $text = preg_replace(
                '@(https?://([-\w\.]+)+(/([\w/_\.]*(\?\S+)?(#\S+)?)?)?)@',
                '<a href="$1" target="_blank" rel="nofollow">$1</a>',
                $text
            );

            return $text;

        }


        /**
         * Add href link to twitter username in text string
         * 
         * @since       1.0
         * @link        http://saturnboy.com/2010/02/parsing-twitter-with-regexp/
         * @param       String        $text
         * @return      String
         */

        function add_username ( $text ) {

            $text = preg_replace(
                '/@(\w+)/',
                '<a href="http://twitter.com/$1" target="_blank" rel="nofollow">@$1</a>',
                $text
            );

            return $text;

        }


        /**
         * Add href link to twitter hashtag in text string
         * 
         * @since       1.0
         * @param       String    $text
         * @return      type
         * @link        http://saturnboy.com/2010/02/parsing-twitter-with-regexp/
         */

        function add_hashtag ( $text ) {

            $text = preg_replace(
                '/\s+#(\w+)/',
                ' <a href="http://search.twitter.com/search?q=%23$1" target="_blank" rel="nofollow">#$1</a>',
                $text
            );

            return $text;

        }


        
        
    }
    
    
}

