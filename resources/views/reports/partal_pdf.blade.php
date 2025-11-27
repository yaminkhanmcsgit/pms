<!DOCTYPE html>
<html lang="ur" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>پڑتال رپورٹ</title>
    <style>
        body { font-family: 'Noto Nastaleeq Urdu', sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
    </style>
</head>
<body>
    <h1>پڑتال رپورٹ</h1>
    <p>تاریخ سے: {{ $from_date }} تا تاریخ: {{ $to_date }}</p>
    <style>
        body { font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif; direction: rtl; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; text-align: center; }
    </style>
    <table>
        <thead>
            <tr>
                <th rowspan="2">سیریل نمبر</th>
                <th colspan="7">بنیادی معلومات</th>
                <th colspan="2">پڑتال پیمائش موقع</th>
                <th colspan="2">تصدیق آخیر ملکیت وغیرہ بر موقع</th>
                <th colspan="2">تصدیق آخیر شجرہ نسب</th>
                <th colspan="2">تصدیق ملکیت و قبضہ کاشت وغیرہ</th>
                <th rowspan="2">تبصرہ</th>
            </tr>
            <tr>
                <th>ضلع نام</th>
                <th>تحصیل نام</th>
                <th>موضع نام</th>
                <th>پٹواری نام</th>
                <th>اہلکار نام</th>
                <th>از تاریخ</th>
                <th>تا تاریخ</th>
                <th>تصدیق ملکیت/پیمود شدہ نمبرات خسرہ</th>
                <th>تعداد برامدہ بدرات</th>
                <th>تصدیق ملکیت و قبضہ کاشت نمبرات خسرہ</th>
                <th>تعداد برامدہ بدرات</th>
                <th>تعداد گھری</th>
                <th>تعداد برامدہ بدرات</th>
                <th>مقابلہ کھتونی ہمراہ کاپی چومنڈہ</th>
                <th>تعداد برامدہ بدرات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($query as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->districtNameUrdu }}</td>
                <td>{{ $item->tehsilNameUrdu }}</td>
                <td>{{ $item->mozaNameUrdu }}</td>
                <td>{{ $item->patwari_nam }}</td>
                <td>{{ $item->ahalkar_nam }}</td>
                <td>{{ $from_date }}</td>
                <td>{{ $to_date }}</td>
                <td>{{ $item->tasdeeq_milkiat_pemuda_khasra }}</td>
                <td>{{ $item->tasdeeq_milkiat_pemuda_khasra_badrat }}</td>
                <td>{{ $item->tasdeeq_milkiat_qabza_kasht_khasra }}</td>
                <td>{{ $item->tasdeeq_milkiat_qabza_kasht_badrat }}</td>
                <td>{{ $item->tasdeeq_shajra_nasab_guri }}</td>
                <td>{{ $item->tasdeeq_shajra_nasab_badrat }}</td>
                <td>{{ $item->muqabala_khatoni_chomanda }}</td>
                <td>{{ $item->muqabala_khatoni_chomanda_badrat }}</td>
                <td>{{ $item->tabsara }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>