<?php

namespace App\Http\Controllers;

use App\Models\fck;
use Illuminate\Http\Request;

class FckController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
        return view('fcks.Fcks');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
     * @param  \App\Models\fck  $fck
     * @return \Illuminate\Http\Response
     */
    public function show(fck $fck)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\fck  $fck
     * @return \Illuminate\Http\Response
     */
    public function edit(fck $fck)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\fck  $fck
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, fck $fck)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\fck  $fck
     * @return \Illuminate\Http\Response
     */
    public function destroy(fck $fck)
    {
        //
    }
}
