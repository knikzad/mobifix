<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $device_models = DB::select("
            SELECT 
                dm.model_id,
                dm.model_name,
                b.brand_name,
                dt.type_name AS device_type_name
            FROM device_model dm
            JOIN brand b ON dm.brand_id = b.brand_id
            JOIN device_type_brand dtb ON b.brand_id = dtb.brand_id
            JOIN device_type dt ON dt.device_type_id = dtb.device_type_id
            WHERE EXISTS (
                SELECT 1 
                FROM device_type_brand dtb2 
                WHERE dtb2.brand_id = dm.brand_id 
                AND dtb2.device_type_id = dt.device_type_id
            )
            ORDER BY dm.model_name ASC
        ");

        return view('devices.models.index', ['device_models' => $device_models]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
