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
print("Starting Imports...")
import os
import subprocess
import sys

# Check for existing .env file
try:
    env_file_lines = open("/Booklib/.env", "r").readlines()
    print("Existing .env file discovered. Will not change settings.")

# Try to pull Environment variables
except:
    print("Searching for Environmental Variables...")
    for item in ENV_FILE.keys():
        try:
            e_v = os.getenv(item)
            if e_v == None:
                raise OSError("ENV Variable not set")
            else:
                ENV_FILE[item] = os.getenv(item)
        except:
            print(item+" was not set, using default.")
            # If it's a required item that is missing, shut down the server and fail.
            if item in ["DB_HOST", "DB_DATABASE", "DB_USERNAME", "DB_PASSWORD"]:
                print("Cannot continue. "+item+" is a required variable and MUST BE SET.")
                os.system("poweroff")
                sys.exit()
    ENV_FILE_LIST = []
    for item in ENV_FILE.keys():
        ENV_FILE_LIST.append(item+"="+str(ENV_FILE[item]))
    print("Writing new .env file and forming SymLink")
    with open('/storage/.env', 'w') as file:
        for line in ENV_FILE_LIST:
            file.write(line+"\n\n")
    os.system("ln -s /storage/.env /Booklib/.env")

# If we've never seen the user before, run initial DB key generation
# We don't know if this fails
try:
    InitS = open("/storage/Init_Success", "r")
    print("This container has been run before, skipping key generation")
    InitS.close()
except:
    print("This container has not been run before, doing initial key generation")
    result = subprocess.run("cd /Booklib && php artisan key:generate", shell=True)
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
result = subprocess.run("cd /Booklib && php artisan migrate --force", shell=True, capture_output=True)
if result.returncode != 0:
    print("Encountered error during Migration")
    print("Exit code for Migration was "+str(result.returncode))
    print(result.stderr)
    print("----")
    print(result.stdout)
    print("----")
else:
    print("Migration Successful")

result = subprocess.run("cd /Booklib && php artisan db:seed --force", shell=True, capture_output=True)
if result.returncode != 0:
    print("Encountered error during seeding")
    print("Exit code for seeding was "+str(result.returncode))
    print(result.stderr)
    print("----")
    print(result.stdout)
    print("----")
else:
    print("Seeding Successful")

# Finally, launch supervisord and start the server.
print("Launching Supervisord...")
svd = subprocess.Popen(["/usr/bin/supervisord " " -c " " /etc/supervisor/conf.d/supervisord.conf"],shell=True)
while True:
    pass