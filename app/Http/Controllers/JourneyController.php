<?php
// app/Http/Controllers/JourneyController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CareerGoal;
use App\Models\LearningPlanModule;
use App\Models\LearningPlanCourse;
use App\Models\Enrollment;
use App\Models\Course;

class JourneyController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $goal = CareerGoal::with(['modules.planCourses.course'])
            ->where('user_id', $user->id)
            ->first();

        // Semua course untuk pilihan ketika set goal
        $allCourses = Course::published()->orderBy('title')->get();

        return view('journey.index', compact('goal', 'allCourses'));
    }

    // Simpan/update goal karir
    public function storeGoal(Request $request)
    {
        $request->validate([
            'goal_title'    => 'required|string|max:255',
            'role_saat_ini' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        CareerGoal::updateOrCreate(
            ['user_id' => $user->id],
            [
                'goal_title'    => $request->goal_title,
                'role_saat_ini' => $request->role_saat_ini,
            ]
        );

        return back()->with('success', 'Goal karir berhasil disimpan!');
    }

    // Tambah modul ke learning plan
    public function storeModule(Request $request)
    {
        $request->validate(['judul_modul' => 'required|string|max:255']);

        $goal = CareerGoal::where('user_id', Auth::id())->firstOrFail();
        $urutan = $goal->modules()->count() + 1;

        LearningPlanModule::create([
            'career_goal_id' => $goal->id,
            'judul_modul'    => $request->judul_modul,
            'deskripsi'      => $request->deskripsi,
            'urutan'         => $urutan,
        ]);

        return back()->with('success', 'Modul berhasil ditambahkan!');
    }

    // Tambah course ke modul
    public function addCourseToModule(Request $request, LearningPlanModule $module)
    {
        $request->validate(['course_id' => 'required|exists:courses,id']);

        // Cegah duplikat
        $exists = LearningPlanCourse::where('module_id', $module->id)
            ->where('course_id', $request->course_id)->exists();

        if (!$exists) {
            $urutan = $module->planCourses()->count() + 1;
            LearningPlanCourse::create([
                'module_id' => $module->id,
                'course_id' => $request->course_id,
                'urutan'    => $urutan,
            ]);
        }

        return back()->with('success', 'Course berhasil ditambahkan ke modul!');
    }

    // Hapus modul
    public function deleteModule(LearningPlanModule $module)
    {
        $module->delete();
        return back()->with('success', 'Modul dihapus.');
    }
}