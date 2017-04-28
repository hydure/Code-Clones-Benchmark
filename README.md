Description:
Code Clones are pieces of source code that are similar. These pieces can be defined as clones if they are textually, structurally or functionally similar. While there are several code clone detection techniques, an open crowd-sourced benchmark of true clones to evaluate these clones effectively is missing. The goal of this project is to create a web solution for running and evaluating clone detectors. The system will allow users to select and parameterize a set of clone detectors on an uploaded dataset (or upload a new one). Additionally, there should be basic functionality to support uploading a new clone detection approach.  The web interface should also be deployed as a web-app where a user can explore the database and see the actual source code of the clone pairs. Then, it should offer the possibility for any user to evaluate clone pairs (either on their own private results or the public benchmark).

Technologies Used: HTML, CSS, PHP, mySQL, Bootstrap, Nicad, Deckard, CCFinderX



Set up:
git clone https://gitlab.com/WM-CSCI435-S17/Code-Clones-Benchmark
For hosting site:
Change apache document root to Code-Clones-Benchmark

For correct project upload:
Change apache user and group to 'whoami' or userID
Add folder for sessions path in user domain and set session path in php.ini
Change project upload path to folder of your chose by modifying filepath upload_projects.php line 24 and CCBModProjects.php line 24


For database functionality:
Change MySQL password to match files: *XMmySQ$
4 Database to copy and pastes in MySQL: 
create database cc_bench;
create table Accounts( firstname varchar(20) NOT NULL,lastname varchar(20) NOT NULL, email varchar(40) NOT NULL UNIQUE, username varchar(40) NOT NULL UNIQUE, password varchar(20) NOT NULL, userId int(11) NOT NULL AUTO_INCREMENT, userStatus enum('Y', 'N') NOT NULL DEFAULT 'N', tokencode varchar(40) NOT NULL, vercode  varchar(40) NOT NULL, PRIMARY KEY (userId) );
Create table Projects( projectID int(11) NOT NULL AUTO_INCREMENT, title varchar(255), commit varchar(255), last_accessed varchar(255), uploaded varchar(255), ownership int(11), userID int(11) NOT NULL, url varchar(255),  size int(11) NOT NULL, author VARCHAR(255), PRIMARY KEY(projectID) );


References:
Web Design with HTML, CSS, JavaScript and jQuery Set by Jon Duckett

Team Members:
Charles Rouse,
Tyler Reid,
Zachary Allison,
Jason Kimko,
Colin Lightfoot
