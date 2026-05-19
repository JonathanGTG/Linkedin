<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\Enrollment;

class HomeController extends Controller
{
    public function index()
    {
        $user        = Auth::user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $topPicks    = Course::published()->withLearningMetrics()
            ->orderByDesc('local_learner_count')->orderByDesc('jumlah_learner')->limit(8)->get();
        $shortCourses= Course::published()->short()->withLearningMetrics()
            ->orderByDesc('user_rating_avg')->orderByDesc('rating')->limit(8)->get();
        $inProgress  = Enrollment::with('course')->whereHas('course')->where('user_id',$user->id)->where('status','in_progress')->latest()->limit(4)->get();
        $saved       = Enrollment::with('course')->whereHas('course')->where('user_id',$user->id)->where('status','saved')->latest()->limit(8)->get();
        return view('home.index', compact('topPicks','shortCourses','inProgress','saved'));
    }
}
