import argparse
import json
import os

parser = argparse.ArgumentParser(description='Creates MQTT usernames and passwords for the given text file provided.')
parser.add_argument(dest='jsonFile', metavar="jsonFile", help='Path to a json file which contains the credentials to be added.')

args = parser.parse_args()

mqttCredentials = []

#Open the json file load it to a dict
try:
    with open(args.jsonFile) as jsonFile:
        mqttCredentials = json.load(jsonFile)

    for credential in mqttCredentials:
        os.system('mosquitto_passwd -b /etc/mosquitto/passwd ' + str(credential['mqttUsername']) + ' ' + str(credential['mqttPassword']))

except Exception as e:
    print(e)
    exit()

