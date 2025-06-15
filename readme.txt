#Project: Simple Music Cloud Community System using PHP/MySQLi

# Aboutthe project
The Simple Music Cloud Community System is a simple project that is inspired by the Spotify Web Application. This system requires system credentials to access all of the data and functionalities of the music cloud community system. The system has 2 types of users which is the Administrator and the Subscriber. The Admin user can manage all the functionalities of the system including the music uploaded, and playlist of the subscriber user while the Subscriber user can only manage the songs they uploaded and playlist they created but since this also like and music publishing site, the system allows all users to listen all uploaded music or songs and also listen to the playlist songs created by other subscribers and admin. The Subscriber user can also download any song that can be accessed in this system.

# How the System Works
The Simple Music Cloud Community System has a registration form on the Login Page by clicking the "Create New Account". After creating a new account, the system will automatically login to the new subscriber using his/her created credentials. On the "Home Page", the summary numbers of all Genres, Playlist, Songs, Subscriber, Subscriber's Uploaded Songs, and Subscriber's Created Playlist. The Genre page can only be managed by the Admin users which makes the subscribers read the Genre's Details and listen to songs uploaded filtered into the viewed or selected genre. The Playlist Page is the page where the user view, create and manage the playlist. The system navigates to each page without refreshing the window so the music doesn't stop when redirecting other pages.

# Features:
* Login Page
* Registration Page
* Home Page
* Manage Music/Songs
* Manage Playlist
* Manage Genres
* Manage Users
* Music Player Panel

The Simple Music Cloud Community System was developed using HTML, PHP/MySQLi, CSS, JavaScript (jQuery/Ajax), and Bootstrap for the design. The source code is fully functional and easy to modify or enhance. Follow the instruction below to have an actual experience using this simple project.

# How to Run
1. Download the source code and extract the zip file.
2. Download or set up any local web server that runs PHP script.
3. Open the web-server database and create a new database name it evo_project_music2_db.
4. Import the SQL file located in the database folder of the source code.
5. Copy and paste the source code to the location where your local web server accessing your local projects. Example for XAMPP('C:\xampp\htdocs')
6. Open a web browser and browse the project. E.g [http://localhost/Music-Management-System2]

---------------------------------------------------
# Admin Access
email: admin@admin.com
password: admin123
# Sample Downloaded Music: https://www.bensound.com
----------------------------------------------------

# Bug Detected and Fixed:
1. Access Control Logic Refinement
* Strengthened role-based access control for admins and subscribers.
* Prevented unauthorized access to admin pages.
* Fixed content deletion permissions via UI conditionals and server-side checks.
2. Robust Input Validation and Sanitization
* Enforced character limits, required fields, and correct input types.
* Applied htmlspecialchars() and prepared statements to prevent XSS and SQL Injection.
3. User Interface & Experience Enhancements
* Improved mobile responsiveness and layout consistency.
* Fixed broken buttons and navigation.
* Refined display elements for a smoother UI.
4. Playlist & Song Management Reliability
* Resolved bugs in playlist editing and deletion functions.
* Ensured proper logic for managing song and playlist content.
5. System Stability & Data Integrity
* Fixed issues with duplicate ID handling.
* Improved session management, including logout behavior and profile updates.

Enjoy to use!
