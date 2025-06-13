<?php

namespace App\Http\Controllers\Student2Khalifa;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class UseCaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = DB::select("
            SELECT au.user_id, au.first_name, au.last_name, 
                au.user_type, e.role 
            FROM app_user au 
            LEFT JOIN employee e ON au.user_id = e.user_id 
            WHERE au.status = 'active' 
            ORDER BY au.user_type = 'employee' DESC, au.first_name ASC
        ");

        return view('student2khalifa.SQL.admin.user_selection', ['users' => $users]);
    }

    public function selectUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:app_user,user_id',
        ]);

        // Retrieve user details
        $user = DB::selectOne("
            SELECT au.user_id, au.first_name, au.last_name, 
                au.user_type, e.role 
            FROM app_user au 
            LEFT JOIN employee e ON au.user_id = e.user_id 
            WHERE au.user_id = ?
        ", [$request->user_id]);

        // Store user in session
        session([
            'selected_user' => [
                'user_id' => $user->user_id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'user_type' => $user->user_type,
                'role' => $user->role ?? null,
            ]
        ]);

        // Redirect based on user type
        if ($user->user_type === 'employee') {
            return redirect()->route('admin.repair-orders.index'); // Redirect employees to repair orders
        } else {
            return redirect()->route('use_case.appointment.create'); // Redirect customers to the appointment booking page
        }
    }
    // list the repair service, service method, and user info for creating appointment
    public function createAppointment()
    {
        // Ensure only customers access this page
        $selectedUser = session('selected_user');
        if (!$selectedUser || $selectedUser['user_type'] !== 'customer') {
            return redirect()->route('use_case.index')->withErrors('Only customers can book appointments.');
        }

        // Fetch available repair services, including price and time taken
        $repairServices = DB::select("
            SELECT service_id, service_name, description, price, time_taken
            FROM repair_service
            ORDER BY service_name ASC
        ");

        // Fetch service methods
        $serviceMethods = DB::select("
            SELECT method_id, method_name, estimated_time, cost, note
            FROM service_method
            ORDER BY method_name ASC
        ");

        // Fetch customer details for review
        $customerDetails = DB::selectOne("
            SELECT au.user_id, au.first_name, au.last_name, 
                au.email, au.phone, au.street_name, au.house_number,
                au.city, au.postal_code
            FROM app_user au
            WHERE au.user_id = ?
        ", [$selectedUser['user_id']]);

        return view('student2khalifa.SQL.customer.create_appointment', [
            'repairServices' => $repairServices,
            'serviceMethods' => $serviceMethods,
            'customerDetails' => $customerDetails
        ]);
    }




    public function storeAppointment(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'service_ids' => 'required|array',
            'method_id' => 'required|exists:service_method,method_id',
            'appointment_date' => 'required|date',
            'time_slot' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'street_name' => 'nullable|string',
            'house_number' => 'nullable|string',
            'city' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'total_price' => 'required|numeric'
        ]);

        $customerId = session('selected_user')['user_id'];

        // Update customer's contact details in `app_user`
        DB::update("
            UPDATE app_user SET email = ?, phone = ?, street_name = ?, 
                house_number = ?, city = ?, postal_code = ?
            WHERE user_id = ?
        ", [
            $request->email, $request->phone, $request->street_name,
            $request->house_number, $request->city, $request->postal_code, $customerId
        ]);

        // Generate appointment ID
        $appointmentId = DB::selectOne("SELECT UUID() AS id")->id;

        // Convert time format to MySQL-compatible format
        $formattedDateTime = date('Y-m-d H:i:s', strtotime($request->appointment_date . ' ' . $request->time_slot));

        DB::insert("
            INSERT INTO repair_appointment (appointment_id, customer_id, method_id, date_time, status, total_price)
            VALUES (?, ?, ?, ?, 'Pending', ?)
        ", [$appointmentId, $customerId, $request->method_id, $formattedDateTime, $request->total_price]);


        // Insert selected repair services into `repair_service_appointment`
        foreach ($request->service_ids as $serviceId) {
            DB::insert("
                INSERT INTO repair_service_appointment (service_id, appointment_id)
                VALUES (?, ?)
            ", [$serviceId, $appointmentId]);
        }

        return redirect()->route('customer.appointments')->with('success', 'Appointment booked successfully!');
    }

// =============================================
    public function listAppointments()
    {
        // Ensure only customers access this page
        $selectedUser = session('selected_user');
        if (!$selectedUser || $selectedUser['user_type'] !== 'customer') {
            return redirect()->route('use_case.index')->withErrors('Only customers can view appointments.');
        }

        // Fetch all appointments for the selected customer
        $appointments = DB::select("
            SELECT ra.appointment_id, ra.date_time, ra.status, ra.total_price, 
                sm.method_name, 
                GROUP_CONCAT(rs.service_name SEPARATOR ', ') AS services
            FROM repair_appointment ra
            LEFT JOIN service_method sm ON ra.method_id = sm.method_id
            LEFT JOIN repair_service_appointment rsa ON ra.appointment_id = rsa.appointment_id
            LEFT JOIN repair_service rs ON rsa.service_id = rs.service_id
            WHERE ra.customer_id = ?
            GROUP BY ra.appointment_id, ra.date_time, ra.status, ra.total_price, sm.method_name
            ORDER BY ra.date_time DESC
        ", [$selectedUser['user_id']]);

        return view('student2khalifa.SQL.customer.appointments', ['appointments' => $appointments]);
    }



    public function analyticsReport()
    {
        $report = DB::select("
            SELECT 
                sm.method_name,
                COUNT(DISTINCT ra.appointment_id) AS total_appointments,
                ROUND(AVG(ra.total_price), 2) AS avg_repair_price,
                ROUND(SUM(ra.total_price), 2) AS total_revenue,
                COUNT(DISTINCT ra.customer_id) AS unique_customers
            FROM 
                repair_appointment ra
            JOIN 
                service_method sm ON ra.method_id = sm.method_id
            WHERE 
                ra.date_time >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
            GROUP BY 
                sm.method_name
            ORDER BY 
                total_revenue DESC
        ");

        return view('student2khalifa.SQL.admin.analytics_report', ['stats' => $report]);
    }




}
