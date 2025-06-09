<?php

namespace App\Http\Controllers\Device;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $device_types = DB::select("
            SELECT 
                dt.device_type_id,
                dt.type_name,
                COUNT(DISTINCT dtb.brand_id) AS brands_count,
                COUNT(DISTINCT dm.model_id) AS models_count
            FROM device_type dt
            LEFT JOIN device_type_brand dtb ON dt.device_type_id = dtb.device_type_id
            LEFT JOIN brand b ON dtb.brand_id = b.brand_id
            LEFT JOIN device_model dm ON dm.brand_id = b.brand_id
            GROUP BY dt.device_type_id, dt.type_name
            ORDER BY dt.type_name
        ");

        return view('devices.types.index', ['device_types' => $device_types]);
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

    public function showBrands($id)
    {
        // Fetch brands for the given device type ID
        $brands = DB::select("
            SELECT 
                b.brand_id,
                b.brand_name,
                COUNT(DISTINCT dm.model_id) AS models_count
            FROM brand b
            INNER JOIN device_type_brand dtb ON b.brand_id = dtb.brand_id
            LEFT JOIN device_model dm ON b.brand_id = dm.brand_id
            WHERE dtb.device_type_id = ?
            GROUP BY b.brand_id, b.brand_name
            ORDER BY b.brand_name
        ", [$id]);

        // Fetch the device type name for the title
        $device = DB::selectOne("SELECT * FROM device_type WHERE device_type_id = ?", [$id]);

        return view('devices.types.brands', [
            'brands' => $brands,
            'device' => $device
        ]);
    }


}
