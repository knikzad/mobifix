<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $brands = DB::select("
            SELECT 
                b.brand_id,
                b.brand_name,
                dt.device_type_id,
                dt.type_name AS device_type_name,
                COUNT(dm.model_id) AS models_count
            FROM brand b
            JOIN device_type_brand dtb ON b.brand_id = dtb.brand_id
            JOIN device_type dt ON dt.device_type_id = dtb.device_type_id
            LEFT JOIN device_model dm ON b.brand_id = dm.brand_id
            GROUP BY b.brand_id, b.brand_name, dt.device_type_id, dt.type_name
            ORDER BY b.brand_name, dt.type_name
        ");

        return view('devices.brands.index', ['brands' => $brands]);
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


    public function showModels($brand_id, $device_type_id)
    {
        $models = DB::select("
            SELECT dm.model_id, dm.model_name, b.brand_name, dt.type_name
            FROM device_model dm
            JOIN brand b ON dm.brand_id = b.brand_id
            JOIN device_type_brand dtb ON dtb.brand_id = b.brand_id
            JOIN device_type dt ON dt.device_type_id = dtb.device_type_id
            WHERE b.brand_id = ? AND dt.device_type_id = ?
        ", [$brand_id, $device_type_id]);

        return view('devices.models.list', [
            'models' => $models
        ]);
    }


}
