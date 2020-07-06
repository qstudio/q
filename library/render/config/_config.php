<?php
 return [
  'post' => [
    '_global' => [
      'config' => [
        'run' => '1',
        'debug' => '0',
        'allow_comments' => '1',
        'date_format' => 'F j, Y',
      ],
    ],
    'content' => [
      'markup' => [
        'template' => '<div class=\\"pb-1 col-12 the-content\\">{{ content }}</div>',
      ],
    ],
    'data' => [
      'markup' => [
        'template' => 'Posted {{ post_date_human }} ago by <a href=\\"{{ author_permalink }}\\" title=\\"See posts by {{ author_title }}\\">{{ author_title }}</a>in <a href=\\"{{ category_permalink }}\\" title=\\"See posts in {{ category_title }}\\">{{ category_title }}</a>Tagged: {{# tags }}	<a href=\\"{{ tag_permalink }}\\" title=\\"See posts in {{ tag_title }}\\">{{ tag_title }}</a> {{/#}}Comments: <a href=\\"{{ comment_permalink }}\\" title=\\"Comments\\">{{ comment_title }} </a>',
        'wrap' => '<div class=\\"post-meta col-12 mb-3\\">{{ content }}</div>',
      ],
    ],
    'excerpt' => [
      'markup' => [
        'template' => '<div class=\\"pb-1 col-12 mb-3 the-excerpt\\">{{ content }}</div>',
      ],
      'args' => [
        'limit' => '300',
      ],
    ],
    'parent' => [
      'markup' => [
        'template' => '<h4 class=\\"pb-1 col-12 the-parent\\"><a href=\\"{{ permalink }}\\" title=\\"Open {{ title }}\\">{{ title }}</a></h4>',
      ],
    ],
    'query' => [
      'config' => [
        'debug' => '1',
      ],
      'markup' => [
        'template' => '<div class=\\"pb-1 col-12 the-posts\\">
	<div class=\\"row\\"><h5 class=\\"col-12 mt-2\\">{{ total }} Results Found.</h5></div>
	<div class=\\"row mt-3\\">{{ posts }}</div>
	<div class=\\"row\\"><div class=\\"col-12\\">{{ pagination }}</div></div>
</div>',
        'posts' => '<div class=\\"card p-0 col-12 col-md-6 col-lg-4 mb-3\\">
	<a href=\\"{{ post_permalink }}\\" title=\\"{{ post_title }}\\" class=\\"mb-3\\">
		<img class=\\"lazy fit card-img-top\\" style=\\"height: 200px;\\" data-src=\\"{{ src }}\\" src=\\"\\" />
	</a>
	<div class=\\"card-body\\">
		<h5 class=\\"card-title\\">
			<a href=\\"{{ post_permalink }}\\">
				{{ post_title }}
			</a>
		</h5>
		<p class=\\"card-text\\">{{ post_excerpt }}</p>
		<p class=\\"card-text\\">
			<small class=\\"text-muted\\">Posted {{ post_date_human }} ago</small>
			<small class=\\"text-muted\\">in <a href=\\"{{ category_permalink }}\\" title=\\"{{ category_name }}\\">{{ category_name }}</a> </small>    
		</p>
	</div>
</div>',
        'no_results' => '<div class=\\"col-12\\"><p>We cannot not find any matching posts, please check again later.</p></div>',
      ],
      'wp_query_args' => [
        'post_type' => 'post',
        'posts_per_page' => '3',
        'limit' => '3',
      ],
      'args' => [
        'length' => '200',
        'handle' => 'medium',
      ],
    ],
    'title' => [
      'markup' => [
        'template' => '<h1 class=\\"pb-2 col-12 the-title\\">{{ title }}</h1>',
      ],
    ],
  ],
  'ui' => [
    '_global' => [
      'config' => [
        'run' => '1',
        'debug' => '1',
      ],
    ],
    'close' => [
      'markup' => [
        'template' => '	</div>
</main>',
      ],
    ],
    'footer' => [
      'markup' => [
        'template' => '<footer>FOOTER</footer>',
      ],
    ],
    'header' => [
      'markup' => [
        'template' => '<header>HEADER</header>',
      ],
    ],
    'open' => [
      'config' => [
        'run' => '1',
        'debug' => '1',
      ],
      'markup' => [
        'template' => '<main class=\\"container {{ classes }}\\">
	<div class=\\"row\\">',
        'wrap' => '<div>{{ content }}</div>',
      ],
    ],
  ],
];
