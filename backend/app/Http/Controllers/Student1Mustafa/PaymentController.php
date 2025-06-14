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
                u.city,
                c.preferred_contact_method,
                r.status AS appointment_status,
                r.total_price,
                p.payment_method, 
                p.amount, 
                p.payment_status
            FROM app_user u
            JOIN customer c ON u.user_id = c.user_id
            JOIN repair_appointment r ON c.user_id = r.customer_id
            JOIN payment p ON r.appointment_id = p.appointment_id
            WHERE r.status = 'completed'
            ORDER BY r.date_time DESC
        ");

        $cardStats = DB::selectOne("
            SELECT 
                COUNT(*) AS total,
                SUM(p.amount) AS total_collected
            FROM repair_appointment r
            JOIN payment p ON r.appointment_id = p.appointment_id
            WHERE r.status = 'completed'
            AND p.payment_status = 'paid'
            AND p.payment_method = 'card'
        ");

        $cashStats = DB::selectOne("
            SELECT 
                COUNT(*) AS total,
                SUM(p.amount) AS total_collected
            FROM repair_appointment r
            JOIN payment p ON r.appointment_id = p.appointment_id
            WHERE r.status = 'completed'
            AND p.payment_status = 'paid'
            AND p.payment_method = 'cash'
        ");

        $unpaidStats = DB::selectOne("
            SELECT COUNT(*) AS total
            FROM repair_appointment r
            LEFT JOIN payment p ON r.appointment_id = p.appointment_id
            WHERE r.status = 'completed'
            AND (p.payment_status IS NULL OR p.payment_status <> 'paid')
        ");

        return view('student1mustafa.SQL_Part.analytics_report', compact('results', 'cardStats', 'cashStats', 'unpaidStats'));
    }


    public function useCasePage(Request $request)
    {
        $customers = DB::table('app_user')
            ->join('customer', 'app_user.user_id', '=', 'customer.user_id')
            ->join('repair_appointment AS ra', 'ra.customer_id', '=', 'customer.user_id')
            ->leftJoin('payment AS p', 'ra.appointment_id', '=', 'p.appointment_id')
            ->where('ra.status', 'completed') 
            ->where('p.payment_status', 'unpaid')
            ->select('app_user.user_id', 'app_user.first_name', 'app_user.last_name')
            ->distinct() 
            ->get();



        $appointments = [];

        if ($request->filled('user_id')) {
        $appointments = DB::table('repair_appointment AS ra')
            ->leftJoin('payment AS p', 'ra.appointment_id', '=', 'p.appointment_id')
            ->where('ra.customer_id', $request->user_id)
            ->Where('p.payment_status', 'unpaid')
            ->Where('ra.status', 'completed')
            ->get();
         }

        return view('student1mustafa.SQL_Part.use_case', [
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

    $existingPayment = DB::table('payment')
        ->where('appointment_id', $request->appointment_id)
        ->first();

    $paymentStatus = ($request->payment_method === 'cash') ? 'unpaid' : 'paid';

    if ($existingPayment) {
        DB::table('payment')
            ->where('appointment_id', $request->appointment_id)
            ->update([
                'payment_status'   => $paymentStatus,
                'payment_method'   => $request->payment_method,
                'amount'           => $appointment->total_price,
                'payment_date_time' => now()
            ]);
    } else {
        DB::table('payment')->insert([
            'appointment_id'     => $appointment->appointment_id,
            'payment_number'     => 1, 
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

        return view('student1mustafa.SQL_Part.pay_form', compact('appointment'));
    }


    // ++++++++++++++++++++++++++++++++++++++++++ this part is implemented for the project logic not required for this
    public function pendingPaymentsPage()
    {
        $pendingPayments = DB::table('payment AS p')
            ->join('repair_appointment AS ra', 'p.appointment_id', '=', 'ra.appointment_id')
            ->join('app_user AS au', 'ra.customer_id', '=', 'au.user_id')
            ->where([
                ['p.payment_status', '=', 'unpaid'],
                ['p.payment_method', '=', 'cash']
            ])
            ->where('ra.status', 'completed')
            ->select('p.appointment_id', 'p.amount', 'p.payment_method', 'au.first_name', 'au.last_name')
            ->get();

        return view('student1mustafa.SQL_Part.pending_payments', compact('pendingPayments'));
    }

    public function confirmPayment(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:payment,appointment_id',
        ]);

        DB::table('payment')
            ->where('appointment_id', $request->appointment_id)
            ->update(['payment_status' => 'paid', 'payment_date_time' => now()]);

        return redirect()->route('mustafa.pendingPayments')->with('success', 'Payment confirmed successfully!');
    }


}
