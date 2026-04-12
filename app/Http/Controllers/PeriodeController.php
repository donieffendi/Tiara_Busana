<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Periode;
use Illuminate\Http\Request;

class PeriodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $periodeLama = $request->session()->get('periode', []);

        $periodeBaru = array_merge($periodeLama, $request->input());

        // default kalau belum ada
        $periodeBaru['dept'] = $periodeBaru['dept'] ?? 'NF';
        // default kalau belum ada
        $periodeBaru['cabang'] = $periodeBaru['cabang'] ?? Auth::user()->CBG;

        $request->session()->put('periode', $periodeBaru);

        return back();
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
     * @param  \App\Models\Periode  $periode
     * @return \Illuminate\Http\Response
     */
    public function show(Periode $periode)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Periode  $periode
     * @return \Illuminate\Http\Response
     */
    public function edit(Periode $periode)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Periode  $periode
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Periode $periode)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Periode  $periode
     * @return \Illuminate\Http\Response
     */
    public function destroy(Periode $periode)
    {
        //
    }
}
