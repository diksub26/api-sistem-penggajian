<table>
    <thead>
        <tr>
            <th>No Induk</th>
            <th>Nama Lengkap</th>
            <th>Divisi</th>
            <th>Hadir</th>
            <th>Sakit</th>
            <th>Izin</th>
            <th>Terlambat</th>
        </tr>
    </thead>
    <tbody>
        @foreach($employees as $employee)
            <tr>
                <td>{{ $employee->no_induk }}</td>
                <td>{{ $employee->fullname }}</td>
                <td>{{ $employee->division }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        @endforeach
    </tbody>
</table>