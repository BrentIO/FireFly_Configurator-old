/* Create the necessary settings */
INSERT INTO settings (name, displayName, value) VALUES ('mqttServer', 'MQTT Server Name', 'my.mqttserver.com');
INSERT INTO settings (name, displayName, value) VALUES ('mqttPort', 'MQTT Server Port', '1883');
INSERT INTO settings (name, displayName, value) VALUES ('mqttUsername', 'MQTT Username', '$DEVICENAME$');
INSERT INTO settings (name, displayName, value) VALUES ('mqttPassword', 'MQTT Password', '$DEVICENAME$');
INSERT INTO settings (name, displayName, value) VALUES ('clientTopic', 'MQTT Client Topic', 'myhouse/client/$DEVICENAME$');
INSERT INTO settings (name, displayName, value) VALUES ('controlTopic', 'MQTT Control Topic', 'myhouse/control/');
INSERT INTO settings (name, displayName, value) VALUES ('eventTopic', 'MQTT Event Topic', 'myhouse/button/');
INSERT INTO settings (name, displayName, value) VALUES ('wifiSSID', 'WiFi SSID Name', 'myWiFiSSID');
INSERT INTO settings (name, displayName, value) VALUES ('wifiKey', 'WiFi Password', 'myWiFiPassword');
INSERT INTO settings (name, displayName, value) VALUES ('firmwareRefreshMs', 'Firmware Refresh (ms)', 3600000);

/* Create the button colors */
CALL editButtonColor(NULL, 'Blue', 'Blue', '#034efc', 0, 100);
CALL editButtonColor(NULL, 'White', 'White', '#ffffff', 0, 100);
CALL editButtonColor(NULL, 'Green', 'Green', '#488a20', 0,  100);
CALL editButtonColor(NULL, 'Red', 'Red', '#a82727', 0,  100);
CALL editButtonColor(NULL, 'Amber', 'Amber', '#c79112', 0, 100);
CALL editButtonColor(NULL, 'None', 'None', '#000000', 0, 100);

/* Create brightness names */
CALL editBrightnessName(NULL, 'Off', 'Off', 0);
CALL editBrightnessName(NULL, 'Bright', 'Bright', 100);
CALL editBrightnessName(NULL, 'Dim', 'Dim', 30);
CALL editBrightnessName(NULL, 'Default', 'Default', 80);

