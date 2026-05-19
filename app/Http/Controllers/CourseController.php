<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\VideoProgress;
use App\Models\CourseRating;

class CourseController extends Controller
{
    public function show(Course $course)
    {
        $user = Auth::user();
        $course->load(['category','videos','skills','chapters.videos','instructors']);
        $course->loadCount(['activeEnrollments as local_learner_count', 'ratings as user_rating_count']);
        $course->loadAvg('ratings as user_rating_avg', 'rating');
        $enrollment    = Enrollment::where('user_id',$user->id)->where('course_id',$course->id)->first();
        $userRating = CourseRating::where('user_id',$user->id)->where('course_id',$course->id)->first();
        $comments = CourseRating::with('user')
            ->where('course_id', $course->id)
            ->whereNotNull('review')
            ->where('review', '!=', '')
            ->latest()
            ->limit(20)
            ->get();
        $videoProgress = VideoProgress::where('user_id',$user->id)
            ->whereIn('video_id',$course->videos->pluck('id'))->get()->keyBy('video_id');
        $totalVideos = $course->videos->count();
        $completedCount = $videoProgress->where('is_completed', true)->count();
        $progressPct = $totalVideos > 0 ? (int) round(($completedCount / $totalVideos) * 100) : 0;
        $related = Course::published()->where('category_id',$course->category_id)
            ->where('id','!=',$course->id)->withLearningMetrics()
            ->orderByDesc('local_learner_count')->orderByDesc('jumlah_learner')->limit(4)->get();
        return view('course.show', compact(
            'course',
            'enrollment',
            'userRating',
            'comments',
            'videoProgress',
            'related',
            'totalVideos',
            'completedCount',
            'progressPct'
        ));
    }

    public function rate(Request $request, Course $course)
    {
        $enrollment = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->first();

        if (! $enrollment || $enrollment->status !== 'completed') {
            return back()->with('error', 'Rating hanya bisa diberikan setelah course selesai.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'max:2000'],
        ]);

        CourseRating::updateOrCreate(
            ['user_id' => Auth::id(), 'course_id' => $course->id],
            ['rating' => (float) $validated['rating'], 'review' => $validated['review'] ?? null]
        );

        return back()->with('success', 'Terima kasih, rating kamu tersimpan.');
    }

    public function markVideoComplete(Request $request, Course $course)
    {
        $user    = Auth::user();
        $videoId = $request->input('video_id');

        VideoProgress::updateOrCreate(
            ['user_id'=>$user->id,'video_id'=>$videoId],
            ['is_completed'=>true,'detik_terakhir'=>$request->input('detik',0)]
        );
        Enrollment::updateOrCreate(
            ['user_id'=>$user->id,'course_id'=>$course->id],
            ['status'=>'in_progress','enrolled_at'=>now()]
        );

        $totalVideo   = $course->videos()->count();
        $selesaiCount = VideoProgress::where('user_id',$user->id)
            ->whereIn('video_id',$course->videos->pluck('id'))
            ->where('is_completed',true)->count();

        if ($selesaiCount >= $totalVideo) {
            Enrollment::where('user_id',$user->id)->where('course_id',$course->id)
                ->update(['status'=>'completed','completed_at'=>now()]);
        }
        return response()->json(['success'=>true,'progress'=>round(($selesaiCount/$totalVideo)*100)]);
    }
}
