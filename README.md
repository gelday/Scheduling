# 📘 Subject Scheduling API

This is a custom **PHP-based Web API** designed for scheduling purposes.  
By providing a specific **time query**, the API responds with the **scheduled subject, assigned teacher, and time slot**.

---

## ✨ Features
-Time-based Schedule Lookup
-Returns Complete Schedule Information

---

## ⚙️ Installation & Setup Instructions

### ✅ Requirements
- PHP (7.x or 8.x)  
- Local server like **XAMPP**  
- Web browser or tool like **Postman** to test the API  

### 📥 Steps
1. **Clone or Download the Repository**
   ```bash
   git clone https://github.com/your-username/SubjectAPI.git
2. Place it in Your Local Server Directory
Example for XAMPP:
C:/xampp/htdocs/SubjectAPI/


3. Start Apache in XAMPP Control Panel.

4. Access the API via Browser or Postman.
Example:
http://localhost/SubjectAPI/api.php?time=7:30

📌 Example Request and Response
🔹 Request
GET http://localhost/SubjectAPI/api.php?time=7:30

🔹 Response (JSON)
{
  "subject": "EVD",
  "teacher": "Mr. Jade Louis Cabucos",
  "time": "07:30 - 09:00"
}

🌐 Repository

GitHub Repository

