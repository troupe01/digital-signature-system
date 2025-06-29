<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Dokumen - Berhasil</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .verify-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .success-icon {
            font-size: 4em;
            color: #28a745;
            margin-bottom: 20px;
        }

        .verify-title {
            color: #2c3e50;
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .document-details {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            margin: 30px 0;
            text-align: left;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .btn {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 25px;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
        }
    </style>
</head>

<body>
    <div class="verify-container">
        <div class="success-icon">✅</div>
        <h1 class="verify-title">Dokumen Terverifikasi</h1>
        <p>Dokumen ini telah ditandatangani secara digital dan valid</p>

        <div class="document-details">
            <div class="detail-item">
                <span><strong>Nama Dokumen:</strong></span>
                <span><?= esc($signature['original_name'] ?? $signature['filename']) ?></span>
            </div>
            <div class="detail-item">
                <span><strong>Penandatangan:</strong></span>
                <span><?= esc($signature['signer_name']) ?></span>
            </div>
            <div class="detail-item">
                <span><strong>Posisi/Jabatan:</strong></span>
                <span><?= esc($signature['signer_position']) ?></span>
            </div>
            <div class="detail-item">
                <span><strong>Email:</strong></span>
                <span><?= esc($signature['signer_email']) ?></span>
            </div>
            <div class="detail-item">
                <span><strong>Waktu Tanda Tangan:</strong></span>
                <span><?= date('d F Y, H:i', strtotime($signature['signature_date'])) ?> WIB</span>
            </div>
        </div>

        <div>
            <a href="<?= base_url('api/documents/public-download/' . $signature['verification_code']) ?>" class="btn">
                📥 Download Dokumen
            </a>
            <a href="<?= base_url() ?>" class="btn">
                🏠 Kembali ke Beranda
            </a>
        </div>
    </div>
</body>

</html>