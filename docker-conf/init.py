print("Starting Container...")

#Initial import
import os
import subprocess
print("Imports Succesful")

# Supervisord takes it from here.
nginx = subprocess.Popen(["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"])
print("Supervisord has Authority...")