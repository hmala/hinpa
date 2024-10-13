<?php

namespace App\Http\Controllers;

use App\Models\Moh;
use Illuminate\Http\Request;

class MohController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('mohs.mohs');
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
     * @param  \App\Models\Moh  $moh
     * @return \Illuminate\Http\Response
     */
    public function show(Moh $moh)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Moh  $moh
     * @return \Illuminate\Http\Response
     */
    public function edit(Moh $moh)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Moh  $moh
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Moh $moh)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Moh  $moh
     * @return \Illuminate\Http\Response
     */
    public function destroy(Moh $moh)
    {
        //
    }
}
