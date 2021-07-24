#!/usr/bin/python3
import json
import os
import pymysql

#Installation: sudo apt-get install python3-pymysql

settings = {}
mqttCredentials = []

try:
    with open("/var/www/config.json") as settingsFile:
        settings = json.load(settingsFile)
        settingsFile.close()

    db = pymysql.connect(settings['sqlServer'], settings['sqlUsername'], settings['sqlPassword'], settings['database'], charset='utf8mb4', cursorclass=pymysql.cursors.DictCursor)

    cursor = db.cursor()

    cursor.execute("SELECT json FROM getAllMqttCredentials;")

    queryResult = cursor.fetchall()

    db.close()

    for row in queryResult:
        mqttCredentials.append(json.loads(row['json']))
        #os.system('mosquitto_passwd -b /etc/mosquitto/passwd ' + str(mqttCredential['mqttUsername']) + ' ' + str(mqttCredential['mqttPassword']))

    with open('mqttCredentials.json', 'w') as outfile:
        json.dump(mqttCredentials, outfile)

except Exception as e:
    print("ERROR: ")
    print(e)
    exit()