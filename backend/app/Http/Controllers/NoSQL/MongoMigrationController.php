<?php

namespace App\Http\Controllers\NoSQL;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use MongoDB\Client as MongoClient;
use MongoDB\BSON\UTCDateTime;

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
        
        $this->ensureAppointmentIndexes();
        
        return redirect()->route('admin.home')->with('success', 'Full migration to MongoDB completed successfully.');
    }


    protected function clearAll()
    {
        foreach ([
            'users', 'appointments', 'repair_service', 'service_method',
            'device_model', 'brand', 'device_type',
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
                'password' => $user->password,
                'salt' => $user->salt,
                'user_type' => $user->user_type,
                'status' => $user->status,
                'address' => [
                    'street_name' => $user->street_name,
                    'house_number' => $user->house_number,
                    'city' => $user->city,
                    'postal_code' => $user->postal_code,
                ],
            ];

            $customer = DB::table('customer')->where('user_id', $user->user_id)->first();
            $employee = DB::table('employee')->where('user_id', $user->user_id)->first();

            if ($customer) {
                $doc['customer'] = [
                    'preferred_contact_method' => $customer->preferred_contact_method,
                    'loyalty_points' => (int) $customer->loyalty_points,
                ];
            }

            if ($employee) {
                $doc['employee'] = [
                    'job_title' => $employee->job_title,
                    'shift' => $employee->shift,
                    'role' => $employee->role,
                    'salary' => (float) $employee->salary,
                    'hire_date' => new UTCDateTime(strtotime($employee->hire_date) * 1000),
                ];
            }

            $this->mongo->users->insertOne($doc);
        }
    }

    protected function migrateDeviceData()
    {
        // Step 1: Migrate brands first (separately)
        $brands = DB::table('brand')->get();
        foreach ($brands as $b) {
            $this->mongo->brand->insertOne([
                '_id' => $b->brand_id,
                'brand_name' => $b->brand_name,
                'country' => $b->country,
                'founded_year' => $b->founded_year !== null ? (int) $b->founded_year : null,
            ]);
        }

        // Step 2: Fetch all device_type_brand mappings grouped by device_type_id
        $dtbRows = DB::table('device_type_brand')->get();

        $deviceTypeToBrandIds = [];
        $brandIdToDeviceTypeId = [];
        foreach ($dtbRows as $row) {
            $deviceTypeToBrandIds[$row->device_type_id][] = $row->brand_id;
            // For quick lookup device_type_id by brand_id
            $brandIdToDeviceTypeId[$row->brand_id] = $row->device_type_id;
        }

        // Step 3: Migrate device types with embedded brand_ids
        $deviceTypes = DB::table('device_type')->get();
        foreach ($deviceTypes as $dt) {
            $brandIds = $deviceTypeToBrandIds[$dt->device_type_id] ?? [];

            $this->mongo->device_type->insertOne([
                '_id' => $dt->device_type_id,
                'type_name' => $dt->type_name,
                'description' => $dt->description,
                'brand_ids' => $brandIds,  // Embed brand IDs here
            ]);
        }

        // Step 4: Migrate device models, add device_type_id by looking up brand_id
        $models = DB::table('device_model')->get();
        foreach ($models as $m) {
            $device_type_id = $brandIdToDeviceTypeId[$m->brand_id] ?? null;

            $this->mongo->device_model->insertOne([
                '_id' => $m->model_id,
                'model_name' => $m->model_name,
                'release_year' => $m->release_year !== null ? (int) $m->release_year : null,
                'brand_id' => $m->brand_id,
                'device_type_id' => $device_type_id,  // Added dynamically
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
                'description' => $s->description,
                'price' => (float) $s->price,
                'time_taken' => (int) $s->time_taken,
                'model_id' => $s->model_id
            ]);
        }
    }

    protected function migrateAppointments()
    {
        // Step 1: Migrate all service methods
        $sqlServiceMethods = DB::table('service_method')->get();

        foreach ($sqlServiceMethods as $method) {
            $this->mongo->service_method->insertOne([
                '_id' => $method->method_id,
                'method_name' => $method->method_name,
                'estimated_time' => (int) $method->estimated_time,
                'cost' => (float) $method->cost,
                'note' => $method->note
            ]);
        }

        // Step 2: Migrate appointments with embedded service_ids
        $appointments = DB::table('repair_appointment')->get();

        foreach ($appointments as $appt) {
            $payment = DB::table('payment')
                ->where('appointment_id', $appt->appointment_id)
                ->orderByDesc('payment_number')
                ->first();

            // Fetch related service IDs (many-to-many)
            $serviceIds = DB::table('repair_service_appointment')
                ->where('appointment_id', $appt->appointment_id)
                ->pluck('service_id')
                ->toArray();

            $doc = [
                '_id' => $appt->appointment_id,
                'date_time' => new UTCDateTime(strtotime($appt->date_time) * 1000),
                'status' => $appt->status,
                'total_price' => (float) $appt->total_price,
                'customer_id' => $appt->customer_id,
                'employee_id' => $appt->employee_id,
                'method_id' => $appt->method_id, // Reference to service_method
                'service_ids' => $serviceIds,     // Embed service references
                'payment' => $payment ? [
                    'payment_number' => $payment->payment_number,
                    'amount' => (float) $payment->amount,
                    'payment_method' => $payment->payment_method,
                    'payment_status' => $payment->payment_status,
                    'payment_date_time' => new UTCDateTime(strtotime($payment->payment_date_time) * 1000)
                ] : null
            ];

            $this->mongo->appointments->insertOne($doc);
        }
    }

    protected function ensureAppointmentIndexes()
    {
        // Index for faster analytics filtering by date_time
        $this->mongo->appointments->createIndex(['date_time' => 1]);
        // Indexes for faster filtering or joining on customer_id and employee_id
        $this->mongo->appointments->createIndex(['customer_id' => 1]);
        $this->mongo->appointments->createIndex(['employee_id' => 1]);
        // Optional: Index for faster lookup by service method (if queried often)
        $this->mongo->appointments->createIndex(['method_id' => 1]);
        // New index for filtering by status
        $this->mongo->appointments->createIndex(['status' => 1]);
        // New compound index for payment status and method
        $this->mongo->appointments->createIndex(['payment.payment_status' => 1, 'payment.payment_method' => 1]);

        $this->mongo->users->createIndex(['user_type' => 1]);
    }



}
