<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $users = User::count();
        $files = File::all();
        $widget = [
            'users' => $users,
            'files' => $files
        ];

        return view('home', [
            'users' => $users,
            'files' => $files
        ]);
    }

    // public function monitor(){
    //     $files = \DB::table('files')->paginate(10);
    //     return view('home');
    // }
}
