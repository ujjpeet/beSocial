## Telepítés

Nem  kell másolat az app/.env fájlról, mert git követett

### 1. Konténerek indítása
`docker-compose up -d --build`

### 2. app/var mappa
Hozd letre a kezzel a `var` mappat az app mappán belül, vagy ha mar letrejott akkor add at a sajat user-ednek
`sudo chown -R MY_USER: app/var`

### 3. Telepítsd a függőségeket
`docker-compose exec php-container composer install`


### 4. Hozd létre az adatbázist
Érdemes egy DB kezelő szoftvert használni, de meg lehet tenni az IDE-ben is.
`típus: mySql`
`Host: 0.0.0.0`
`db name: beSocial`
`user: root`
`pw: secret`
`port: 3306`

### 5. Töltsd be a dumpot
Így nem kell manuálisan adatot felvinned az adatbázisba az adminon keresztül.

### 6. Bejön az oldal.
http://localhost:8080/

