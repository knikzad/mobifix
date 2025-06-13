<?php

namespace App\Http\Controllers\NoSQL;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use MongoDB\Client as MongoClient;

class MongoMigrationController extends Controller
{
    protected $mongo;

    public function __construct()
    {
        $this->mongo = (new MongoClient("mongodb://mobifix-mongo:27017"))->mobifix_nosql;
    }

    public function migrateAll()
    {
        $this->clearAll();

        $this->migrateUsers();
        $this->migrateDeviceData();
        $this->migrateRepairServices();
        $this->migrateAppointments();

        return response()->json(['message' => 'Full migration to MongoDB completed successfully.']);
    }

    protected function clearAll()
    {
        foreach ([
            'users', 'appointments', 'repair_service',
            'repair_service_appointment', 'device_model',
            'brand', 'device_type', 'device_type_brand'
        ] as $collection) {
            $this->mongo->$collection->drop();
        }
    }

    protected function migrateUsers()
    {
        $users = DB::table('app_user')->get();

        foreach ($users as $user) {
            $doc = [
                '_id' => $user->user_id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
            ];

            $customer = DB::table('customer')->where('user_id', $user->user_id)->first();
            $employee = DB::table('employee')->where('user_id', $user->user_id)->first();

            if ($customer) {
                $doc['customer'] = [
                    'preferred_contact_method' => $customer->preferred_contact_method,
                    'loyalty_points' => $customer->loyalty_points
                ];
            }

            if ($employee) {
                $doc['employee'] = [
                    'job_title' => $employee->job_title,
                    'shift' => $employee->shift,
                    'role' => $employee->role
                ];
            }

            $this->mongo->users->insertOne($doc);
        }
    }

    protected function migrateDeviceData()
    {
        // Brands
        $brands = DB::table('brand')->get();
        foreach ($brands as $b) {
            $this->mongo->brand->insertOne([
                '_id' => $b->brand_id,
                'brand_name' => $b->brand_name,
                'country' => $b->country
            ]);
        }

        // Device Types
        $deviceTypes = DB::table('device_type')->get();
        foreach ($deviceTypes as $dt) {
            $this->mongo->device_type->insertOne([
                '_id' => $dt->device_type_id,
                'type_name' => $dt->type_name
            ]);
        }

        // Device Type <-> Brand (Intermediate)
        $dtb = DB::table('device_type_brand')->get();
        foreach ($dtb as $row) {
            $this->mongo->device_type_brand->insertOne([
                'device_type_id' => $row->device_type_id,
                'brand_id' => $row->brand_id
            ]);
        }

        // Device Models
        $models = DB::table('device_model')->get();
        foreach ($models as $m) {
            $this->mongo->device_model->insertOne([
                '_id' => $m->model_id,
                'model_name' => $m->model_name,
                'release_year' => $m->release_year,
                'brand_id' => $m->brand_id
            ]);
        }
    }

    protected function migrateRepairServices()
    {
        $services = DB::table('repair_service')->get();

        foreach ($services as $s) {
            $this->mongo->repair_service->insertOne([
                '_id' => $s->service_id,
                'service_name' => $s->service_name,
                'price' => $s->price,
                'time_taken' => $s->time_taken,
                'model_id' => $s->model_id
            ]);
        }
    }

    protected function migrateAppointments()
    {
        $appointments = DB::table('repair_appointment')->get();

        foreach ($appointments as $appt) {
            $method = DB::table('service_method')->where('method_id', $appt->method_id)->first();
            $payment = DB::table('payment')
                ->where('appointment_id', $appt->appointment_id)
                ->orderByDesc('payment_number')->first();

            $doc = [
                '_id' => $appt->appointment_id,
                'date_time' => $appt->date_time,
                'status' => $appt->status,
                'total_price' => $appt->total_price,
                'customer_id' => $appt->customer_id,
                'employee_id' => $appt->employee_id,
                'service_method' => $method ? [
                    'method_id' => $method->method_id,
                    'method_name' => $method->method_name,
                    'estimated_time' => $method->estimated_time,
                    'cost' => $method->cost,
                    'note' => $method->note
                ] : null,
                'payment' => $payment ? [
                    'payment_number' => $payment->payment_number,
                    'amount' => $payment->amount,
                    'payment_method' => $payment->payment_method,
                    'payment_status' => $payment->payment_status,
                    'payment_date_time' => $payment->payment_date_time
                ] : null
            ];

            $this->mongo->appointments->insertOne($doc);

            // Services (Intermediate)
            $services = DB::table('repair_service_appointment')
                ->where('appointment_id', $appt->appointment_id)
                ->get();

            foreach ($services as $s) {
                $this->mongo->repair_service_appointment->insertOne([
                    'appointment_id' => $s->appointment_id,
                    'service_id' => $s->service_id
                ]);
            }
        }
    }
}
