<?php

namespace App\Exports;

use App\Models\ServiceRequest;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

class ReportExport implements FromQuery
{
    use Exportable;

    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct(int $id , $from_year = null, $to_year = null )
    {
        $this->id           = $id;
        $this->from_year    = $from_year;
        $this->to_year      = $to_year;
    }

    public function query()
    {
        if(  $this->from_year =='' && $this->to_year =='' ){
            return ServiceRequest::query()->where('service_requests.vendor_id',$this->id)
                                ->select('orders.id as ID', 'services.service_title as serviceTitle', 'users.name as customerName', 'service_requests.status as RequestStatus',
                                        'orders.order_amount as Amount' , 'orders.paid_status as Paidstatus', 'orders.payment_through as PaymentBy',
                                        'orders.status as OrderStatus' ,'orders.vat_amount as VatAmount' ,'orders.b_mechanic as Bmechanic %' ,'orders.created_at as OrderDate')
                                ->leftJoin('services', 'service_requests.service_id', '=', 'services.id')
                                ->leftJoin('orders', 'service_requests.id', '=', 'orders.service_request_id')
                                ->leftJoin('users', 'service_requests.customer_id', '=', 'users.id');
        }else{
            return ServiceRequest::where('service_requests.vendor_id',$this->id)
                                    ->select('orders.id as ID', 'services.service_title as serviceTitle', 'users.name as customerName', 'service_requests.status as RequestStatus',
                                            'orders.order_amount as Amount' , 'orders.paid_status as Paidstatus', 'orders.payment_through as PaymentBy',
                                            'orders.status as OrderStatus' ,'orders.vat_amount as VatAmount' ,'orders.b_mechanic as Bmechanic %' ,'orders.created_at as OrderDate')
                                    ->leftJoin('services', 'service_requests.service_id', '=', 'services.id')
                                    ->leftJoin('orders', 'service_requests.id', '=', 'orders.service_request_id')
                                    ->leftJoin('users', 'service_requests.customer_id', '=', 'users.id')
                                    ->whereBetween('orders.created_at',[$this->from_year,$this->to_year]);
            
        }
        
    }
}
