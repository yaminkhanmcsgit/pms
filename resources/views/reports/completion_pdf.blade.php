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
                @foreach($types as $type)
                    <th>{{ $type->title_ur }}</th>
                @endforeach
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
                @foreach($types as $type)
                    @if(!empty($type->field_name))
                        <td class="{{ ($item->{$type->field_name} > 0) ? 'highlight' : '' }}">{{ $item->{$type->field_name} }}</td>
                    @else
                        <td>-</td>
                    @endif
                @endforeach
                <td>{{ $from_date }}</td>
                <td>{{ $to_date }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>