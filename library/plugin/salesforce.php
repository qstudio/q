<?php

namespace q\plugin;

use q\core\core as core;
use q\core\helper as helper;
#use q\core\options as options;
#use q\controller\generic as generic;

// load it up ##
#\q\plugin\salesforce::run();

class salesforce extends \Q {

    public static function run()
    {


    }

    
    /**
    * Pardot code for insertion in template
    * 
    * @since       1.6.4
    * @return      string   HTML
    */
    public static function the_pardot()
    {

?>
        <script type="text/javascript">
        // <![CDATA[
            piAId = '188322';
            piCId = '1479';

            (function() {
                function async_load(){
                    var s = document.createElement('script'); s.type = 'text/javascript';
                    // s.src = ('https:' == document.location.protocol ? 'https://pi' : 'http://cdn') + '.pardot.com/pd.js';
                    s.src = '<?php echo self::get_plugin_url( 'javascript/pd.js' ); ?>'; // local script ##
                    var c = document.getElementsByTagName('script')[0]; c.parentNode.insertBefore(s, c);
                }
                if(window.attachEvent) { window.attachEvent('onload', async_load); }
                else { window.addEventListener('load', async_load, false); }
            })();
        // ]]>
        </script>
<?php

    }



}