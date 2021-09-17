#!/usr/local/bin/python3
print("Starting Container...")
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

# Initial import
print("Starting Imports")
import os
import subprocess
import sys

# Check for existing .env file
try:
    env_file_lines = open("/Booklib/.env", "r").readlines()
    print("Existing .env file discovered. Will not change settings.")

# Try to pull Environment variables
except:
    for item in ENV_FILE.keys():
        try:
            ENV_FILE[item] = os.getenv(item)
        except:
            print(item+" was not set, setting to default.")
            # If it's a required item that is missing, shut down the server and fail.
            if item in ["DB_HOST", "DB_DATABASE", "DB_USERNAME", "DB_PASSWORD"]:
                print("Cannot continue. "+item+" is a required variable and MUST BE SET.")
                os.system("poweroff")
                sys.exit()
    ENV_FILE_LIST = []
    for item in ENV_FILE.keys():
        ENV_FILE_LIST.append(item+"="+str(ENV_FILE[item]))
    with open('/storage/.env', 'w') as file:
        file.writelines( ENV_FILE_LIST )
    os.system("ln -s /storage/.env /Booklib/.env")

# If we've never seen the user before, run initial DB key generation
# We don't know if this fails
try:
    InitS = open("/storage/Init_Success", "r")
    print("This container has been run before, skipping key generation")
    InitS.close()
except:
    print("This container has not been run before, doing initial key generation")
    result = subprocess.run("cd /Booklib && php artisan key:generate")
    if result.returncode != 0:
        print("Encountered error during Key Generation")
        print("Exit code for Key Generation was "+str(result.returncode))
    else:
        print("Key Generation Successful")
        InitS = open("/storage/Init_Success", "w")
        InitS.write("Key Generated")
        InitS.close()

# If everything else has passed, run php artisan commands
print("Running Artisan")
result = subprocess.run("cd /Booklib && php artisan migrate")
print("Exit code for Migration was "+str(result.returncode))
result = subprocess.run("cd /Booklib && php artisan db:seed")
print("Exit code for seeding was "+str(result.returncode))

# Finally, launch supervisord and start the server.
print("Launching Supervisord...")
svd = subprocess.Popen(["/usr/bin/supervisord" "-c" "/etc/supervisor/conf.d/supervisord.conf"], stdout=subprocess.PIPE, stderr=subprocess.PIPE)
stdout, stderr = svd.communicate()