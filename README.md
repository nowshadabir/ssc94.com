# 🤝 SSC Batch '94 Alumni Platform

A modern, high-performance community platform designed for the **SSC Batch 1994** alumni to stay connected, organize events, manage donations, and help each other.

---

## 🌟 Key Features

- **Profile Management**: Customizable member profiles with secure login and data privacy.
- **Friend Discovery**: Search for old batchmates by name, school, location, or profession.
- **Blood Donor Network**: A dedicated module to find blood donors within the community during emergencies.
- **Event & Reunion Management**: 
  - Host and register for gatherings ("Adda").
  - Digital ticketing with QR code verification.
  - Interactive maps for event venues.
- **Secure Payments**: Integrated with **bKash** and **Rupantorpay** for membership fees and event registrations.
- **Responsive Design**: Premium dark-mode aesthetics using **Tailwind CSS**, optimized for mobile and desktop.

---

## 🛠️ Technology Stack

- **Backend**: PHP 8.x (Vanilla + PDO for SQL Safety)
- **Database**: MySQL 8.0+
- **Frontend**: HTML5, Tailwind CSS, Lucide Icons, JavaScript (ES6)
- **APIs**: bKash Tokenized API, OpenStreetMap (Leaflet.js)
- **Environment**: XAMPP / Apache / Nginx (CPanel compatible)

---

## 🚀 Installation & Setup

### 1. Prerequisites
- PHP 8.0 or higher.
- MySQL/MariaDB server.
- Composer (Optional, for future dependencies).

### 2. Clone the Repository
```bash
git clone https://github.com/nowshadabir/ssc94.com.git
cd ssc94.com
```

### 3. Database Initialization
1. Create a database named `ssc94_db`.
2. Import the schema file located at `database/schema.sql`:
```bash
mysql -u your_user -p ssc94_db < database/schema.sql
```

### 4. Configuration
1. Copy the example environment file:
   ```bash
   cp .env.example .env
   ```
2. Open `.env` and fill in your details:
   - Database credentials (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`).
   - bKash API credentials (for payment processing).
   - SMTP details (for password resets and verification emails).

3. Update **Payment Gateway Settings** in the `payment_gateway_settings` table in your database (to replace localhost URLs with your live domain).

### 5. Deployment
- Upload the files to your server's root directory.
- Ensure the `assets/uploads/` directory is writable by the web server (`chmod 755` or `777` depending on environment).
- Point your domain to the library root.

---

## 🔒 Security Notes
- All database queries are executed via **PDO Prepared Statements** to prevent SQL injection.
- Passwords are encrypted using `password_hash()` (bcrypt).
- Sensitivity files like `.env` and `logs/` are hidden via `.gitignore`.
- **Note**: Ensure `ini_set('display_errors', 0)` is active on production (handled in `config/config.php`).

---

## 🤝 Contributing
Since this is a public repository, we welcome contributions!
1. Fork the project.
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`).
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`).
4. Push to the branch (`git push origin feature/AmazingFeature`).
5. Open a Pull Request.

---

## 📜 License
*This platform is designed exclusively for the SSC Batch '94 community.*

**&copy; 2026 SSC Batch '94 Association.**  
*Friends For Friends.*  
[ssc94.com](https://ssc94.com)
