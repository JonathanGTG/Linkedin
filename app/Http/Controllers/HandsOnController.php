<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class HandsOnController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::query()
            ->published()
            ->with(['category'])
            ->withLearningMetrics()
            ->where('title', 'like', '%Hands-On%');

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        if ($request->filled('durasi')) {
            match($request->durasi) {
                'short' => $query->where('durasi_detik', '<=', 1800),
                'medium' => $query->whereBetween('durasi_detik', [1801, 3600]),
                'long' => $query->where('durasi_detik', '>', 3600),
                default  => null,
            };
        }

        if ($request->filled('q')) {
            $query->where('title', 'like', '%'.$request->q.'%');
        }

        $courses = $query
            ->orderByDesc('local_learner_count')
            ->orderByDesc('jumlah_learner')
            ->orderBy('title')
            ->paginate(12)
            ->withQueryString();

        $title = 'Hands-On Tech - LinkedIn Learning';

        return view('hands-on.index', compact('courses', 'title'));
    }
}
