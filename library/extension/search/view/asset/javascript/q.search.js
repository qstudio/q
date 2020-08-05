(function ($) {

    // store page title ##
    var title_default = document.title;
    var doing_ajax = false;
    var adminbarheight;
    var fromTop;

    var QS_Filter = function (opts) {
        this.init(opts);
    };

    QS_Filter.prototype = {

        selected: function () {

            var self = this,
            arr = this.loop( $('.' + self.selected_filters), 'tax' );

            // Join the array with an "&" so we can break it later.
            return arr.join('&');

        },

        // progress: function (i) {

        //     // Increase the progress bar based on the value passed.
        //     this.progbar.stop(true, true).animate({
        //         width: i + '%'
        //     }, 30);

        // },

        title: function () {

            var $q_search_input = $("input#searcher").val();
            var $category = $("select#category").val();

            // default ##
            var $filter_title = '';
            var $filter_page_title = '';
            var $filter_title_update = false; // nope ##

            // add search term ? ##
            if ( $q_search_input > '' ) {
                $filter_title = $filter_title + $("input#searcher").val() + ' | ';
                if ( $filter_page_title.length > 0 ) { $filter_page_title = $filter_page_title+' + '; }
                $filter_page_title = $filter_page_title +'"'+ $("input#searcher").val()+'"';
                $filter_title_update = true; // yep ##
            }

            // get current page title ##
            $title_default = document.getElementsByTagName("title")[0].innerHTML;

            if ( $filter_title_update === true ) {

                // get <title> tag ##
                $title = q_search.search+' | '+q_search.site_name;

                // update <title> tag ##
                document.title = $filter_title+$title;

                // get <h1> title ##
                $page_title = q_search.search+': <span class="bold">'+$filter_page_title+'</span>';

                // update <h1> title ##
                $("#q-search h1.entry-title").html($page_title);

            // reset titles ##
            } else {

                // update title tag ##
                document.title = $title_default;

                // get base title ##
                $page_title = q_search.search;

                // update page title ##
                $("#q-search h1.entry-title").html('');

            }

        } ,

        loop: function ( node, tax ) {

            // Return an array of selected navigation classes.
            var arr = [];
            node.each(function () {
                if ( $(this).attr("id") == 'searcher' ) {
                    var id = "search="+$("#searcher").val()
                } else {
                    var id = $(this).data( tax );
                }
                if ( id ) arr.push(id);
            });
            // console.dir( arr );
            return arr;

        },

        filter: function (arr) {

            // console.log("running filter");

            var self = this;

            // Return all the relevant posts...
            $.ajax({

                url: QS_CONFIG['ajaxurl'],
                type: 'post',
                data: {
                    'action': 		    'q_search',
                    'filters': 		    arr,
                    'callback':         QS_CONFIG['callback'],
                    'table':            QS_CONFIG['table'],
                    'application':      QS_CONFIG['application'],
                    'device':           QS_CONFIG['device'],
                    'post_type':        QS_CONFIG['post_type'],
                    'posts_per_page':   QS_CONFIG['posts_per_page'],
                    'template':         QS_CONFIG['template'],
                    'order':            QS_CONFIG['order'],
                    'order_by':         QS_CONFIG['order_by'],
                    'category_name':    QS_CONFIG['category_name'],
        	        'author_name':      QS_CONFIG['author_name'],
        	        'tag':    			QS_CONFIG['tag'],
                    'queried_object':   QS_CONFIG['queried_object'],
                    'paged': 		    QS_CONFIG['page_number'],
                    '_ajax_nonce':      QS_CONFIG['nonce']
                },

                beforeSend: function () {

                    // remove result count ##
                    $('.q-search-count-results').remove();

                    doing_ajax = true;
                    
                    // self.loader.fadeIn();
                    self.section.animate({
                        'opacity': .0
                    }, 'slow');
                    
                    $("#q-search .pagination").hide("slow"); // show pagination ##
                    
                    if ( typeof NProgress !== 'undefined' ) { NProgress.start(); }
                    self.disable(); // disable all selects, inputs and buttons ##

                    // empty all previous results ##
                    $('#q-search-results').empty();

                },

                success: function ( html ) {

                    // console.log( 'success..' );
                    // console.log( html );
                    // console.log( self.section );

                    // append new results ##
                    $('#q-search-results').append(html);

                    $('.q-search-count-results').hide();
                    var count = $('.q-search-count-results').html();
                    // console.log( 'count: '+ count );

                    // $('.q-search-count-results').remove();

                    if ( count !== undefined && count.length > 0 ) {

                        // console.log( 'show count triggered...' );
                        $('.q-search-count-results').html(count).show();

                        // move up one level ##
                        $results = $('.q-search-count-results');
                        $results.insertBefore( $results.parent() );

                    }

                },

                complete: function () {

                    //console.log("ID: "+self.section.attr("id"));

                    $(".ajax-loaded").fadeIn();
                    $('html, body').animate({scrollTop: q_get_from_top() }, 300);
                    $('#ajax-filtered-section').animate({
                        'opacity': 1
                    }, 'slow');
                    $(".pagination").show("slow"); // show pagination ##

                    // callback ##
                    if ( QS_CONFIG['callback'] ) {

                        // console.log( 'callback defined as: '+ QS_CONFIG['callback'] );

                        self.callback( QS_CONFIG['callback'] );

                    }

                    self.reenable(); // re-enable all selects, inputs and buttons ##

                    // show reset ##
                    // $("#q-search input[type='reset']").show('fast');

                    // show all filters ##
                    //self.show_filters();

                    // self.progress(100);
                    if ( typeof NProgress !== 'undefined' ) { NProgress.done(); }
                    // self.loader.fadeOut();

                    //self.title();
                    self.running = false;

                    // update history ##
                    // self.history( self.selected() );

                    // reset tracker ##
                    doing_ajax = false;

                },

                error: function () {}

            });
        },

        show_filters: function() {

            $( "#q-search ul.ajax-filters li.ajax-filters-li:gt(0)" ).show();

        },

        hide_filters: function() {

            $( "#q-search ul.ajax-filters li.ajax-filters-li:gt(0)" ).hide();

        },

        disable: function() {
            $("#q-search-form select").prop('disabled', 'disabled');
            $("#q-search-form :input, #q-search-form button, #q-search-form .ajax-button").attr("disabled", true);

        },

        reenable: function() {

            q_search_reenable();

        },

        callback: function( ref ) {

            if ( ! ref || false === ref ) {

                // console.log( 'No callback defined' );

                return false;

            }

            // load up modal engine ##
            if ( window[ref] ) {

                // console.log( 'calling callback with delay from q_search: '+ref );

                // setTimeout(
                    // function()
                    // {
                      //do something special
                    //   console.log( 'Now....' );
                      window[ref]();
                    // }, 1000);

            } else {

                // console.log( 'callback not available' );

            }

        },

        // we need to update the hash value
        hash: function( filter, value ) {

            // update hash with '/filter/' prefix ##
            window.location.hash = '/filter/'+ar;

        },

        clicker: function () {

            var self = this;

            $( document ).on( 'click', this.links, function (e) {
                if ( self.running == false ) {

                    self.first = false; // load normally from now ##

                    // Set to true to stop function chaining.
                    self.running = true;

                    // The following line resets the queried_object var so that in an ajax request it page's queried object is ignored.
                    QS_CONFIG['queried_object'] = 'qs_null';

                    // Cache some of the DOM elements for re-use later in the method.
                    var link = $(this),
                        parent = link.parent('li'),
                        relation = link.attr('rel');

                    if (parent.length > 0) {
                        parent.toggleClass(self.selected_filters);
                        QS_CONFIG['page_number'] = 1;
                    }

                    if (relation === 'next') {
                        QS_CONFIG['page_number']++;
                    } else if (relation === 'prev') {
                        QS_CONFIG['page_number']--;
                    } else if (link.hasClass('pagelink')) {
                        QS_CONFIG['page_number'] = relation;
                    }

                    self.filter($('#q-search-form').serialize());

                }

                e.preventDefault();

            });

            $( document ).on('blur', this.inputs, function (e) {

                if ( self.running == false ) {

                    self.first = false; // load normally from now ##

                    // Set to true to stop function chaining.
                    self.running = true;

                    // The following line resets the queried_object var so that in an ajax request it page's queried object is ignored.
                    QS_CONFIG['queried_object'] = 'qs_null';

                    // Cache some of the DOM elements for re-use later in the method.
                    var link = $(this),
                        parent = link.parent('li'),
                        relation = link.attr('rel');

                    if (parent.length > 0) {
                        parent.toggleClass(self.selected_filters);
                        QS_CONFIG['page_number'] = 1;
                    }

                    if (relation === 'next') {
                        QS_CONFIG['page_number']++;
                    } else if (relation === 'prev') {
                        QS_CONFIG['page_number']--;
                    } else if (link.hasClass('pagelink')) {
                        QS_CONFIG['page_number'] = relation;
                    }

                    self.filter($('#q-search-form').serialize());

                }

                e.preventDefault();

            });

			// cancel submit default behaviour ##
            $("#q-search-form").on('submit', function(e) {
                e.preventDefault();
			});
			
			// reset from class callback ##
			$("#q-search").on('click', '.qs-reset', function(e) {
				// console.log( 'RESET...' );
				$("#q-search-form").trigger("reset");
            });


            $("#q-search-form").on("change submit reset", function(e) {
				
				if ( self.running == false ) {

                    if (e.type == 'reset') {

                        formData = '';
						
						// hide reset ##
						$("#q-search-form input[type='reset']").hide();

						// empty searcher ##
						$('#searcher').removeAttr('value');

						// reset selects ##
						$('.q-search-select').prop('selectedIndex',0);
						
                    } else {

                        var formData = $('#q-search-form').serialize();
						$("#q-search-form input[type='reset']").show();
						
                    }

					// console.log( 'Are we here>..?' );

                    //self.first = false; // load normally from now ##

                    // Set to true to stop function chaining.
					self.running = true;
					
                    // The following line resets the queried_object var so that in an ajax request it page's queried object is ignored.
                    QS_CONFIG['queried_object'] = 'qs_null';

					// remove all selected_filters from options in this <select> ##
                    $(this).find('option').removeClass(self.selected_filters);

                    // Cache some of the DOM elements for re-use later in the method.
                    var link = $(this),
                        parent = link.parent('select'),
                        relation = link.attr('rel');

                    $(this).find(':selected').toggleClass(self.selected_filters);

                    QS_CONFIG['page_number'] = 1;

                    if (relation === 'next') {
                        QS_CONFIG['page_number']++;
                    } else if (relation === 'prev') {
                        QS_CONFIG['page_number']--;
                    } else if (link.hasClass('pagelink')) {
                        QS_CONFIG['page_number'] = relation;
                    }

                    // console.log(a);
					self.filter(formData);
					
                }

                // e.preventDefault();
            });

            $( document ).on('change', this.selects, function (e) {

                // console.log( 'here..' );
                // console.dir( this.selects );

                if ( self.running == false ) {

                    //self.first = false; // load normally from now ##

                    // Set to true to stop function chaining.
                    self.running = true;

                    // The following line resets the queried_object var so that in an ajax request it page's queried object is ignored.
                    QS_CONFIG['queried_object'] = 'qs_null';

                    // remove all selected_filters from options in this <select> ##
                    $(this).find('option').removeClass(self.selected_filters);

                    // Cache some of the DOM elements for re-use later in the method.
                    var link = $(this),
                        parent = link.parent('select'),
                        relation = link.attr('rel');

                    $(this).find(':selected').toggleClass(self.selected_filters);

                    QS_CONFIG['page_number'] = 1;

                    if (relation === 'next') {
                        QS_CONFIG['page_number']++;
                    } else if (relation === 'prev') {
                        QS_CONFIG['page_number']--;
                    } else if (link.hasClass('pagelink')) {
                        QS_CONFIG['page_number'] = relation;
                    }

                    self.filter(self.selected());

                }

                e.preventDefault();

            });

        },

        reset: function () {

            // console.log( 'reset..' );

            $("#q-search #ajax-filtered-section").append("<p class='no-results'></p>"); // add msg ##
            $(".no-results").html(q_search.on_load_text).fadeIn();
            $("#q-search .ajax-loaded").hide(); // hide all results ##
            $("#q-search .pagination").hide(); // hide pagination ##

            // hide all filters ##
            // this.hide_filters();

            //$('html, body').animate({
            //    scrollTop: $("#ajax-filtered-section").offset().top -120
            //}, 500);

            // remove all other ".no-results" ##
            $(".no-results").hide();
            $('.count-results').hide();

			// $('html, body').animate({scrollTop: 0}, "fast");
			$('html, body').animate({scrollTop: q_get_from_top() }, 300);

        },

        init: function (opts) {

            // console.dir( opts );

            // Set up the properties
            this.opts = opts;
            this.running = false;
            this.section = $(this.opts['section']);
            this.links = this.opts['links'];
            this.inputs = this.opts['inputs'];
            this.selects = this.opts['selects'];
            this.progbar = $(this.opts['progbar']);
			this.selected_filters = this.opts['selected_filters'];
			
			// hide reset ##
			$("#q-search-form input[type='reset']").hide();

            // $title_default = document.getElementsByTagName("title")[0].innerHTML;

            // move results ##
            $results = $('.q-search-count-results');
            $results.insertBefore( $results.parent() );

            // Run the methods.
            this.clicker();

        }

    };

    // instatiate class ##
    var qs_filter = new QS_Filter({
        'loader': 			'#ajax-loader',
        'section': 			'#ajax-filtered-section',
        'links': 			'.paginationNav, .pagelink, #go',
        'inputs': 			'.searcher',
        'selects': 			'.q-search-select',
        'progbar': 			'#progbar',
        'selected_filters': 'filter-selected'
    });

    // pagination clicks scroll the viewer back to the top of the page ##
    $("body").on( 'click', 'nav.pagination a, .q-scroll-top', function(e) {

        // stop default action ##
        e.preventDefault();

        $('html, body').animate({scrollTop: q_get_from_top() }, 300 );

    });

    // toggle placeholder text on search input ##
    var placeholder_search = $('#searcher').attr('placeholder');
    $('#searcher').focus(function(){
        $(this).attr('placeholder','');
    });
    $('#searcher').focusout(function(){
        $(this).attr('placeholder', placeholder_search );
    });

    // $( document ).on ('keypress', "input#searcher", function(event) {
    //     if (event.which == 13) {
    //         // console.log("pressed enter");
    //         event.preventDefault();
    //         $('input#go')[0].click()
    //     }
    // });

    // hide all search filters on load ( other than audience selection ) ##
    // $( "#q-search ul.ajax-filters li.ajax-filters-li:gt(0)" ).hide();

    // reset selects on load ##
    $( 'select' ).each( function() {
        $(this)
        .prop( 'selectedIndex', 0 )
        // console.log( 'Reset select..' );
    });

    function q_get_from_top() {

        var adminbarheight = ( typeof adminBarHeight === 'function' ) ? adminBarHeight() : 0 ;
        var fromTop = $("#q-search-form").length ? $("#q-search-form").offset().top - adminbarheight : 0 ;
        // console.log( 'From Top: '+fromTop );

		return 0;

        return fromTop;

    }

    function q_get_url() {

        return window.location.protocol + "//" + window.location.host + window.location.pathname;

    }

    function q_search_reenable(){
        $("#q-search-form select").prop('disabled', false);
        $("#q-search-form :input, #q-search-form button, #q-search-form .ajax-button").attr("disabled", false);
    }

})(jQuery);