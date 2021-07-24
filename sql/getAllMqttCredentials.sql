USE `firefly`;
CREATE OR REPLACE VIEW `getAllMqttCredentials` AS
    SELECT 
        JSON_OBJECT('deviceType',
                deviceType,
                'deviceName',
                deviceName,
                'mqttUsername',
                d.mqttUsername,
                'mqttPassword',
                d.mqttPassword) AS 'json'
    FROM
        (SELECT 
            'CONTROLLER' AS 'deviceType',
                HEX(macAddress) AS deviceName,
                REPLACE(GETMQTTUSERNAME(macAddress), '$DEVICENAME$', HEX(macAddress)) AS mqttUsername,
                REPLACE(GETMQTTPASSWORD(macAddress), '$DEVICENAME$', HEX(macAddress)) AS mqttPassword
        FROM
            firefly.controllers UNION SELECT 
            'SWITCH' AS 'deviceType',
                HEX(macAddress) AS deviceName,
                REPLACE(GETMQTTUSERNAME(macAddress), '$DEVICENAME$', HEX(macAddress)) AS mqttUsername,
                REPLACE(GETMQTTPASSWORD(macAddress), '$DEVICENAME$', HEX(macAddress)) AS mqttPassword
        FROM
            switches) d
    ORDER BY deviceType , deviceName;
