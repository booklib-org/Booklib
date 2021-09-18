#!/usr/local/bin/python3
# Initial import
import os
import subprocess
import sys
import logging

# Configure Logging
logging.basicConfig(
    level=logging.DEBUG,
    format="%(asctime)s [%(levelname)s] %(message)s",
    handlers=[
        logging.StreamHandler(sys.stdout),
        logging.FileHandler(filename="/storage/logs/init.log", encoding="utf-8")
    ]
)

logging.info("Starting Container...")
ENV_FILE = {
    "APP_NAME":"BookLib",
    "APP_ENV":"local",
    "APP_KEY":"base64:dNfoxqhw5AjVqARjy3NpdRHztMbkzKSYOgzhzjAQU+U=",
    "APP_DEBUG":"true",
    "APP_URL":"http://localhost",
    "LOG_CHANNEL":"file",
    "LOG_LEVEL":"debug",
    "DB_CONNECTION":"mysql",
    "DB_HOST":"localhost",
    "DB_PORT":"3306",
    "DB_DATABASE":"booklib",
    "DB_USERNAME":"booklib",
    "DB_PASSWORD":"SomethingSecretAndSecure",
    "BROADCAST_DRIVER":"log",
    "CACHE_DRIVER":"file",
    "QUEUE_CONNECTION":"database",
    "SESSION_DRIVER":"file",
    "SESSION_LIFETIME":"120"
}

# Check for existing .env file
try:
    env_file_lines = open("/Booklib/.env", "r").readlines()
    logging.warning("Existing .env file discovered. Will not change settings.")

# Try to pull Environment variables
except:
    logging.info("Searching for Environmental Variables...")
    for item in ENV_FILE.keys():
        try:
            e_v = os.getenv(item)
            if e_v == None:
                raise OSError("ENV Variable not set")
            else:
                ENV_FILE[item] = os.getenv(item)
        except:
            logging.warning(item+" was not set, using default.")
            # If it's a required item that is missing, shut down the server and fail.
            if item in ["DB_HOST", "DB_DATABASE", "DB_USERNAME", "DB_PASSWORD"]:
                logging.error("Cannot continue. "+item+" is a required variable and MUST BE SET.")
                os.system("poweroff")
                sys.exit()
    ENV_FILE_LIST = []
    for item in ENV_FILE.keys():
        ENV_FILE_LIST.append(item+"="+str(ENV_FILE[item]))
    logging.info("Writing new .env file and forming SymLink")
    with open('/storage/.env', 'w') as file:
        for line in ENV_FILE_LIST:
            file.write(line+"\n")
    os.system("ln -s /storage/.env /Booklib/.env")

# If we've never seen the user before, run initial DB key generation
# We don't know if this fails
try:
    InitS = open("/storage/Init_Success", "r")
    logging.info("This container has been run before, skipping key generation")
    InitS.close()
except:
    logging.info("This container has not been run before, doing initial key generation")
    result = subprocess.run("cd /Booklib && php artisan key:generate", shell=True)
    if result.returncode != 0:
        logging.error("Encountered error ["+str(result.returncode)+"]during Key Generation")
    else:
        logging.info("Key Generation Successful")
        InitS = open("/storage/Init_Success", "w")
        InitS.write("Key Generated")
        InitS.close()

# If everything else has passed, run php artisan commands
logging.info("Running Artisan")
result = subprocess.run("cd /Booklib && php artisan migrate --force", shell=True, capture_output=True)
if result.returncode != 0:
    logging.error("Encountered error ["+str(result.returncode)+"]during Migration")
    logging.warning(result.stderr)
    logging.warning("----")
    logging.warning(result.stdout)
    logging.warning("----")
else:
    logging.info("Migration Successful")

result = subprocess.run("cd /Booklib && php artisan db:seed --force", shell=True, capture_output=True)
if result.returncode != 0:
    logging.error("Encountered error ["+str(result.returncode)+"]during Seeding")
    logging.warning(result.stderr)
    logging.warning("----")
    logging.warning(result.stdout)
    logging.warning("----")
else:
    logging.info("Seeding Successful")

# Run a Git Pull to make sure it's up to date unless environmental variable "AUTO_UPDATE" is set to false.
try:
    e_v = os.getenv("AUTO_UPDATE")
    if e_v == None:
        raise OSError("AUTO_UPDATE Variable not set, defaulting to true")
    else:
        if e_v == "TRUE":
            raise OSError("AUTO_UPDATE Variable set to TRUE")
        else:
            logging.warning("AUTO_UPDATE set to false, skipping update check.")
except:
    logging.info("Checking for updates....")
    result = subprocess.run("cd /Booklib && git pull https://github.com/MKaterbarg/Booklib.git", shell=True, capture_output=True)
    if result.returncode != 0:
        logging.error("Encountered error ["+str(result.returncode)+"]during Update")
        logging.warning(result.stderr)
        logging.warning("----")
        logging.warning(result.stdout)
        logging.warning("----")
    else:
        logging.warning("Update Successful or no Update Required")

# Finally, launch supervisord and start the server.
logging.info("Launching Supervisord...")
svd = subprocess.Popen(["/usr/bin/supervisord " " -c " " /etc/supervisor/conf.d/supervisord.conf"],shell=True)
while True:
    pass
