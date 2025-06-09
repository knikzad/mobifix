<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class ImportController extends Controller
{
    public function import(Request $request)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $tables = [
            'repair_service_appointment','payment','repair_appointment',
            'service_method','repair_service','device_model',
            'device_type_brand','device_type','brand',
            'employee','customer','app_user'
        ];
        foreach ($tables as $t) {
            DB::table($t)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        DB::transaction(function () {

            $faker = Faker::create();
            

            // ==== 1. app_user, customer ====

            $users = [];
            $customers = [];
            $customerIds = [];

            for ($i = 0; $i < 100; $i++) {

                $salt = Str::random(32); // generate unique salt per user
                $plainPassword = $faker->password;
                $hashedPassword = hash('sha256', $plainPassword . $salt); // hash password+salt

                $uid = (string) Str::uuid();
                $users[] = [
                    'user_id' => $uid,
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                    'email' => $faker->unique()->safeEmail,
                    'phone' => $faker->unique()->phoneNumber,
                    'password' => $hashedPassword,
                    'salt' => $salt,
                    'user_type' => 'customer',
                    'status' => $faker->randomElement(['active','inactive']),
                    'street_name' => $faker->streetName,
                    'house_number' => $faker->buildingNumber,
                    'city' => $faker->city,
                    'postal_code' => $faker->postcode,
                    'referred_by' => null
                ];
                $customers[] = [
                    'user_id' => $uid,
                    'preferred_contact_method' => $faker->randomElement(['email','phone','sms']),
                    'loyalty_points' => $faker->numberBetween(0, 1000),
                ];
                $customerIds[] = $uid;
            }

            DB::table('app_user')->insert($users);
            DB::table('customer')->insert($customers);

            // ==== 2. employee ====

            $employees = [];
            $employeeDetails = [];
            $employeeIds = [];
            $roles = array_merge(['admin'], array_fill(0, 5, 'technician'));

            for ($i = 0; $i < 6; $i++) {
                $uid = (string) Str::uuid();
                $employees[] = [
                    'user_id' => $uid,
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                    'email' => $faker->unique()->safeEmail,
                    'phone' => $faker->unique()->phoneNumber,
                    'password' => $hashedPassword,
                    'salt' => Str::random(32),
                    'user_type' => 'employee',
                    'status' => $faker->randomElement(['active','inactive']),
                    'street_name' => $faker->streetName,
                    'house_number' => $faker->buildingNumber,
                    'city' => $faker->city,
                    'postal_code' => $faker->postcode,
                    'referred_by' => null
                ];
                $employeeDetails[] = [
                    'user_id' => $uid,
                    'job_title' => $faker->jobTitle,
                    'salary' => $faker->randomFloat(2, 30000, 80000),
                    'hire_date' => $faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
                    'shift' => $faker->randomElement(['morning','evening','night']),
                    'role' => $roles[$i],
                ];
                $employeeIds[] = $uid;
            }

            DB::table('app_user')->insert($employees);
            DB::table('employee')->insert($employeeDetails);

            // ==== 3. service_method (5 entries) ====

            $serviceMethods = [];
            $methodIds = [];

            for ($i = 0; $i < 5; $i++) {
                $mid = (string) Str::uuid();
                $serviceMethods[] = [
                    'method_id' => $mid,
                    'method_name' => $faker->word . ' Method',
                    'estimated_time' => $faker->numberBetween(30, 180),
                    'cost' => $faker->randomFloat(2, 20, 200),
                    'note' => $faker->sentence,
                ];
                $methodIds[] = $mid;
            }

            DB::table('service_method')->insert($serviceMethods);

            // ==== 4. brand (10 entries) ====

            $brands = [];
            $brandIds = [];

            for ($i = 0; $i < 10; $i++) {
                $bid = (string) Str::uuid();
                $brands[] = [
                    'brand_id' => $bid,
                    'brand_name' => $faker->company,
                    'country' => $faker->country,
                    'founded_year' => $faker->year,
                ];
                $brandIds[] = $bid;
            }

            DB::table('brand')->insert($brands);

            // ==== 5. device_type (8 entries) ====

            $deviceTypes = [];
            $dtIds = [];

            for ($i = 0; $i < 8; $i++) {
                $dt = (string) Str::uuid();
                $deviceTypes[] = [
                    'device_type_id' => $dt,
                    'type_name' => $faker->word . ' Device',
                    'description' => $faker->sentence,
                ];
                $dtIds[] = $dt;
            }

            DB::table('device_type')->insert($deviceTypes);

            // ==== 6. device_type_brand many-to-many ====

            $deviceTypeBrands = [];

            foreach ($dtIds as $dt) {
                $randomBrandIds = $faker->randomElements($brandIds, 3);
                foreach ($randomBrandIds as $bid) {
                    $deviceTypeBrands[] = [
                        'device_type_id' => $dt,
                        'brand_id' => $bid,
                    ];
                }
            }

            DB::table('device_type_brand')->insert($deviceTypeBrands);

            // ==== 7. device_model ====

            $deviceModels = [];
            $modelIds = [];

            foreach ($brandIds as $bid) {
                for ($i = 0; $i < 5; $i++) {
                    $mid = (string) Str::uuid();
                    $deviceModels[] = [
                        'model_id' => $mid,
                        'model_name' => $faker->word . ' ' . $faker->bothify('Model ##'),
                        'release_year' => $faker->year,
                        'brand_id' => $bid,
                    ];
                    $modelIds[] = $mid;
                }
            }

            DB::table('device_model')->insert($deviceModels);

            // ==== 8. repair_service ====

            $repairServices = [];
            $srvIds = [];

            foreach ($modelIds as $mid) {
                $sid = (string) Str::uuid();
                $repairServices[] = [
                    'service_id' => $sid,
                    'service_name' => $faker->word . ' Repair',
                    'description' => $faker->sentence,
                    'price' => $faker->randomFloat(2, 20, 300),
                    'time_taken' => $faker->numberBetween(15, 240),
                    'model_id' => $mid,
                ];
                $srvIds[] = $sid;
            }

            DB::table('repair_service')->insert($repairServices);

            // ==== 9. repair_appointment ====

            $appointments = [];

            foreach ($customerIds as $cid) {
                for ($j = 0; $j < 2; $j++) {
                    $aid = (string) Str::uuid();
                    $appointments[] = [
                        'appointment_id' => $aid,
                        'customer_id' => $cid,
                        'employee_id' => $faker->randomElement($employeeIds),
                        'method_id' => $faker->randomElement($methodIds),
                        'date_time' => $faker->dateTimeBetween('-30 days', '+30 days'),
                        'status' => $faker->randomElement(['pending', 'completed', 'cancelled']),
                        'total_price' => $faker->randomFloat(2, 20, 500),
                    ];
                }
            }

            DB::table('repair_appointment')->insert($appointments);

            // ==== 10. payment ====

            $payments = [];
            foreach ($appointments as $appt) {
                $pid = (string) Str::uuid();
                $payments[] = [
                    'appointment_id' => $appt['appointment_id'],
                    'payment_number' => $pid,
                    'amount' => $faker->randomFloat(2, 10, 500),
                    'payment_status' => $faker->randomElement(['paid', 'pending', 'failed']),
                    'payment_method' => $faker->randomElement(['cash','card','transfer']),
                    'payment_date_time' => $faker->dateTimeBetween('-10 days','now'),
                ];
            }

            DB::table('payment')->insert($payments);

            // ==== 11. repair_service_appointment ====

            $repairServiceAppointments = [];
            foreach ($appointments as $appt) {
                $randomServices = $faker->randomElements($srvIds, rand(1, 3));
                foreach ($randomServices as $sid) {
                    $repairServiceAppointments[] = [
                        'appointment_id' => $appt['appointment_id'],
                        'service_id' => $sid,
                    ];
                }
            }

            DB::table('repair_service_appointment')->insert($repairServiceAppointments);

        });

        return response()->json(['message' => 'Randomized data imported']);
    }
}
