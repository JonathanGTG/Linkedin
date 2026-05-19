<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Support\LinkedInTopicCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TopicController extends Controller
{
    public function show(string $topic, Request $request)
    {
        $meta = LinkedInTopicCatalog::find($topic);
        abort_if(!$meta, 404);

        $featuredCourses = $this->topicCourseQuery($meta)
            ->orderByDesc('local_learner_count')
            ->orderByDesc('jumlah_learner')
            ->limit(8)
            ->get();

        $query = $this->topicCourseQuery($meta);

        if ($request->filled('q')) {
            $this->applySearch($query, (string) $request->q);
        }

        if ($request->filled('level')) {
            $query->level($request->level);
        }

        if ($request->filled('durasi')) {
            match ($request->durasi) {
                'short' => $query->where('durasi_detik', '<=', 1800),
                'medium' => $query->whereBetween('durasi_detik', [1801, 7200]),
                'long' => $query->where('durasi_detik', '>', 7200),
                default => null,
            };
        }

        $this->applySort($query, (string) $request->query('sort', 'popular'));

        $courses = $query->paginate(12)->withQueryString();
        $subtopics = $meta['subtopics'];
        $relatedTopics = LinkedInTopicCatalog::relatedTopics($meta['type'], $meta['slug']);
        $typeTabs = LinkedInTopicCatalog::typeLabels();

        return view('topics.show', compact(
            'meta',
            'courses',
            'featuredCourses',
            'subtopics',
            'relatedTopics',
            'typeTabs'
        ));
    }

    private function topicCourseQuery(array $meta)
    {
        $aliases = $meta['aliases'];

        return Course::published()
            ->with(['category', 'skills'])
            ->withLearningMetrics()
            ->where('id', 'like', '%'.$meta['type'])
            ->where(function ($query) use ($aliases) {
                foreach ($aliases as $term) {
                    $query
                        ->orWhere('title', 'like', '%'.$term.'%')
                        ->orWhere('description', 'like', '%'.$term.'%')
                        ->orWhereHas('category', function ($category) use ($term) {
                            $category
                                ->where('name', 'like', '%'.$term.'%')
                                ->orWhere('slug', Str::slug($term));
                        })
                        ->orWhereHas('skills', function ($skill) use ($term) {
                            $skill
                                ->where('skills.name', 'like', '%'.$term.'%')
                                ->orWhere('skills.slug', Str::slug($term));
                        });
                }
            });
    }

    private function applySearch($query, string $term): void
    {
        $query->where(function ($search) use ($term) {
            $search
                ->where('title', 'like', '%'.$term.'%')
                ->orWhere('description', 'like', '%'.$term.'%')
                ->orWhereHas('category', function ($category) use ($term) {
                    $category->where('name', 'like', '%'.$term.'%');
                })
                ->orWhereHas('skills', function ($skill) use ($term) {
                    $skill->where('skills.name', 'like', '%'.$term.'%');
                });
        });
    }

    private function applySort($query, string $sort): void
    {
        match ($sort) {
            'recent' => $query->orderByDesc('release_date'),
            'rating' => $query->orderByDesc('rating'),
            default => $query->orderByDesc('local_learner_count')->orderByDesc('jumlah_learner'),
        };
    }
}
