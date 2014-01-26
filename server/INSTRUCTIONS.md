## Set up server enviroment (Windows)
INSTALL [MySQL Server](http://dev.mysql.com/).  
Ensure to configure a MySQL Instance, untick 'modify security settings'.  
Start the MySQL Instance.

GET [PHP binaries](http://php.net/) and extract it to your desire location.  
Edit line below in *\php\php.ini* to full directory path of php folder.

    extension_dir = "..\php\ext"

PUT *assets/php5apache2_4.dll* under php directory.

INSTALL [vcredist_x86](http://www.microsoft.com/en-us/download/details.aspx?id=5555) if you don't have Visual Studio 2010 installed.  
It is *Microsoft Visual C++ 2010 Redistributable Package (x86)*,  
a prerequisite for *php5apache2_4.dll*.

GET [Apache HTTPD](http://httpd.apache.org/) and extract it to your desire location.  
Edit followings line in *\apache\conf\httpd.conf* to respectives full directory path.

    ServerRoot "../apache"
    LoadModule php5_module "../php/php5apache2_4.dll"
    PHPIniDir "../php"
    DocumentRoot "../www"
    <Directory "../www">
    ScriptAlias /cgi-bin/ "../apache/cgi-bin/"

PUT *assets/php.ini* under apache directory.

INSTALL Apache service.

    Run the command console (cmd.exe) As Administrator.
    Change directory to ..\apache\bin.
    Type the command "httpd -k install" to install

MAKE SURE you have Windows IIS disable.  
You are advised to restart the computer now.

TextFarm is now accessible locally through 'http://127.0.0.1/'


## Set up a new database
*/assets/textfarm_db.sql* containing SQL queries required  
for initializing a new instance of textfarm database.

Alternately, */assets/textfarm_db_sample.sql* is a version  
contained some sample data. You can use it as reference to  
create your instance.

Both can be restore through [phpMyAdmin](http://www.phpmyadmin.net/) web interface.  
There are no PHP script responsible for creating new database  
available here.


## Test Scripts
Testing scripts showed in API Guidelines can be located under /test.  
Is accessible through URL : 'http://127.0.0.1/test/'


## Development Configuration
TextFarm is develop under the enviroment:  
X-Powered-By : PHP 5.4.6  
Server       : Apache/2.4.3 (Win32)  
MySQL Server : 5.5.27


### Additional reference
More instructions on enviroment set up:  
http://www.devraju.com/php/installing-php-5-4-with-apache-2-4-in-32-bit-of-windows/