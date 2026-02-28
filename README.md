## 📌 Project Title

*Raksha - AI Based Smart Women Safety System*

---

## 👨‍💻 Team Members

-   *Pratik Wani* 
-   *Moiz Shaikh*

---

## ❗ Problem Statement

Women safety is a critical issue in modern society. In emergency
situations, victims may not be able to call for help quickly due to
panic, fear, or lack of access to resources. Traditional systems often
lack real-time tracking, immediate response, and intelligent assistance.

There is a need for a smart, AI-powered system that can provide instant
help, automate complaint generation, and alert authorities with minimal
user interaction.

---

## 💡 Solution Approach

Raksha is designed as a smart web-based safety system that integrates
Artificial Intelligence, Geolocation, and Real-Time Communication to
provide immediate assistance.

The system enables users to send SOS alerts, share live location, find
nearby police stations, and generate complaints automatically. Admins
can monitor SOS alerts in real-time and take quick action.

---

## 🛠 Tech Stack

### Frontend
- HTML5
- CSS3
- JavaScript (ES6)

### Backend
- PHP (Core PHP)

### Database
- MySQL Database

### ML Model
- Tensorflow js

### Libraries
- Leaflet
- Chart.js
- Swiper.js

### Authentication
- Firebase
- Cloudflare

### APIs
- Geolocation API
- Web Speech API

---

## ⚙️ Installation Steps (Docker Setup)

1. Clone the repository


git clone https://github.com/your-username/raksha.git
cd raksha


2. Install Docker Desktop

Download and install Docker from:
https://www.docker.com/products/docker-desktop/

Make sure Docker is running before proceeding.

---

3. Configure Database Connection

Open config/db.php and update:


$host = "raksha_db";
$user = "raksha_user";
$password = "raksha_pass";
$database = "raksha_ai";


---

4. Import Database (First Time Only)

Open phpMyAdmin:


http://localhost:8081


Login:
- Username: root
- Password: root

Steps:
- Create database raksha_ai
- Import raksha.sql

---

5. Start Docker Containers


docker-compose up --build


This will start:
- PHP + Apache server
- MySQL database
- phpMyAdmin

---

## ▶️ How to Run

1. Start the containers


docker-compose up


2. Open the application


http://localhost:8080


3. Use the system

- Register/Login as user
- Send SOS alert
- View Admin Panel

4. Open Admin Panel


http://localhost:8080/admin


5. Open Database (phpMyAdmin)


http://localhost:8081


---

## ✨ Features  

### 👩 User Features  
- User Registration & Login  
- Shake-to-SOS Detection  
- One Click SOS  
- Share Live Location  
- Nearby Police Stations Finder  
- Report Complaints  
- View Complaint Status  
- Secure Logout System  

### 🤖 AI Features  
- AI Powered Heatmap for Crime Analysis  
- Safest Route Navigation (Shortest Path)  
- Personal Chatbot for Assistance  
- F.I.R Generator

### 🛡 Admin Features  
- Admin Login System  
- View Live SOS Alerts  
- SOS Map View (Real-time Tracking)  
- Alert Sound Notification (Siren + Vibration)  
- Monitor Users  
- Manage Complaints  
- Dashboard Analytics  

### 🔐 Security Features  
- Password Hashing  
- Session-Based Authentication  
- XSS Protection  

---

## 🚀 Future Scope
- Real-time Video Streaming during SOS
- Wearable Device Integration (Smart Band)
- Offline Emergency SMS System

---

## ⭐ Support
If you found this project helpful, consider giving it a star ⭐ on GitHub!