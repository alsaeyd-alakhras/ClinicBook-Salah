<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8" />
    <title>كشف التخصيصات</title>
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
                            <span>كشف التخصيصات</span>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="20" align="center" style="color: #000;border:0;">
                        <h3>كشف التخصيصات</h3>
                    </td>
                </tr>
                <tr style="background: #dddddd;">
                    <th class="text-secondary opacity-7 text-center">#</th>
                    <th>تاريخ <br> التخصيص</th>
                    <th>رقم <br> الموازنة</th>
                    <th>الاسم المختصر</th>
                    <th>المؤسسة</th>
                    <th>المشروع</th>
                    <th>الصنف</th>
                    <th>الكمية</th>
                    <th>السعر</th>
                    <th>إجمالي $</th>
                    <th>التخصيص</th>
                    <th>العملة</th>
                    <th>المبلغ $</th>
                    <th>عدد المستفيدين</th>
                    <th>بنود التنفيد</th>
                    <th>تاريخ القبض</th>
                    <th>بيان</th>
                    <th>المبلغ المقبوض $</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($allocations as $allocation)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $allocation->date_allocation }}</td>
                        <td>{{ $allocation->budget_number }}</td>
                        <td>{{ $allocation->broker_name }}</td>
                        <td>{{ $allocation->organization_name }}</td>
                        <td>{{ $allocation->project_name }}</td>
                        <td>{{ $allocation->item_name }}</td>
                        <td>{{ $allocation->quantity }}</td>
                        <td>{{ $allocation->price }}</td>
                        <td>{{ $allocation->total_dollar }}</td>
                        <td>{{ $allocation->allocation }}</td>
                        <td>{{ $allocation->currency_allocation }}</td>
                        <td>{{ $allocation->amount }}</td>
                        <td>{{ $allocation->number_beneficiaries }}</td>
                        <td>{{ $allocation->implementation_items }}</td>
                        <td>{{ $allocation->date_implementation }}</td>
                        <td>{{ $allocation->implementation_statement }}</td>
                        <td>{{ $allocation->amount_received }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7" align="right">الإجمالي</td>
                    <td>{{  number_format($allocationsTotal['quantity'],0) }}</td>
                    <td colspan="4"></td>
                    <td>{{ number_format($allocationsTotal['amount'],0) }}</td>
                    <td colspan="3"></td>
                    <td>{{ number_format($allocationsTotal['amount_received'],0) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
        <table width="100%" style="vertical-align: bottom; color: #000000;  margin: 1em">
            <tr>
                <td width="33%"></td>
                <td width="33%" align="center"></td>
                <td width="33%" align="center">
                    <table class="table align-items-center mb-0 table-hover table-bordered">
                        <tbody>
                            <tr>
                                <th>المبالغ المخصصة</th>
                                <td>
                                    {{ number_format($amounts_allocated, 2) ?? 0 }}
                                </td>
                            </tr>
                            <tr>
                                <th>المبالغ المستلمة</th>
                                <td>
                                    {{ number_format($amounts_received, 2) ?? 0 }}
                                </td>
                            </tr>
                            <tr style="background: #ddd;">
                                <th>المتبقي</th>
                                <td>
                                    {{ number_format($remaining, 2) ?? 0 }}
                                </td>
                            </tr>
                            <tr class="text-danger">
                                <th>نسبة التحصيل</th>
                                <td>
                                    {{ number_format($collection_rate, 2) ?? 0 }} %
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
                        <td width="33%" style="text-align: left;">اسم المستخدم</td>
                    @endauth
                </tr>
            </table>
        </htmlpagefooter>
    </div>


</body>

</html>
