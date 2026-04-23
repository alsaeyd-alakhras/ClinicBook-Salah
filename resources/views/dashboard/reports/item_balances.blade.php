<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8" />
    <title>كشف أرصدة المناطق</title>
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
                    <td colspan="3" style="border:0;">
                        <p>
                            <span>قسم المشاريع</span> /
                            <span>كشف أرصدة المناطق</span>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="{{ 4 + (count($items) *3) }}" align="center" style="color: #000;border:0;">
                        <h3>كشف أرصدة المناطق</h3>
                    </td>
                </tr>
                <tr style="background: #dddddd;">
                    <th rowspan="2">#</th>
                    <th rowspan="2">المؤسسة</th>
                    <th rowspan="2">نسبة التمويل</th>
                    <th rowspan="2">مبلغ التخصيص $</th>
                    @foreach ($items as $item)
                        <th colspan="3" align="center" style="background: #ffe699;">{{ $item }}</th>
                    @endforeach
                </tr>
                <tr style="background: #fef9f9de;">
                    @foreach ($items as $item)
                        <th>التخصيص</th>
                        <th>المنفذ</th>
                        <th style="color: red;">المتبقي</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php
                    $total_allocations = 0;
                @endphp
                @foreach ($brokers as $broker)
                    @php
                        $amounts_allocations = App\Models\Allocation::where('broker_name', $broker)->sum('amount');
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $broker }}</td>
                        <td>
                            @php
                                if($allocationsTotalArray['amounts_allocations'] == 0){
                                    $allocationsTotalArray['amounts_allocations'] = 1;
                                }
                            @endphp
                            {{ number_format(($amounts_allocations / $allocationsTotalArray['amounts_allocations'] * 100),2) }} %
                        </td>
                        <td>{{ number_format($amounts_allocations,0) }}</td>
                        @foreach ($items as $item)
                            @php
                                $quantity_allocations = App\Models\Allocation::where('broker_name', $broker)->where('item_name', $item)->sum('quantity');
                                $quantity_executives = App\Models\Executive::where('broker_name', $broker)->where('item_name', $item)->sum('quantity');
                            @endphp
                            <td>{{ number_format($quantity_allocations,0) }}</td>
                            <td>{{ number_format($quantity_executives,0) }}</td>
                            <td style="@if (($quantity_allocations - $quantity_executives) != 0) background: #c6efce; @endif">
                                {{ number_format($quantity_allocations - $quantity_executives,0) }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" align="right">الإجمالي</td>
                    <td>{{ number_format($allocationsTotalArray['amounts_allocations'],0) }}</td>
                    @foreach ($items as $item)
                        @php
                            $quantity_allocations = App\Models\Allocation::where('item_name', $item)->sum('quantity');
                            $quantity_executives = App\Models\Executive::where('item_name', $item)->sum('quantity');
                        @endphp
                        <td>{{ number_format($quantity_allocations,0) }}</td>
                        <td>{{ number_format($quantity_executives,0) }}</td>
                        <td  style="@if (($quantity_allocations - $quantity_executives) != 0) background: #c6efce; @endif">
                            {{ number_format($quantity_allocations - $quantity_executives,0) }}
                        </td>
                    @endforeach
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
