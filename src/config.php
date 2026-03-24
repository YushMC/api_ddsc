<?php
Flight::register('db', 'PDO', [
    sprintf('mysql:host=%s;port=%s;dbname=%s', getenv('DB_HOST'), getenv('DB_PORT'), getenv('DB_NAME')),
    getenv('DB_USER'),
    getenv('DB_PASS')
]);
Flight::register('dbAntigua', 'PDO',array('mysql:host=127.0.0.1;dbname=u296035512_ddsc','u296035512_adminddsc','~y6O^XshN3'));