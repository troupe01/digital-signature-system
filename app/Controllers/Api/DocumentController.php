<?php

namespace App\Controllers\Api;

use App\Models\AuditLogModel;
use CodeIgniter\RESTful\ResourceController;
use App\Models\DocumentModel;
use App\Models\SignatureModel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use setasign\Fpdi\Tcpdf\Fpdi;

class DocumentController extends ResourceController
{
    protected $documentModel;
    protected $signatureModel;
    protected $auditLogModel;
    protected $format = 'json';

    public function __construct()
    {
        $this->documentModel = new DocumentModel();
        $this->signatureModel = new SignatureModel();
        $this->auditLogModel = new AuditLogModel();
        helper(['filesystem', 'url']);
    }


    // GET /api/documents - Admin sees all, Users see own only
    public function index()
    {
        try {
            $userId = session()->get('user_id');
            $userRole = session()->get('role');

            if (!$userId) {
                return $this->fail('User tidak teridentifikasi');
            }

            // Admin can see all documents with owner info, users see only their own
            if ($userRole === 'admin') {
                $documents = $this->documentModel->getWithOwnerInfo(); // All documents with owner
            } else {
                $documents = $this->documentModel->getWithOwnerInfo($userId); // Only user's documents
            }

            return $this->respond([
                'status' => 'success',
                'data' => $documents,
                'user_role' => $userRole, // Send role to frontend for UI logic
                'message' => 'Data dokumen berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return $this->fail('Gagal mengambil data dokumen: ' . $e->getMessage());
        }
    }


    // POST /api/documents/upload - RESTRICTED FOR ADMIN
    public function upload()
    {
        try {
            $userRole = session()->get('role');

            // ✅ ADMIN RESTRICTION: Admin cannot upload documents
            if ($userRole === 'admin') {
                return $this->fail('Admin tidak diizinkan untuk upload dokumen. Fitur ini hanya untuk user.', 403);
            }

            // Get user ID from session and convert to integer
            $userId = session()->get('user_id');
            $userId = (int) $userId;

            log_message('info', 'Upload attempt - User ID: ' . $userId);

            if (!$userId) {
                return $this->fail('User tidak teridentifikasi');
            }

            $validation = \Config\Services::validation();
            $validation->setRules([
                'pdf_file' => [
                    'label' => 'File PDF',
                    'rules' => 'uploaded[pdf_file]|max_size[pdf_file,10240]|ext_in[pdf_file,pdf]'
                ]
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                return $this->fail($validation->getErrors());
            }

            $file = $this->request->getFile('pdf_file');

            if (!$file->isValid()) {
                return $this->fail('File tidak valid');
            }

            // Debug: Check file before move
            $tempPath = $file->getTempName();
            log_message('info', 'Temp file path: ' . $tempPath);
            log_message('info', 'Temp file size: ' . filesize($tempPath));
            log_message('info', 'Original file size: ' . $file->getSize());

            // Create upload directory if not exists
            $uploadPath = WRITEPATH . 'uploads/documents/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Generate unique filename
            $fileName = $file->getRandomName();
            $targetPath = $uploadPath . $fileName;

            // MANUAL MOVE instead of $file->move() to preserve binary data
            if (move_uploaded_file($tempPath, $targetPath)) {
                log_message('info', 'File moved successfully to: ' . $targetPath);
                log_message('info', 'Final file size: ' . filesize($targetPath));
            } else {
                log_message('error', 'Failed to move uploaded file');
                return $this->fail('Gagal memindahkan file');
            }

            // Verify file integrity after move
            if (!$this->verifyPDFIntegrity($targetPath)) {
                log_message('error', 'PDF file integrity check failed');
                // CLEANUP: Delete corrupted file
                if (file_exists($targetPath)) {
                    unlink($targetPath);
                }
                return $this->fail('File PDF corrupt setelah upload');
            }

            // Save to database with user_id
            $documentData = [
                'user_id' => $userId, // Associate with current user
                'filename' => $fileName,
                'original_name' => $file->getClientName(),
                'original_path' => 'writable/uploads/documents/' . $fileName,
                'file_size' => filesize($targetPath), // Use actual file size
                'upload_date' => date('Y-m-d H:i:s')
            ];

            log_message('info', 'Document data to insert: ' . json_encode($documentData));

            $documentId = $this->documentModel->insert($documentData);

            if (!$documentId) {
                // CLEANUP: Delete uploaded file if database insert fails
                if (file_exists($targetPath)) {
                    unlink($targetPath);
                }
                return $this->fail('Gagal menyimpan data dokumen');
            }

            log_message('info', 'Document inserted successfully with ID: ' . $documentId);

            // Log activity
            $this->auditLogModel->logActivity(
                'upload',
                $userId,
                $documentId,
                'Document uploaded: ' . $file->getClientName(),
                $this->request
            );

            return $this->respondCreated([
                'status' => 'success',
                'data' => [
                    'document_id' => $documentId,
                    'filename' => $file->getClientName(),
                    'file_size' => filesize($targetPath),
                    'is_signed' => false, // ✅ FIX: Explicitly set as unsigned
                    'upload_date' => date('Y-m-d H:i:s')
                ],
                'message' => 'File berhasil diupload'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Upload error: ' . $e->getMessage());

            // CLEANUP: Delete uploaded file if exists on error
            if (isset($targetPath) && file_exists($targetPath)) {
                unlink($targetPath);
            }

            return $this->fail('Gagal upload file: ' . $e->getMessage());
        }
    }

    private function verifyPDFIntegrity($filePath)
    {
        try {
            // Check if file exists and readable
            if (!file_exists($filePath) || !is_readable($filePath)) {
                return false;
            }

            // Check PDF header
            $handle = fopen($filePath, 'rb');
            if (!$handle) {
                return false;
            }

            $header = fread($handle, 4);
            fclose($handle);

            // PDF files must start with %PDF
            if ($header !== '%PDF') {
                log_message('error', 'Invalid PDF header: ' . bin2hex($header));
                return false;
            }

            log_message('info', 'PDF integrity check passed');
            return true;
        } catch (\Exception $e) {
            log_message('error', 'PDF integrity check failed: ' . $e->getMessage());
            return false;
        }
    }

    // POST /api/documents/sign - RESTRICTED FOR ADMIN
    public function sign()
    {
        try {
            $userId = session()->get('user_id');
            $userRole = session()->get('role');

            // ✅ ADMIN RESTRICTION: Admin cannot sign documents
            if ($userRole === 'admin') {
                return $this->fail('Admin tidak diizinkan untuk menandatangani dokumen. Fitur ini hanya untuk user.', 403);
            }

            $json = $this->request->getJSON(true);

            // Validation
            $validation = \Config\Services::validation();
            $validation->setRules([
                'document_id' => 'required|integer',
                'signer_name' => 'required|max_length[255]',
                'signer_position' => 'required|max_length[255]',
                'signer_email' => 'required|valid_email|max_length[255]'
            ]);

            if (!$validation->run($json)) {
                return $this->fail($validation->getErrors());
            }

            // Check if document exists
            $document = $this->documentModel->find($json['document_id']);
            if (!$document) {
                return $this->fail('Dokumen tidak ditemukan');
            }

            // Check ownership (only owner can sign)
            if ($document['user_id'] != $userId) {
                return $this->fail('Anda tidak memiliki akses untuk menandatangani dokumen ini', 403);
            }

            // Check if already signed
            if ($document['is_signed']) {
                return $this->fail('Dokumen sudah ditandatangani');
            }

            // Generate verification code
            $verificationCode = $this->signatureModel->generateVerificationCode();

            // Generate QR Code
            $qrCodePath = $this->generateQRCode($verificationCode);

            if (!$qrCodePath) {
                log_message('error', 'QR Code generation failed');
                $qrCodePath = 'writable/uploads/qrcodes/placeholder.png';
            }

            // Save signature data
            $signatureData = [
                'document_id' => $json['document_id'],
                'signer_name' => $json['signer_name'],
                'signer_position' => $json['signer_position'],
                'signer_email' => $json['signer_email'],
                'verification_code' => $verificationCode,
                'qr_code_path' => $qrCodePath,
                'signature_date' => date('Y-m-d H:i:s'),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString()
            ];

            $signatureId = $this->signatureModel->insert($signatureData);

            if (!$signatureId) {
                return $this->fail('Gagal menyimpan data tanda tangan');
            }

            // Create signed PDF
            $signedPdfPath = $this->createTrueEmbeddedPDF($document, $qrCodePath, $signatureData);

            // Update document status
            $updateData = ['is_signed' => true];
            if ($signedPdfPath) {
                $updateData['signed_path'] = $signedPdfPath;
            }
            $this->documentModel->update($json['document_id'], $updateData);

            // Log activity
            $this->auditLogModel->logActivity(
                'sign',
                $userId,
                $json['document_id'],
                'Document signed by: ' . $json['signer_name'],
                $this->request
            );

            return $this->respondCreated([
                'status' => 'success',
                'data' => [
                    'signature_id' => $signatureId,
                    'verification_code' => $verificationCode,
                    'qr_code_url' => $qrCodePath ? base_url($qrCodePath) : null,
                    'verify_url' => base_url('verify/' . $verificationCode),
                    'signed_pdf_path' => $signedPdfPath
                ],
                'message' => 'Dokumen berhasil ditandatangani dengan QR Code embedded di dalam PDF'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Sign document error: ' . $e->getMessage());
            return $this->fail('Gagal menandatangani dokumen: ' . $e->getMessage());
        }
    }

    // Add new method for admin statistics
    public function getAdminStats()
    {
        try {
            $userRole = session()->get('role');

            // Only admin can access statistics
            if ($userRole !== 'admin') {
                return $this->fail('Akses ditolak. Fitur ini hanya untuk admin.', 403);
            }

            $systemStats = $this->documentModel->getSystemStats();
            $userStats = $this->documentModel->getUserStats();

            return $this->respond([
                'status' => 'success',
                'data' => [
                    'system_stats' => $systemStats,
                    'user_stats' => $userStats
                ],
                'message' => 'Statistik berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return $this->fail('Gagal mengambil statistik: ' . $e->getMessage());
        }
    }

    // Perbaikan pada method createTrueEmbeddedPDF() - cleanup on failure
    private function createTrueEmbeddedPDF($document, $qrCodePath, $signatureData)
    {
        try {
            // Create signed PDF directory if not exists
            $signedPath = WRITEPATH . 'uploads/signed/';
            if (!is_dir($signedPath)) {
                mkdir($signedPath, 0755, true);
            }

            // Generate signed filename
            $signedFileName = 'signed_' . time() . '_' . basename($document['filename']);
            $signedFilePath = $signedPath . $signedFileName;

            // Convert relative path to absolute path for original file
            $originalAbsolutePath = ROOTPATH . $document['original_path'];

            // Normalize paths untuk Windows
            $originalAbsolutePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $originalAbsolutePath);
            $signedFilePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $signedFilePath);
            $qrAbsolutePath = ROOTPATH . $qrCodePath;
            $qrAbsolutePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $qrAbsolutePath);

            // Debug logs
            log_message('info', 'FPDI - Original file: ' . $originalAbsolutePath);
            log_message('info', 'FPDI - Output file: ' . $signedFilePath);
            log_message('info', 'FPDI - QR code file: ' . $qrAbsolutePath);

            // Check if original file exists
            if (!file_exists($originalAbsolutePath)) {
                log_message('error', 'Original file not found: ' . $originalAbsolutePath);
                return null;
            }

            // Check if QR code exists
            if (!file_exists($qrAbsolutePath)) {
                log_message('error', 'QR code file not found: ' . $qrAbsolutePath);
                // Fallback: create signed PDF without QR
                if (copy($originalAbsolutePath, $signedFilePath)) {
                    return 'writable/uploads/signed/' . $signedFileName;
                }
                return null;
            }

            // Use FPDI to create TRUE embedded PDF
            $result = $this->embedQRWithFPDI($originalAbsolutePath, $qrAbsolutePath, $signedFilePath, $signatureData);

            if ($result) {
                log_message('info', 'TRUE embedded PDF created successfully with FPDI: ' . $signedFilePath);
                return 'writable/uploads/signed/' . $signedFileName;
            } else {
                log_message('error', 'FPDI embedding failed, using fallback');

                // CLEANUP: Remove failed signed file
                if (file_exists($signedFilePath)) {
                    unlink($signedFilePath);
                }

                // Fallback: simple copy
                if (copy($originalAbsolutePath, $signedFilePath)) {
                    log_message('info', 'Fallback: Signed file created without QR embedding: ' . $signedFilePath);
                    return 'writable/uploads/signed/' . $signedFileName;
                }
                return null;
            }
        } catch (\Exception $e) {
            log_message('error', 'Create TRUE embedded PDF failed: ' . $e->getMessage());

            // CLEANUP: Remove failed signed file
            if (isset($signedFilePath) && file_exists($signedFilePath)) {
                unlink($signedFilePath);
            }

            return null;
        }
    }

    private function getQRPositionConfig()
    {
        // Configuration for QR positioning - kept for future enhancement
        return [
            'position' => 'bottom_right', // Fixed position as requested
            'size' => 20,                 // 20mm QR size
            'margin' => 10,               // 10mm from edge
            'info_width' => 50,           // 50mm info box width
            'info_gap' => 5               // 5mm gap between info and QR
        ];
    }

    private function embedQRWithFPDI($originalPdfPath, $qrCodePath, $outputPath, $signatureData)
    {
        try {
            // Initialize FPDI (extends TCPDF)
            $pdf = new Fpdi();

            // Set document information
            $pdf->SetCreator('Digital Signature System with FPDI');
            $pdf->SetAuthor($signatureData['signer_name']);
            $pdf->SetTitle('Digitally Signed Document');
            $pdf->SetSubject('Document with Embedded Digital Signature QR Code');

            // Remove default header/footer
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            // Set margins
            $pdf->SetMargins(0, 0, 0);
            $pdf->SetAutoPageBreak(false);

            // Import existing PDF pages
            $pageCount = $pdf->setSourceFile($originalPdfPath);
            log_message('info', 'FPDI - Total pages in original PDF: ' . $pageCount);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                // Import page
                $templateId = $pdf->importPage($pageNo);

                // Get page dimensions
                $size = $pdf->getTemplateSize($templateId);
                $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';

                // Add new page with same orientation
                $pdf->AddPage($orientation, [$size['width'], $size['height']]);

                // Use the imported page as template
                $pdf->useTemplate($templateId, 0, 0, $size['width'], $size['height']);

                // Add QR code and signature info on the last page only
                if ($pageNo == $pageCount) {
                    $this->addQROverlayToPage($pdf, $qrCodePath, $signatureData, $size);
                }

                log_message('info', 'FPDI - Processed page ' . $pageNo . ' (' . $orientation . ', ' . $size['width'] . 'x' . $size['height'] . ')');
            }

            // Save the PDF
            $pdf->Output($outputPath, 'F');

            log_message('info', 'FPDI - PDF with true embedded QR code created successfully');
            return true;
        } catch (\Exception $e) {
            log_message('error', 'FPDI QR embedding failed: ' . $e->getMessage());
            log_message('error', 'FPDI Error details: ' . $e->getTraceAsString());
            return false;
        }
    }

    private function addQROverlayToPage($pdf, $qrCodePath, $signatureData, $pageSize)
    {
        try {
            // Get page dimensions in mm directly from TCPDF
            $pageWidth = $pdf->getPageWidth();
            $pageHeight = $pdf->getPageHeight();

            log_message('info', 'TCPDF Page dimensions: ' . $pageWidth . 'mm x ' . $pageHeight . 'mm');
            log_message('info', 'Original FPDI page size: ' . $pageSize['width'] . ' x ' . $pageSize['height'] . ' points');

            // QR Code configuration
            $qrSize = 15; // Reduce QR size to 15mm (was 20mm)
            $edgeMargin = 8; // Increase margin to 8mm (was 5mm)

            // Calculate absolute bottom-right position with safety margins
            $qrX = $pageWidth - $qrSize - $edgeMargin;
            $qrY = $pageHeight - $qrSize - $edgeMargin;

            // Info box configuration - make it smaller
            $infoWidth = 35; // Reduce width to 35mm (was 45mm)
            $infoHeight = $qrSize; // Same height as QR
            $infoGap = 2; // Reduce gap to 2mm (was 3mm)
            $infoX = $qrX - $infoWidth - $infoGap;
            $infoY = $qrY;

            // SAFETY CHECK: Detect page type and adjust accordingly
            if ($pageWidth >= 200 && $pageWidth <= 220 && $pageHeight >= 280 && $pageHeight <= 300) {
                // A4 Portrait detected (210mm x 297mm)
                $qrX = 185; // 25mm from right edge
                $qrY = 270; // 27mm from bottom
                $infoX = 145; // 185 - 35 - 5 = 145mm
                $infoY = 270;
                log_message('info', 'A4 Portrait detected - using absolute coordinates');
            } elseif ($pageHeight >= 200 && $pageHeight <= 220 && $pageWidth >= 280 && $pageWidth <= 300) {
                // A4 Landscape detected (297mm x 210mm)
                $qrX = 270; // 27mm from right edge
                $qrY = 185; // 25mm from bottom  
                $infoX = 230; // 270 - 35 - 5 = 230mm
                $infoY = 185;
                log_message('info', 'A4 Landscape detected - using absolute coordinates');
            } else {
                // Other page sizes - use calculated positions with extra safety
                $safetyMargin = 15; // 15mm safety margin for unknown page sizes

                if ($qrX < $safetyMargin) {
                    $qrX = $pageWidth - $qrSize - $safetyMargin;
                }
                if ($qrY < $safetyMargin) {
                    $qrY = $pageHeight - $qrSize - $safetyMargin;
                }
                if ($infoX < 5) {
                    $infoX = 5;
                    $infoWidth = $qrX - $infoX - $infoGap;
                }

                log_message('info', 'Custom page size - using calculated coordinates with safety');
            }

            log_message('info', 'FINAL QR position: X=' . $qrX . ', Y=' . $qrY . ', Size=' . $qrSize);
            log_message('info', 'FINAL Info box position: X=' . $infoX . ', Y=' . $infoY . ', Width=' . $infoWidth);

            // Add QR code image at FINAL position
            if (file_exists($qrCodePath)) {
                $pdf->Image($qrCodePath, $qrX, $qrY, $qrSize, $qrSize, 'PNG');
                log_message('info', 'QR code placed successfully');
            } else {
                log_message('error', 'QR code file not found: ' . $qrCodePath);
                return;
            }

            // Add signature information box with smaller content
            // Background box for signature info (semi-transparent)
            $pdf->SetFillColor(255, 255, 255); // White background
            $pdf->SetAlpha(0.9); // More opaque
            $pdf->Rect($infoX, $infoY, $infoWidth, $infoHeight, 'F');
            $pdf->SetAlpha(1); // Reset transparency

            // Border for info box
            $pdf->SetDrawColor(200, 200, 200); // Light gray border
            $pdf->Rect($infoX, $infoY, $infoWidth, $infoHeight, 'D');

            // Add signature text with smaller font sizes
            $pdf->SetFont('helvetica', 'B', 6); // Reduce font size
            $pdf->SetTextColor(0, 0, 0); // Black text
            $pdf->SetXY($infoX + 1, $infoY + 1);
            $pdf->Cell($infoWidth - 2, 2.5, 'Digitally Signed:', 0, 1, 'L');

            $pdf->SetFont('helvetica', 'B', 7);
            $pdf->SetXY($infoX + 1, $infoY + 3.5);
            $signerName = strlen($signatureData['signer_name']) > 15 ?
                substr($signatureData['signer_name'], 0, 12) . '...' :
                $signatureData['signer_name'];
            $pdf->Cell($infoWidth - 2, 2.5, $signerName, 0, 1, 'L');

            $pdf->SetFont('helvetica', '', 5);
            $pdf->SetXY($infoX + 1, $infoY + 6);
            $position = strlen($signatureData['signer_position']) > 18 ?
                substr($signatureData['signer_position'], 0, 15) . '...' :
                $signatureData['signer_position'];
            $pdf->Cell($infoWidth - 2, 2, $position, 0, 1, 'L');

            $pdf->SetXY($infoX + 1, $infoY + 8);
            $pdf->Cell($infoWidth - 2, 2, date('d/m/Y H:i', strtotime($signatureData['signature_date'])), 0, 1, 'L');

            $pdf->SetFont('helvetica', 'B', 4);
            $pdf->SetTextColor(220, 53, 69); // Red color for verification code
            $pdf->SetXY($infoX + 1, $infoY + 10.5);
            $pdf->Cell($infoWidth - 2, 2, 'Code: ' . $signatureData['verification_code'], 0, 1, 'L');

            $pdf->SetFont('helvetica', '', 4);
            $pdf->SetTextColor(100, 100, 100); // Gray color
            $pdf->SetXY($infoX + 1, $infoY + 12.5);
            $pdf->Cell($infoWidth - 2, 1.5, 'Scan QR to verify', 0, 1, 'L');

            log_message('info', 'QR overlay completed successfully for page type: ' . $pageWidth . 'x' . $pageHeight);
        } catch (\Exception $e) {
            log_message('error', 'Failed to add QR overlay: ' . $e->getMessage());
        }
    }
    public function publicDownload($verificationCode = null)
    {
        try {
            if (!$verificationCode) {
                return $this->fail('Kode verifikasi tidak boleh kosong');
            }

            // Get signature by verification code
            $signature = $this->signatureModel->getByVerificationCode($verificationCode);

            if (!$signature) {
                return $this->failNotFound('Kode verifikasi tidak valid');
            }

            // Get document
            $document = $this->documentModel->find($signature['document_id']);

            if (!$document) {
                return $this->failNotFound('Dokumen tidak ditemukan');
            }

            // Only allow download of signed documents
            if (!$document['is_signed']) {
                return $this->fail('Dokumen belum ditandatangani');
            }

            // Use signed version if available, otherwise original
            $relativePath = $document['signed_path'] ?: $document['original_path'];
            $absolutePath = ROOTPATH . $relativePath;
            $absolutePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $absolutePath);

            if (!file_exists($absolutePath)) {
                return $this->failNotFound('File tidak ditemukan');
            }

            $downloadName = 'verified_' . ($document['original_name'] ?: 'document.pdf');

            log_message('info', 'Public download via verification - Code: ' . $verificationCode . ', Document: ' . $document['id']);

            // Download headers
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $downloadName . '"');
            header('Content-Length: ' . filesize($absolutePath));
            header('Cache-Control: must-revalidate');
            header('Pragma: public');

            readfile($absolutePath);
            exit;
        } catch (\Exception $e) {
            log_message('error', 'Public download error: ' . $e->getMessage());
            return $this->fail('Gagal download: ' . $e->getMessage());
        }
    }

    // Removed unused smart positioning methods - using fixed bottom-right position

    // GET /api/documents/verify/{code}
    public function verify($code = null)
    {
        try {
            if (!$code) {
                return $this->fail('Kode verifikasi tidak boleh kosong');
            }

            $signature = $this->signatureModel->getByVerificationCode($code);

            if (!$signature) {
                return $this->failNotFound('Kode verifikasi tidak valid');
            }

            return $this->respond([
                'status' => 'success',
                'data' => $signature,
                'message' => 'Dokumen berhasil diverifikasi'
            ]);
        } catch (\Exception $e) {
            return $this->fail('Gagal verifikasi dokumen: ' . $e->getMessage());
        }
    }

    // GET /api/documents/download/{id} - Only owner can download
    public function download($id = null)
    {
        try {
            $userId = session()->get('user_id');
            $userRole = session()->get('role');

            if (!$id) {
                return $this->fail('ID dokumen tidak boleh kosong');
            }

            $document = $this->documentModel->find($id);

            if (!$document) {
                return $this->failNotFound('Dokumen tidak ditemukan');
            }

            // Check ownership (only owner or admin can download)
            if ($userRole !== 'admin' && $document['user_id'] != $userId) {
                return $this->fail('Anda tidak memiliki akses untuk mengunduh dokumen ini', 403);
            }

            $relativePath = $document['is_signed'] && $document['signed_path']
                ? $document['signed_path']
                : $document['original_path'];

            $absolutePath = ROOTPATH . $relativePath;
            $absolutePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $absolutePath);

            if (!file_exists($absolutePath)) {
                return $this->failNotFound('File tidak ditemukan');
            }

            $downloadName = $document['original_name'] ?: 'document_' . $id . '.pdf';
            if ($document['is_signed']) {
                $downloadName = 'signed_' . $downloadName;
            }

            // Log download activity
            $this->auditLogModel->logActivity(
                $userId,
                $id,
                'download',
                'Document downloaded: ' . $downloadName,
                $this->request
            );

            // Simple download with proper headers
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $downloadName . '"');
            header('Content-Length: ' . filesize($absolutePath));
            header('Cache-Control: must-revalidate');
            header('Pragma: public');

            readfile($absolutePath);
            exit;
        } catch (\Exception $e) {
            return $this->fail('Gagal download: ' . $e->getMessage());
        }
    }

    // DELETE /api/documents/{id} - Only owner can delete
    public function delete($id = null)
    {
        try {
            $userId = session()->get('user_id');
            $userRole = session()->get('role');

            if (!$id) {
                return $this->fail('ID dokumen tidak boleh kosong');
            }

            $document = $this->documentModel->find($id);

            if (!$document) {
                return $this->failNotFound('Dokumen tidak ditemukan');
            }

            // Check ownership (only owner or admin can delete)
            if ($userRole !== 'admin' && $document['user_id'] != $userId) {
                return $this->fail('Anda tidak memiliki akses untuk menghapus dokumen ini', 403);
            }

            // Convert relative paths to absolute paths
            $originalAbsolutePath = ROOTPATH . $document['original_path'];
            $originalAbsolutePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $originalAbsolutePath);

            // Delete original file
            if (file_exists($originalAbsolutePath)) {
                if (unlink($originalAbsolutePath)) {
                    log_message('info', 'Original file deleted successfully: ' . $originalAbsolutePath);
                } else {
                    log_message('error', 'Failed to delete original file: ' . $originalAbsolutePath);
                }
            }

            // Delete signed file if exists
            if ($document['signed_path']) {
                $signedAbsolutePath = ROOTPATH . $document['signed_path'];
                $signedAbsolutePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $signedAbsolutePath);

                if (file_exists($signedAbsolutePath)) {
                    if (unlink($signedAbsolutePath)) {
                        log_message('info', 'Signed file deleted successfully: ' . $signedAbsolutePath);
                    } else {
                        log_message('error', 'Failed to delete signed file: ' . $signedAbsolutePath);
                    }
                }
            }

            // Delete QR code file if exists
            $signature = $this->signatureModel->where('document_id', $id)->first();
            if ($signature && $signature['qr_code_path']) {
                $qrAbsolutePath = ROOTPATH . $signature['qr_code_path'];
                $qrAbsolutePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $qrAbsolutePath);

                if (file_exists($qrAbsolutePath)) {
                    if (unlink($qrAbsolutePath)) {
                        log_message('info', 'QR code file deleted successfully: ' . $qrAbsolutePath);
                    } else {
                        log_message('error', 'Failed to delete QR code file: ' . $qrAbsolutePath);
                    }
                }
            }

