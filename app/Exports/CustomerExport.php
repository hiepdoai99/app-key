<?php

namespace App\Exports;

use App\Helpers\Traits\DateRangeHelper;
use App\Helpers\Traits\DateTimeHelper;
use App\Models\subscription;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Attendance\AttendanceDetails;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;


class CustomerExport implements FromArray, WithHeadings, ShouldAutoSize
{
    use Exportable, DateTimeHelper, DateRangeHelper;


    private   $customer;

    public function __construct(Collection $customer)
    {
        $this->customers = $customer;
    }



    public function headings(): array
    {
        return [
            'Họ tên',
            'Email',
            'Số điện thoại ',
            'Ngày tạo',
            'Điểm',
        ];
    }

    public function array(): array
    {
        return $this->customers->map(function ($customer) {
            return $this->makeCustomerRow($customer);
        })->toArray();
    }

    public function makeCustomerRow($customer): array
    {
            return [
                $customer->name,
                $customer->email,
                $customer->phone,
                $customer->created_at,
                $customer->point,
            ];

    }

}
