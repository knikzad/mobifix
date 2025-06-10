<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RepairOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Retrieve the selected user from the session
        $selectedUser = session('selected_user');

        if (!$selectedUser) {
            return redirect()->route('use_case.index')->withErrors('No user selected.');
        }

        // Build the query based on the user's role
        $query = "
            SELECT
                ra.appointment_id,
                ra.date_time,
                ra.status,
                ra.total_price,
                
                c.user_id AS customer_id,
                cu.first_name AS customer_first_name,
                cu.last_name AS customer_last_name,
                cu.email AS customer_email,
                
                e.user_id AS employee_id,
                eu.first_name AS employee_first_name,
                eu.last_name AS employee_last_name,
                eu.email AS employee_email,
                
                sm.method_id,
                sm.method_name

            FROM repair_appointment ra
            LEFT JOIN customer c ON ra.customer_id = c.user_id
            LEFT JOIN app_user cu ON c.user_id = cu.user_id
            
            LEFT JOIN employee e ON ra.employee_id = e.user_id
            LEFT JOIN app_user eu ON e.user_id = eu.user_id
            
            LEFT JOIN service_method sm ON ra.method_id = sm.method_id
        ";

        $conditions = [];
        $params = [];

        // Apply filtering logic based on user type and role
        if ($selectedUser['user_type'] === 'employee') {
            if ($selectedUser['role'] === 'admin') {
                // Admin sees all repair appointments, no filter needed
            } else {
                // Technicians should only see appointments assigned to them
                $conditions[] = "ra.employee_id = ?";
                $params[] = $selectedUser['user_id'];
            }
        } elseif ($selectedUser['user_type'] === 'customer') {
            // Customers should only see their own appointments
            $conditions[] = "ra.customer_id = ?";
            $params[] = $selectedUser['user_id'];
        }

        // Append WHERE conditions if applicable
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        // Ensure sorting remains intact
        $query .= " ORDER BY ra.date_time DESC";

        // Execute the query with filtering conditions
        $repair_orders = DB::select($query, $params);

        return view('admin.repair-orders.index', ['repair_orders' => $repair_orders]);
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
