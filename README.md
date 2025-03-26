# Getting up and running
Hi! To get up and running, you should execute the following commands while having your cwd set to the challenge folder:
`docker-compose build`
`docker-compose up -d`
This command should be run next, if it fails, you may need to wait ~30 seconds for the database to finish booting up before trying again:
`docker exec -it challenge-php-1 php /var/www/src/migrations/form.php up` 

You can now navigate to https://localhost:8080 and the form should be fully functional and usable!