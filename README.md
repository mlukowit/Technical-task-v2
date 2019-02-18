# Technical-task-v2

PHP driven online quiz application from requirements that specified:

________________________________________________________________________________________________________________________________________
1. index page
2. A  quiz view
3. User Results
_________________________________________________________________________________________________________________________________________

included:

--index.php - main routine with requests to a MySQL database.

--Css in the "styles" directory

--DB connecton file in a db directory


SQL database dump file included: consists of techtask database: consisting of 4 different tables:
- users: main users table, username and score + any additiona user data could be needed
- quizname: Quiz ID, text description of each, number of questions of each
- questionsanswers: the table of questions, associated to the quiz ID
- useranswers: log of each user ID, Question ID, answer progressive number. 
 (for purpose of debugging -- question text and the answer text are logged.) 

 import the SQL dump file. Change the “DBconnect.php” credentials to access to your own database.
 For development purposes: Bitnami Wampstack 7,1.26 was used: https://bitnami.com/stack/wamp/installer
 
 
