<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8" />
    <title>كشف التنفيذات</title>
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
    <style>
        .table {
            border-collapse: collapse;
            margin: 1em 0;
            width: 100%;
            color: #000000;
        }
        .table {
            width: 100%;
            margin-bottom: 1rem;
            background-color: transparent;
            border: 1px solid #dee2e6;
        }
        .table-bordered {
            border: 1px solid #dee2e6;
        }
        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }
        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
        }
        .text-danger {
            color: #dc3545;
        }
        .align-items-center {
            vertical-align: middle !important;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
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
                            <span>كشف التنفيذات</span>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="15" align="center" style="color: #000;border:0;">
                        <h3>كشف التنفيذات</h3>
                    </td>
                </tr>
                <tr style="background: #dddddd;">
                    <th class="text-secondary opacity-7 text-center">#</th>
                    <th>التاريخ</th>
                    <th>المؤسسة</th>
                    <th>الحساب</th>
                    <th>الاسم</th>
                    <th>المشروع</th>
                    <th>التفصيل..</th>
                    <th>الصنف</th>
                    <th>الكمية</th>
                    <th>السعر ₪</th>
                    <th>إجمالي ₪</th>
                    <th>المستلم</th>
                    <th>ملاحظات</th>
                    <th>الدفعات</th>
                    <th>آلية الدفع</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($executives as $executive)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $executive->implementation_date }}</td>
                        <td>{{ $executive->broker_name }}</td>
                        <td>{{ $executive->account }}</td>
                        <td>{{ $executive->affiliate_name }}</td>
                        <td>{{ $executive->project_name }}</td>
                        <td>{{ $executive->detail }}</td>
                        <td>{{ $executive->item_name }}</td>
                        <td>{{ $executive->quantity }}</td>
                        <td>{{ $executive->price }}</td>
                        <td>{{ $executive->total_ils }}</td>
                        <td>{{ $executive->received }}</td>
                        <td>{{ $executive->notes }}</td>
                        <td>{{ $executive->amount_payments }}</td>
                        <td>{{ $executive->payment_mechanism }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8" align="right">الإجمالي</td>
                    <td>{{  number_format($executivesTotal['quantity'],0) }}</td>
                    <td></td>
                    <td>{{ number_format($executivesTotal['total_ils'],0) }}</td>
                    <td colspan="2"></td>
                    <td>{{ number_format($executivesTotal['amount_payments'],0) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        <table  width="100%" style="vertical-align: bottom; color: #000000;  margin: 1em">
            <tr>
                <td width="33%"></td>
                <td width="33%" align="center"></td>
                <td width="33%" align="center">
                    <table class="table align-items-center mb-0 table-bordered">
                        <thead>
                            <tr>
                                <th></th>
                                <th>بالشيكل</th>
                                <th>بالدولار</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>اجمالي مبالغ شيكل</th>
                                <td>
                                    {{number_format($total_amounts,2) ?? 0}}
                                </td>
                                <td class="text-danger">
                                    {{number_format($total_amounts * $ILS,2) ?? 0}}
                                </td>
                            </tr>
                            <tr>
                                <th>اجمالي الدفعات شيكل</th>
                                <td>
                                    {{number_format($total_payments,2) ?? 0}}
                                </td>
                                <td class="text-danger">
                                    {{ number_format($ILS * $total_payments,2) ?? 0}}
                                </td>
                            </tr>
                            <tr>
                                <th>الرصيد المتبقي شيكل</th>
                                <td>
                                    {{number_format($remaining_balance,2) ?? 0}}
                                </td>
                                <td class="text-danger">
                                    {{ number_format($ILS * $remaining_balance,2) ?? 0}}
                                </td>
                            </tr>
                            <tr class="text-danger">
                                <th colspan="2">سعر الدولار / الشيكل</th>
                                <td>
                                    {{number_format(1 / $ILS,2) ?? 0}}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
        <htmlpagefooter name="page-footer">
            <table width="100%" style="vertical-align: bottom; color: #000000;  margin: 1em">
                <tr>
                    <td width="33%">{DATE j-m-Y}</td>
                    <td width="33%" align="center">{PAGENO}/{nbpg}</td>
                    @auth
                        <td width="33%" style="text-align: left;">{{ Auth::user()->name }}</td>
                    @else
                        <td width="33%" style="text-align: left;"></td>
                    @endauth
                </tr>
            </table>
        </htmlpagefooter>
    </div>


</body>

</html>
