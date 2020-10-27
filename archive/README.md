# Archive file server with dynamic links integration

This works based on the same functions and integration that of our download server.

## MySQL Setup

<b>Database Name:</b> _downloads_<br>
<b>Create table queries:</b><br>

<b>Archive table:</b>
_archive\_tokens_

``` 
CREATE TABLE `downloads` . `archive_tokens` ( 
`id` INT NOT NULL AUTO_INCREMENT , 
`token_identifier` VARCHAR(64) NOT NULL , 
`time_before_expire` INT NOT NULL, 
`ip_address` VARCHAR(20) NOT NULL , 
    PRIMARY KEY ( `id` ), 
    UNIQUE KEY ( `token_identifier` )
) ENGINE = InnoDB;
```
