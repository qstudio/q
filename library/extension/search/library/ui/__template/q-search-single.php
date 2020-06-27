<?php

namespace q\q_lms\theme\template;

use q\controller\generic as generic;
#use q\theme\ui\view\page\page as page;
use q\q_lms\theme\theme as theme;

// header ##
\get_header();

// open content ##
generic::the_content_open();

// title ##
generic::the_title();


// -------

// get all page content ##
theme::render_single();


// -------

// close content ##
generic::the_content_close();

\get_footer();