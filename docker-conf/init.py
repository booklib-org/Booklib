print("Starting Container...")

#Initial import
import os
import subprocess

# Supervisord takes it from here.
nginx = subprocess.Popen(["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"])