# Download server with Generating Dynamic Urls
Initially a <i>POST</i> request with file attributes/identifiers is sent which will be processed to retrive or generate a new dynamic link.

On further receiving a <i>GET</i> request with the provided URL holding all the attributes/data will be processed to identify and provide the file for download.

All configuration parameters rest under <b><i>config/</i></b>

## Setup
- Web server (Apache2, ...)
- PHP
- MySQL

## MySQL Setup
<b>Create table queries:</b><br>

<b>Files table:</b>
_{version}\_files\_{variant}_
```
CREATE TABLE `downloads`.`arrow-10.0_files_official` ( `id` INT NOT NULL AUTO_INCREMENT , `file_sha256` VARCHAR(64) NOT NULL , `filename` VARCHAR(64) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
```

<b>File URLs table:</b>
```
CREATE TABLE `downloads`.`file_urls` ( `id` INT NOT NULL AUTO_INCREMENT , `file_sha256` VARCHAR(64) NOT NULL , `token_identifier` VARCHAR(64) NOT NULL , `time_before_expire` TIMESTAMP NOT NULL , `ip_address` VARCHAR(20) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
```