<?php

namespace App\Exports;

use App\Helpers\Traits\DateRangeHelper;
use App\Helpers\Traits\DateTimeHelper;
use App\Models\subscription;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Attendance\AttendanceDetails;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;


class PlanSubscriptionExport implements FromArray, WithHeadings, ShouldAutoSize
{
    use Exportable, DateTimeHelper, DateRangeHelper;

    private   $subscriptions;
    protected   $index = 0;

    public function __construct(Collection $subscription)
    {
        $this->subscriptions = $subscription;
    }

    public function headings(): array
    {
        return [
            'STT',
            'Họ tên',
            'Email',
            'Số điện thoại ',
            'Sản phẩm',
            'Gói',
            'Ngày bán',
            'Người bán',
            'Ngày kết thúc',
            'Còn lại',
            'Giá Tiền',
            'Thanh toán',
            'Mã kích hoạt',
            'Tình trạng'
        ];
    }

    public function array(): array
    {
        return $this->subscriptions->map(function ($subscription) {
            return $this->makeSubscriptionRow($subscription);
        })->toArray();
    }

    public function makeSubscriptionRow($subscription): array
    {
        $invoice = $subscription->invoices[0];

        return [
            ++$this->index,
            $subscription->subscriber->last_name .' ' . $subscription->subscriber->first_name,
            $subscription->subscriber->email,
            $subscription->subscriber->phone,
            $subscription->product->name,
            $subscription->name,
            $subscription->starts_at,
            $invoice->user?->name,
            $subscription->ends_at,
            $subscription->remaing_day,
            $subscription->price,
            $invoice->bank?->name_bank . ' - '. $invoice->bank?->account_holder. ' - '. $invoice->bank?->account_number,
            $subscription->tag,
            $invoice->status
        ];

    }

}
