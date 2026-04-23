<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8" />
    <title>تقرير بأعداد وكميات أصناف المشاريع المنفذة</title>
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
                            <span>جمعية دار اليتيم الفلسطيني</span>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="5" style="border:0;">
                        <p>
                            <span>تقرير إغاثات حملة طوفان الأقصى</span>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="{{ 5 + count($months) }}" align="center" style="color: #000;border:0;">
                        <h2>تقرير بأعداد وكميات أصناف المشاريع المنفذة</h2>
                    </td>
                </tr>
                <tr style="background: #dddddd;">
                    <th>#</th>
                    <th>الأصناف</th>
                    <th>{{$lastYear}}</th>
                    @foreach ($months as $monthV)
                        <th>{{ $monthNameAr[Carbon\Carbon::parse($monthV)->format('m')] }}</th>
                    @endforeach
                    <th>إجمالي العدد</th>
                    <th>إجمالي المبلغ بالشيكل</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    @php
                        $executive = App\Models\Executive::whereBetween('implementation_date', [$year . '-01-01', $year . '-12-31'])->where('item_name', $item)->get();
                        $executiveLastYear = App\Models\Executive::whereBetween('implementation_date', [ $lastYear .'-01-01',  $lastYear .'-12-31'])->where('item_name', $item)->get();
                        $quantity = $executive->sum('quantity') + $executiveLastYear->sum('quantity');
                        $total_ils = $executive->sum('total_ils') + $executiveLastYear->sum('total_ils');
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item }}</td>
                        <td>{{ $executiveLastYear->sum('quantity') }}</td>
                        @foreach ($months as $month2)
                            <td>{{ number_format($executive->where('month', Carbon\Carbon::parse($month2)->format('Y-m'))->sum('quantity'),0) }}</td>
                        @endforeach
                        <td>{{ number_format($quantity,0) }}</td>
                        <td>{{ number_format($total_ils,0) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" align="right">الإجمالي</td>
                    <td>{{ number_format($executivesTotal[$lastYear],0) }}</td>
                    @foreach ($months as $month)
                        <td>{{ number_format($executivesTotal[Carbon\Carbon::parse($month)->format('m')],0) }}</td>
                    @endforeach
                    <td>{{ number_format($executivesTotal['quantity'],0) }}</td>
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
