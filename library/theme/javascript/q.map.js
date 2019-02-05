/* 
 * Q Google Map functions
 */

// info & markers tracking variables ##
var infos = [], markers = [], bounds, map;


// initialize G Map ##
function initialize() {
    
    bounds = new google.maps.LatLngBounds();
    
    // set map options 
    var myOptions = {
        scrollwheel: false, // block mousewheel scrolling ##
        //center: new google.maps.LatLng(33.890542, 151.274856),
        //zoom: 2,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    
    // declare map variable ##
    map = new google.maps.Map(document.getElementById("map-canvas"), myOptions );
        
    // zoom map 
    //map.setZoom(8);
        
    // load in markers ##
    setMarkers( map, locations )
    
    //console.log("google maps loaded");
        
    // center and zoom based on found bounds ##
    map.fitBounds(bounds);
    map.panToBounds(bounds); 
    
}


function setMarkers(map,locations){

    // variables ##
    var marker, i;

    for ( i = 0; i < locations.length; i++ ) {  

        var $title = locations[i][0];
        var $lat = locations[i][1];
        var $lon = locations[i][2];
        var $info =  locations[i][3];
        var $image =  locations[i][4];
        var $link =  locations[i][5];
        var $category =  locations[i][6];
        
        // check category ##
        //console.log("add '"+$title+"' to '"+$category+"'");
        
        $latlngset = new google.maps.LatLng( $lat, $lon );
        
        var marker = new google.maps.Marker({  
                map: map
            ,   title: $title
            ,   position: $latlngset
            ,   icon: $icon
            ,   category: $category
        });
        
        // add marker to correct category ##
        marker.category = $category;     

        // update markers array ##
        markers.push(marker);
        
        // center map ##
        //map.setCenter(marker.getPosition())
        
        //extend the bounds to include each marker's position
        bounds.extend(marker.position);
        
        // build html content ##
        var content = "<div class='hook'><a href='"+htmlDecode($link)+"' class='iw_image'><img src='"+htmlDecode($image)+"' /></a><div class='text'><h2>" + htmlDecode($title) + "</h2><p>" + htmlDecode($info) + "</p><a href='"+htmlDecode($link)+"' class='iw_link'>VIEW</a></div></div>"; 
        
        var infowindow = new google.maps.InfoWindow()
        
        google.maps.event.addListener(marker,'click', (function(marker,content,infowindow){ 
            
            return function() {
        
                /* close the previous info-window */
                closeInfos();

                infowindow.setContent(content);
                infowindow.open(map,marker);

                /* keep the handle, in order to close it on next click event */
                infos[0] = infowindow;
        
            };
        })(marker,content,infowindow)); 
        
    }
}

function closeInfos() {
 
   if(infos.length > 0){
 
      /* detach the info-window from the marker ... undocumented in the API docs */
      infos[0].set("marker", null);
 
      /* and close it */
      infos[0].close();
 
      /* blank the array */
      infos.length = 0;
      
   }
   
}


// jQuery ##
if ( typeof jQuery !== 'undefined' ) {
    
    jQuery(document).ready(function() {

        // map filters ##
        jQuery(".map-controls a").click(function(){
            
            /* close open info-window */
            closeInfos();
            
            // category was clicked ##
            var $category = jQuery(this).data('category');
            //console.log("category value: "+$category);
            
            // remove all active classes ##
            jQuery(".map-controls a").removeClass("active");
            
            // add active to clicked item ##
            jQuery(this).addClass("active");
            
            var i;
            for ( i = 0; i < markers.length; i++ ) {
                
                if ( $category == 'all' ) { // show all 
                    
                    markers[i].setVisible(true);
                    
                } else {
                
                    if ( markers[i].category == $category ) {

                        markers[i].setVisible(true);
                        //console.log( "category: "+markers[i].category);

                    } else {

                        markers[i].setVisible(false);

                    }
                    
                }

            }
            
            return false;
            
        });
        
    });
    
}

// js decode html ##
// http://stackoverflow.com/questions/1912501/unescape-html-entities-in-javascript
function htmlDecode( input ) {
    
    var e = document.createElement('div');
    e.innerHTML = input;
    return e.childNodes.length === 0 ? "" : e.childNodes[0].nodeValue;
    
}