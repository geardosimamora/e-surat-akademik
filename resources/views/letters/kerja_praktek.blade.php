<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Permohonan Kerja Praktek</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; margin: 2cm; }
        .kop-surat { text-align: center; border-bottom: 3px solid black; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-surat h1 { font-size: 16pt; margin: 0; text-transform: uppercase; }
        .kop-surat p { font-size: 12pt; margin: 0; }
        .info-surat { margin-bottom: 20px; }
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

    <div class="info-surat clearfix">
        <div style="float: right;">
            Aceh Utara, {{ \Carbon\Carbon::parse($letter->updated_at ?? now())->format('d F Y') }}
        </div>
        <div style="float: left;">
            <table>
                <tr><td style="width: 60px;">Nomor</td><td style="width: 10px;">:</td><td>{{ $letterNumber ?? '-' }}</td></tr>
                <tr><td>Lamp.</td><td>:</td><td>1 (Satu) Berkas Proposal</td></tr>
                <tr><td>Hal</td><td>:</td><td><strong>Permohonan Kerja Praktek</strong></td></tr>
            </table>
        </div>
    </div>

    <br><br><br>

    <p>
        Kepada Yth.<br>
        <strong>Pimpinan {{ $letter->additional_data['nama_instansi'] ?? 'Instansi Tujuan' }}</strong><br>
        di - <br>
        &nbsp;&nbsp;&nbsp;&nbsp;{{ $letter->additional_data['alamat_instansi'] ?? 'Tempat' }}
    </p>

    <p>Dengan hormat,</p>
    
    <p style="text-align: justify;">
        Dalam rangka mengaplikasikan ilmu pengetahuan yang diperoleh di bangku kuliah serta untuk memenuhi salah satu syarat kelulusan pada Program Studi Sistem Informasi Universitas Malikussaleh, maka mahasiswa diwajibkan untuk melaksanakan Kerja Praktek. Sehubungan dengan hal tersebut, kami memohon kesediaan Bapak/Ibu untuk dapat menerima mahasiswa kami berikut ini:
    </p>

    <table style="margin-left: 20px; width: 90%;">
        <tr>
            <td style="width: 25%;">Nama</td>
            <td style="width: 2%;">:</td>
            <td><strong>{{ $snapshot['name'] ?? 'Geardo Lapista Simamora' }}</strong></td>
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

    <p style="text-align: justify;">
        Adapun Kerja Praktek tersebut direncanakan akan dilaksanakan selama 30 hari kerja, terhitung mulai tanggal <strong>{{ $letter->additional_data['tanggal_mulai'] ?? '15 Oktober 2025' }}</strong>.
    </p>

    <p style="text-align: justify;">
        Demikian surat permohonan ini kami sampaikan. Atas perhatian dan kerja sama yang baik dari Bapak/Ibu, kami ucapkan terima kasih.
    </p>

    <div class="ttd-container clearfix">
        <div class="qr-box">
            @if(!empty($qrCode))
            <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" width="100">
            @endif
            <p style="font-size: 8pt; margin-top: 5px;">Scan untuk verifikasi<br>keaslian surat.</p>
        </div>
        
        <div class="ttd-box">
            <p>Ketua Program Studi,</p>
            <br><br><br>
            <p><strong>Dr. Fulan, S.Kom., M.Kom.</strong><br>NIP. 198001012005011001</p>
        </div>
    </div>

</body>
</html>