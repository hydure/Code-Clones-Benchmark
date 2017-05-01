# Description
Code Clones are pieces of source code that are similar. These pieces can be 
defined as clones if they are textually, structurally or functionally similar. 
While there are several code clone detection techniques, an open crowd-sourced 
benchmark of true clones to evaluate these clones effectively is missing. 

The goal of this project is to create a web solution for running and evaluating 
clone detectors. The system will allow users to select and parameterize a set of
clone detectors on an uploaded dataset (or upload a new one). Additionally, 
there should be basic functionality to support uploading a new clone detection 
approach. 

The web interface should also be deployed as a web-app where a user 
can explore the database and see the actual source code of the clone pairs. 
Then, it should offer the possibility for any user to evaluate clone pairs 
(either on their own private results or the public benchmark).

# Technologies Used
* HTML5
* CSS3
* Linux
* Apache
* MySQL
* PHP
* Bootstrap
* Nicad
* Deckard
* CCFinderX

# Setup

## Clone repository

`git clone https://gitlab.com/WM-CSCI435-S17/Code-Clones-Benchmark` onto 
Linux Machine

## Hosting machine

* Change apache document root to Code-Clones-Benchmark/code

### For correct project upload

* Change apache user and group to 'whoami' or userID
* Add folder for sessions path in user domain and set session path in php.ini
* Change project upload path to folder of your chose by modifying filepath 
upload_projects.php line 24 and CCBModProjects.php line 24

### For database functionality:

* Change MySQL password to match files: *XMmySQ$
* Copy and paste into MySQL: 

```
CREATE DATABASE cc_bench; USE cc_bench;
CREATE TABLE Accounts( firstname varchar(20) NOT NULL,   \
    lastname varchar(20) NOT NULL,                       \
    email varchar(40) NOT NULL UNIQUE,                   \
    username varchar(40) NOT NULL UNIQUE,                \
    password varchar(20) NOT NULL,                       \
    userId int(11) NOT NULL AUTO_INCREMENT,              \
    userStatus enum('Y', 'N') NOT NULL DEFAULT 'N',      \
    tokencode varchar(40) NOT NULL,                      \
    vercode  varchar(40) NOT NULL,                       \
    PRIMARY KEY (userId) );
CREATE TABLE Projects( projectID int(11) NOT NULL AUTO_INCREMENT,   \
    title varchar(255),                                             \
    commit varchar(255),                                            \
    last_accessed varchar(255),                                     \
    uploaded varchar(255),                                          \
    ownership int(11),                                              \
    userID int(11) NOT NULL,                                        \
    url varchar(255),                                               \
    size int(11) NOT NULL,                                          \
    author VARCHAR(255),                                            \
    PRIMARY KEY(projectID) );
CREATE TABLE Datasets( datasetID int(11) NOT NULL,      \
    projectID int(11) NOT NULL,                         \
    userId int(11) NOT NULL,                            \
    submit_date varchar(255),                           \
    status tinyint(1),                                  \
    percent int(11),                                    \
    CCFinderX_flag tinyint(1),                          \
    Deckard_flag tinyint(1),                            \
    Nicad_flag tinyint(1),                              \
    last_ran varchar(255),                              \
    ownership int(11) );
CREATE TABLE Clones( cloneID int(11) NOT NULL,  \
    datasetID int(11),                          \
    projectID int(11) NOT NULL,                 \
    userID int(11) NOT NULL,                    \
    file varchar(255),                          \
    start int(11),                              \
    end int(11),                                \
    sim int(11),                                \
    detector varchar(255),                      \
    language varchar(255) );
```
# References
Web Design with HTML, CSS, JavaScript and jQuery Set by Jon Duckett

# Team Members
* Charles Rouse
* Tyler Reid
* Zachary Allison
* Jason Kimko
* Colin Lightfoot
