<?php

use Carbon\Carbon;

return [
    'baseUrl' => '',
    'production' => false,
    'siteName' => 'Kevin Cunningham',
    'siteDescription' => '',
    'siteAuthor' => 'Kevin Cunningham',
    'cover' => '',

    // collections
    'collections' => [
        'posts' => [
            'author' => 'Kevin',
            'extends' => '_layouts.post',
            'path' => 'blog/{date|Y/m/d}/{-title}',
            'sort' => ['-date'],
            'tags' => [],
        ],
        'tags' => [
            'path' => 'tag/{filename}',
        ],
    ],

    // helpers
    'getDate' => function ($page) {
      return Datetime::createFromFormat('U', $page->date);
    },
    'getExcerpt' => function ($page, $length = 255) {
        $content = $page->excerpt ?? $page->getContent();
        $cleaned = strip_tags(
            preg_replace(['/<pre>[\w\W]*?<\/pre>/', '/<h\d>[\w\W]*?<\/h\d>/'], '', $content),
            '<code>'
        );

        $truncated = substr($cleaned, 0, $length);

        if (substr_count($truncated, '<code>') > substr_count($truncated, '</code>')) {
            $truncated .= '</code>';
        }

        return strlen($cleaned) > $length
            ? preg_replace('/\s+?(\S+)?$/', '', $truncated) . '...'
            : $cleaned;
    },
    'isActive' => function ($page, $path) {
        return ends_with(trimPath($page->getPath()), trimPath($path));
    },
    'getPostsForTag' => function ($page, $articles, $tag) {
        return $articles->filter(function ($article) use ($tag) {
            return collect($article->tags)->contains($tag);
        });
    },

];
