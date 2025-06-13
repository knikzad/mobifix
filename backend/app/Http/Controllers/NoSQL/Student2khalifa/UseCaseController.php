<?php

namespace App\Http\Controllers\NoSQL\Student2khalifa;

use App\Http\Controllers\Controller;
use MongoDB\Client as MongoClient;
use MongoDB\BSON\UTCDateTime;
use Carbon\Carbon;

class UseCaseController extends Controller
{
    public function analyticsReport()
    {
        $mongo = (new MongoClient("mongodb://mobifix-mongo:27017"))->mobifix_nosql;

        // Format one month ago as a string (same format as in MongoDB)
        $oneMonthAgo = Carbon::now()->subMonth()->format('Y-m-d H:i:s');

        $pipeline = [
            [
                '$match' => [
                    'date_time' => ['$gte' => $oneMonthAgo],
                    'service_method.method_name' => ['$exists' => true]
                ]
            ],
            [
                '$group' => [
                    '_id' => '$service_method.method_name',
                    'total_appointments' => ['$sum' => 1],
                    'avg_repair_price' => ['$avg' => ['$toDouble' => '$total_price']],
                    'total_revenue' => ['$sum' => ['$toDouble' => '$total_price']],
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

        $report = $mongo->appointments->aggregate($pipeline)->toArray();


        return view('student2khalifa.NoSQL.admin.analytics_report', ['stats' => $report]);

    }
}
