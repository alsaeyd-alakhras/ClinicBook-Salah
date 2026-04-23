<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8" />
    <title>كشف أرصدة المؤسسات الداعمة</title>
    <style>
        body {
            font-family: 'XBRiyaz', sans-serif;
        }

        @page {
            header: page-header;
            footer: page-footer;
        }
    </style>
    <style>
        table.blueTable {
            width: 100%;
            text-align: right;
            border-collapse: collapse;
        }

        table.blueTable td,
        table.blueTable th {
            border: 1px solid #AAAAAA;
            padding: 5px 9px;
            white-space: nowrap;
        }

        table.blueTable tbody td {
            font-size: 13px;
            color: #000000;
        }

        table.blueTable tbody tr:nth-child(even) {
            background: #F5F5F5;
        }

        table.blueTable thead {
            background: #b8b8b8;
            background: -moz-linear-gradient(top, #dedede 0%, #d7d7d7 66%, #D3D3D3 100%);
            background: -webkit-linear-gradient(top, #dedede 0%, #d7d7d7 66%, #D3D3D3 100%);
            background: linear-gradient(to bottom, #dedede 0%, #d7d7d7 66%, #D3D3D3 100%);
            border-bottom: 2px solid #444444;
        }

        table.blueTable thead th {
            font-size: 18px;
            font-weight: bold;
            text-align: right;
        }

        table.blueTable tfoot {
            font-size: 14px;
            font-weight: bold;
            color: #FFFFFF;
            background: #EEEEEE;
            background: -moz-linear-gradient(top, #f2f2f2 0%, #efefef 66%, #EEEEEE 100%);
            background: -webkit-linear-gradient(top, #f2f2f2 0%, #efefef 66%, #EEEEEE 100%);
            background: linear-gradient(to bottom, #f2f2f2 0%, #efefef 66%, #EEEEEE 100%);
            border-top: 2px solid #444444;
        }

        table.blueTable tfoot td {
            font-size: 14px;
        }

        table.blueTable tfoot .links {
            text-align: right;
        }

        table.blueTable tfoot .links a {
            display: inline-block;
            background: #1C6EA4;
            color: #FFFFFF;
            padding: 2px 8px;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <htmlpageheader name="page-header">

    </htmlpageheader>

    <div lang="ar">
        <table class="blueTable">
            <thead>
                <tr>
                    <td colspan="7" style="border:0;">
                        <p>
                            <span>قسم المشاريع</span> /
                            <span>كشف أرصدة المؤسسات الداعمة</span>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" align="center" style="color: #000;border:0;">
                        <h1>كشف أرصدة المؤسسات الداعمة </h1>
                    </td>
                </tr>
                <tr style="background: #dddddd;">
                    <th>#</th>
                    <th>المؤسسة</th>
                    <th>نسبة التمويل</th>
                    <th>الرصيد السابق</th>
                    <th>مبلغ التخصيص</th>
                    <th>القبض بالدولار</th>
                    <th>الرصيد بالدولار</th>
                    <th>نسبة التحصيل</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($brokers as $broker)
                    @php
                        $allocation = App\Models\Allocation::where('broker_name', $broker);

                        if($month != '1970-01'){
                            $allocation_sub = App\Models\Allocation::where('broker_name', $broker)
                                            ->where("date_allocation","<",Carbon\Carbon::parse($month))
                                            ->sum('amount');
                        }else{
                            $allocation_sub = 0;
                        }

                        $allocation = $allocation->where("date_allocation",">=",Carbon\Carbon::parse($month)->format('Y-m-d'));

                        if($to_month != null){
                            $to_month = Carbon\Carbon::parse($to_month)->addMonth()->format('Y-m-d');
                            $allocation = $allocation->where("date_allocation","<",$to_month);
                        }

                        $allocation = $allocation->get();
                        $amount = $allocation->sum('amount');
                        $amount_received = $allocation->sum('amount_received');

                        $allocationsTotalReceived = App\Models\Allocation::where('broker_name', $broker)->sum('amount_received');
                        //  لحل مشكلة القسمة
                        $totalAmount = ($allocationsTotal['amount'] == 0 ? 1 : $allocationsTotal['amount']); ;
                    @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $broker }}</td>
                            <td>{{ number_format($amount / $totalAmount,5) * 100 }} %</td>
                            <td>{{ number_format($allocation_sub,2) }}</td>
                            <td>{{ number_format($amount,2) }}</td>
                            <td>{{ number_format($amount_received,2) }}</td>
                            <td>
                                {{ number_format(($allocation_sub + $amount) - $allocationsTotalReceived,2) }}
                            </td>
                            <td>
                                {{ number_format(($amount_received == 0 ? 1 : $amount_received) / ($allocation_sub + ($amount == 0 ? 1 : $amount)),5) * 100 }} %
                            </td>
                        </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" align="right">المجموع</td>
                    @php
                        $amount_sub = 0;
                        foreach ($brokers as $broker) {
                            if($month != '1970-01'){
                                $amount_sub += App\Models\Allocation::where('broker_name', $broker)
                                                ->where("date_allocation","<",Carbon\Carbon::parse($month))
                                                ->sum('amount');
                            }else{
                                $amount_sub = 0;
                            }
                        }
                    @endphp
                    <td>{{ number_format($amount_sub,2) }}</td>
                    <td>{{ number_format($allocationsTotal['amount'],2) }}</td>
                    <td>{{ number_format($allocationsTotal['amount_received'],2) }}</td>
                    <td>{{ number_format($allocationsTotal['amount'] - $allocationsTotal['amount_received'],2) }}</td>
                    <td> {{ number_format($allocationsTotal['amount_received'] / $allocationsTotal['amount'],5) * 100 }} %</td>
                </tr>

            </tfoot>
        </table>
        <htmlpagefooter name="page-footer">
            <table width="100%" style="vertical-align: bottom; color: #000000;  margin: 1em">
                <tr>
                    <td width="33%">{DATE j-m-Y}</td>
                    <td width="33%" align="center">{PAGENO}/{nbpg}</td>
                    @auth
                        <td width="33%" style="text-align: left;">{{ Auth::user()->name }}</td>
                    @else
                        <td width="33%" style="text-align: left;">اسم المستخدم</td>
                    @endauth
                </tr>
            </table>
        </htmlpagefooter>
    </div>


</body>

</html>
