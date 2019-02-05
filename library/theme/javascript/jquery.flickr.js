/* 
 * Q Flickr
 * jQuery image loader
 * example call ##
 * q_flickr( '57548ffd829fddf0272cfebb27f93526', '72157632758194917', '1', '.qflickr_left', 'z' ); // 1 big ##
 * 
 * http://stackoverflow.com/questions/4383527/jquery-json-flickr-api-returning-photos-in-a-set?rq=1
 * http://www.flickr.com/services/api/misc.urls.html
 */

function q_flickr( api_key, photoset_id, per_page, element, size, placement, waiting ){

    // required elements ##
    if ( !api_key || !photoset_id || !per_page ) { 

        //console.log('Flickr Error');
        return false; 

    }

    // variables ##
    size = typeof size !== 'undefined' ? size : 'm';
    element = typeof element !== 'undefined' ? element : '.qflickr';
    placement = typeof placement !== 'undefined' ? placement : '';
    $waiting = typeof $waiting !== 'undefined' ? $waiting : 2000; // wait to load ##

    //SET API CALL BASED ON INPUT
    var apiCall = "http://api.flickr.com/services/rest/?photoset_id="+photoset_id+"&per_page="+per_page+"&api_key="+api_key+"&page=1&format=json&method=flickr.photosets.getPhotos&jsoncallback=?";

    //SEND API CALL AND RETURN RESULTS TO A FUNCTION
    clearTimeout('qflickr');
    qflickr = setTimeout( function() { // delay requests ##

        jQuery.getJSON( apiCall, null, function(data) {

            var listItems = '';
            
            //LOOP THROUGH DATA
            jQuery.each(data.photoset.photo, function(i,photo){
                
                //console.log("data");
                
                //LINK TO IMAGE SOURCE
                var img_src = "http://farm" + photo.farm + ".static.flickr.com/" + photo.server + "/" + photo.id + "_" + photo.secret + "_" + size + ".jpg";

                //LINK TO IMAGE PAGE (REQUIRED BY FLICKR TOS)
                var a_href = "http://www.flickr.com/photos/" + data.photoset.owner + "/" + photo.id + "/";

                if ( placement == 'background' ) {

                    // place the image on the background of the li to allow for contained image resizing ##
                    listItems += '<li style="background-image: url('+img_src+');"><a href="' + a_href + '" class="whole" target="_blank" title="View on Flickr"></a></li>';

                // wrap the image inside the <a> ##
                } else {

                    listItems += '<li><a href="' + a_href + '" target="_blank" title="View on Flickr"><img src="'+img_src+'" /></a></li>';

                }

            });

            // append to passed element ##
            //console.log('adding lis to: '+element);
            jQuery(element).append(listItems).hide().fadeIn(800);

        });

     }, $waiting);

}