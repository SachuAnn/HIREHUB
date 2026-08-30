 HireHub

HireHub is a web-based job recruitment platform developed to connect job seekers, employers, and administrators in a single system. Candidates can search and apply for jobs, employers can post and manage job opportunities, and administrators can manage users and reports.

 Features
 Candidate
- Candidate registration and login
- Candidate dashboard
- Search and browse job opportunities
- View job details
- Apply for jobs
- Upload and manage resumes
- Track job applications
- Create and update profile
- Chat with employers
- Send and receive messages
- Submit reviews
- View employer reviews
- Report spam

 Employer

- Employer registration and login
- Employer dashboard
- Create and post job vacancies
- Edit and delete job postings
- View posted jobs
- View applicants
- Manage applications
- Invite candidates for interviews
- View candidate profiles
- Chat and communicate with candidates
- Send and receive messages
- View and manage reviews

 Admin
- Admin login
- Admin dashboard
- Manage users
- Manage spam reports
- Monitor the recruitment platform

 Technologies Used

PHP – Backend development
HTML5– Web page structure
CSS3 – Styling and responsive design
JavaScript – Interactive functionality
MySQL – Database management

 Project Structure
HIREHUB/
│
├── admin/
│   ├── admin_dashboard.php
│   ├── admin_login.php
│   ├── logout.php
│   ├── manage_users.php
│   └── spam_reports.php
│
├── assets/
│   └── bg-video.mp4
│
├── candidate/
│   ├── resumes/
│   ├── apply_job.php
│   ├── candidate_dashboard.php
│   ├── chat.php
│   ├── create_job_portal.php
│   ├── logout.php
│   ├── my_applications.php
│   ├── report_spam.php
│   ├── search_jobs.php
│   ├── send_message.php
│   ├── submit_review.php
│   ├── update_profile.php
│   ├── upload_resume.php
│   └── view_reviews_candidate.php
│
├── employer/
│   ├── chat.php
│   ├── delete_job.php
│   ├── edit_job.php
│   ├── employer_dashboard.php
│   ├── employer_profile.php
│   ├── interview_invite.php
│   ├── job_details.php
│   ├── jobs.php
│   ├── logout.php
│   ├── post_job.php
│   ├── send_message.php
│   ├── view_applicants.php
│   ├── view_applications.php
│   ├── view_reviews_employer.php
│   └── view_reviews.php
│
├── resumes/
│
├── background.mp4.mp4
├── chat_load.php
├── chat.php
├── config.php
├── create_job_portal.php
├── fetch_messages.php
├── footer.php
├── header.php
├── index.php
├── login.php
├── register.php

How to Run

Prerequisites
•	XAMPP 
•	PHP 
•	MySQL 
•	Web Browser 

Installation
1.	Download or clone this repository. 
2.	Copy the HIREHUB folder into the XAMPP htdocs directory. 
3.	Start Apache and MySQL from the XAMPP Control Panel. 
4.	Create a MySQL database for the project. 
5.	Import the project database into MySQL using phpMyAdmin. 
6.	Update the database credentials in: config.php
7.	Open the project in your browser: 
http://localhost/HIREHUB/

 Project Modules
 
Module	Description

Candidate	Search and apply for jobs, manage applications and resumes
Employer	Post jobs, manage vacancies and applicants
Admin	Manage users and monitor reports
Chat	Communication between candidates and employers
Reviews	Candidate and employer review system
Resume	Upload and manage candidate resumes

 Future Enhancements
 
•	Email notifications for applications and interviews 
•	Advanced job filtering and search 
•	Resume parsing 
•	Job recommendation system 
•	Enhanced admin analytics 
•	Improved security and authentication 

License
This project was developed for educational purposes.

 Developer
Sachu Ann Thomas
HireHub – Web-Based Job Recruitment Platform

