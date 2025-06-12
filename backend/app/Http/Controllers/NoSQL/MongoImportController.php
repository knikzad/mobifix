<?php

namespace App\Http\Controllers\NoSQL;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use MongoDB\Client as MongoClient;

class MongoImportController extends Controller
{
    public function importAppointmentsToMongo()
    {
        $appointments = DB::table('repair_appointment')->get();
        $client = new MongoClient("mongodb://mobifix-mongo:27017");
        $collection = $client->mobifix_nosql->appointments;

        $count = 0;

        foreach ($appointments as $appt) {
            // Customer data
            $customer = DB::table('app_user')
                ->join('customer', 'app_user.user_id', '=', 'customer.user_id')
                ->where('app_user.user_id', $appt->customer_id)
                ->first();

            // Employee data
            $employee = DB::table('app_user')
                ->join('employee', 'app_user.user_id', '=', 'employee.user_id')
                ->where('app_user.user_id', $appt->employee_id)
                ->first();

            // Service Method
            $method = DB::table('service_method')
                ->where('method_id', $appt->method_id)
                ->first();

            // Payment data (only pick latest payment by number)
            $payment = DB::table('payment')
                ->where('appointment_id', $appt->appointment_id)
                ->orderByDesc('payment_number')
                ->first();

            // Services
            $services = DB::table('repair_service_appointment as rsa')
                ->join('repair_service as s', 's.service_id', '=', 'rsa.service_id')
                ->join('device_model as m', 'm.model_id', '=', 's.model_id')
                ->join('brand as b', 'b.brand_id', '=', 'm.brand_id')
                ->where('rsa.appointment_id', $appt->appointment_id)
                ->select(
                    's.service_id', 's.service_name', 's.price', 's.time_taken',
                    'm.model_id', 'm.model_name', 'm.release_year',
                    'b.brand_id', 'b.brand_name', 'b.country'
                )
                ->get();

            $serviceList = [];
            foreach ($services as $svc) {
                $serviceList[] = [
                    'service_id' => $svc->service_id,
                    'service_name' => $svc->service_name,
                    'price' => $svc->price,
                    'time_taken' => $svc->time_taken,
                    'device_model' => [
                        'model_id' => $svc->model_id,
                        'model_name' => $svc->model_name,
                        'release_year' => $svc->release_year,
                        'brand' => [
                            'brand_id' => $svc->brand_id,
                            'brand_name' => $svc->brand_name,
                            'country' => $svc->country
                        ]
                    ]
                ];
            }

            // Build final document
            $doc = [
                '_id' => $appt->appointment_id,
                'date_time' => $appt->date_time,
                'status' => $appt->status,
                'total_price' => $appt->total_price,
                'customer' => $customer ? [
                    'user_id' => $customer->user_id,
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'preferred_contact_method' => $customer->preferred_contact_method,
                    'loyalty_points' => $customer->loyalty_points
                ] : null,
                'employee' => $employee ? [
                    'user_id' => $employee->user_id,
                    'first_name' => $employee->first_name,
                    'last_name' => $employee->last_name,
                    'job_title' => $employee->job_title,
                    'shift' => $employee->shift,
                    'role' => $employee->role
                ] : null,
                'service_method' => $method ? [
                    'method_id' => $method->method_id,
                    'method_name' => $method->method_name,
                    'estimated_time' => $method->estimated_time,
                    'cost' => $method->cost,
                    'note' => $method->note
                ] : null,
                'services' => $serviceList,
                'payment' => $payment ? [
                    'payment_number' => $payment->payment_number,
                    'amount' => $payment->amount,
                    'payment_method' => $payment->payment_method,
                    'payment_status' => $payment->payment_status,
                    'payment_date_time' => $payment->payment_date_time
                ] : null
            ];

            $collection->insertOne($doc);
            $count++;
        }

        return " {$count} appointment(s) successfully migrated to MongoDB.";
    }


    public function clearAndMigrate()
    {
        $client = new MongoClient("mongodb://mobifix-mongo:27017");
        $collection = $client->mobifix_nosql->appointments;

        // Clear old data
        $collection->drop();

        // Reimport
        return $this->importAppointmentsToMongo();
    }


}
