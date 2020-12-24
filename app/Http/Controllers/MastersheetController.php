<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MastersheetController extends Controller
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
        return view('mastersheet', [
            'component' => 'mastersheet'
        ]);
    }

    public function show($group, $component)
    {
        return view('mastersheet', compact('group', 'component'));
    }
}
