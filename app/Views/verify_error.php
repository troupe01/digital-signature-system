<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Dokumen - Gagal</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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

        .error-icon {
            font-size: 4em;
            color: #dc3545;
            margin-bottom: 20px;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        .verify-title {
            color: #dc3545;
            font-size: 2.5em;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .verify-subtitle {
            color: #6c757d;
            font-size: 1.2em;
            margin-bottom: 30px;
        }

        .error-details {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 15px;
            padding: 30px;
            margin: 30px 0;
            text-align: left;
        }

        .error-code {
            font-family: 'Courier New', monospace;
            font-size: 1.5em;
            font-weight: bold;
            color: #721c24;
            background: rgba(255, 255, 255, 0.8);
            padding: 10px 15px;
            border-radius: 8px;
            margin: 15px 0;
            text-align: center;
            letter-spacing: 2px;
        }

        .error-reasons {
            margin-top: 20px;
        }

        .error-reasons h4 {
            color: #721c24;
            margin-bottom: 15px;
            font-size: 1.1em;
        }

        .error-reasons ul {
            color: #721c24;
            text-align: left;
            margin-left: 20px;
        }

        .error-reasons li {
            margin-bottom: 8px;
            line-height: 1.5;
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
            font-size: 1.1em;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: linear-gradient(45deg, #6c757d, #495057);
        }

        .btn-secondary:hover {
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
        }

        .help-section {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 15px;
            padding: 25px;
            margin-top: 30px;
            text-align: left;
        }

        .help-section h4 {
            color: #004085;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .help-section p {
            color: #004085;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .contact-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-top: 15px;
        }

        .contact-info strong {
            color: #495057;
        }

        @media (max-width: 768px) {
            .verify-container {
                padding: 30px 20px;
                margin: 10px;
            }

            .verify-title {
                font-size: 2em;
            }

            .error-icon {
                font-size: 3em;
            }

            .btn {
                display: block;
                margin: 10px 0;
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="verify-container">
        <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>

        <h1 class="verify-title">Verifikasi Gagal</h1>
        <p class="verify-subtitle">Dokumen tidak dapat diverifikasi</p>

        <div class="error-details">
            <div class="error-code">
                <?= esc($code ?? 'INVALID_CODE') ?>
            </div>

            <div class="error-reasons">
                <h4><i class="fas fa-list"></i> Kemungkinan Penyebab:</h4>
                <ul>
                    <li><strong>Kode verifikasi tidak valid</strong> - Kode yang Anda masukkan tidak ditemukan di sistem</li>
                    <li><strong>Kode sudah kedaluwarsa</strong> - Kode verifikasi mungkin sudah tidak berlaku</li>
                    <li><strong>Kesalahan pengetikan</strong> - Periksa kembali kode yang Anda masukkan</li>
                    <li><strong>QR Code rusak</strong> - QR Code mungkin tidak terbaca dengan benar</li>
                    <li><strong>Dokumen telah dihapus</strong> - Dokumen mungkin sudah tidak tersedia di sistem</li>
                </ul>
            </div>
        </div>

        <div class="help-section">
            <h4><i class="fas fa-question-circle"></i> Apa yang bisa Anda lakukan?</h4>
            <p><strong>1. Periksa kode verifikasi</strong> - Pastikan Anda memasukkan kode dengan benar, termasuk huruf besar/kecil</p>
            <p><strong>2. Scan ulang QR Code</strong> - Jika menggunakan QR Code, coba scan ulang dengan pencahayaan yang baik</p>
            <p><strong>3. Hubungi penandatangan</strong> - Minta kode verifikasi yang baru dari orang yang menandatangani dokumen</p>
            <p><strong>4. Periksa sumber dokumen</strong> - Pastikan dokumen berasal dari sumber yang terpercaya</p>

            <div class="contact-info">
                <p><strong><i class="fas fa-info-circle"></i> Catatan:</strong>
                    Jika Anda yakin kode verifikasi benar, kemungkinan dokumen belum ditandatangani secara digital atau ada masalah teknis pada sistem.</p>
            </div>
        </div>

        <div style="margin-top: 30px;">
            <a href="javascript:history.back()" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <a href="<?= base_url() ?>" class="btn">
                <i class="fas fa-home"></i> Ke Beranda
            </a>
        </div>

        <div style="margin-top: 20px; color: #6c757d; font-size: 0.9em;">
            <p><i class="fas fa-shield-alt"></i> Sistem Tanda Tangan Digital</p>
            <p>Verifikasi Timestamp: <?= date('d F Y, H:i:s') ?> WIB</p>
        </div>
    </div>
</body>

</html>