/* Create the controller pins */
CALL editControllerPins(NULL, 2, 2, true, true, true);
CALL editControllerPins(NULL, 2, 3, true, true, true);
CALL editControllerPins(NULL, 2, 4, true, true, true);
CALL editControllerPins(NULL, 2, 5, true, true, true);
CALL editControllerPins(NULL, 2, 6, true, true, true);
CALL editControllerPins(NULL, 2, 7, true, true, true);
CALL editControllerPins(NULL, 2, 8, true, true, true);
CALL editControllerPins(NULL, 2, 9, true, true, true);
CALL editControllerPins(NULL, 2, 11, true, true, true);
CALL editControllerPins(NULL, 2, 13, true, true, true);
CALL editControllerPins(NULL, 2, 22, true, true, false);
CALL editControllerPins(NULL, 2, 23, true, true, false);
CALL editControllerPins(NULL, 2, 24, true, true, false);
CALL editControllerPins(NULL, 2, 25, true, true, false);
CALL editControllerPins(NULL, 2, 26, true, true, false);
CALL editControllerPins(NULL, 2, 27, true, true, false);
CALL editControllerPins(NULL, 2, 28, true, true, false);
CALL editControllerPins(NULL, 2, 29, true, true, false);
CALL editControllerPins(NULL, 2, 30, true, true, false);
CALL editControllerPins(NULL, 2, 31, true, true, false);
CALL editControllerPins(NULL, 2, 32, true, true, false);
CALL editControllerPins(NULL, 2, 33, true, true, false);
CALL editControllerPins(NULL, 2, 34, true, true, false);
CALL editControllerPins(NULL, 2, 35, true, true, false);
CALL editControllerPins(NULL, 2, 36, true, true, false);
CALL editControllerPins(NULL, 2, 37, true, true, false);
CALL editControllerPins(NULL, 2, 38, true, true, false);
CALL editControllerPins(NULL, 2, 39, true, true, false);
CALL editControllerPins(NULL, 2, 40, true, true, false);
CALL editControllerPins(NULL, 2, 41, true, true, false);
CALL editControllerPins(NULL, 2, 42, true, true, false);
CALL editControllerPins(NULL, 2, 43, true, true, false);
CALL editControllerPins(NULL, 2, 44, true, true, true);
CALL editControllerPins(NULL, 2, 45, true, true, true);
CALL editControllerPins(NULL, 2, 46, true, true, true);
CALL editControllerPins(NULL, 2, 47, true, true, false);
CALL editControllerPins(NULL, 2, 48, true, true, false);
CALL editControllerPins(NULL, 2, 49, true, true, false);
CALL editControllerPins(NULL, 2, 55, true, true, false);
CALL editControllerPins(NULL, 2, 56, true, true, false);
CALL editControllerPins(NULL, 2, 57, true, true, false);
CALL editControllerPins(NULL, 2, 58, true, true, false);
CALL editControllerPins(NULL, 2, 59, true, true, false);
CALL editControllerPins(NULL, 2, 60, true, true, false);
CALL editControllerPins(NULL, 2, 61, true, true, false);
CALL editControllerPins(NULL, 2, 62, true, true, false);
CALL editControllerPins(NULL, 2, 63, true, true, false);
CALL editControllerPins(NULL, 2, 64, true, true, false);
CALL editControllerPins(NULL, 2, 65, true, true, false);
CALL editControllerPins(NULL, 2, 66, true, true, false);
CALL editControllerPins(NULL, 2, 67, true, true, false);
CALL editControllerPins(NULL, 2, 68, true, true, false);
CALL editControllerPins(NULL, 2, 69, true, true, false);
CALL editControllerPins(NULL, 2, 70, true, true, false);
CALL editControllerPins(NULL, 2, 71, true, true, false);
CALL editControllerPins(NULL, 2, 72, true, true, false);
CALL editControllerPins(NULL, 2, 73, true, true, false);
CALL editControllerPins(NULL, 2, 74, true, true, false);
CALL editControllerPins(NULL, 2, 75, true, true, false);
CALL editControllerPins(NULL, 2, 76, true, true, false);
CALL editControllerPins(NULL, 2, 77, true, true, false);
CALL editControllerPins(NULL, 2, 78, true, true, false);
CALL editControllerPins(NULL, 2, 79, true, true, false);
CALL editControllerPins(NULL, 2, 80, true, true, false);
CALL editControllerPins(NULL, 2, 81, true, true, false);
CALL editControllerPins(NULL, 2, 82, true, true, false);
CALL editControllerPins(NULL, 2, 83, true, true, false);
CALL editControllerPins(NULL, 2, 84, true, true, false);
CALL editControllerPins(NULL, 2, 85, true, true, false);

/* Create the controller ports */
CALL editControllerPorts(NULL, 2, 1, true, false);
CALL editControllerPorts(NULL, 2, 2, true, false);
CALL editControllerPorts(NULL, 2, 3, true, false);
CALL editControllerPorts(NULL, 2, 4, true, false);
CALL editControllerPorts(NULL, 2, 5, true, false);
CALL editControllerPorts(NULL, 2, 6, true, false);
CALL editControllerPorts(NULL, 2, 7, true, false);
CALL editControllerPorts(NULL, 2, 8, true, false);
CALL editControllerPorts(NULL, 2, 9, true, false);
CALL editControllerPorts(NULL, 2, 10, true, false);
CALL editControllerPorts(NULL, 2, 11, true, false);
CALL editControllerPorts(NULL, 2, 12, true, false);
CALL editControllerPorts(NULL, 2, 13, true, false);
CALL editControllerPorts(NULL, 2, 14, true, false);
CALL editControllerPorts(NULL, 2, 15, true, false);
CALL editControllerPorts(NULL, 2, 16, true, false);
CALL editControllerPorts(NULL, 2, 17, false, true);
CALL editControllerPorts(NULL, 2, 18, false, true);
CALL editControllerPorts(NULL, 2, 19, false, true);
CALL editControllerPorts(NULL, 2, 20, false, true);
CALL editControllerPorts(NULL, 2, 21, false, true);
CALL editControllerPorts(NULL, 2, 22, false, true);
CALL editControllerPorts(NULL, 2, 23, false, true);
CALL editControllerPorts(NULL, 2, 24, false, true);
CALL editControllerPorts(NULL, 2, 25, false, true);
CALL editControllerPorts(NULL, 2, 26, false, true);
CALL editControllerPorts(NULL, 2, 27, false, true);
CALL editControllerPorts(NULL, 2, 28, false, true);
CALL editControllerPorts(NULL, 2, 29, false, true);
CALL editControllerPorts(NULL, 2, 30, false, true);
CALL editControllerPorts(NULL, 2, 31, false, true);
CALL editControllerPorts(NULL, 2, 32, false, true);