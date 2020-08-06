<?php
 
//  namespace q\core;

//  use q\core as core;
use q\core\helper as h;

// quick check :) ##
defined( 'ABSPATH' ) OR exit;

// re-usable config ------ ##

// h::log( 't:>Check if extension->search array set, if not set when adding to cache.. to allow for simpler array format in config files??' ) ;

// return an array ##
return [ 'extension' => [ 'consent' => [

		'config'			=> [
		
			// run context->task ##
			'run' 			=> true,
			
			// context->task debugging ##
			'debug'			=> false,

			// do not echo ##
			'return'		=> 'echo'
	
		],

		// markup ##
		'markup' 			=> [

			// setting used in modal ##
			'settings'		=> '
				<div class="col-12 p-0">
				
					<p>This website uses cookies to let you interact with our services and for marketing and advertising purposes. Some of these cookies are strictly necessary for our sites to function and by using this site you agree that you have read and understand our use of cookies.</p>
					<p>Our marketing and advertising cookies are non-essential and you can opt out of using them with this tool. Blocking cookies may impact your experience on our website.</p>
					<hr />

					<div class="settings">
						<div class="setting">
							<div class="row">
								<div class="col-10">
									<h5>Functional Cookies</h5>
									<p>These cookies are necessary for our sites to function properly. These cookies secure our forms, support login sessions and remember user dialogue. Because the site does not function without these cookies, opt-out is not available. They are not used for marketing or analytics.</p>
								</div>
								
								<div class="col-2 pt-4 pr-5 text-right">
									<div class="q-consent-wrapper">{{ option_functional }}</div>
								</div>
							</div>
						</div>
						
						<hr />

						<div class="setting">
							<div class="row">
								<div class="col-10">
									<h5>Marketing Cookies</h5>
									<p>These cookies are used to enhance the relevance of our advertising on social media and to tailor messages relevant to your interests.</p>
								</div>

								<div class="col-2 pt-4 pr-5 text-right">
									<div class="q-consent-wrapper">{{ option_marketing }}</div>
								</div>
							</div>
						</div>
						
						<hr />

						<div class="setting">
							<div class="row">
								<div class="col-10">
									<h5>Analytical Cookies</h5>
									<p>These cookies collect anonymous data on how visitors use our site and how our pages perform. We use this information to make the best site possible for our users.</p>
								</div>

								<div class="col-2 pt-4 pr-5 text-right">
									<div class="q-consent-wrapper">{{ option_analytics }}</div>
								</div>
							</div>
						</div>
					</div>
					
					<hr />

					<div class="text-right">
						{{ buttons }}
					</div>
				</div>
			',

			// consent bar ##
			'bar'			=> '
				<div class="q-bsg q-consent">
					<div class="q-consent-bar">
						<div class="container-fluid">
							<div class="row align-items-center">
								<div class="col-xl-9 col-lg-8 col-md-7 col-12 content">
									This website uses cookies for basic functionality, analytics, and marketing. Visit our <a href="{{ privacy_permalink }}" >Privacy Policy</a> page to find out more.
								</div>

								<div class="col-xl-3 col-lg-4 col-md-5 col-12 cta">
									<a 
										class="btn btn-border" 
										href="#consent" 
										data-modal-target="#q_modal" 
										data-modal-size="modal-lg" 
										data-modal-title="Consent Settings" 
										data-modal-body="{{ settings }}">
										SETTINGS
									</a>
									<button type="button" class="btn btn-light accept {{ button_class }}" {{ data }}>
										ACCEPT
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			',

		],
		
	]	

]];
