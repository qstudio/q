// BS Form validation
(function() {
	'use strict';
	window.addEventListener('load', function() {
		
		// Fetch all the forms we want to apply custom Bootstrap validation styles to
	  	var forms = document.getElementsByClassName('needs-validation');
		  
		// Loop over them and prevent submission
	  	var validation = Array.prototype.filter.call(forms, function(form) {
		
			form.addEventListener('submit', function(event) {
				
				if (
					form.checkValidity() === false
				) {

					event.preventDefault();
					event.stopPropagation();

					// mark as validated ##
					form.classList.add('was-validated');

					return;

				} 

				// mark as validated ##
				form.classList.add('was-validated');

				// console.dir( validation );

				// declare vars ##
				var sitekey;
				var q_action;

				// check if recaptcha is enabled, if so, fire off ##
				if ( 
					null !== document.getElementById( 'g-recaptcha' ) 
					&& null !== document.getElementById( 'g-recaptcha' ).getAttribute("data-sitekey")
					&& null !== document.getElementById( 'q_action' ) 
				) {

					// get action ##
					q_action = document.getElementById( 'q_action' ).value;
					// console.log( 'recaptcha action: '+q_action );

					// get sitekey ##
					sitekey = document.getElementById( 'g-recaptcha' ).getAttribute("data-sitekey");
					// console.log( 'recaptcha key: '+sitekey );

					grecaptcha.ready(function () {
						grecaptcha.execute( sitekey, { action: q_action }).then(function ( token ) {
							
							// console.log( 'recapatcha token: '+token );
							
							var input = document.createElement( 'input' );// prepare a new input DOM element
							input.setAttribute( 'name','q_recaptcha' ); // set the param name
							input.setAttribute( 'value', token ); // set the value
							input.setAttribute( 'type', 'hidden' ) // set the type, like "hidden" or other
						
							form.appendChild(input); // append the input to the form

							// confirm ##
							q_snack({
								content:    'Houston... Tranquility base here. The Eagle has landed.', // msg ##
								timeout:    -1, // never timeout ##
								style: 		'warning'
							});
							
							// in case we go again ..
							// grecaptcha.reset();

							if (
								form.checkValidity() === true
							) {

								// console.log( 'OK to submit..' );

								// submit ##
								form.submit();

							}

						});
					});
				}

			}, false);
		});
	}, false);
})();

