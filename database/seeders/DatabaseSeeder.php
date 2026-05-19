<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Category;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Video;
use App\Models\Skill;
use App\Models\Enrollment;
use App\Models\VideoProgress;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── USERS ────────────────────────────────────────────────────────────
        $admin = User::create([
            'name' => 'Admin', 'email' => 'admin@example.com',
            'password' => Hash::make('password'), 'role' => 'admin',
        ]);
        $user = User::create([
            'name' => 'Demo User', 'email' => 'user@example.com',
            'password' => Hash::make('password'), 'role' => 'user',
            'headline' => 'Software Developer',
        ]);

        // ── CATEGORIES ───────────────────────────────────────────────────────
        $tech     = Category::create(['id' => 'TOP-DEMO-T', 'name' => 'Technology', 'slug' => 'technology']);
        $business = Category::create(['id' => 'TOP-DEMO-B', 'name' => 'Business',   'slug' => 'business']);
        $creative = Category::create(['id' => 'TOP-DEMO-C', 'name' => 'Creative',   'slug' => 'creative']);
        $catMap = ['Technology' => $tech, 'Business' => $business, 'Creative' => $creative];

        // ── COURSES ──────────────────────────────────────────────────────────
        $courseMap = [];
        $skillCache = [];

        $coursesData = json_decode(<<<'JSON'
[
        {
                "title": "Figure Drawing: Tonal Rendering",
                "slug": "figure-drawing-tonal-rendering",
                "description": "How can you make a good figure drawing into a great figure drawing? Give it drama with tonal rendering. This course—taught on the iPad Pro—provides you with step-by-step demonstrations that can help you enhance the dimensionality and drama of your figure drawings using tonal rendering techniques. In",
                "instructor": "LinkedIn Learning",
                "level": "Intermediate",
                "durasi_detik": 5400,
                "jumlah_learner": 6877912,
                "rating": 4.7,
                "category": "Technology",
                "skills": [
                        "Monetization",
                        "Podcasting"
                ],
                "chapters": [
                        {
                                "judul": "Introduction",
                                "urutan": 1,
                                "videos": [
                                        {
                                                "title": "Making money from podcasting",
                                                "durasi_detik": 112,
                                                "urutan": 1,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "What is calculus?",
                                                "durasi_detik": 222,
                                                "urutan": 1,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "What is a function?",
                                                "durasi_detik": 517,
                                                "urutan": 2,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Types of functions",
                                                "durasi_detik": 158,
                                                "urutan": 3,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Polynomial and rational functions",
                                                "durasi_detik": 501,
                                                "urutan": 4,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Inverse functions",
                                                "durasi_detik": 437,
                                                "urutan": 5,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Trigonometric functions",
                                                "durasi_detik": 866,
                                                "urutan": 6,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Exponential and logarithmic functions",
                                                "durasi_detik": 885,
                                                "urutan": 7,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Chapter Quiz",
                                                "durasi_detik": 0,
                                                "urutan": 8,
                                                "tipe": "video"
                                        }
                                ]
                        },
                        {
                                "judul": "1. Products and Services",
                                "urutan": 2,
                                "videos": [
                                        {
                                                "title": "Lead magnets and tripwires",
                                                "durasi_detik": 105,
                                                "urutan": 1,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Tangent lines, velocity, and area",
                                                "durasi_detik": 204,
                                                "urutan": 1,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "What is a limit?",
                                                "durasi_detik": 248,
                                                "urutan": 2,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Digital courses",
                                                "durasi_detik": 134,
                                                "urutan": 2,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Ebooks",
                                                "durasi_detik": 108,
                                                "urutan": 3,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "One-sided limits",
                                                "durasi_detik": 420,
                                                "urutan": 3,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Limit properties",
                                                "durasi_detik": 430,
                                                "urutan": 4,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Templates and checklists",
                                                "durasi_detik": 99,
                                                "urutan": 4,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Precise definition of a limit",
                                                "durasi_detik": 351,
                                                "urutan": 5,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Packages",
                                                "durasi_detik": 101,
                                                "urutan": 5,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Coaching and masterminds",
                                                "durasi_detik": 80,
                                                "urutan": 6,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Infinite limits",
                                                "durasi_detik": 427,
                                                "urutan": 6,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Chapter Quiz",
                                                "durasi_detik": 0,
                                                "urutan": 7,
                                                "tipe": "video"
                                        }
                                ]
                        },
                        {
                                "judul": "2. Memberships and Donations",
                                "urutan": 3,
                                "videos": [
                                        {
                                                "title": "Premium content",
                                                "durasi_detik": 118,
                                                "urutan": 1,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Methods to calculate limits",
                                                "durasi_detik": 122,
                                                "urutan": 1,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Membership sites",
                                                "durasi_detik": 156,
                                                "urutan": 2,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Calculate limits using properties, part 1",
                                                "durasi_detik": 590,
                                                "urutan": 2,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Fan clubs, donations, and tip jars",
                                                "durasi_detik": 146,
                                                "urutan": 3,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Calculate limits using properties, part 2",
                                                "durasi_detik": 539,
                                                "urutan": 3,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Calculate limits using factoring",
                                                "durasi_detik": 427,
                                                "urutan": 4,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Calculate the limits of polynomial and rational functions",
                                                "durasi_detik": 512,
                                                "urutan": 5,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Calculate the limits of exponential and logarithmic functions",
                                                "durasi_detik": 322,
                                                "urutan": 6,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Calculate limits using the squeeze theorem",
                                                "durasi_detik": 461,
                                                "urutan": 7,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Calculate the limits of trigonometric functions",
                                                "durasi_detik": 268,
                                                "urutan": 8,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "When limits fail to exist",
                                                "durasi_detik": 333,
                                                "urutan": 9,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Chapter Quiz",
                                                "durasi_detik": 0,
                                                "urutan": 10,
                                                "tipe": "video"
                                        }
                                ]
                        },
                        {
                                "judul": "3. Networking and Events",
                                "urutan": 4,
                                "videos": [
                                        {
                                                "title": "Consulting",
                                                "durasi_detik": 146,
                                                "urutan": 1,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "What is continuity?",
                                                "durasi_detik": 201,
                                                "urutan": 1,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Live events and virtual events",
                                                "durasi_detik": 126,
                                                "urutan": 2,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Continuity at a point",
                                                "durasi_detik": 347,
                                                "urutan": 2,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Speaking",
                                                "durasi_detik": 108,
                                                "urutan": 3,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Continuity on an interval",
                                                "durasi_detik": 418,
                                                "urutan": 3,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "What is discontinuity?",
                                                "durasi_detik": 481,
                                                "urutan": 4,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Composite function theorem",
                                                "durasi_detik": 333,
                                                "urutan": 5,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Intermediate value theorem",
                                                "durasi_detik": 285,
                                                "urutan": 6,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Chapter Quiz",
                                                "durasi_detik": 0,
                                                "urutan": 7,
                                                "tipe": "video"
                                        }
                                ]
                        },
                        {
                                "judul": "4. Partnerships, Sponsorships, and Advertisers",
                                "urutan": 5,
                                "videos": [
                                        {
                                                "title": "Joint ventures",
                                                "durasi_detik": 109,
                                                "urutan": 1,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "What is a derivative?",
                                                "durasi_detik": 292,
                                                "urutan": 1,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Affiliate relationships",
                                                "durasi_detik": 112,
                                                "urutan": 2,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Rate of change",
                                                "durasi_detik": 252,
                                                "urutan": 2,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Sponsors and advertisers",
                                                "durasi_detik": 160,
                                                "urutan": 3,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Calculating derivatives using limits",
                                                "durasi_detik": 482,
                                                "urutan": 3,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Tangent lines and slopes",
                                                "durasi_detik": 396,
                                                "urutan": 4,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Continuity with derivatives",
                                                "durasi_detik": 355,
                                                "urutan": 5,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Computing with differentiation",
                                                "durasi_detik": 157,
                                                "urutan": 6,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Chapter Quiz",
                                                "durasi_detik": 0,
                                                "urutan": 7,
                                                "tipe": "video"
                                        }
                                ]
                        },
                        {
                                "judul": "Conclusion",
                                "urutan": 6,
                                "videos": [
                                        {
                                                "title": "Next steps",
                                                "durasi_detik": 70,
                                                "urutan": 1,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Differentiation rules",
                                                "durasi_detik": 102,
                                                "urutan": 1,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Constant rules",
                                                "durasi_detik": 228,
                                                "urutan": 2,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Sum and difference rules",
                                                "durasi_detik": 341,
                                                "urutan": 3,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Power rule",
                                                "durasi_detik": 292,
                                                "urutan": 4,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Product rule",
                                                "durasi_detik": 354,
                                                "urutan": 5,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Quotient rule",
                                                "durasi_detik": 355,
                                                "urutan": 6,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Chain rule",
                                                "durasi_detik": 504,
                                                "urutan": 7,
                                                "tipe": "video"
                                        },
                                        {
                                                "title": "Chapter Quiz",
                                                "durasi_detik": 0,
                                                "urutan": 8,
                                                "tipe": "video"
                                        }
                                ]
                        }
                ]
        },
        {
                "title": "Delivering an Authentic Elevator Pitch",
                "slug": "delivering-an-authentic-elevator-pitch",
                "description": "Find out how to craft an authentic personal pitch while projecting competence and warmth—two key factors in making a great first impression. Instructor Tatiana Kolovou shares how building trust with people and making a positive first impression isn’t a matter of magic. Tatiana reveals how verbal and",
                "instructor": "Ronnell Richards",
                "level": "Beginner",
                "durasi_detik": 2299,
                "jumlah_learner": 3526220,
                "rating": 4.8,
                "category": "Technology",
                "skills": [
                        "Personal Branding",
                        "Sales Effectiveness"
                ],
                "chapters": [
                        {
                                "judul": "1. Building Your Personal Brand",
                                "urutan": 1,
                                "videos": []
                        },
                        {
                                "judul": "2. Using Your Personal Branding to Boost Sales",
                                "urutan": 2,
                                "videos": []
                        }
                ]
        }
]
JSON, true);

        if ($coursesData === null) {
            $coursesData = [];
            $this->command->warn('coursesData JSON decode failed, skipping demo courses.');
        }

        foreach ($coursesData as $courseIndex => $cd) {
            $cat = $catMap[$cd['category']];
            $typeSuffix = ['Technology' => 'T', 'Business' => 'B', 'Creative' => 'C'][$cd['category']] ?? 'B';
            $courseId = 'CRS-DEMO-'.str_pad((string) ($courseIndex + 1), 3, '0', STR_PAD_LEFT).$typeSuffix;

            // Ensure unique slug
            $slug = $cd['slug'];
            $i = 0;
            while (Course::where('slug', $slug)->exists()) {
                $i++;
                $slug = $cd['slug'] . '-' . $i;
            }

            $course = Course::create([
                'id'             => $courseId,
                'category_id'    => $cat->id,
                'title'          => $cd['title'],
                'slug'           => $slug,
                'description'    => $cd['description'] ?: null,
                'instructor_name'=> $cd['instructor'],
                'level'          => $cd['level'],
                'durasi_detik'   => $cd['durasi_detik'],
                'jumlah_learner' => $cd['jumlah_learner'],
                'rating'         => $cd['rating'],
                'is_published'   => true,
            ]);

            // Skills
            $skillIds = [];
            foreach ($cd['skills'] as $sname) {
                if (!isset($skillCache[$sname])) {
                    $skillCache[$sname] = Skill::firstOrCreate(
                        ['id' => 'SKL-DEMO-'.Str::slug($sname)],
                        ['name' => $sname, 'slug' => Str::slug($sname)]
                    )->id;
                }
                $skillIds[] = $skillCache[$sname];
            }
            if ($skillIds) $course->skills()->attach($skillIds);

            // Chapters & Videos
            $vUrutan = 0;
            foreach ($cd['chapters'] as $chapterIndex => $ch) {
                $chapter = Chapter::create([
                    'id'        => $courseId.'-CH-'.str_pad((string) ($chapterIndex + 1), 2, '0', STR_PAD_LEFT),
                    'course_id' => $course->id,
                    'judul'     => $ch['judul'],
                    'urutan'    => $ch['urutan'],
                ]);
                foreach ($ch['videos'] as $v) {
                    $vUrutan++;
                    $vslug = Str::slug($v['title']);
                    $j = 0;
                    while (Video::where('slug', $vslug)->exists()) {
                        $j++;
                        $vslug = Str::slug($v['title']) . '-' . $j;
                    }
                    Video::create([
                        'id'           => $courseId.'-VID-'.str_pad((string) $vUrutan, 3, '0', STR_PAD_LEFT),
                        'course_id'    => $course->id,
                        'chapter_id'   => $chapter->id,
                        'title'        => $v['title'],
                        'slug'         => $vslug,
                        'video_url'    => 'videos/' . $slug . '/v' . $vUrutan . '.mp4',
                        'durasi_detik' => $v['durasi_detik'],
                        'urutan'       => $v['urutan'],
                        'is_preview'   => ($v['urutan'] === 1),
                    ]);
                }
            }

            $courseMap[$cd['title']] = $course;
        }

        // ── SAMPLE ENROLLMENT ────────────────────────────────────────────────
        $first  = Course::first();
        $second = Course::skip(1)->first();
        if ($first) {
            Enrollment::create(['user_id'=>$user->id,'course_id'=>$first->id,'status'=>'in_progress','enrolled_at'=>now()->subDays(5)]);
            $vid = $first->videos()->first();
            if ($vid) VideoProgress::create(['user_id'=>$user->id,'video_id'=>$vid->id,'detik_terakhir'=>$vid->durasi_detik,'is_completed'=>true]);
        }
        if ($second) {
            Enrollment::create(['user_id'=>$user->id,'course_id'=>$second->id,'status'=>'saved']);
        }

        $this->command->info('Seeder selesai! Login: user@example.com / password');
    }
}
