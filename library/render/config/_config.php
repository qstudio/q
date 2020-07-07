<?php
 return [
  'media' => [
    '_global' => [
      'config' => [
        'run' => '1',
        'debug' => '0',
      ],
      'src' => [
        'srcset' => '1',
        'meta' => '1',
        'picture' => '0',
        'holder' => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/PjxzdmcgZGF0YS1uYW1lPSJMYXllciAxIiBpZD0iTGF5ZXJfMSIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHN0eWxlPi5jbHMtMXtmaWxsOm5vbmU7c3Ryb2tlOiMwODNiNDM7c3Ryb2tlLWxpbmVjYXA6cm91bmQ7c3Ryb2tlLWxpbmVqb2luOnJvdW5kO3N0cm9rZS13aWR0aDoyMHB4O30uY2xzLTJ7ZmlsbDojMDgzYjQzO308L3N0eWxlPjwvZGVmcz48dGl0bGUvPjxjaXJjbGUgY2xhc3M9ImNscy0xIiBjeD0iMjU2IiBjeT0iMjY3LjQyIiByPSIzMy45OSIvPjxwb2x5Z29uIGNsYXNzPSJjbHMtMSIgcG9pbnRzPSIzMDIuMiAyMDIuNTIgMjg4LjkgMTc5LjY4IDIyMy4xIDE3OS42OCAyMDkuOCAyMDIuNTIgMTQ0IDIwMi41MiAxNDQgMzMyLjMyIDM2OCAzMzIuMzIgMzY4IDIwMi41MiAzMDIuMiAyMDIuNTIiLz48Y2lyY2xlIGNsYXNzPSJjbHMtMiIgY3g9IjMzNy44IiBjeT0iMjMyLjQ5IiByPSIxMS40OSIvPjwvc3ZnPg==',
        'generate' => '1',
        'ratio' => '1.618',
        'open' => '9999',
        'open_height' => '600',
        'scale' => '1',
        'pixel' => '2',
        'default' => 'horizontal-lg',
      ],
      'src_sizes' => [
        'xs' => '300',
        'sm' => '576',
        'md' => '720',
        'lg' => '960',
        'xl' => '1200',
      ],
    ],
    'avatar' => [
      'markup' => [
        'template' => '<div class=\\"col-12\\"><img class=\\"avatar\\" src=\\"{{ src }}\\"/></div>',
      ],
    ],
    'handles' => [
      'square' => [
        'sizes' => 'all',
        'width' => 'equal',
        'height' => 'equal',
        'pixel' => '1',
        'crop' => '1',
        'open' => '0',
      ],
      'horizontal' => [
        'sizes' => 'all',
        'width' => 'equal',
        'height' => 'divide',
        'crop' => '1',
        'open' => 'width',
        'pixel' => '1',
      ],
      'vertical' => [
        'sizes' => 'all',
        'width' => 'equal',
        'height' => 'multiply',
        'crop' => '1',
        'open' => 'height',
        'pixel' => '1',
      ],
    ],
    'thumbnail' => [
      'markup' => [
        'template' => '<img class=\\"col-12 fill lazy mt-2 mb-2\\" src=\\"\\" data-src=\\"{{ src }}\\" srcset=\\"{{ src_srcset }}\\" sizes=\\"{{ src_sizes }}\\" alt=\\"{{ src_alt }}\\" data-src-caption=\\"{{ src_caption }}\\" data-src-title=\\"{{ src_title }}\\" data-src-content=\\"{{ src_description }}\\">',
      ],
    ],
  ],
  'navigation' => [
    '_global' => [
      'config' => [
        'run' => '1',
        'debug' => '0',
      ],
    ],
    'menu' => [
      'args' => [
        'echo' => '1',
      ],
    ],
    'pagination' => [
      'markup' => [
        'template' => '<li class=\\"{{ li_class }}{{ active-class }}\\">{{ item }}</li>',
        'wrap' => '<div class=\\"row row justify-content-center mt-5 mb-5\\"><ul class=\\"pagination\\">{{ content }}</ul></div>',
      ],
      'args' => [
        'end_size' => '0',
        'mid_size' => '2',
      ],
      'ui' => [
        'prev_text' => '‹ Previous',
        'next_text' => 'Next ›',
        'first_text' => '« First',
        'last_text' => 'Last &raquo',
        'li_class' => 'page-item',
        'class_link_item' => 'page-link',
        'class_link_first' => 'd-none d-md-inline page-link page-first d-none d-md-block',
        'class_link_last' => 'd-none d-md-inline page-link page-last d-none d-md-block',
      ],
    ],
  ],
];
