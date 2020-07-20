/* Drop and recreate the user */
DROP USER IF EXISTS 'firefly'@'localhost';
CREATE USER 'firefly'@'localhost' IDENTIFIED BY 'firefly';

/* Grant permissions to the views */
GRANT SELECT ON firefly.getActionsJson TO 'firefly'@'localhost';
GRANT SELECT ON firefly.getColorBrightnessNames TO 'firefly'@'localhost';
GRANT SELECT ON firefly.getControllerPinsUnused TO 'firefly'@'localhost';
GRANT SELECT ON firefly.getControllerPinsUsed TO 'firefly'@'localhost';
GRANT SELECT ON firefly.getControllerPortsUnused TO 'firefly'@'localhost';
GRANT SELECT ON firefly.getControllerPortsUsed TO 'firefly'@'localhost';
GRANT SELECT ON firefly.getControllers TO 'firefly'@'localhost';
GRANT SELECT ON firefly.getInputs TO 'firefly'@'localhost';
GRANT SELECT ON firefly.getOutputs TO 'firefly'@'localhost';
GRANT SELECT ON firefly.getSwitchButtons TO 'firefly'@'localhost';
GRANT SELECT ON firefly.getSwitches TO 'firefly'@'localhost';
GRANT SELECT ON firefly.getBreakers TO 'firefly'@'localhost';

/* Grant permissions to the stored procedures */ 
GRANT EXECUTE ON PROCEDURE firefly.deleteBreaker TO 'firefly'@'localhost';
GRANT EXECUTE ON PROCEDURE firefly.deleteBrightnessName TO 'firefly'@'localhost';
GRANT EXECUTE ON PROCEDURE firefly.deleteButtonColor TO 'firefly'@'localhost';
GRANT EXECUTE ON PROCEDURE firefly.deleteController TO 'firefly'@'localhost';
GRANT EXECUTE ON PROCEDURE firefly.deleteControllerPins TO 'firefly'@'localhost';
GRANT EXECUTE ON PROCEDURE firefly.deleteInput TO 'firefly'@'localhost';
GRANT EXECUTE ON PROCEDURE firefly.deleteOutput TO 'firefly'@'localhost';
GRANT EXECUTE ON PROCEDURE firefly.deleteSetting TO 'firefly'@'localhost';
GRANT EXECUTE ON PROCEDURE firefly.deleteSwitch TO 'firefly'@'localhost';
GRANT EXECUTE ON PROCEDURE firefly.editAction TO 'firefly'@'localhost';
GRANT EXECUTE ON PROCEDURE firefly.editBreaker TO 'firefly'@'localhost';
GRANT EXECUTE ON PROCEDURE firefly.editBrightnessName TO 'firefly'@'localhost';
GRANT EXECUTE ON PROCEDURE firefly.editButtonColor TO 'firefly'@'localhost';
GRANT EXECUTE ON PROCEDURE firefly.editController TO 'firefly'@'localhost';
GRANT EXECUTE ON PROCEDURE firefly.editControllerPins TO 'firefly'@'localhost';
GRANT EXECUTE ON PROCEDURE firefly.editControllerPorts TO 'firefly'@'localhost';
GRANT EXECUTE ON PROCEDURE firefly.editFirmware TO 'firefly'@'localhost';
GRANT EXECUTE ON PROCEDURE firefly.editInput TO 'firefly'@'localhost';
GRANT EXECUTE ON PROCEDURE firefly.editOutput TO 'firefly'@'localhost';
GRANT EXECUTE ON PROCEDURE firefly.editSetting TO 'firefly'@'localhost';
GRANT EXECUTE ON PROCEDURE firefly.editSwitch TO 'firefly'@'localhost';
GRANT EXECUTE ON PROCEDURE firefly.getHeartbeat TO 'firefly'@'localhost';
GRANT EXECUTE ON PROCEDURE firefly.incrementSwitchBootstrapCounter TO 'firefly'@'localhost';


/* Grant permissions to the functions */ 
GRANT EXECUTE ON FUNCTION firefly.adjustBrightnessLevels TO 'firefly'@'localhost';
GRANT EXECUTE ON FUNCTION firefly.formatMacAddress TO 'firefly'@'localhost';
GRANT EXECUTE ON FUNCTION firefly.getBootstrapURL TO 'firefly'@'localhost';
GRANT EXECUTE ON FUNCTION firefly.getButtonColorId TO 'firefly'@'localhost';
GRANT EXECUTE ON FUNCTION firefly.getMQTTPassword TO 'firefly'@'localhost';
GRANT EXECUTE ON FUNCTION firefly.getMQTTUsername TO 'firefly'@'localhost';
GRANT EXECUTE ON FUNCTION firefly.getNextInputPin TO 'firefly'@'localhost';
GRANT EXECUTE ON FUNCTION firefly.getNextOutputPin TO 'firefly'@'localhost';
GRANT EXECUTE ON FUNCTION firefly.getNextPort TO 'firefly'@'localhost';
GRANT EXECUTE ON FUNCTION firefly.getSetting TO 'firefly'@'localhost';

/* Pickup the changes */
FLUSH PRIVILEGES;