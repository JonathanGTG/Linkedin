<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HandsOnLab;

class HandsOnController extends Controller
{
    public function index(Request $request)
    {
        $query = HandsOnLab::where('is_published', true);
        if ($request->filled('type'))   $query->where('type', $request->type);
        if ($request->filled('level'))  $query->where('level', $request->level);
        if ($request->filled('durasi')) {
            match($request->durasi) {
                'short'  => $query->where('durasi_menit','<=',30),
                'medium' => $query->whereBetween('durasi_menit',[31,60]),
                'long'   => $query->where('durasi_menit','>',60),
                default  => null,
            };
        }
        if ($request->filled('q')) $query->where('title','like','%'.$request->q.'%');
        $labs = $query->orderByDesc('created_at')->paginate(12)->withQueryString();
        return view('hands-on.index', compact('labs'));
    }
}
