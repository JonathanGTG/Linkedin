<?php
// app/Http/Controllers/AdminController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Course;
use App\Models\Category;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\Video;

class AdminController extends Controller
{
    // ── DASHBOARD ─────────────────────────────────────────────
    public function dashboard()
    {
        $stats = [
            'total_users'       => User::where('role', 'user')->count(),
            'total_courses'     => Course::count(),
            'total_enrollments' => Enrollment::count(),
            'total_completed'   => Enrollment::where('status', 'completed')->count(),
        ];

        // 5 course paling populer
        $topCourses = Course::with('category')->withLearningMetrics()
            ->orderByDesc('local_learner_count')->orderByDesc('jumlah_learner')->limit(5)->get();

        // Enrollment terbaru
        $recentEnrollments = Enrollment::with(['user', 'course'])
            ->latest()->limit(8)->get();

        return view('admin.dashboard', compact('stats', 'topCourses', 'recentEnrollments'));
    }

    // ── COURSES ───────────────────────────────────────────────
    public function courses(Request $request)
    {
        $query = Course::with('category')->withLearningMetrics();
        if ($request->filled('q')) {
            $query->where('title', 'like', '%' . $request->q . '%');
        }
        $courses = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $categories = Category::all();
        return view('admin.courses', compact('courses', 'categories'));
    }

    public function storeCourse(Request $request)
    {
        $request->validate([
            'title'           => 'required|string|max:255',
            'category_id'     => 'required|exists:categories,id',
            'instructor_name' => 'required|string|max:255',
            'level'           => 'required|in:Beginner,Intermediate,Advanced',
            'durasi_detik'    => 'required|integer|min:0',
            'rating'          => 'nullable|numeric|min:0|max:5',
        ]);

        Course::create([
            'title'           => $request->title,
            'slug'            => Str::slug($request->title) . '-' . time(),
            'category_id'     => $request->category_id,
            'instructor_name' => $request->instructor_name,
            'level'           => $request->level,
            'durasi_detik'    => $request->durasi_detik,
            'rating'          => $request->rating ?? 0,
            'jumlah_learner'  => 0,
            'description'     => $request->description,
            'is_published'    => $request->boolean('is_published', true),
        ]);

        return back()->with('success', 'Course berhasil ditambahkan!');
    }

    public function updateCourse(Request $request, Course $course)
    {
        $request->validate([
            'title'           => 'required|string|max:255',
            'category_id'     => 'required|exists:categories,id',
            'instructor_name' => 'required|string|max:255',
            'level'           => 'required|in:Beginner,Intermediate,Advanced',
            'durasi_detik'    => 'required|integer|min:0',
        ]);

        $course->update($request->only(
            'title', 'category_id', 'instructor_name',
            'level', 'durasi_detik', 'rating', 'description', 'is_published'
        ));

        return back()->with('success', 'Course berhasil diupdate!');
    }

    public function deleteCourse(Course $course)
    {
        $course->delete();
        return back()->with('success', 'Course dihapus.');
    }

    // ── USERS ─────────────────────────────────────────────────
    public function users(Request $request)
    {
        $query = User::withCount('enrollments');
        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->q.'%')
                  ->orWhere('email', 'like', '%'.$request->q.'%');
        }
        $users = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        return view('admin.users', compact('users'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:user,admin',
        ]);

        $user->update($request->only('name', 'email', 'role'));
        return back()->with('success', 'User berhasil diupdate.');
    }

    public function deleteUser(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Tidak bisa menghapus admin!');
        }
        $user->delete();
        return back()->with('success', 'User dihapus.');
    }
}
