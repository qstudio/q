<?php

/**
 * WP API Transient Control Class ##
 *
 * @package WordPress
 * @subpackage 4Trees
 * @since 0.4
 */

class Q_Transients {
    
    public $q_transients;
    public $q_transients_comments;
    public $decay = array(
                        'three_hours'   => 10800,
                        'six_hours'     => 21600,
                        'twelve_hours'  => 43200,
                        'one_day'       => 86400,
                        'two_days'      => 172800,
                        'one_week'      => 604800,
                    );
    
    public function __construct(){
        $this->build();
        $this->decay = q_array_to_object( $this->decay ); // convert to object ## 
    }
    
    public function __destruct(){
        
        // all transients ##
        $q_transients = get_option('q_transients'); // load saved options ##
        $q_transients = ( is_array($q_transients) ? array_merge( $q_transients, $this->q_transients ) : $this->q_transients ); // merge with new ##
        $q_transients = array_unique($q_transients); // delete duplicate entries ##
        #echo 'all: '; pr($q_transients); // test ##
        q_add_update_option( 'q_transients', $q_transients, '', 'yes' ); // update ##
        
        // comment transients
        $q_transients_comments = get_option('q_transients_comments'); // load saved options ##
        $q_transients_comments = ( is_array($q_transients_comments) ? array_merge( $q_transients_comments, $this->q_transients_comments ) : $this->q_transients_comments ); // merge
        $q_transients_comments = array_unique($q_transients_comments); // delete duplicate entries ##
        #echo 'comments: '; pr($q_transients_comments); // test ##
        q_add_update_option( 'q_transients_comments', $q_transients_comments, '', 'yes' ); // update ##
        
    }

    public function build( ){
        $this->q_transients = array();
        $this->q_transients_comments = array();
    }

    public function add( $key ){
        if ( $key ) {
            $this->q_transients[] = $key; // add to internal array ##
            #echo 'added: '.$key;
        }
    }

    public function comments( $key ) {
        if ( $key ) {
            $this->q_transients_comments[] = $key; // add to internal array ##
        }
    }
    
    public function get( $key, $type = 'array' ){
        if ( $key ) {
            
            $this->add( $key ); // add key ##
            $get = get_transient( $key ); // get transient data ##
            if ( $type === 'object' ) {
                $get = q_array_to_object( $get ); // convert to object ##    
            }
            if ( $get === false ) {
                
                #echo '"'.$key.'" is empty: <br />';
                return false;
                
            } else {
                
                return $get;
                
            }
        }
    }
    
    public function __toString()  
    {  
        #echo "Using the toString method: ";  
        return $this->get();  
    }  
    
    public function set( $key, $value, $decay = '', $comments = false ){
        if ( $key && $value ) {
            // work out decay ##
            $decay = ( $decay ? $decay : $this->decay->one_day );
            #echo 'set: '.$key.' // for: '.intval($decay);
            set_transient( $key, $value, intval($decay) );
            #$this->add( $key ); // add key ##
            if ( $comments ) { 
                $this->comments( $key ); // add comment note ##
            }
        }
    }

    public function delete( $key = false ){
        
        if ( $key == 'all' ) { // clear all ##
            $all_q_transients = get_option('q_transients');
            #echo 'all transients:'; pr($all_q_transients);
            if ( $all_q_transients ) {
                foreach ( $all_q_transients as $key ) {
                    #echo 'delete: '.$key.'<br />';
                    delete_transient( $key );
                }
            }
            
            // delete option also ##
            delete_option('q_transients');
            delete_transient( 'q_get_option' ); // delete options
        
        } elseif ( $key == 'comments' ) { // clear all transients with comments ##
            $all_q_transients_comments = get_option('q_transients_comments');
            #echo 'all transients with comments:'; pr($all_q_transients_comments);
            if ( $all_q_transients_comments ) {
                foreach ( $all_q_transients_comments as $key ) {
                    #echo 'delete: '.$key.'<br />';
                    delete_transient( $key );
                }
            }
            
            // delete option also ##
            delete_option('q_transients_comments');
            
        } elseif ( $key ) { // remove one field ##
            #echo 'delete: '.$key;
            delete_transient( $key );
        }
        
    }
    
}

// instatiate global class object ##
#global $q_transients;
#$q_transients = new Q_Transients();