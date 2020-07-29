<?php

// return an array ##
return [ 'user' => [

	'config'			=> [
		
		// run context->task ##
		'run' 			=> true,
		
		// context->task debugging ##
		'debug'			=> false,

	],

	// user menu ##
	'menu_authentication'		=> [

		'config'				=> [
			'return'			=> true // do not echo ##
		],		
		'args' 					=> [
			'echo'				=> false,
			'theme_location'	=> \is_user_logged_in() ? 'q-authenticated' : 'q-unauthenticated', // swap based on login state ##
			'depth'	          	=> 1, // 1 = no dropdowns, 2 = with dropdowns.
			'container'       	=> 'div',
			'container_class' 	=> 'collapse navbar-collapse mb-4 menu_content',  
			'container_id'    	=> 'user_menu_content',
			'menu_class'      	=> 'navbar-nav mr-auto',
			'fallback_cb'     	=> 'Q_Nav_Walker::fallback',
			'walker'          	=> new \Q_Nav_Walker(),
		], 
		'markup'				=> [
			'template'			=> '{{ menu_authentication }}'
		]

	],


	// user menu ##
	'menu_profile'		=> [

		'markup'		=> [
			'template'	=> '
				<div class="collapse menu_content">
					<span class="avatar-header-right" data-tgt="#r-submenu">
						<a href="{{ permalink }}" title="View your profile">
							{{ avatar }}
						</a>
					</span>
					<span class="notification-count outer" data-notification-count="{{ notification_count }}">
						<span class="notification-inner q-notification-count">
							{{ notification_count }}
						</span>
					</span>
					<ul class="" id="r-submenu">
						<li class="">
							<span class="q-row">
								<h5>{{ display_name }}</h5>
							</span>
							<span class="">
								<a href="{{ profile_permalink }}">Edit Profile</a>
								<a href="{{ logout_permalink }}">Logout</a>
							</span>
						</li>
						<li class="">
							<ul class="">
								{@ {: notifications :} 
								<li>
									<a href="{{ permalink }}">
										{{ icon }}
										{{ short }}
										<time class="q-date">{{ date }} ago</time>
									</a>
								</li>
								@}
								<li class="view-all">
									<a href="{{ notification_permalink }}">
										View all
									</a>
								</li>
							</ul>
						</li>
					</ul>
				</div>  
			'
		]

	],


	// user menu ##
	'menu_notifications'=> [

		'markup'		=> [
			'template'	=> '
				<span class="badge badge-primary" data-notification-count="{{ count }}" style="
				position: relative;
				top: -21px;
				left: 31px;
			">
					{{ count }}
				</span>'
		]

	],

]];
