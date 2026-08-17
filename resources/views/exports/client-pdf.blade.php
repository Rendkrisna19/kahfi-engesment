<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .badge {
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 10px;
        }
        .success { background-color: #d1fae5; color: #065f46; }
        .pending { background-color: #fef3c7; color: #92400e; }
        .danger { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Tanggal Cetak: {{ $date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Platform</th>
                <th>Campaign</th>
                <th>URL</th>
                <th class="text-right">Views</th>
                <th class="text-right">Likes</th>
                <th class="text-right">Comments</th>
                <th class="text-right">ER (%)</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($links as $index => $link)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ ucfirst($link->platform) }}</td>
                <td>{{ $link->campaign->nama_campaign ?? '-' }}</td>
                <td>{{ \Illuminate\Support\Str::limit($link->url, 40) }}</td>
                <td class="text-right">{{ number_format($link->views ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($link->likes ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($link->comments ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($link->engagement_rate ?? 0, 2) }}%</td>
                <td class="text-center">
                    @if($link->status_scraping == 'Completed')
                        <span class="badge success">Selesai</span>
                    @elseif($link->status_scraping == 'Pending')
                        <span class="badge pending">Antrean</span>
                    @else
                        <span class="badge danger">Gagal</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
