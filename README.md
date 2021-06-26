<h1 align="center"width="400">MINI ASPIRE</h1>

<h3>How to run</h3>
<ul>
    <li>Clone the project</li>
    <li>Go to the folder application using cd command on your cmd or terminal</li>
    <li>Run composer install on your cmd or terminal</li>
    <li>Copy .env.example file to .env on the root folder. You can type copy .env.example .env if using command prompt Windows or cp .env.example .env if using terminal, Ubuntu</li>
    <li>Open your .env file and change the database name (DB_DATABASE) to whatever you have, username (DB_USERNAME) and password (DB_PASSWORD) field correspond to your configuration.</li>
    <li>Run php artisan key:generate</li>
    <li>Run php artisan migrate</li>
    <li>Run php artisan jwt:secret</li>
     <li>Postman API document: https://documenter.getpostman.com/view/12252261/TzedhQYN</li>
</ul>
