<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Category;

class HomeController extends Controller
{
    private array $contentTypes = [
        'B' => 'Business',
        'T' => 'Technology',
        'C' => 'Creative',
    ];

    public function index()
    {
        $user        = Auth::user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $topPicks = Course::published()->with('category')->withLearningMetrics()
            ->orderByDesc('local_learner_count')->orderByDesc('jumlah_learner')->limit(8)->get();

        $shortCourses = Course::published()->short()->with('category')->withLearningMetrics()
            ->orderByDesc('user_rating_avg')->orderByDesc('rating')->limit(8)->get();

        $recentCourses = Course::published()->with('category')->withLearningMetrics()
            ->whereNotNull('release_date')
            ->orderByDesc('release_date')
            ->limit(8)
            ->get();

        $inProgress = Enrollment::with(['course.videos', 'course.category'])
            ->whereHas('course')
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->latest()
            ->limit(4)
            ->get();

        $saved = Enrollment::with(['course.category'])
            ->whereHas('course')
            ->where('user_id', $user->id)
            ->where('status', 'saved')
            ->latest()
            ->limit(8)
            ->get();

        $learningCategoryIds = Enrollment::query()
            ->join('courses', 'courses.id', '=', 'enrollments.course_id')
            ->where('enrollments.user_id', $user->id)
            ->pluck('courses.category_id')
            ->filter()
            ->unique()
            ->values();

        $recommended = Course::published()->with('category')->withLearningMetrics()
            ->when($learningCategoryIds->isNotEmpty(), fn ($query) => $query->whereIn('category_id', $learningCategoryIds))
            ->orderByDesc('jumlah_learner')
            ->limit(8)
            ->get();

        if ($recommended->isEmpty()) {
            $recommended = $topPicks;
        }

        $contentStats = collect($this->contentTypes)->map(fn ($label, $type) => [
            'type' => $type,
            'label' => $label,
            'count' => Course::published()->where('id', 'like', '%'.$type)->count(),
            'url' => route('browse', ['type' => $type]),
        ])->values();

        $featuredCategories = collect($this->contentTypes)->mapWithKeys(fn ($label, $type) => [
            $label => Category::query()
                ->whereHas('courses', fn ($query) => $query->published()->where('id', 'like', '%'.$type))
                ->withCount(['courses as total_courses' => fn ($query) => $query->published()->where('id', 'like', '%'.$type)])
                ->orderByDesc('total_courses')
                ->limit(4)
                ->get(),
        ]);

        $trendingSkills = DB::table('skills')
            ->select('skills.name', 'skills.slug', DB::raw('COUNT(*) as total'))
            ->join('course_skill', 'course_skill.skill_id', '=', 'skills.id')
            ->join('courses', 'courses.id', '=', 'course_skill.course_id')
            ->where('courses.is_published', true)
            ->groupBy('skills.id', 'skills.name', 'skills.slug')
            ->orderByDesc('total')
            ->limit(14)
            ->get();

        $roleGuides = collect([
            'Business' => ['Marketing Manager', 'Project Manager', 'Financial Analyst', 'Business Analyst'],
            'Technology' => ['Software Engineer', 'Data Analyst', 'Cloud Engineer', 'Cybersecurity Analyst'],
            'Creative' => ['Graphic Designer', 'UX Designer', 'Video Editor', 'Content Creator'],
        ])->map(fn ($roles) => collect($roles)->map(fn ($role) => [
            'title' => $role,
            'url' => route('roles.show', 'role-'.Str::slug($role)),
        ]));

        return view('home.index', compact(
            'topPicks',
            'shortCourses',
            'recentCourses',
            'recommended',
            'inProgress',
            'saved',
            'contentStats',
            'featuredCategories',
            'trendingSkills',
            'roleGuides',
        ));
    }
}
