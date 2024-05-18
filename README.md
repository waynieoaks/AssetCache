# AssetCache

## ABOUT
AssetCache is a no thrills asset inventory management system for home use. It is intended to keep a list of all your possessions and their value in case of needing to claim on insurance (amongst other things). It is based visually off of a much better system called [HomeBox](https://github.com/hay-kot/homebox). For more control and features I strongly recommend looking at [HomeBox](https://github.com/hay-kot/homebox).

## FREQUENTLY ASKED QUESTIONS

### Why so grey?
Home inventory is not supposed to be fun so we wanted to reflect that in the design.

### Could you just...?
Nope... I wrote this to meet my needs, but figured it may be useful for others too. While I don't have the time to alter the design, I made the code open source so you can take the source code and tinker (I cannot support you in your adventures though, I am afraid).

## INSTALLATION
To get started with AssetCache, follow these steps:

### Prerequisites
- Ensure you have a web server that supports PHP (such as Apache or Nginx).
- Make sure you have a MySQL or MariaDB database set up.

### Steps
1. **Take a copy:**
   Download the contents of the www folder and put this on your web server

2. ** Install the SQL **
   Edit installation.sql and change the email address to your email address
   Use the installation.sql code to install the assetcache database on your SQL server
   Set up a username and password with read/write access to the newly created database
   
3. ** Configure the web site **
   rename mailcon.template.php to mailcon.php and dbconfig.template.php to dbconfig.php
   Edit mailcon.php to include you mail server details. This is needed for the password reset to work
   Edit dbconfig.php with your database details so that the website con connect
   
4. ** Get access **
   If you have configured your database connection correctly, the site should now load with a password screen. 
   If you have configured your mail server connection correctly, you can click 'forgot password' and enter the email you set at the top of installation.sql
   
## USAGE
Basic information about how to use the site will be published here

## CONTRIBUTING
If you want to share your improvements, feel free to fork this repository and submit a pull request. While I appreciate any contributions, if it does not work for my use case I may not publish - but you can share with others your improvements from your fork.

## License
This is licensed under GNU GENERAL PUBLIC LICENSE. See the 'LICENSE' file for details.

## CONTACT
Really? Well I guess you can reach out to me on my [Discord](https://discord.gg/9tkGM4Tcpm), but I am a busy and unsociable person so don't take offence if I fail to respond. (It's me, not you). 

## STILL READING?
Why, this is the bottom of the page, you weirdo!