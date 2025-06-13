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
        $mongo = (new MongoClient("mongodb://mobifix-mongo:27017"))->mobifix_nosql;

        // Get all completed + paid appointments
        $appointments = $mongo->appointments->find([
            'status' => 'completed',
            'payment.payment_status' => 'paid'
        ]);

        $results = [];

        foreach ($appointments as $appt) {
            $user = $mongo->users->findOne(['_id' => $appt['customer_id']]);

            $results[] = [
                'name' => ($user->first_name ?? '-') . ' ' . ($user->last_name ?? '-'),
                'contact' => $user->customer['preferred_contact_method'] ?? '-',
                'method' => $appt->payment['payment_method'] ?? '-',
                'amount' => $appt->payment['amount'] ?? 0,
                'status' => $appt->payment['payment_status'] ?? '-',
            ];
        }

        return view('student1mustafa.NoSQL_Part.analytics_nosql', ['results' => $results]);
    }


    public function useCasePage(Request $request)
    {
        $client = new MongoClient("mongodb://mobifix-mongo:27017");
        $mongo = $client->mobifix_nosql;

        $unpaid = $mongo->appointments->find([
            'status' => 'completed',
            '$or' => [
                ['payment' => null],
                ['payment.payment_status' => ['$ne' => 'paid']]
            ]
        ]);

        $customerIds = [];
        $appointmentsByCustomer = [];

        foreach ($unpaid as $doc) {
            $cid = $doc['customer_id'];
            $customerIds[$cid] = true;
            $appointmentsByCustomer[$cid][] = $doc;
        }

        $customers = $mongo->users->find([
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
        $mongo = (new MongoClient("mongodb://mobifix-mongo:27017"))->mobifix_nosql;
        $appointment = $mongo->appointments->findOne(['_id' => $appointment_id]);

        if (!$appointment) {
            return redirect()->route('mustafa.nosql.use_case')->with('error', 'Appointment not found.');
        }

        return view('student1mustafa.NoSQL_Part.pay_form_nosql', ['appointment' => $appointment]);
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required',
            'payment_method' => 'required|in:Card,Cash'
        ]);

        $mongo = (new MongoClient("mongodb://mobifix-mongo:27017"))->mobifix_nosql;
        $collection = $mongo->appointments;

        $status = $request->payment_method === 'Cash' ? 'pending' : 'paid';

        $collection->updateOne(
            ['_id' => $request->appointment_id],
            ['$set' => [
                'payment.payment_status' => $status,
                'payment.payment_method' => $request->payment_method,
                'payment.payment_date_time' => now()
            ]]
        );

        return redirect()->route('mustafa.nosql.use_case')->with('success', 'Payment processed successfully.');
    }

}
