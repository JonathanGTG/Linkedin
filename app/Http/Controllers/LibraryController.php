<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\Enrollment;

class LibraryController extends Controller
{
    public function index()  { return $this->tab('in-progress'); }

    public function tab(string $tab)
    {
        $user   = Auth::user();
        $status = match($tab) { 'saved'=>'saved','completed'=>'completed',default=>'in_progress' };
        $enrollments = Enrollment::with('course.category')
            ->whereHas('course')
            ->where('user_id',$user->id)->where('status',$status)->latest()->get();
        foreach ($enrollments as $e) $e->progress_percent = $e->progress_percent;
        return view('library.index', compact('enrollments','tab'));
    }

    public function save(Request $request, Course $course)
    {
        Enrollment::updateOrCreate(
            ['user_id'=>Auth::id(),'course_id'=>$course->id],
            ['status'=>'saved']
        );
        return back()->with('success', "\"$course->title\" disimpan ke Library.");
    }

    public function enroll(Request $request, Course $course)
    {
        Enrollment::updateOrCreate(
            ['user_id'=>Auth::id(),'course_id'=>$course->id],
            ['status'=>'in_progress','enrolled_at'=>now()]
        );

        return back()->with('success', "Selamat belajar \"$course->title\"!");
    }
}