            // Log activity before deletion
            $this->auditLogModel->logActivity(
                $userId,
                $id,
                'delete',
                'Document deleted: ' . ($document['original_name'] ?: $document['filename']),
                $this->request
            );

            // Delete from database (this will also delete related signatures due to CASCADE)
            if ($this->documentModel->delete($id)) {
                log_message('info', 'Document deleted from database successfully: ID ' . $id);

                return $this->respondDeleted([
                    'status' => 'success',
                    'message' => 'Dokumen dan semua file terkait berhasil dihapus'
                ]);
            } else {
                log_message('error', 'Failed to delete document from database: ID ' . $id);
                return $this->fail('Gagal menghapus data dokumen dari database');
            }
        } catch (\Exception $e) {
            log_message('error', 'Delete document error: ' . $e->getMessage());
            return $this->fail('Gagal menghapus dokumen: ' . $e->getMessage());
        }
    }

    private function generateQRCode($verificationCode)
    {
        try {
            // Create QR code directory if not exists
            $qrPath = WRITEPATH . 'uploads/qrcodes/';
            if (!is_dir($qrPath)) {
                mkdir($qrPath, 0755, true);
            }

            $fileName = 'qr_' . $verificationCode . '.png';
            $filePath = $qrPath . $fileName;

            // Generate verification URL
            $verifyUrl = base_url('verify/' . $verificationCode);

            // Try Endroid QR Code first
            if (class_exists('\Endroid\QrCode\QrCode')) {
                try {
                    return $this->generateRealQRCode($verifyUrl, $filePath, $fileName);
                } catch (\Exception $e) {
                    log_message('error', 'Endroid QR failed, using placeholder: ' . $e->getMessage());
                }
            }

            // Fallback to placeholder (always works)
            $this->createQRCodePlaceholder($filePath, $verifyUrl);
            return 'writable/uploads/qrcodes/' . $fileName;
        } catch (\Exception $e) {
            log_message('error', 'QR Code generation failed: ' . $e->getMessage());
            return false;
        }
    }

    private function generateRealQRCode($verifyUrl, $filePath, $fileName)
    {
        try {
            // Create QrCode object with data
            $qrCode = new QrCode($verifyUrl);

            // Try different API versions for compatibility
            try {
                // v5.x/v6.x API
                $qrCode->setSize(200);
                $qrCode->setMargin(10);
            } catch (\Exception $e) {
                // v4.x API or different method
                log_message('info', 'Using alternative QR code configuration');
            }

            $writer = new PngWriter();

            // Try different write methods for compatibility
            try {
                $result = $writer->write($qrCode);
                $result->saveToFile($filePath);
            } catch (\Exception $e) {
                // Alternative method
                $qrCodeString = $writer->write($qrCode)->getString();
                file_put_contents($filePath, $qrCodeString);
            }

            log_message('info', 'Real QR code generated successfully: ' . $filePath);
            return 'writable/uploads/qrcodes/' . $fileName;
        } catch (\Exception $e) {
            log_message('error', 'Real QR Code generation failed: ' . $e->getMessage());

            // Fallback to placeholder
            $this->createQRCodePlaceholder($filePath, $verifyUrl);
            return 'writable/uploads/qrcodes/' . $fileName;
        }
    }

    private function createQRCodePlaceholder($filePath, $content)
    {
        // Create a simple QR code placeholder image
        $width = 200;
        $height = 200;

        $image = imagecreate($width, $height);
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);

        // Fill background
        imagefill($image, 0, 0, $white);

        // Draw QR pattern (simplified)
        for ($i = 0; $i < 10; $i++) {
            for ($j = 0; $j < 10; $j++) {
                if (($i + $j) % 2 == 0) {
                    imagefilledrectangle($image, $i * 20, $j * 20, ($i + 1) * 20, ($j + 1) * 20, $black);
                }
            }
        }

        imagepng($image, $filePath);
        imagedestroy($image);
    }
    // GET /api/documents/qr/{code} - Serve QR code image
    public function getQRCode($code = null)
    {
        try {
            if (!$code) {
                return $this->failNotFound('Kode verifikasi tidak ditemukan');
            }

            // Get signature by verification code
            $signature = $this->signatureModel->getByVerificationCode($code);

            if (!$signature) {
                return $this->failNotFound('Kode verifikasi tidak valid');
            }

            // Get QR code file path
            $qrCodePath = $signature['qr_code_path'];
            if (!$qrCodePath) {
                return $this->failNotFound('QR code tidak tersedia');
            }

            // Convert to absolute path
            $qrAbsolutePath = ROOTPATH . $qrCodePath;
            $qrAbsolutePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $qrAbsolutePath);

            // Check if QR file exists
            if (!file_exists($qrAbsolutePath)) {
                log_message('error', 'QR code file not found: ' . $qrAbsolutePath);
                return $this->failNotFound('File QR code tidak ditemukan');
            }

            // Serve the QR code image
            $mimeType = 'image/png';
            $fileSize = filesize($qrAbsolutePath);

            // Set headers for image response
            header('Content-Type: ' . $mimeType);
            header('Content-Length: ' . $fileSize);
            header('Cache-Control: public, max-age=3600'); // Cache for 1 hour
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($qrAbsolutePath)) . ' GMT');

            // Output the file
            readfile($qrAbsolutePath);
            exit;
        } catch (\Exception $e) {
            log_message('error', 'Get QR code error: ' . $e->getMessage());
            return $this->failServerError('Gagal mengambil QR code: ' . $e->getMessage());
        }
    }

    // Add new method for admin activity logs
    public function getActivityLogs()
    {
        try {
            $userRole = session()->get('role');

            // Only admin can access activity logs
            if ($userRole !== 'admin') {
                return $this->fail('Akses ditolak. Fitur ini hanya untuk admin.', 403);
            }

            $logs = $this->auditLogModel->getAuditLogs(50, 0); // Get last 50 activities

            return $this->respond([
                'status' => 'success',
                'data' => $logs,
                'message' => 'Activity logs berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return $this->fail('Gagal mengambil activity logs: ' . $e->getMessage());
        }
    }
}
