<?php

namespace App\Http\Controllers;

use App\Models\salat;
use Illuminate\Http\Request;
use App\Models\mohs;
use App\Models\fck;

use App\Models\rdhs;
use App\Models\surg;
use App\Models\salsurs;
use Illuminate\Support\Facades\Auth;

class SurgController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fck=fck::all();
        $salsurs = salsurs::all(); 
        $mohs=mohs::all(); 
        $surg=surg::all(); 

        return view('surg.surg',compact('mohs','salsurs','fck','surg'));
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
     * @param  \App\Models\surg  $surg
     * @return \Illuminate\Http\Response
     */
    public function show(surg $surg)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\surg  $surg
     * @return \Illuminate\Http\Response
     */
    public function edit(surg $surg)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\surg  $surg
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, surg $surg)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\surg  $surg
     * @return \Illuminate\Http\Response
     */
    public function destroy(surg $surg)
    {
        //
    }
}
