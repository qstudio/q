<?php

// namespace ##
namespace q\extension\consent;

// Q ##
use q\core;
use q\core\helper as h;
use q\extension;

// load it up ##
//  \q\extension\consent\api::run();

class api extends extension\consent {

    /**
     * Instatiate Class
     *
     * @since       0.1.0
     * @return      void
     */
    public static function run()
    {

    }


    /**
     * Get privacy content via REST API request to greenheart.org
     * Cache result in WP Transients for 1 day
     *
     * @access      public
     * @since       0.1.0
     * @return      string
     */
    public static function privacy()
    {

        // uncomment this to defeat cache ##
        // // \delete_site_transient( 'q_consent_privacy' );

        // check if we have a match in the cache first and return that ##
        if ( false === ( $string = \get_site_transient( 'q_consent_privacy' ) ) ) {

            // try to fetch privacy content from API ##
            // The API on greenheart.org is extended with a new "page" end-point to accept "privacy" parameter and uses get_page_by_path() from there ##
            // http://v2.wp-api.org/reference/pages/
            // https://greenheart.org/api/v2/page/get/privacy

            // if API request fails or timesout, display default message ##
            // $default =  \sprintf(
            //     'Sorry, we could not fetch the Privacy Policy right now, please view <a href="%s" target="_blank">Privacy Policy</a> or try again later.'
            //     , 'https://greenheart.org/privacy/'
            // );

            $default = '<h3>Privacy Policy</h3>
<p>Thank you for visiting our website. This privacy policy tells you how Greenheart Exchange Online uses personally identifying information (PII) collected at this site. Please read this privacy policy before using the site or submitting any personal information. By using the site, you are accepting the practices described in this privacy policy. These practices may be changed, but any changes will be posted and changes will only apply to activities and information on a going forward, not retroactive basis. You are encouraged to review the privacy policy whenever you visit the site to make sure that you understand how any personal information you provide will be used.</p>
<p>Note that the privacy practices set forth in this privacy policy are for this website only. If you link to other websites, please review the privacy policies posted at those sites.</p>
<p><strong>Collection of Personally Identifying Information</strong><br>
PII includes names, postal addresses, email addresses, etc., when voluntarily submitted by our visitors. The information you provide is used to fulfill your specific request. This information is only used to fulfill your specific request, unless you give us permission to use it in another manner, for example to add you to one of our mailing lists. If you identify yourself to us by sending us an email with questions or comments. We may, at our sole discretion, either file your comments for future reference or discard the information after we have received it.</p>
<p>We exercise great care in providing secure transmissions of your PII from your PC to our servers. Our secure server software encrypts information, ensuring that Internet transactions remain private (unless sent by an unsecured means such as email). Neither Greenheart Exchange nor any telephone network nor service providers we utilize are responsible for incorrect or inaccurate transcriptions of information or for any human error, technical malfunctions, lost-delayed data transmission, omission, interruption, deletion, defect, line failures or any telephone network, computer equipment, software, inability to access any website or on-line service or any other error or malfunction or misdirected entries.</p>
<p><strong>Cookie/Tracking Technology</strong><br>
The site may use cookie and tracking technology to collect non-personal identifying information depending on the features offered. Non-PII might include the browser you use, the type of computer operating system you use, the Internet service providers and other similar information. Our system also automatically gathers information about the areas you visit on our sites and about the links you may select from within our site to the other areas of the World Wide Web or elsewhere online. We use such information in the aggregate to understand how our users as a group use the services and resources provided on our site. This way we know which areas of our sites are favorites of our users, which areas need improvement and what technologies are being used so that we may continually improve our sites. Cookie and tracking technology are useful for gathering information such as identifying a website user’s browser type and operating system, tracking the number of visitors to the site, and understanding how visitors use the site. Cookies can also help customize the site for visitors. Personal information cannot be collected via cookies and other tracking technology, however, if you previously provided personally identifiable information, cookies may be tied to such information. Aggregate cookie and tracking information may be shared with third parties but that aggregate information does not identify individual website users. Our web servers do not record email addresses of the visitors unless information is submitted by users. We may determine what technology is available through your browser in order to provide you with the most appropriate version of a web page.</p>
<p><strong>Distribution of Information</strong><br>
We may share information gathered by us from our website with governmental agencies or other companies assisting us in fraud prevention or investigation. We may do so when: (1) permitted or required by law; or, (2) trying to protect against or prevent actual or potential fraud or unauthorized transactions; or, (3) investigating fraud which has already taken place. The information is not provided to these companies for marketing purposes.</p>
<p>Third parties who provide hosting services or other day-to-day services that make possible the operation of this website may have access to information that you provide us to the extent those third parties require access to our databases to service the website.</p>
<p>We reserve the right to transfer information we have secured from you in connection with the sale or transfer of all or part of our assets. We are not responsible for any breach of security or for any actions of any third parties that receive information from us.</p>
<p>This website is operated in the United States of America. If you are located outside of the United States, please be aware that any information you provide will be transferred to the United States. By using this website, you consent to this transfer.</p>
<p><strong>Commitment to Data Security</strong><br>
Your personally identifiable information is kept secure. All emails and newsletters from this site allow you to opt out of further mailings.</p>
<p><strong>Privacy Contact information</strong><br>
If you have any questions, concerns, or comments about our privacy policy you may contact us using the information below:<br>
By email: support@greenheart.org<br>
By phone: 312-944-2544</p>
<p>We reserve the right to make changes to this policy at any time and you should check this policy periodically for updates. Any changes to this policy will be posted here.</p>';

            // use local when local ##
            $url =
                h::is_localhost() ?
                'https://ghorg.qlocal.com/api/v2/page/get/privacy' :
                'https://greenheart.org/api/v2/page/get/privacy' ;

            global $wp_version;
            $args = array(
                'timeout'     => 5,
                'redirection' => 5,
                'httpversion' => '1.0',
                'user-agent'  => 'WordPress/' . $wp_version . '; ' . \home_url(),
                'blocking'    => true,
                'headers'     => array(),
                'cookies'     => array(),
                'body'        => null,
                'compress'    => false,
                'decompress'  => true,
                'sslverify'   => h::is_localhost() ? false : true , // no SSL locally ##
                'stream'      => false,
                'filename'    => null
            );

            // login user via a GET request to API v2 ##
            $response = \wp_remote_get( $url, $args );

            // default ##
            $string = $default;

            // array returned ##
            if ( is_array( $response ) ){

                // body is JSON encoded ##
                $body = json_decode( $response['body'] ) ;

                // helper::log( 'wp_remote_get said: ' );
                // helper::log( $body->data->content );

                // check we have the content we need and filter it accordingly ##
                $string = $body->data->content ? \wpautop( $body->data->content ) : $default ;

            }

            // store it ##
            \set_site_transient( 'q_consent_privacy', $string, 1 * DAY_IN_SECONDS );

        }

        // kitck it back ##
        return $string;

    }



}
