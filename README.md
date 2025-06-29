# Digital Signature System

## 📋 Overview
A comprehensive multi-user digital signature system built with CodeIgniter 4. This system allows users to upload PDF documents, add digital signatures with QR codes, and provides public verification capabilities.

## ✨ Features

### Core Features
- **Multi-user Authentication** - Secure login/register system with session management
- **Role-based Authorization** - User and Admin roles with different access levels
- **Document Management** - Upload, sign, download, and delete PDF documents
- **Digital Signature** - Add signatures with embedded QR codes for verification
- **Public Verification** - QR code scanning for document authenticity without login
- **Real-time Dashboard** - Admin monitoring with statistics and user management

### Advanced Features
- **Audit Logging** - Complete activity tracking for security and compliance
- **File Cleanup** - Automatic deletion of orphaned files
- **Document Ownership** - Users can only access their own documents
- **Public Download** - Secure document access via verification codes
- **Interactive UI** - Modern responsive design with drag-and-drop upload

## 🛠️ Technical Stack

- **Backend**: PHP 8.1+ with CodeIgniter 4
- **Database**: MySQL 5.7+
- **Frontend**: Vanilla JavaScript, HTML5, CSS3
- **PDF Processing**: TCPDF, FPDI for PDF manipulation
- **QR Code**: PHP QR Code library
- **Authentication**: Session-based with password hashing

## 📦 Requirements

- PHP 8.1 or higher
- MySQL 5.7 or higher  
- Composer
- Apache/Nginx web server
- PHP Extensions: mysqli, gd, mbstring, json, curl

## 🚀 Installation

### 1. Clone Repository
```bash
git clone https://github.com/yourusername/digital-signature-system.git
cd digital-signature-system
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Environment Configuration
```bash
cp .env.example .env
```

Edit `.env` file with your database credentials:
```env
database.default.hostname = localhost
database.default.database = digital_signature
database.default.username = your_username
database.default.password = your_password
database.default.DBDriver = MySQLi

app.baseURL = 'http://your-domain.com/'
```

### 4. Database Setup
- Create a MySQL database named `digital_signature`
- Import the database structure (SQL file included in project)
- Or run migrations if available

### 5. File Permissions
```bash
chmod -R 755 writable/
chmod -R 755 public/uploads/
```

### 6. Web Server Configuration
Point your web server document root to the `public/` folder.

## 👥 Default Users

The system comes with default test accounts:

**Admin Account:**
- Username: `admin`
- Password: `password`

**User Account:**
- Username: `user1`
- Password: `password`

## 📖 Usage

### For Users
1. **Register/Login** - Create account or login with existing credentials
2. **Upload Document** - Drag and drop or select PDF files to upload
3. **Add Signature** - Fill signature details and generate signed document
4. **Download/Share** - Download signed documents or share verification codes
5. **Verify Documents** - Use QR codes or verification codes to check authenticity

### For Administrators
1. **Access Dashboard** - Admins are automatically redirected to admin panel
2. **Monitor Activity** - View real-time statistics and user activities
3. **Manage Users** - Monitor user activities and document counts
4. **Verify Documents** - Directly verify document codes from dashboard
5. **View Logs** - Access complete audit trails and system activities

## 🔧 API Endpoints

### Authentication
- `POST /api/auth/login` - User login
- `POST /api/auth/register` - User registration
- `POST /api/auth/logout` - User logout
- `GET /api/auth/me` - Get current user info

### Documents
- `POST /api/documents/upload` - Upload PDF document
- `GET /api/documents/list` - Get user's documents
- `POST /api/documents/sign` - Add digital signature
- `GET /api/documents/verify/{code}` - Verify document
- `GET /api/documents/public-download/{code}` - Download via verification code

### Admin
- `GET /api/admin/stats` - Get dashboard statistics
- `GET /api/admin/users` - Get users list with activity
- `GET /api/admin/documents` - Get all documents (admin only)

## 📁 Project Structure

```
digital-signature-system/
├── app/
│   ├── Controllers/
│   │   ├── Home.php                 # Main system controller
│   │   └── Api/
│   │       ├── AuthController.php   # Authentication API
│   │       └── DocumentController.php # Document management API
│   ├── Models/
│   │   ├── UserModel.php           # User management
│   │   ├── DocumentModel.php       # Document operations
│   │   └── AuditLogModel.php       # Activity logging
│   └── Views/
│       ├── home.php                # Main application UI
│       ├── admin_dashboard.php     # Admin panel
│       ├── auth.php                # Login/register page
│       └── verify_success.php      # Verification result page
├── public/
│   ├── index.php                   # Entry point
│   └── uploads/                    # Document storage
└── writable/                       # Logs and cache
```

## 🔒 Security Features

- **Password Hashing** - Secure bcrypt password storage
- **Session Management** - Secure PHP session handling
- **File Validation** - PDF header verification and size limits
- **SQL Injection Protection** - Parameterized queries
- **XSS Prevention** - Input sanitization and output escaping
- **CSRF Protection** - Built-in CodeIgniter CSRF tokens
- **Role-based Access** - Proper authorization checks

## 🚀 Deployment

### Shared Hosting (Hostinger, cPanel)
1. Upload files to public_html or domain folder
2. Set document root to `public/` folder
3. Import database and configure `.env`
4. Set proper file permissions

### VPS/Dedicated Server
1. Configure Apache/Nginx virtual host
2. Point document root to `public/` folder
3. Configure SSL certificate
4. Set up automated backups

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🐛 Known Issues

- Activity timestamps show timezone differences (not critical)
- JavaScript could be modularized for better maintainability
- Success message timing could be improved

## 🔮 Future Enhancements

- Real-time notifications system
- Document analytics with charts
- User profile management
- Export functionality for admin
- Mobile app development
- Multi-language support

## 📞 Support

If you encounter any issues or have questions, please create an issue in the GitHub repository.

## 🏆 Status

**Current Status**: Production Ready ✅
- Core system: 100% Complete
- Admin dashboard: 100% Complete  
- UX improvements: 100% Complete
- Ready for deployment and advanced features

---

**Developed with ❤️ using CodeIgniter 4**