# 🚨 Raksha – A.I based Smart Women Safety System

Raksha is an a.i based smart women safety platform designed to provide quick emergency support through SOS alerts, live location, and complaint management. The system allows users to send instant SOS requests that are monitored by administrators for rapid response.

---

## 📌 Short Description

Raksha helps users, especially women, to stay safe by enabling real-time emergency alerts, complaint reporting, and admin monitoring. It provides a secure and user-friendly interface for both users and administrators.

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