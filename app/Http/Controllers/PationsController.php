<?php

namespace App\Http\Controllers;

use App\Models\pations;
use App\Models\mohs;
use Illuminate\Http\Request;

class PationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pations= pations::all();
        return view('pations.pations',compact('pations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $mohs= mohs::all();
        return view('pations.add_pations', compact('mohs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\pations  $pations
     * @return \Illuminate\Http\Response
     */
    public function show(pations $pations)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\pations  $pations
     * @return \Illuminate\Http\Response
     */
    public function edit(pations $pations)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\pations  $pations
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, pations $pations)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\pations  $pations
     * @return \Illuminate\Http\Response
     */
    public function destroy(pations $pations)
    {
        //
    }
}
