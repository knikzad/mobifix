<?php

namespace App\Http\Controllers\NoSQL\Student1Mustafa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use MongoDB\Client as MongoClient;

class MustafaNoSQLController extends Controller
{
    protected $mongo;

    public function __construct()
    {
        $this->mongo = (new MongoClient("mongodb://mobifix-mongo:27017"))->mobifix_nosql;
    }

    public function analyticsReport()
    {
        $appointments = $this->mongo->appointments->find(
            ['status' => 'completed'],
            ['sort' => ['date_time' => -1]]
        );

        $results = [];
        $cardStats = ['total' => 0, 'total_collected' => 0];
        $cashStats = ['total' => 0, 'total_collected' => 0];
        $unpaidStats = ['total' => 0];

        foreach ($appointments as $appt) {
            $user = $this->mongo->users->findOne(['_id' => $appt['customer_id'] ?? null]);
            $payment = $appt['payment'] ?? null;

            if (!$payment || ($payment['payment_status'] ?? '') !== 'paid') {
                $unpaidStats['total']++;
            } else {
                $method = $payment['payment_method'] ?? '';
                $amount = $payment['amount'] ?? 0;

                if ($method === 'card') {
                    $cardStats['total']++;
                    $cardStats['total_collected'] += $amount;
                } elseif ($method === 'cash') {
                    $cashStats['total']++;
                    $cashStats['total_collected'] += $amount;
                }
            }

            $results[] = [
                'name' => ($user['first_name'] ?? '-') . ' ' . ($user['last_name'] ?? '-'),
                'contact' => $user['customer']['preferred_contact_method'] ?? '-',
                'method' => $payment['payment_method'] ?? '-',
                'amount' => $payment['amount'] ?? 0,
                'status' => $payment['payment_status'] ?? '-',
            ];
        }

        return view('student1mustafa.NoSQL_Part.analytics_nosql', compact('results', 'cardStats', 'cashStats', 'unpaidStats'));
    }


    public function useCasePage(Request $request)
    {
        $unpaidAppointments = $this->mongo->appointments->find([
            'status' => 'completed',
            '$or' => [
                ['payment' => null],
                ['payment.payment_status' => ['$ne' => 'paid']]
            ]
        ]);

        $customerIds = [];
        $appointmentsByCustomer = [];

        foreach ($unpaidAppointments as $doc) {
            $cid = $doc['customer_id'];
            $customerIds[$cid] = true;
            $appointmentsByCustomer[$cid][] = $doc;
        }

        $customers = $this->mongo->users->find([
            '_id' => ['$in' => array_keys($customerIds)]
        ]);

        $selectedUserId = $request->query('user_id');
        $appointments = $selectedUserId && isset($appointmentsByCustomer[$selectedUserId])
            ? $appointmentsByCustomer[$selectedUserId]
            : [];

        return view('student1mustafa.NoSQL_Part.use_case_nosql', [
            'customers' => $customers,
            'appointments' => $appointments,
            'selectedUserId' => $selectedUserId
        ]);
    }

    public function showPaymentForm($appointment_id)
    {
        $appointment = $this->mongo->appointments->findOne(['_id' => $appointment_id]);

        if (!$appointment) {
            return redirect()->route('mustafa.nosql.use_case')->with('error', 'Appointment not found.');
        }

        return view('student1mustafa.NoSQL_Part.pay_form_nosql', compact('appointment'));
    }


    public function processPayment(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required',
            'payment_method' => 'required|in:card,cash'
        ]);

        $status = $request->payment_method === 'cash' ? 'unpaid' : 'paid';

        $this->mongo->appointments->updateOne(
            ['_id' => $request->appointment_id],
            ['$set' => [
                'payment.payment_status' => $status,
                'payment.payment_method' => strtolower($request->payment_method),
                'payment.amount' => (float) ($this->mongo->appointments->findOne(['_id' => $request->appointment_id])['total_price'] ?? 0),
                'payment.payment_date_time' => now()
            ]]
        );

        return redirect()->route('mustafa.nosql.use_case')->with('success', 'Payment processed successfully.');
    }

    // ++++++++++++++++++++++++++++++++++++++++++ this part is implemented for the project logic not required for this
    public function pendingPaymentsPage()
    {
        $pendingPayments = $this->mongo->appointments->find([
            'status' => 'completed',
            'payment.payment_status' => 'unpaid',
            'payment.payment_method' => 'cash'
        ]);

        $results = [];

        foreach ($pendingPayments as $doc) {
            $user = $this->mongo->users->findOne(['_id' => $doc['customer_id']]);

            $results[] = [
                'appointment_id' => $doc['_id'],
                'amount' => $doc['payment']['amount'] ?? 0,
                'payment_method' => $doc['payment']['payment_method'] ?? '-',
                'first_name' => $user['first_name'] ?? '-',
                'last_name' => $user['last_name'] ?? '-',
            ];
        }

        return view('student1mustafa.NoSQL_Part.pending_payments_nosql', [
            'pendingPayments' => $results
        ]);
    }

    public function confirmPayment(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required'
        ]);

        $this->mongo->appointments->updateOne(
            ['_id' => $request->appointment_id],
            ['$set' => [
                'payment.payment_status' => 'paid',
                'payment.payment_date_time' => now()
            ]]
        );

        return redirect()->route('mustafa.nosql.pendingPayments')->with('success', 'Payment confirmed successfully.');
    }

}
