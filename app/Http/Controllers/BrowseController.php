<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Course;
use App\Models\Category;
use App\Models\Skill;
use App\Support\LinkedInTopicCatalog;

class BrowseController extends Controller
{
    private array $contentTypes = [
        'B' => 'Business',
        'T' => 'Technology',
        'C' => 'Creative',
    ];

    public function index(Request $request)
    {
        $selectedType = $this->selectedType($request);
        $typeLabel = $this->contentTypes[$selectedType];
        $categories = $this->categoriesForType($selectedType);
        $topicGroups = $this->topicGroupsForType($selectedType);
        [$roleGuides, $extraRoleGuides] = $this->roleGuidesForType($selectedType);
        $linkedinTopics = $this->linkedinTopicsForType($selectedType);
        $query = Course::published()->with('category')->withLearningMetrics();
        $this->applyContentType($query, $selectedType);

        if ($request->filled('level'))  $query->level($request->level);
        if ($request->filled('durasi')) {
            match($request->durasi) {
                'short'  => $query->where('durasi_detik','<=',1800),
                'medium' => $query->whereBetween('durasi_detik',[1801,7200]),
                'long'   => $query->where('durasi_detik','>',7200),
                default  => null,
            };
        }
        if ($request->filled('q')) $this->applySearch($query, (string) $request->q);
        $this->applySort($query, (string) $request->query('sort', 'popular'));
        $courses = $query->paginate(12)->withQueryString();

        return view('browse.index', compact('courses','categories','topicGroups','selectedType','typeLabel','roleGuides','extraRoleGuides','linkedinTopics'));
    }

    public function byCategory(string $category, Request $request)
    {
        $category = Category::where('slug', $category)->orWhere('id', $category)->firstOrFail();
        $selectedType = $this->selectedType($request);
        $typeLabel = $this->contentTypes[$selectedType];
        $categories = $this->categoriesForType($selectedType);
        $topicGroups = $this->topicGroupsForType($selectedType);
        [$roleGuides, $extraRoleGuides] = $this->roleGuidesForType($selectedType);
        $linkedinTopics = $this->linkedinTopicsForType($selectedType);
        $query = Course::published()->where('category_id',$category->id)->with('category')->withLearningMetrics();
        $this->applyContentType($query, $selectedType);

        if ($request->filled('level')) $query->level($request->level);
        if ($request->filled('durasi')) {
            match($request->durasi) {
                'short'  => $query->where('durasi_detik','<=',1800),
                'medium' => $query->whereBetween('durasi_detik',[1801,7200]),
                'long'   => $query->where('durasi_detik','>',7200),
                default  => null,
            };
        }
        if ($request->filled('q')) $this->applySearch($query, (string) $request->q);
        if ($request->filled('skill')) {
            $skill = (string) $request->skill;
            $query->whereHas('skills', function ($q) use ($skill) {
                $q->where('skills.slug', $skill)->orWhere('skills.id', $skill);
            });
        }
        $this->applySort($query, (string) $request->query('sort', 'popular'));
        $courses = $query->paginate(12)->withQueryString();

        $topicChips = Skill::query()
            ->select('skills.*')
            ->join('course_skill', 'course_skill.skill_id', '=', 'skills.id')
            ->join('courses', 'courses.id', '=', 'course_skill.course_id')
            ->where('courses.is_published', true)
            ->where('courses.category_id', $category->id)
            ->where('courses.id', 'like', '%'.$selectedType)
            ->groupBy('skills.id', 'skills.name', 'skills.slug', 'skills.created_at', 'skills.updated_at')
            ->orderByDesc(DB::raw('COUNT(*)'))
            ->limit(8)
            ->get();

        return view('browse.index', compact('courses','categories','topicGroups','category','selectedType','typeLabel','topicChips','roleGuides','extraRoleGuides','linkedinTopics'));
    }

    private function selectedType(Request $request): string
    {
        $type = strtoupper((string) $request->query('type', 'B'));

        return array_key_exists($type, $this->contentTypes) ? $type : 'B';
    }

    private function applyContentType($query, string $type): void
    {
        $query->where('id', 'like', '%'.$type);
    }

    private function categoriesForType(string $type)
    {
        return Category::whereHas('courses', function ($query) use ($type) {
            $query->published()->where('id', 'like', '%'.$type);
        })
            ->withCount(['courses as type_courses_count' => function ($query) use ($type) {
                $query->published()->where('id', 'like', '%'.$type);
            }])
            ->orderByDesc('type_courses_count')
            ->orderBy('name')
            ->get();
    }

