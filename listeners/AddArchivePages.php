<?php
namespace App\Listeners;
use TightenCo\Jigsaw\Jigsaw;
use App\Listeners\PseudoCollectionGenerator;
use Illuminate\Support\Collection;

class AddArchivePages extends PseudoCollectionGenerator
{
    /**
     * Helpers that should be registered.
     *
     * @return array
     */
    protected static function helpers()
    {
        return [
            'getPostsForYear' => function ($page, $posts) {
                return $posts->filter(function ($post) use ($page) {
                    return date('Y', $post->date) === $page->year;
                });
            },
            'getPostsForYearAndMonth' => function ($page, $posts) {
                return $page->getPostsForYear($posts)->filter(function ($post) use ($page) {
                    return date('m', $post->date) === $page->month;
                });
            },
            'getPostsForYearMonthAndDay' => function ($page, $posts) {
                return $page->getPostsForYearAndMonth($posts)->filter(function ($post) use ($page) {
                    return date('d', $post->date) === $page->day;
                });
            },
        ];
    }
    /**
     * Get new collections configurations.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getCollectionsConfigurations()
    {
        $collections = collect();
        $dates = $this->getDates($this->jigsaw);
        $this->setPostsByYearCollection($dates, $collections);
        $this->setPostsByMonthCollection($dates, $collections);
        $this->setPostsByDayCollection($dates, $collections);
        return $collections;
    }
    /**
     * Get all dates when we have posts in form of array with year, month and day.
     *
     * @param  \TightenCo\Jigsaw\Jigsaw  $jigsaw
     * @return \Illuminate\Support\Collection
     */
    protected function getDates(Jigsaw $jigsaw)
    {
        return $jigsaw->getCollection('posts')->map(function ($post) {
            return [
                'year' => date('Y', $post->date),
                'month' => date('m', $post->date),
                'day' => date('d', $post->date),
            ];
        })->values()->toBase();
    }
    /**
     * Add remote collection `posts by year` into configuration.
     *
     * @param  \Illuminate\Support\Collection  $dates
     * @return void
     */
    protected function setPostsByYearCollection(Collection $dates, Collection $collections)
    {
        $collections->put('posts_by_year', [
            'extends' => '_posts_by_year._index',
            'path' => 'blog/{year}',
            'items' => $this->getYearItems($dates),
        ]);
    }
    /**
     * Add remote collection `posts by month` into configuration.
     *
     * @param  \Illuminate\Support\Collection  $dates
     * @return void
     */
    protected function setPostsByMonthCollection(Collection $dates, Collection $collections)
    {
        $collections->put('posts_by_month', [
            'extends' => '_posts_by_month._index',
            'path' => 'blog/{year}/{month}',
            'items' => $this->getMonthItems($dates),
        ]);
    }
    /**
     * Add remote collection `posts by day` into configuration.
     *
     * @param  \Illuminate\Support\Collection  $dates
     * @return void
     */
    protected function setPostsByDayCollection(Collection $dates, Collection $collections)
    {
        $collections->put('posts_by_day', [
            'extends' => '_posts_by_day._index',
            'path' => 'blog/{year}/{month}/{day}',
            'items' => $this->getDayItems($dates),
        ]);
    }
    /**
     * Get items for posts by year.
     *
     * @param  \Illuminate\Support\Collection  $dates
     * @return \Illuminate\Support\Collection
     */
    protected function getYearItems(Collection $dates)
    {
        return $this->getYears($dates)->map(function ($year) {
            return [
                'title' => 'Archive',
                'year' => $year,
            ];
        });
    }
    /**
     * Get items for posts by month.
     *
     * @param  \Illuminate\Support\Collection  $dates
     * @return \Illuminate\Support\Collection
     */
    protected function getMonthItems(Collection $dates)
    {
        return $this->getYears($dates)->map(function ($year) use ($dates) {
            return $this->getMonths($dates, $year)->map(function ($month) use ($year) {
                return $this->getMonthItem($year, $month);
            });
        })->flatten(1);
    }
    /**
     * Get items for posts by day.
     *
     * @param  \Illuminate\Support\Collection  $dates
     * @return \Illuminate\Support\Collection
     */
    protected function getDayItems(Collection $dates)
    {
        return $this->getYears($dates)->map(function ($year) use ($dates) {
            return $this->getMonths($dates, $year)->map(function ($month) use ($dates, $year) {
                return $this->getDays($dates, $year, $month)->map(function ($day) use ($year, $month) {
                    return $this->getDayItem($year, $month, $day);
                });
            });
        })->flatten(2);
    }
    /**
     * Get years where we have posts.
     *
     * @param  \Illuminate\Support\Collection  $dates
     * @return \Illuminate\Support\Collection
     */
    protected function getYears(Collection $dates)
    {
        return $dates->pluck('year')->unique()->values();
    }
    /**
     * Get months of given year where we have posts.
     *
     * @param  \Illuminate\Support\Collection  $dates
     * @param  string  $year
     * @return \Illuminate\Support\Collection
     */
    protected function getMonths(Collection $dates, $year)
    {
        return $dates->where('year', $year)->pluck('month')->unique()->values();
    }
    /**
     * Get days in given month of given year where we have posts.
     *
     * @param  \Illuminate\Support\Collection  $dates
     * @param  string  $year
     * @param  string  $month
     * @return \Illuminate\Support\Collection
     */
    protected function getDays(Collection $dates, $year, $month)
    {
        return $dates->where('year', $year)->where('month', $month)->pluck('day')->unique()->values();
    }
    /**
     * Get one posts by month item with metadata.
     *
     * @param  string  $year
     * @param  string  $month
     * @return array
     */
    protected function getMonthItem($year, $month)
    {
        return [
            'title' => 'Archive',
            'year' => $year,
            'month' => $month,
        ];
    }
    /**
     * Get one posts by day item with metadata.
     *
     * @param  string  $year
     * @param  string  $month
     * @param  string  $day
     * @return array
     */
    protected function getDayItem($year, $month, $day)
    {
        return [
            'title' => 'Archive',
            'year' => $year,
            'month' => $month,
            'day' => $day,
        ];
    }
}
