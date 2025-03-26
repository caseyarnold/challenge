# Getting up and running
Hi! To get up and running, you should execute the following commands while having your cwd set to the challenge folder:

`docker-compose build`

`docker-compose up -d`

This command should be run next, if it fails, you may need to wait ~30 seconds for the database to finish booting up before trying again:

`docker exec -it challenge-php-1 php /var/www/src/migrations/form.php up` 

You can now navigate to http://localhost:8080 and the form should be fully functional and usable!

# Areas of Improvement
1. I'd like to add more advanced error handling, specificaly for files. It's common for malicious attackers to use file uploaders to try to upload malicious scripts in order to gain unauthorized access to systems. I'd like to check for viruses using a queue that checks files against something like clamav before uploading and updating the record with the appropriate file location.
2. File uploads - right now, the file uploader always just ignores the file and returns a path. Ideally, uploading these to an FTP site or a service like AWS S3 would be great.