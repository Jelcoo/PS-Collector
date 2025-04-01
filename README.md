# PS-Collector

PS-Collector is a web application for keeping track of your postage stamp collection.

## Visiting the application

The application is hosted at [ps-collector.jelco.xyz](https://ps-collector.jelco.xyz/)

## Starting the application

Please copy php/config.php.example to php/config.php and edit it to your needs.
Please replace the `JWT_SECRET` with a random secure string.
Please also set the mail credentials in php/config.php.

You can then start the application by running the following command:
```
docker compose up -d
```
