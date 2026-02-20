<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Aktif Kuliah</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; margin: 2cm; }
        .kop-surat { text-align: center; border-bottom: 3px solid black; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-surat h1 { font-size: 16pt; margin: 0; text-transform: uppercase; }
        .kop-surat p { font-size: 12pt; margin: 0; }
        .judul-surat { text-align: center; margin-bottom: 20px; text-decoration: underline; font-weight: bold; }
        .nomor-surat { text-align: center; margin-top: -15px; margin-bottom: 30px; }
        table { width: 100%; margin-bottom: 20px; }
        td { vertical-align: top; padding: 2px 0; }
        .ttd-container { width: 100%; margin-top: 50px; }
        .ttd-box { float: right; width: 300px; text-align: left; }
        .qr-box { float: left; width: 150px; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>

    <div class="kop-surat">
        <h1>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</h1>
        <h1>UNIVERSITAS MALIKUSSALEH</h1>
        <p>PROGRAM STUDI SISTEM INFORMASI</p>
        <p style="font-size: 10pt;">Jl. Cot Tengku Nie, Reuleut, Aceh Utara, 24355</p>
    </div>

    <div class="judul-surat">SURAT KETERANGAN AKTIF KULIAH</div>
    <div class="nomor-surat">Nomor: {{ $letterNumber ?? '-' }}</div>

    <p>Yang bertanda tangan di bawah ini, menerangkan bahwa:</p>

    <table>
        <tr>
            <td style="width: 30%;">Nama</td>
            <td style="width: 2%;">:</td>
            <td style="width: 68%;"><strong>{{ $snapshot['name'] ?? 'Geardo Lapista Simamora' }}</strong></td>
        </tr>
        <tr>
            <td>NIM</td>
            <td>:</td>
            <td>{{ $snapshot['nim'] ?? '230180121' }}</td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td>:</td>
            <td>Sistem Informasi</td>
        </tr>
    </table>

    <p>Adalah benar mahasiswa aktif pada Program Studi Sistem Informasi Universitas Malikussaleh pada semester berjalan.</p>
    
    @if(!empty($letter->additional_data['tujuan_surat']))
    <p>Surat keterangan ini diberikan untuk keperluan: <strong>{{ $letter->additional_data['tujuan_surat'] }}</strong>.</p>
    @else
    <p>Surat keterangan ini diberikan untuk dipergunakan sebagaimana mestinya.</p>
    @endif

    <div class="ttd-container clearfix">
        <div class="qr-box">
            @if(!empty($qrCode))
            <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" width="100">
            @endif
            <p style="font-size: 8pt; margin-top: 5px;">Scan untuk verifikasi<br>keaslian surat.</p>
        </div>
        
        <div class="ttd-box">
            <p>Aceh Utara, {{ \Carbon\Carbon::parse($letter->updated_at ?? now())->format('d F Y') }}<br>Ketua Program Studi,</p>
            <br><br><br>
            <p><strong>Dr. Fulan, S.Kom., M.Kom.</strong><br>NIP. 198001012005011001</p>
        </div>
    </div>

</body>
</html>