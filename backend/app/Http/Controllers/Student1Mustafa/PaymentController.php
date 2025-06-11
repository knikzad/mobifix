<?php

namespace App\Http\Controllers\Student1Mustafa;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;


class PaymentController extends Controller
{

    public function analyticsReport()
    {
        $results = DB::select("
            SELECT 
                u.first_name, 
                u.last_name, 
                c.preferred_contact_method, 
                p.payment_method, 
                p.amount, 
                p.payment_status
            FROM app_user u
            JOIN customer c ON u.user_id = c.user_id
            JOIN repair_appointment r ON c.user_id = r.customer_id
            JOIN payment p ON r.appointment_id = p.appointment_id
            WHERE r.status = 'Completed' AND p.payment_status = 'paid'
            ORDER BY r.date_time DESC
        ");



        return view('student1mustafa.analytics_report', compact('results'));
    }



    // Show dropdown and appointments for selected user
    public function useCasePage(Request $request)
    {
        // Load customers
        $customers = DB::table('app_user')
            ->join('customer', 'app_user.user_id', '=', 'customer.user_id')
            ->join('repair_appointment AS ra', 'ra.customer_id', '=', 'customer.user_id')
            ->leftJoin('payment AS p', 'ra.appointment_id', '=', 'p.appointment_id')
            ->where('ra.status', 'completed') // Only completed appointments
            ->where(function ($query) {
                $query->whereNull('p.payment_status') // Include unpaid records (null)
                    ->orWhere('p.payment_status', '<>', 'Paid'); // Explicitly exclude paid
            })
            ->select('app_user.user_id', 'app_user.first_name', 'app_user.last_name')
            ->distinct() // Ensure unique customer records
            ->get();



        $appointments = [];

        if ($request->filled('user_id')) {
        $appointments = DB::table('repair_appointment AS ra')
            ->leftJoin('payment AS p', 'ra.appointment_id', '=', 'p.appointment_id')
            ->where('ra.customer_id', $request->user_id)
            ->Where('p.payment_status', '<>', 'Paid')
            ->Where('ra.status', 'completed')
            ->get();
         }

        return view('student1mustafa.use_case', [
            'customers' => $customers,
            'appointments' => $appointments,
            'selectedUserId' => $request->user_id,
        ]);
    }

public function processUserAppointmentPayment(Request $request)
{
    $request->validate([
        'appointment_id' => 'required|exists:repair_appointment,appointment_id',
        'payment_method' => 'required|string',
    ]);

    $appointment = DB::table('repair_appointment')
        ->where('appointment_id', $request->appointment_id)
        ->first();

    // Check if a payment already exists
    $existingPayment = DB::table('payment')
        ->where('appointment_id', $request->appointment_id)
        ->first();

    // Determine payment status based on method
    $paymentStatus = ($request->payment_method === 'Cash') ? 'Pending' : 'Paid';

    if ($existingPayment) {
        // Update payment status if payment already exists
        DB::table('payment')
            ->where('appointment_id', $request->appointment_id)
            ->update(['payment_status' => $paymentStatus,'amount' => $appointment->total_price, 'payment_date_time' => now()]);
    } else {
        // Create a new payment record if none exists
        DB::table('payment')->insert([
            'appointment_id'     => $appointment->appointment_id,
            'payment_number'     => 1, // First payment entry
            'amount'             => $appointment->total_price,
            'payment_status'     => $paymentStatus,
            'payment_method'     => $request->payment_method,
            'payment_date_time'  => now(),
        ]);
    }

    return redirect()->route('mustafa.use_case.page')->with('success', 'Payment processed successfully!');
}



    public function showPaymentForm($appointment_id)
    {
        $appointment = DB::table('repair_appointment')
            ->where('appointment_id', $appointment_id)
            ->first();

        if (!$appointment) {
            return redirect()->route('mustafa.use_case.page')->with('error', 'Appointment not found.');
        }

        return view('student1mustafa.pay_form', compact('appointment'));
    }



}
