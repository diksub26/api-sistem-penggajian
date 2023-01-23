<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Cuti | {{ $month }}</title>
</head>
<style>
    *{
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-size: 12pt;
    }
    body{
        width: 29,7cm;
        height: 21cm;
    }
    table, th, td {
        border: 1px solid;
    }
    table {
        border-collapse: collapse;
    }
    .title-container{
        display: flex;
        margin-top: 32px;
        justify-content: center;
        align-items: center;
        height: 120px;
        width: 100%;
    }
</style>
<body>
    <div class="title-container">
        <h1>Laporan Cuti Karyawan Bulan {{ $month }}</h1>
    </div>
    <table>
        <thead>
            <tr>
                <th>Nama Karyawan</th>
                <th>Type Cuti</th>
                <th>Alasan</th>
                <th>Status</th>
                <th>Nama Manager</th>
                <th>Tgl Mulai Cuti</th>
                <th>Tgl Akhir Cuti</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($leaves as $item)
                <tr>
                    <td>{{ $item->employeeName}}</td>
                    <td>{{ $item->type}}</td>
                    <td>{{ $item->reason}}</td>
                    <td>{{ $item->status}}</td>
                    <td>{{ $item->managerName}}</td>
                    <td>{{ $item->startDate}}</td>
                    <td>{{ $item->endDate}}</td>
                    <td>{{ $item->amount}}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8"></td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>