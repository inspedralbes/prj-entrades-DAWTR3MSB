<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Esdeveniment;

class EventController extends Controller
{
    public function index()
    {
        return response()->json(Esdeveniment::all());
    }

    public function show($id)
    {
        $event = Esdeveniment::with('seients')->findOrFail($id);
        return response()->json($event);
    }
}
