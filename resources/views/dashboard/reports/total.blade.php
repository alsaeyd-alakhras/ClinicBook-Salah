<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8" />
    <title>كشف الإجمالي</title>
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
                    <td colspan="5" style="border:0;">
                        <p>
                            <span>قسم المشاريع</span> /
                            <span>كشف الإجمالي</span>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="5" align="center" style="color: #000;border:0;">
                        <h1>كشف الإجمالي</h1>
                    </td>
                </tr>
                <tr style="background: #dddddd;">
                    <th>#</th>
                    <th>الصنف</th>
                    <th>المخصص</th>
                    <th>المنفذ</th>
                    <th>المتبقي للتنفيذ</th>
                    <th>سعر ش</th>
                    <th>مبلغ التخصيص</th>
                    <th>المنفذ بالشيكل</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_allocations = 0;
                @endphp
                @foreach ($items as $item)
                    @php
                        $allocation = App\Models\Allocation::where('item_name', $item)->get();
                        $executive = App\Models\Executive::where('item_name', $item)->get();

                        $quantityAllocation = $allocation->sum('quantity');
                        $quantityExecutive = $executive->sum('quantity');

                        $total_ils = $executive->sum('total_ils');

                        $item_price  = App\Models\Item::where('name', $item)->first();
                        if($item_price != null){
                            $item_price = $item_price->price;
                        }else {
                            $item_price = $total_ils / $quantityExecutive;
                        }

                        $total_allocation = $quantityAllocation * $item_price;

                        $total_allocations += $total_allocation;
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item }}</td>
                        <td>{{ number_format($quantityAllocation,0) }}</td>
                        <td>{{ number_format($quantityExecutive,0) }}</td>
                        <td>
                            {{ number_format($quantityAllocation - $quantityExecutive,0) }}
                        </td>
                        <td>
                            {{ number_format($item_price,0) }}
                        </td>
                        <td>{{ number_format($total_allocation,0) }}</td>
                        <td>{{ number_format($total_ils,0) }}</td>

                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" align="right">المجموع</td>
                    <td>{{ number_format($executivesTotal['quantity_allocations'],0) }}</td>
                    <td>{{ number_format($executivesTotal['quantity_executives'],0) }}</td>
                    <td>{{ number_format($executivesTotal['quantity_allocations'] - $executivesTotal['quantity_executives'],0) }}</td>
                    <td></td>
                    <td>{{ number_format($total_allocations,0) }}</td>
                    <td>{{ number_format($executivesTotal['total_ils'],0) }}</td>
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
