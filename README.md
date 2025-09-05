#Schedule API Documentation

This API allows you to manage a schedule stored in a JSON file (schedule.json). The schedule consists of time slots, each having a subject and a corresponding teacher.

## ✨ Features
The Schedule API allows you to view the schedule or a specific time slot using the GET method, add a new schedule entry with a subject and teacher using the POST method, and remove a schedule entry by its time using the DELETE method.

---

## ⚙️ Installation & Setup Instructions

### ✅ Requirements
- PHP (7.x or 8.x)  
- Local server like **XAMPP**  
- Web browser or tool like **Postman** to test the API  

### 📥 Steps
1. **Clone or Download the Repository**
   ```bash
   git clone https://github.com/your-username/SIA.git
2. Place it in Your Local Server Directory
Example for XAMPP:
C:/xampp/htdocs/SIA/schedule_api.php


3. Start Apache in XAMPP Control Panel.

4. Access the API via Browser or Postman.
   Example:
   http://localhost/SIA/schedule_api.php?time=07:30

📌 Example Request and Response
   🔹 Request
      GET http://localhost/SIA/schedule_api.php?time=07:30

🔹 Response (JSON)
{
  "subject": "EVD",
  "teacher": "Mr. Jade Louis Cabucos",
  "time": "07:30 - 09:00"
}


🔹 Request
      POST http://localhost/SIA/schedule_api.php?time=07:30

   🔹 Body

   {
    "time": "7:30",
    "end": "9:30",
    "subject": "IT EVD 31",
    "teacher": "Mr.Cabucos"
}

🔹 Response (JSON)
{
    "time": "7:30",
    "end": "9:30",
    "subject": "IT EVD 31",
    "teacher": "Mr.Cabucos"
}

🔹 Request
      DELETE http://localhost/SIA/schedule_api.php?time=07:30

 🔹 Body
    {
  "time": "07:30"
}


🔹 Response (JSON)
{
    "message": "Schedule deleted for time 07:30"
}
