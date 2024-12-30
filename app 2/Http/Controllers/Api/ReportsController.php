<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceRequest;
use App\Models\Order;
use DB;
use Carbon\Carbon;
use Excel;
use App\Exports\ReportExport;
class ReportsController extends Controller 
{
    //
    public function index( Request $request , $id = null ){

        $fromDate           =   ( $request->has('from_date') ) ? date('Y-m-d', strtotime($request->from_date)) : '';
        $toDate             =   ( $request->has('to_date') ) ? date('Y-m-d',strtotime($request->to_date)) : '';
        
        if( $fromDate !='' && $toDate !='' ){
            $_vendors   =   ServiceRequest::where('vendor_id',$id)
                                ->where('status',6)
                                ->whereBetween('created_at',[$fromDate,$toDate])
                                ->pluck('id')
                                ->toArray();
        }else{
            $_vendors   =   ServiceRequest::where('vendor_id',$id)
                            ->where('status',6)
                            //->whereBetween('created_at',[$fromDate,$toDate])
                            ->pluck('id')
                            ->toArray();
        }
        



        if( !empty($_vendors) ){
            $orders     =   Order::whereIn('service_request_id',$_vendors)
                                ->select(  DB::raw('count(id) as total_order'), DB::raw('SUM(order_amount) as total_sales'))
                                ->get()
                                ->toArray()[0];
        

        if( !empty($orders) ){
                $orders    = array_merge($orders,['services' => count($_vendors) ]);
                return response()->json([
                    'status'        => true,
                    'message'       => 'Success',
                    'data'          => $orders
                ],200);

            }
        }else{
            return response()->json([
                'status'        => false,
                'message'       => 'No data',
                'data'          => [
                                        "total_order"   => 0,
                                        "total_sales"   => 0,
                                        "services"      => 0,
                                    ]
            ],200);
        }
        //print_r( $_vendors);

    }

    public function generateReport( Request $request , $id = null ){

        $request->validate([
            'from_date'     =>  'required',
            'to_date'       =>  'required',
        ]);

        $fromDate           =   date('Y-m-d', strtotime($request->from_date));
        $toDate             =   date('Y-m-d',strtotime($request->to_date));

        $fileDate           =   date('Y-m-d-h:i:s');

        if( $fromDate !='' && $toDate !='' ){
            $excel =  Excel::store(new ReportExport($id ,$fromDate , $toDate), $fileDate .'_reports.xlsx', 'excel_report');
        }else{
            $excel =   Excel::store(new ReportExport($id), $fileDate .'_reports.xlsx', 'excel_report');
        }

        if( !empty($excel) ){
                return response()->json([
                    'status'        => true,
                    'message'       => 'Success',
                    'data'          =>  'uploads/reports/'.$fileDate .'_reports.xlsx',
                    'file_name'     => date('Y_m_d_h_m_s'),
                ],200);

            
        }else{
            return response()->json([
                'status'        => false,
                'message'       => 'No data',
                'data'          => ''
            ],200);
        }


    }
}
