<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function movies()
    {
        $movies = Movie::with(['genre', 'ageRating'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.movies.index', compact('movies'));
    }
}