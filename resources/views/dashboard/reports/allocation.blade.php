<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8" />
    <title>بيانات تخصيص</title>
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'XBRiyaz', sans-serif;
        }
        @page {
            header: page-header;
            footer: page-footer;
        }
        hr {
            right: 25px;
        }
        html {
            direction: rtl;
        }

        .head_td{
            text-align: right;
        }
    </style>
    <style>
        .container {
            max-width: 100%;
            margin: 0 10px;
        }

        .personal-info-title {
            text-align: right;
            color: #632423;
        }

        .table-responsive {
            text-align: justify;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table td {
            padding: 12px;
            border: 1px solid #000000;
            font-size: 35px;
        }

        .head_td {
            background: #d6e3bc;
            font-weight: bold;
            width: 350px;
            font-size: 35px;
        }

        .data_td {
            background: #fdf8ed;
            color: #000000;
        }

        /* Specific width adjustments */
        .gender-label {
            width: 207px;
        }

        .wide-cell {
            width: 800px;
        }

        .medium-cell {
            width: 700px;
        }

        .relation-label {
            width: 366px;
        }

        /* Table responsiveness */
        @media screen and (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
            }

            .table td {
                white-space: nowrap;
            }
        }
    </style>
</head>

<body>
    <htmlpageheader name="page-header">
        <img src="{{ public_path('imgs/header/yatem.jpg') }}" alt="">
    </htmlpageheader>

    <div id="content" lang="ar" style="margin-top: 5em;">
        <div class="container">
            <h3 class="personal-info-title">بيانات التخصيص لصالح : {{ $allocation->broker_name }}</h3>
            <div class="table-responsive">
                <table class="table">
                    <tbody>
                        <tr>
                            <td class="head_td">رقم الموازنة :</td>
                            <td class="data_td wide-cell">{{ $allocation->budget_number }}</td>
                            <td class="head_td gender-label"> تاريخ التخصيص: &nbsp;</td>
                            <td class="data_td medium-cell">{{ $allocation->date_allocation }}</td>
                        </tr>
                        <tr>
                            <td class="head_td">المؤسسة :</td>
                            <td class="data_td wide-cell">{{ $allocation->broker_name }}</td>
                            <td class="head_td gender-label">المتبرع  :&nbsp;</td>
                            <td class="data_td medium-cell">{{ $allocation->organization_name }}</td>
                        </tr>
                        <tr>
                            <td class="head_td">المشروع :</td>
                            <td class="data_td wide-cell">{{ $allocation->project_name }}</td>
                            <td class="head_td gender-label">الصنف  :&nbsp;</td>
                            <td class="data_td medium-cell">{{ $allocation->item_name }}</td>
                        </tr>
                        <tr>
                            <td class="head_td">الكمية :</td>
                            <td class="data_td wide-cell">{{ $allocation->quantity }}</td>
                            <td class="head_td gender-label">سعر الوحدة :&nbsp;</td>
                            <td class="data_td medium-cell">{{ $allocation->price }}</td>
                        </tr>
                        <tr>
                            <td class="head_td">التخصيص :</td>
                            <td class="data_td wide-cell">{{ number_format($allocation->allocation,2)  }}</td>
                            <td class="head_td gender-label">العملة  :&nbsp;</td>
                            <td class="data_td medium-cell">{{ App\Models\Currency::where('code', $allocation->currency_allocation)->first()->name }}</td>
                        </tr>
                        <tr>
                            <td class="head_td">سعر الدولار للعملة :</td>
                            <td class="data_td wide-cell">{{ number_format(1 / $allocation->currency_allocation_value,2) }}</td>
                            <td class="head_td gender-label">المبلغ $  :&nbsp;</td>
                            <td class="data_td medium-cell">{{ number_format($allocation->amount,2) }}</td>
                        </tr>
                        <tr>
                            <td class="head_td">عدد المستفيدين :</td>
                            <td class="data_td wide-cell">{{ $allocation->number_beneficiaries }}</td>
                            <td class="head_td gender-label">بنوذ التنفيد  :&nbsp;</td>
                            <td class="data_td medium-cell">{{ $allocation->implementation_items }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="container">
            <h3 class="personal-info-title">بنود القبض</h3>
            <div class="table-responsive">
                <table class="table">
                    <tbody>
                        <tr>
                            <td class="head_td">تاريخ القبض :</td>
                            <td class="data_td wide-cell">{{ $allocation->date_implementation }}</td>
                            <td class="head_td gender-label">المبلغ المقبوض  :&nbsp;</td>
                            <td class="data_td medium-cell">{{ number_format($allocation->amount_received,2) }}</td>
                        </tr>
                        <tr>
                            <td class="head_td">رقم إيصال القبض :</td>
                            <td class="data_td wide-cell">{{ $allocation->arrest_receipt_number }}</td>
                            <td class="head_td gender-label">بيان  :&nbsp;</td>
                            <td class="data_td medium-cell">{{ $allocation->implementation_statement }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <table width="100%" style="vertical-align: bottom; color: #000000;  margin: 1em">
            <tr>
                <td width="33%">{{Carbon\Carbon::now()->format('Y-m-d')}}</td>
                <td width="33%" align="center"></td>
                <td width="33%" style="text-align: left;">توقيع</td>
            </tr>
        </table>

        {{-- <tr>
            <td class="head_td"> :</td>
            <td class="data_td wide-cell"></td>
            <td class="head_td gender-label">  :&nbsp;</td>
            <td class="data_td medium-cell"></td>
        </tr> --}}

    </div>


</body>

</html>
