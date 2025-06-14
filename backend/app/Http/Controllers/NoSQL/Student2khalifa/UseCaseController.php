<?php

namespace App\Http\Controllers\NoSQL\Student2khalifa;

use App\Http\Controllers\Controller;
use MongoDB\Client as MongoClient;
use MongoDB\BSON\UTCDateTime;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UseCaseController extends Controller
{
    private $mongo;

    public function __construct()
    {
        $this->mongo = (new MongoClient("mongodb://mobifix-mongo:27017"))->mobifix_nosql;
    }


    public function index()
    {
        $pipeline = [
            // Filter users who are also customers
            [
                '$match' => [
                    'user_type' => 'customer' // Ensure only customers are selected
                ]
            ],
            // Project only required fields
            [
                '$project' => [
                    '_id' => 1,
                    'first_name' => 1,
                    'last_name' => 1
                ]
            ]
        ];

        // Execute aggregation query
        $users = $this->mongo->users->aggregate($pipeline)->toArray();

        // Pass data to the view
        return view('student2khalifa.NoSQL.customer.user_selection', ['users' => $users]);
    }

    public function selectUser(Request $request)
    {
        // Store user_id in session
        session([
            'selected_user' => [
                'user_id' => $request->user_id,
            ]
        ]);

        // Redirect toward usecase
        return redirect()->route('nosql.use_case.appointment.create');
    }

    // list the repair service, service method, and user info for creating appointment
    public function createAppointment()
    {
        $selectedUser = session('selected_user');

        $repairServices = $this->mongo->repair_service->find([], ['sort' => ['service_name' => 1]])->toArray();

        $serviceMethods = $this->mongo->service_method->find([], ['sort' => ['method_name' => 1]])->toArray();

        // Fetch user data including embedded address
        $customerDetails = $this->mongo->users->findOne(
            ['_id' => $selectedUser['user_id']],
            [
                'projection' => [
                    '_id' => 1,
                    'first_name' => 1,
                    'last_name' => 1,
                    'email' => 1,
                    'phone' => 1,
                    'address.street_name' => 1,
                    'address.house_number' => 1,
                    'address.city' => 1,
                    'address.postal_code' => 1
                ]
            ]
        );

        return view('student2khalifa.NoSQL.customer.create_appointment', [
            'repairServices' => $repairServices,
            'serviceMethods' => $serviceMethods,
            'customerDetails' => $customerDetails
        ]);
    }


    public function storeAppointment(Request $request)
    {
        $request->validate([
            'service_ids' => 'required|array',
            'method_id' => 'required|string',
            'appointment_date' => 'required|date',
            'time_slot' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'street_name' => 'nullable|string',
            'house_number' => 'nullable|string',
            'city' => 'nullable|string',
            'postal_code' => 'nullable|string'
        ]);

        $customerId = session('selected_user')['user_id'];

        // Fetch service method cost
        $serviceMethod = $this->mongo->service_method->findOne(['_id' => $request->method_id]);
        $serviceMethodCost = $serviceMethod ? (float) $serviceMethod['cost'] : 0;

        // Fetch repair service prices
        $totalServiceCost = 0;
        foreach ($request->service_ids as $serviceId) {
            $service = $this->mongo->repair_service->findOne(['_id' => $serviceId]);
            if ($service) {
                $totalServiceCost += (float) $service['price'];
            }
        }

        // Calculate total price
        $totalPrice = $totalServiceCost + $serviceMethodCost;

        // Update customer contact info
        $this->mongo->users->updateOne(
            ['_id' => $customerId],
            ['$set' => [
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => [
                    'street_name' => $request->street_name,
                    'house_number' => $request->house_number,
                    'city' => $request->city,
                    'postal_code' => $request->postal_code
                ]
            ]]
        );

        $dateTime = Carbon::parse($request->appointment_date . ' ' . $request->time_slot);

        $appointmentDoc = [
            'customer_id' => $customerId,
            'method_id' => $request->method_id,
            'date_time' => new UTCDateTime($dateTime->getTimestamp() * 1000),
            'status' => 'booked',
            'total_price' => $totalPrice,
        ];

        $insertedAppointment = $this->mongo->appointments->insertOne($appointmentDoc);
        $appointmentId = $insertedAppointment->getInsertedId();

        // Insert linked repair services
        $serviceAppointmentDocs = [];
        foreach ($request->service_ids as $serviceId) {
            $serviceAppointmentDocs[] = [
                'appointment_id' => $appointmentId,
                'service_id' => $serviceId,
            ];
        }
        if (!empty($serviceAppointmentDocs)) {
            $this->mongo->repair_service_appointment->insertMany($serviceAppointmentDocs);
        }

        return redirect()->route('nosql.use_case.appointments')->with('success', 'Appointment booked successfully!');
    }


    public function listAppointments()
    {
        $selectedUser = session('selected_user');
        $pipeline = [
            [
                '$match' => ['customer_id' => $selectedUser['user_id']]
            ],
            [
                '$lookup' => [
                    'from' => 'service_method',
                    'localField' => 'method_id',
                    'foreignField' => '_id',
                    'as' => 'method_info'
                ]
            ],
            [
                '$unwind' => [
                    'path' => '$method_info',
                    'preserveNullAndEmptyArrays' => true
                ]
            ],
            [
                '$lookup' => [
                    'from' => 'repair_service_appointment',
                    'localField' => '_id',
                    'foreignField' => 'appointment_id',
                    'as' => 'service_links'
                ]
            ],
            [
                '$unwind' => [
                    'path' => '$service_links',
                    'preserveNullAndEmptyArrays' => true
                ]
            ],
            [
                '$lookup' => [
                    'from' => 'repair_service',
                    'localField' => 'service_links.service_id',
                    'foreignField' => '_id',
                    'as' => 'service_info'
                ]
            ],
            [
                '$unwind' => [
                    'path' => '$service_info',
                    'preserveNullAndEmptyArrays' => true
                ]
            ],
            [
                '$group' => [
                    '_id' => '$_id',
                    'date_time' => ['$first' => '$date_time'],
                    'status' => ['$first' => '$status'],
                    'total_price' => ['$first' => '$total_price'],
                    'method_name' => ['$first' => '$method_info.method_name'],
                    'services' => ['$addToSet' => '$service_info.service_name']
                ]
            ],
            [
                '$project' => [
                    'appointment_id' => '$_id',
                    'date_time' => 1,
                    'status' => 1,
                    'total_price' => 1,
                    'method_name' => 1,
                    'services' => [
                        '$reduce' => [
                            'input' => '$services',
                            'initialValue' => '',
                            'in' => [
                                '$concat' => [
                                    [
                                        '$cond' => [
                                            'if' => ['$eq' => ['$$value', '']],
                                            'then' => '',
                                            'else' => [
                                                '$concat' => ['$$value', ', ']
                                            ]
                                        ]
                                    ],
                                    '$$this'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                '$sort' => ['date_time' => -1]
            ]
        ];

        $appointments = $this->mongo->appointments->aggregate($pipeline)->toArray();
        return view('student2khalifa.NoSQL.customer.appointments', ['appointments' => $appointments]);
    }

    public function analyticsReport()
    {
        $oneMonthAgo = new UTCDateTime(Carbon::now()->subMonth()->getTimestamp() * 1000);

        $pipeline = [
            [
                '$match' => [
                    'date_time' => ['$gte' => $oneMonthAgo]
                ]
            ],
            [
                '$lookup' => [
                    'from' => 'service_method',
                    'localField' => 'method_id',
                    'foreignField' => '_id',
                    'as' => 'service_method_info'
                ]
            ],
            [
                '$unwind' => '$service_method_info'
            ],
            [
                '$group' => [
                    '_id' => '$service_method_info.method_name',
                    'total_appointments' => ['$sum' => 1],
                    'avg_repair_price' => ['$avg' => '$total_price'],
                    'total_revenue' => ['$sum' => '$total_price'],
                    'unique_customers_set' => ['$addToSet' => '$customer_id']
                ]
            ],
            [
                '$project' => [
                    'method_name' => '$_id',
                    'total_appointments' => 1,
                    'avg_repair_price' => ['$round' => ['$avg_repair_price', 2]],
                    'total_revenue' => ['$round' => ['$total_revenue', 2]],
                    'unique_customers' => ['$size' => '$unique_customers_set']
                ]
            ],
            [
                '$sort' => ['total_revenue' => -1]
            ]
        ];

        $report = $this->mongo->appointments->aggregate($pipeline)->toArray();

        return view('student2khalifa.NoSQL.admin.analytics_report', ['stats' => $report]);
    }

}
