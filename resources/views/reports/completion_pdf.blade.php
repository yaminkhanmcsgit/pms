<!DOCTYPE html>
<html lang="ur" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تکمیلی کام رپورٹ</title>
    <style>
        body { font-family: 'Noto Nastaleeq Urdu', sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
    </style>
</head>
<body>
    <h1>تکمیلی کام رپورٹ</h1>
    <p>تاریخ سے: {{ $from_date }} تا تاریخ: {{ $to_date }}</p>
    <style>
        body { font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif; direction: rtl; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; text-align: center; }
        .highlight { background-color: yellow; }
    </style>
    <table>
        <thead>
            <tr>
                <th>نمبر شمار</th>
                <th>نام ضلع</th>
                <th>نام تحصیل</th>
                <th>نام موضع</th>
                <th>نام اہلکار</th>
                <th>میزان کھاتہ دار/کھتونی</th>
                <th>پختہ کھتونی درانڈکس خسرہ</th>
                <th>درستی بدرات</th>
                <th>تحریر نقل شجرہ نسب</th>
                <th>تحریر شجرہ نسب مالکان قبضہ</th>
                <th>پختہ کھاتاجات</th>
                <th>خام کھاتہ جات در شجرہ نسب</th>
                <th>تحریر مشترکہ کھاتہ</th>
                <th>پختہ نمبرواں در کھتونی</th>
                <th>خام نمبرواں در کھتونی</th>
                <th>تصدیق آخیر</th>
                <th>متفرق کام</th>
                <th>از تاریخ</th>
                <th>تا تاریخ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($query as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->districtNameUrdu }}</td>
                <td>{{ $item->tehsilNameUrdu }}</td>
                <td>{{ $item->mozaNameUrdu }}</td>
                <td>{{ $item->employee_name }}</td>
                <td class="{{ $item->mizan_khata_dar_khatoni > 0 ? 'highlight' : '' }}">{{ $item->mizan_khata_dar_khatoni }}</td>
                <td class="{{ $item->pukhta_khatoni_drandkas_khasra > 0 ? 'highlight' : '' }}">{{ $item->pukhta_khatoni_drandkas_khasra }}</td>
                <td class="{{ $item->durusti_badrat > 0 ? 'highlight' : '' }}">{{ $item->durusti_badrat }}</td>
                <td class="{{ $item->tehreer_naqal_shajra_nasab > 0 ? 'highlight' : '' }}">{{ $item->tehreer_naqal_shajra_nasab }}</td>
                <td class="{{ $item->tehreer_shajra_nasab_malkan_qabza > 0 ? 'highlight' : '' }}">{{ $item->tehreer_shajra_nasab_malkan_qabza }}</td>
                <td class="{{ $item->pukhta_khatajat > 0 ? 'highlight' : '' }}">{{ $item->pukhta_khatajat }}</td>
                <td class="{{ $item->kham_khatajat_dar_shajra_nasab > 0 ? 'highlight' : '' }}">{{ $item->kham_khatajat_dar_shajra_nasab }}</td>
                <td class="{{ $item->tehreer_mushtarka_khata > 0 ? 'highlight' : '' }}">{{ $item->tehreer_mushtarka_khata }}</td>
                <td class="{{ $item->pukhta_numberwan_dar_khatoni > 0 ? 'highlight' : '' }}">{{ $item->pukhta_numberwan_dar_khatoni }}</td>
                <td class="{{ $item->kham_numberwan_dar_khatoni > 0 ? 'highlight' : '' }}">{{ $item->kham_numberwan_dar_khatoni }}</td>
                <td class="{{ $item->tasdeeq_akhir > 0 ? 'highlight' : '' }}">{{ $item->tasdeeq_akhir }}</td>
                <td class="{{ $item->mutafarriq_kaam > 0 ? 'highlight' : '' }}">{{ $item->mutafarriq_kaam }}</td>
                <td>{{ $from_date }}</td>
                <td>{{ $to_date }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>