    private function topicGroupsForType(string $type)
    {
        return Course::published()
            ->where('id', 'like', '%'.$type)
            ->with('category')
            ->orderByDesc('jumlah_learner')
            ->limit(180)
            ->get()
            ->filter(fn ($course) => $course->category)
            ->groupBy(fn ($course) => $course->category->name)
            ->take(15);
    }

    private function applySort($query, string $sort): void
    {
        match ($sort) {
            'recent' => $query->orderByDesc('release_date'),
            'rating' => $query->orderByDesc('rating'),
            default  => $query->orderByDesc('local_learner_count')->orderByDesc('jumlah_learner'),
        };
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

    private function roleGuidesForType(string $type): array
    {
        $roles = match ($type) {
            'T' => [
                'Software Engineer', 'Data Analyst', 'Data Scientist', 'Web Developer',
                'Full-Stack Developer', 'Front-End Developer', 'Back-End Developer',
                'Cloud Engineer', 'DevOps Engineer', 'Cybersecurity Analyst',
                'IT Support Specialist', 'Network Administrator', 'Database Administrator',
                'Machine Learning Engineer', 'AI Engineer', 'Systems Administrator',
                'QA Engineer', 'Mobile Developer', 'Solutions Architect', 'Data Engineer',
                'Security Engineer', 'Technical Program Manager',
            ],
            'C' => [
                'Graphic Designer', 'UX Designer', 'UI Designer', 'Web Designer',
                'Video Editor', 'Motion Graphics Designer', 'Photographer',
                'Illustrator', '3D Artist', 'Content Creator', 'Copywriter',
                'Creative Director', 'Brand Designer', 'Art Director',
                'Instructional Designer', 'Digital Illustrator', 'Animation Artist',
                'Presentation Designer', 'Design Systems Designer', 'Social Media Designer',
            ],
            default => [
                'Marketing Manager', 'Operations Manager', 'Program Manager',
                'Product Manager', 'Project Manager', 'Financial Analyst',
                'Sales Manager', 'Business Development Manager', 'Accountant',
                'Salesperson', 'Recruiter', 'Marketing Specialist',
                'Human Resources Specialist', 'Supply Chain Specialist',
                'Social Media Manager', 'People Manager', 'Human Resources Manager',
                'Customer Service Manager', 'Customer Service Representative',
                'Business Analyst', 'Account Executive', 'Data Analyst',
                'Chief of Staff', 'Strategy Manager',
            ],
        };

        $mapped = collect($roles)->map(fn ($role) => [
            'title' => $role,
            'slug' => 'role-'.Str::slug($role),
        ]);

        return [$mapped->take(20)->values(), $mapped->slice(20)->values()];
    }

    private function linkedinTopicsForType(string $type)
    {
        return collect(LinkedInTopicCatalog::topicsForType($type))
            ->map(fn (string $topic) => $this->mapLinkedinTopic($topic, $type));
    }

    private function mapLinkedinTopic(string $topic, string $type): array
    {
        $aliases = LinkedInTopicCatalog::aliases($topic);
        $category = Category::query()
            ->whereHas('courses', fn ($query) => $query->published()->where('id', 'like', '%'.$type))
            ->where(function ($query) use ($aliases) {
                foreach ($aliases as $alias) {
                    $query
                        ->orWhere('name', $alias)
                        ->orWhere('slug', Str::slug($alias));
                }
            })
            ->withCount(['courses as type_courses_count' => fn ($query) => $query->published()->where('id', 'like', '%'.$type)])
            ->orderByDesc('type_courses_count')
            ->first();

        if ($category) {
            return [
                'title' => $topic,
                'url' => route('topics.show', Str::slug($topic)),
                'count' => (int) $category->type_courses_count,
                'source' => 'category',
            ];
        }

        $terms = $aliases;
        $count = Course::published()
            ->where('id', 'like', '%'.$type)
            ->where(function ($query) use ($terms) {
                foreach ($terms as $term) {
                    $query
                        ->orWhere('title', 'like', '%'.$term.'%')
                        ->orWhere('description', 'like', '%'.$term.'%');
                }
            })
            ->count();

        return [
            'title' => $topic,
            'url' => route('topics.show', Str::slug($topic)),
            'count' => $count,
            'source' => 'search',
        ];
    }
}
