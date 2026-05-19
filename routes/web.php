<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BrowseController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\HandsOnController;
use App\Http\Controllers\CertificationController;
use App\Http\Controllers\JourneyController;
use App\Http\Controllers\RoleGuideController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('home');
    }

    return view('welcome');
});

Route::middleware('guest')->group(function () {
    // Route::get('/',         [AuthController::class, 'showLogin'])->name('login');
    // Route::get('/login',    [AuthController::class, 'showLogin']);
    // Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout',                        [AuthController::class,      'logout'])->name('logout');

    // Home
    Route::get('/home',                           [HomeController::class,      'index'])->name('home');

    // Browse
    Route::get('/browse',                         [BrowseController::class,    'index'])->name('browse');
    Route::get('/browse/{category}',              [BrowseController::class,    'byCategory'])->name('browse.category');
    Route::get('/topics/{topic}',                  [TopicController::class,     'show'])->name('topics.show');

    // Course detail
    Route::get('/course/{course}',                [CourseController::class,    'show'])->name('course.show');
    Route::post('/course/{course}/video-complete',[CourseController::class,    'markVideoComplete'])->name('course.video.complete');
    Route::post('/course/{course}/rate',          [CourseController::class,    'rate'])->name('course.rate');

    // My Library
    Route::get('/my-library',                     [LibraryController::class,   'index'])->name('library');
    Route::get('/my-library/{tab}',               [LibraryController::class,   'tab'])->name('library.tab');
    Route::post('/library/save/{course}',         [LibraryController::class,   'save'])->name('library.save');
    Route::post('/library/enroll/{course}',       [LibraryController::class,   'enroll'])->name('library.enroll');

    // Hands-On Tech
    Route::get('/hands-on',                       [HandsOnController::class,   'index'])->name('hands-on.index');

    // Certifications
    Route::get('/certifications',                 [CertificationController::class,'index'])->name('certifications.index');
    Route::get('/certifications/{type}',          [CertificationController::class,'type'])->name('certifications.type');
    Route::get('/certifications/{type}/{provider}',[CertificationController::class,'provider'])->name('certifications.provider');

    // Role Guides
    Route::get('/roles/{role}',                    [RoleGuideController::class, 'show'])->name('roles.show');

    Route::get('/journey',                              [JourneyController::class, 'index'])->name('journey.index');
    Route::post('/journey/goal',                        [JourneyController::class, 'storeGoal'])->name('journey.goal');
    Route::post('/journey/module',                      [JourneyController::class, 'storeModule'])->name('journey.module');
    Route::delete('/journey/module/{module}',           [JourneyController::class, 'deleteModule'])->name('journey.module.delete');
    Route::post('/journey/module/{module}/course',      [JourneyController::class, 'addCourseToModule'])->name('journey.module.course');
});

// ── ADMIN ONLY ────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/',              [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/courses',       [AdminController::class, 'courses'])->name('admin.courses');
    Route::post('/courses',      [AdminController::class, 'storeCourse'])->name('admin.courses.store');
    Route::put('/courses/{course}',    [AdminController::class, 'updateCourse'])->name('admin.courses.update');
    Route::delete('/courses/{course}', [AdminController::class, 'deleteCourse'])->name('admin.courses.delete');
    Route::get('/users',         [AdminController::class, 'users'])->name('admin.users');
    Route::put('/users/{user}',        [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{user}',     [AdminController::class, 'deleteUser'])->name('admin.users.delete');
});

require __DIR__.'/auth.php';
