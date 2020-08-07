/* This script must be run from the local server, not through MySQL Workbench. */

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema firefly
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `firefly` ;

-- -----------------------------------------------------
-- Schema firefly
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `firefly` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci ;
USE `firefly` ;

-- -----------------------------------------------------
-- Table `firefly`.`buttonColors`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`buttonColors` ;

CREATE TABLE IF NOT EXISTS `firefly`.`buttonColors` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(20) NOT NULL,
  `displayName` VARCHAR(20) NULL DEFAULT NULL,
  `hexValue` CHAR(6) NOT NULL,
  `brightnessMinimum` TINYINT NOT NULL DEFAULT '0',
  `brightnessMaximum` TINYINT NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `firefly`.`switches`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`switches` ;

CREATE TABLE IF NOT EXISTS `firefly`.`switches` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `controllerId` INT NULL DEFAULT NULL,
  `port` INT NULL DEFAULT NULL,
  `macAddress` BIGINT UNSIGNED NULL DEFAULT NULL,
  `hwVersion` TINYINT(1) NOT NULL,
  `firmwareId` INT NULL DEFAULT NULL,
  `name` VARCHAR(20) NOT NULL,
  `displayName` VARCHAR(20) NULL DEFAULT NULL,
  `mqttUsername` VARCHAR(20) NULL DEFAULT NULL,
  `mqttPassword` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE,
  UNIQUE INDEX `macAddress_UNIQUE` (`macAddress` ASC) VISIBLE,
  INDEX `controllerId_idx` (`controllerId` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `firefly`.`inputs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`inputs` ;

CREATE TABLE IF NOT EXISTS `firefly`.`inputs` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `switchId` INT NOT NULL,
  `port` TINYINT UNSIGNED NOT NULL,
  `pin` TINYINT UNSIGNED NULL DEFAULT NULL,
  `colorId` INT NOT NULL,
  `circuitType` ENUM('NORMALLY_OPEN', 'NORMALLY_CLOSED') NOT NULL,
  `displayName` VARCHAR(20) NULL DEFAULT NULL,
  `broadcastOnChange` TINYINT UNSIGNED NOT NULL DEFAULT '1',
  `enabled` TINYINT UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `firefly`.`controllers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`controllers` ;

CREATE TABLE IF NOT EXISTS `firefly`.`controllers` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `macAddress` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(20) NOT NULL,
  `displayName` VARCHAR(20) NULL DEFAULT NULL,
  `ipAddress` INT UNSIGNED NOT NULL,
  `subnet` INT UNSIGNED NOT NULL,
  `dns` INT UNSIGNED NOT NULL,
  `gateway` INT UNSIGNED NOT NULL,
  `mqttUsername` VARCHAR(20) NULL DEFAULT NULL,
  `mqttPassword` VARCHAR(255) NULL DEFAULT NULL,
  `hwVersion` TINYINT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE,
  UNIQUE INDEX `macAddress_UNIQUE` (`macAddress` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `firefly`.`outputs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`outputs` ;

CREATE TABLE IF NOT EXISTS `firefly`.`outputs` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `controllerId` INT NULL DEFAULT NULL,
  `port` TINYINT UNSIGNED NULL DEFAULT NULL,
  `pin` TINYINT UNSIGNED NULL DEFAULT NULL,
  `name` VARCHAR(20) NOT NULL,
  `displayName` VARCHAR(20) NULL DEFAULT NULL,
  `outputType` ENUM('BINARY', 'VARIABLE') NULL DEFAULT NULL,
  `breakerId` INT NULL DEFAULT NULL,
  `amperage` TINYINT UNSIGNED NULL DEFAULT '0',
  `enabled` TINYINT UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE,
  UNIQUE INDEX `uniqueRow` (`controllerId` ASC, `name` ASC) VISIBLE,
  INDEX `controllerId_idx` (`controllerId` ASC) VISIBLE,
  CONSTRAINT `controllerId`
    FOREIGN KEY (`controllerId`)
    REFERENCES `firefly`.`controllers` (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `firefly`.`actions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`actions` ;

CREATE TABLE IF NOT EXISTS `firefly`.`actions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `inputId` INT NULL DEFAULT NULL,
  `outputId` INT NULL DEFAULT NULL,
  `actionType` ENUM('INCREASE', 'DECREASE', 'TOGGLE') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE,
  UNIQUE INDEX `unique` (`inputId` ASC, `outputId` ASC) VISIBLE,
  INDEX `buttonId_idx` (`inputId` ASC) VISIBLE,
  INDEX `outputId_idx` (`outputId` ASC) VISIBLE,
  CONSTRAINT `buttonId`
    FOREIGN KEY (`inputId`)
    REFERENCES `firefly`.`inputs` (`id`),
  CONSTRAINT `outputId`
    FOREIGN KEY (`outputId`)
    REFERENCES `firefly`.`outputs` (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `firefly`.`breakers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`breakers` ;

CREATE TABLE IF NOT EXISTS `firefly`.`breakers` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(20) NOT NULL,
  `displayName` VARCHAR(20) NULL DEFAULT NULL,
  `amperage` TINYINT UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `firefly`.`brightnessNames`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`brightnessNames` ;

CREATE TABLE IF NOT EXISTS `firefly`.`brightnessNames` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(20) NOT NULL,
  `displayName` VARCHAR(20) NULL DEFAULT NULL,
  `brightnessValue` TINYINT NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `firefly`.`controllerPins`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`controllerPins` ;

CREATE TABLE IF NOT EXISTS `firefly`.`controllerPins` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `hwVersion` TINYINT UNSIGNED NOT NULL,
  `pin` TINYINT UNSIGNED NOT NULL,
  `inputAllowed` TINYINT UNSIGNED NOT NULL DEFAULT '0',
  `binaryOutputAllowed` TINYINT UNSIGNED NOT NULL DEFAULT '0',
  `variableOutputAllowed` TINYINT UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE,
  UNIQUE INDEX `hwVersionPinUnique` (`hwVersion` ASC, `pin` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `firefly`.`controllerPorts`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`controllerPorts` ;

CREATE TABLE IF NOT EXISTS `firefly`.`controllerPorts` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `hwVersion` TINYINT UNSIGNED NOT NULL,
  `port` TINYINT UNSIGNED NOT NULL,
  `inputAllowed` TINYINT UNSIGNED NOT NULL DEFAULT '0',
  `outputAllowed` TINYINT UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE,
  UNIQUE INDEX `unique` (`hwVersion` ASC, `port` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `firefly`.`firmware`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`firmware` ;

CREATE TABLE IF NOT EXISTS `firefly`.`firmware` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `deviceType` ENUM('CONTROLLER', 'SWITCH') NOT NULL,
  `version` INT NOT NULL,
  `url` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `version_UNIQUE` (`version` ASC) VISIBLE,
  UNIQUE INDEX `unique` (`deviceType` ASC, `version` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `firefly`.`settings`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`settings` ;

CREATE TABLE IF NOT EXISTS `firefly`.`settings` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(20) NOT NULL,
  `displayName` varchar(40) DEFAULT NULL,
  `value` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE,
  UNIQUE INDEX `settingName_UNIQUE` (`name` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;

USE `firefly` ;

-- -----------------------------------------------------
-- Placeholder table for view `firefly`.`getActionsJson`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `firefly`.`getActionsJson` (`json` INT, `inputId` INT);

-- -----------------------------------------------------
-- Placeholder table for view `firefly`.`getBreakers`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `firefly`.`getBreakers` (`id` INT, `name` INT, `displayName` INT, `amperage` INT, `json` INT);

-- -----------------------------------------------------
-- Placeholder table for view `firefly`.`getBrightnessNames`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `firefly`.`getBrightnessNames` (`id` INT, `name` INT, `displayName` INT, `brightnessValue` INT, `json` INT);

-- -----------------------------------------------------
-- Placeholder table for view `firefly`.`getButtonColors`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `firefly`.`getButtonColors` (`id` INT, `name` INT, `json` INT);

-- -----------------------------------------------------
-- Placeholder table for view `firefly`.`getColorBrightnessNames`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `firefly`.`getColorBrightnessNames` (`colorId` INT, `brightnessNames` INT);

-- -----------------------------------------------------
-- Placeholder table for view `firefly`.`getControllerBootstraps`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `firefly`.`getControllerBootstraps` (`id` INT, `macAddress` INT, `deviceName` INT, `name` INT, `displayName` INT, `ipAddress` INT, `subnet` INT, `dns` INT, `gateway` INT, `json` INT);

-- -----------------------------------------------------
-- Placeholder table for view `firefly`.`getControllerPinsUnused`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `firefly`.`getControllerPinsUnused` (`controllerId` INT, `pin` INT, `inputAllowed` INT, `binaryOutputAllowed` INT, `variableOutputAllowed` INT, `json` INT);

-- -----------------------------------------------------
-- Placeholder table for view `firefly`.`getControllerPinsUsed`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `firefly`.`getControllerPinsUsed` (`controllerId` INT, `id` INT, `pin` INT, `pinType` INT, `json` INT);

-- -----------------------------------------------------
-- Placeholder table for view `firefly`.`getControllerPortsUnused`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `firefly`.`getControllerPortsUnused` (`controllerId` INT, `port` INT, `inputAllowed` INT, `outputAllowed` INT, `json` INT);

-- -----------------------------------------------------
-- Placeholder table for view `firefly`.`getControllerPortsUsed`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `firefly`.`getControllerPortsUsed` (`controllerId` INT, `id` INT, `port` INT, `portType` INT, `json` INT);

-- -----------------------------------------------------
-- Placeholder table for view `firefly`.`getControllers`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `firefly`.`getControllers` (`id` INT, `macAddress` INT, `name` INT, `displayName` INT, `ipAddress` INT, `subnet` INT, `dns` INT, `gateway` INT, `mqttUsername` INT, `mqttPassword` INT, `hwVersion` INT, `json` INT);

-- -----------------------------------------------------
-- Placeholder table for view `firefly`.`getFirmware`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `firefly`.`getFirmware` (`id` INT, `deviceType` INT, `version` INT, `url` INT, `json` INT);

-- -----------------------------------------------------
-- Placeholder table for view `firefly`.`getInputs`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `firefly`.`getInputs` (`id` INT);

-- -----------------------------------------------------
-- Placeholder table for view `firefly`.`getControllerInputs`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `firefly`.`getControllerInputs` (`controllerId` INT, `name` INT, `displayName` INT, `pin` INT, `circuitType` INT, `broadcastOnStateChange` INT, `enabled` INT, `json` INT);

-- -----------------------------------------------------
-- Placeholder table for view `firefly`.`getOutputs`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `firefly`.`getOutputs` (`outputId` INT, `controllerId` INT, `name` INT, `displayName` INT, `outputType` INT, `pin` INT, `port` INT, `position` INT, `enabled` INT, `amperage` INT, `breakerId` INT, `json` INT);

-- -----------------------------------------------------
-- Placeholder table for view `firefly`.`getSettings`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `firefly`.`getSettings` (`id` INT, `name` INT, `displayName` INT, `value` INT, `json` INT);

-- -----------------------------------------------------
-- Placeholder table for view `firefly`.`getSwitchBootstraps`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `firefly`.`getSwitchBootstraps` (`switchId` INT, `macAddress` INT, `deviceName` INT, `firmwareVersion` INT, `firmwareURL` INT, `name` INT, `displayName` INT, `json` INT);

-- -----------------------------------------------------
-- Placeholder table for view `firefly`.`getSwitchButtons`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `firefly`.`getSwitchButtons` (`switchId` INT, `json` INT);

-- -----------------------------------------------------
-- Placeholder table for view `firefly`.`getSwitches`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `firefly`.`getSwitches` (`id` INT, `controllerId` INT, `port` INT, `macAddress` INT, `hwVersion` INT, `firmwareId` INT, `name` INT, `displayName` INT, `mqttUsername` INT, `mqttPassword` INT, `json` INT);

-- -----------------------------------------------------
-- function adjustBrightnessLevels
-- -----------------------------------------------------

USE `firefly`;
DROP function IF EXISTS `firefly`.`adjustBrightnessLevels`;

DELIMITER $$
USE `firefly`$$
CREATE FUNCTION `adjustBrightnessLevels`(colorMinimum int, colorMaximum int, brightnessLevel int) RETURNS int
    DETERMINISTIC
BEGIN


IF brightnessLevel = 0 THEN

	RETURN brightnessLevel;

END IF;

IF brightnessLevel BETWEEN colorMinimum AND colorMaximum THEN

	
	RETURN brightnessLevel;

END IF;

IF brightnessLevel < colorMinimum THEN

	
	RETURN colorMinimum;
    
END IF;

IF brightnessLevel > colorMaximum THEN

	
	RETURN colorMaximum;
    
END IF;


RETURN brightnessLevel;

END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure deleteBreaker
-- -----------------------------------------------------

USE `firefly`;
DROP procedure IF EXISTS `firefly`.`deleteBreaker`;

DELIMITER $$
USE `firefly`$$
CREATE PROCEDURE `deleteBreaker`(IN _id int)
BEGIN

DECLARE breakerCount int;

SELECT 
    COUNT(*)
INTO breakerCount FROM
    outputs
WHERE
    breakerId = _id;

IF breakerCount > 0 THEN

	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'One or more outputs are using this breaker.';

END IF;

DELETE FROM breakers 
WHERE
    id = _id;

END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure deleteBrightnessName
-- -----------------------------------------------------

USE `firefly`;
DROP procedure IF EXISTS `firefly`.`deleteBrightnessName`;

DELIMITER $$
USE `firefly`$$
CREATE PROCEDURE `deleteBrightnessName`(IN _id int)
BEGIN

DELETE FROM brightnessNames WHERE id = _id;

END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure deleteButtonColor
-- -----------------------------------------------------

USE `firefly`;
DROP procedure IF EXISTS `firefly`.`deleteButtonColor`;

DELIMITER $$
USE `firefly`$$
CREATE PROCEDURE `deleteButtonColor`(IN _id int)
BEGIN

DECLARE buttonCount int;

SELECT 
    COUNT(*)
INTO buttonCount FROM
    inputs
WHERE
    colorId = _id;

IF buttonCount > 0 THEN

	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'One or more buttons are using this color.';

END IF;

DELETE FROM buttonColors 
WHERE
    id = _id;

END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure deleteController
-- -----------------------------------------------------

USE `firefly`;
DROP procedure IF EXISTS `firefly`.`deleteController`;

DELIMITER $$
USE `firefly`$$
CREATE PROCEDURE `deleteController`(
IN _id int
)
BEGIN

DECLARE switchCount, outputCount int;

SELECT 
    COUNT(*)
INTO switchCount FROM
    switches
WHERE
    controllerId = _id;

SELECT 
    COUNT(*)
INTO outputCount FROM
    outputs
WHERE
    controllerId = _id;

IF switchCount + outputCount > 0 THEN

	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'One or more inputs or outputs are using this controller.';

END IF;

DELETE FROM controllers 
WHERE
    id = _id;

END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure deleteControllerPins
-- -----------------------------------------------------

USE `firefly`;
DROP procedure IF EXISTS `firefly`.`deleteControllerPins`;

DELIMITER $$
USE `firefly`$$
CREATE PROCEDURE `deleteControllerPins`(IN _hwVersion tinyint, IN _pin tinyint)
BEGIN

DECLARE inputCount int;
DECLARE outputCount int;
DECLARE _id int;


SELECT 
    id
INTO _id FROM
    controllerPins
WHERE
    hwVersion = _hwVersion AND pin = _pin;

IF _id is null THEN

	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid hwVersion or pin.';

END IF;

SELECT 
    COUNT(*)
INTO inputCount FROM
    firefly.switches
        INNER JOIN
    controllers ON switches.controllerId = controllers.id
        INNER JOIN
    inputs ON inputs.switchId = switches.id
        INNER JOIN
    controllerPins ON controllerPins.hwVersion = controllers.hwVersion
        AND controllerPins.pin = inputs.pin
WHERE
    controllerPins.id = _id;

SELECT 
    COUNT(*)
INTO outputCount FROM
    outputs
        INNER JOIN
    controllers ON outputs.controllerId = controllers.id
        INNER JOIN
    controllerPins ON controllers.hwVersion = controllerPins.hwVersion
        AND outputs.pin = controllerPins.pin
WHERE
    controllerPins.id = _id;
        

IF inputCount > 0 THEN

	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'One or more inputs are using this pin.';

END IF;

IF outputCount > 0 THEN

	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'One or more outputs are using this pin.';

END IF;


DELETE FROM controllerPins 
WHERE
    id = _id;

END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure deleteFirmware
-- -----------------------------------------------------

USE `firefly`;
DROP procedure IF EXISTS `firefly`.`deleteFirmware`;

DELIMITER $$
USE `firefly`$$
CREATE PROCEDURE `deleteFirmware`(IN _id int)
BEGIN

DECLARE firmwareCount int;

SELECT 
    COUNT(*)
INTO firmwareCount FROM
    switches
WHERE
    firmwareId = _id;

IF firmwareCount > 0 THEN

	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'One or more switches are using this firmware.';

END IF;

DELETE FROM firmware 
WHERE
    id = _id;

END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure deleteInput
-- -----------------------------------------------------

USE `firefly`;
DROP procedure IF EXISTS `firefly`.`deleteInput`;

DELIMITER $$
USE `firefly`$$
CREATE PROCEDURE `deleteInput`(IN _id int)
BEGIN



DECLARE actionsCount int;

SELECT 
    COUNT(*)
INTO actionsCount FROM
    actions
WHERE
    inputId = _id;

IF actionsCount > 0 THEN

	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'One or more actions are using this input.';

END IF;

DELETE FROM inputs where id = _id;

END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure deleteOutput
-- -----------------------------------------------------

USE `firefly`;
DROP procedure IF EXISTS `firefly`.`deleteOutput`;

DELIMITER $$
USE `firefly`$$
CREATE PROCEDURE `deleteOutput`(IN _id int)
BEGIN



DECLARE actionsCount int;

SELECT 
    COUNT(*)
INTO actionsCount FROM
    actions
WHERE
    outputId = _id;

IF actionsCount > 0 THEN

	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'One or more actions are using this output.';

END IF;

DELETE FROM outputs where id = _id;

END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure deleteSetting
-- -----------------------------------------------------

USE `firefly`;
DROP procedure IF EXISTS `firefly`.`deleteSetting`;

DELIMITER $$
USE `firefly`$$
CREATE PROCEDURE `deleteSetting`(IN _id int)
BEGIN



DELETE FROM settings where id = _id;

END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure deleteSwitch
-- -----------------------------------------------------

USE `firefly`;
DROP procedure IF EXISTS `firefly`.`deleteSwitch`;

DELIMITER $$
USE `firefly`$$
CREATE PROCEDURE `deleteSwitch`(IN _id int)
BEGIN

DECLARE inputCount int;

SELECT 
    COUNT(*)
INTO inputCount FROM
    inputs
WHERE
    switchId = _id;

IF inputCount > 0 THEN

	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'One or more inputs are using this switch.';

END IF;

DELETE FROM switches 
WHERE
    id = _id;

END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure editAction
-- -----------------------------------------------------

USE `firefly`;
DROP procedure IF EXISTS `firefly`.`editAction`;

DELIMITER $$
USE `firefly`$$
CREATE PROCEDURE `editAction`(IN _id int, IN _inputId int, IN _outputId int, _actionType ENUM('INCREASE','DECREASE','TOGGLE'), OUT id_ int)
BEGIN



DECLARE inputControllerId int;
DECLARE outputControllerId int;
DECLARE _outputType varchar(25);

SELECT 
    switches.controllerId
INTO inputControllerId FROM
    switches
        INNER JOIN
    inputs ON inputs.switchId = switches.id
WHERE
    inputs.id = _inputId;

SELECT 
    controllerId, outputType
INTO outputControllerId , _outputType FROM
    outputs
WHERE
    outputs.id = _outputId;

IF inputControllerId is null THEN

	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No controller found for input.';

END IF;

IF outputControllerId is null THEN

	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No controller found for output.';

END IF;

IF inputControllerId <> outputControllerId is null THEN

	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Input and output are not on the same controller.';

END IF;


IF (_actionType IN ('INCREASE', 'DECREASE') AND _outputType = 'BINARY') OR (_actionType IN ('TOGGLE') AND _outputType = 'VARIABLE') THEN

	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'The action type is not valid for the output type.';

END IF;


INSERT INTO actions (id, inputId, outputId, actionType)
VALUES (_id, _inputId, _outputId, _actionType)
ON DUPLICATE KEY UPDATE
	actionType = _actionType;

SELECT 
    id
INTO id_ FROM
    actions
WHERE
    inputId = _inputId
        AND outputId = _outputId;

END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure editBreaker
-- -----------------------------------------------------

USE `firefly`;
DROP procedure IF EXISTS `firefly`.`editBreaker`;

DELIMITER $$
USE `firefly`$$
CREATE PROCEDURE `editBreaker`(IN _id int,
IN _name varchar(20),
IN _displayName varchar(20),
IN _amperage tinyint)
BEGIN

SET _name = TRIM(_name);
SET _displayName = TRIM(_displayName);

INSERT INTO breakers (id, name, displayName, amperage)
VALUES (_id, _name, _displayName, IFNULL(_amperage, 0))
ON DUPLICATE KEY UPDATE
	name = _name,
    displayName = _displayName,
	amperage = IFNULL(_amperage, 0);

END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure editBrightnessName
-- -----------------------------------------------------

USE `firefly`;
DROP procedure IF EXISTS `firefly`.`editBrightnessName`;

DELIMITER $$
USE `firefly`$$
CREATE PROCEDURE `editBrightnessName`(IN _id int,
IN _name varchar(20),
IN _displayName varchar(20),
IN _brightness tinyint)
BEGIN

SET _displayName = TRIM(_displayName);
SET _name = UPPER(trim(_name));

IF _brightness < 0 THEN
	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Brightness must be >=0.';
END IF;

IF _brightness > 100 THEN
	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Brightness must be <=100.';
END IF;

INSERT INTO brightnessNames
	(id, name, displayName, brightnessValue)
VALUES
	(_id, _name, _displayName, _brightness)
ON DUPLICATE KEY UPDATE
	name = _name,
    displayName = _displayName,
	brightnessValue = _brightness;

END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure editButtonColor
-- -----------------------------------------------------

USE `firefly`;
DROP procedure IF EXISTS `firefly`.`editButtonColor`;

DELIMITER $$
USE `firefly`$$
CREATE PROCEDURE `editButtonColor`(IN _id int,
IN _name varchar(20),
IN _displayName varchar(20),
IN _hexValue char(7),
IN _brightnessMinimum tinyint,
IN _brightnessMaximum tinyint)
BEGIN

SET _name = UPPER(trim(_name));
SET _displayName = trim(_displayName);
SET _hexValue = UPPER(TRIM(REPLACE(_hexValue, '#','')));

IF _brightnessMinimum < 0 THEN
	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Minimum brightness must be >=0.';
END IF;

IF _brightnessMinimum > 100 THEN
	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Minimum brightness must be <=100.';
END IF;

IF _brightnessMaximum < 0 THEN
	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Maximum brightness must be >=0.';
END IF;

IF _brightnessMaximum > 100 THEN
	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Maximum brightness must be <=100.';
END IF;

IF _brightnessMinimum > _brightnessMaximum THEN
	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Minimum brightness must be less than maximum brightness.';
END IF;

INSERT INTO buttonColors
	(id, name, displayName, hexValue, brightnessMinimum, brightnessMaximum)
VALUES
	(_id, _name, _displayName, _hexValue, _brightnessMinimum, _brightnessMaximum)
ON DUPLICATE KEY UPDATE
	name = _name,
    displayName = _displayName,
    hexValue = _hexValue,
	brightnessMinimum = _brightnessMinimum,
	brightnessMaximum = _brightnessMaximum;

END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure editController
-- -----------------------------------------------------

USE `firefly`;
DROP procedure IF EXISTS `firefly`.`editController`;

DELIMITER $$
USE `firefly`$$
CREATE PROCEDURE `editController`(
IN _id int,
IN _macAddress varchar(17),
IN _name varchar(20),
IN _displayName varchar(20),
IN _ipAddress varchar(15),
IN _subnet varchar(15),
IN _dns varchar(15),
IN _gateway varchar(15),
IN _mqttUsername varchar(20),
IN _mqttPassword varchar(255),
IN _hwVersion tinyint(1))
BEGIN


SET _name = trim(_name);
SET _displayName = trim(_displayName);
SET _mqttUsername = trim(_mqttUsername);
SET _mqttPassword = trim(_mqttPassword);
SET _macAddress = trim(REPLACE(_macAddress, ':',''));



IF length(_mqttUsername) = 0 THEN
	SET _mqttUsername = NULL;
END IF;

IF length(_mqttPassword) = 0 THEN
	SET _mqttPassword = NULL;
END IF;


INSERT INTO controllers
	(id, macAddress, name, displayName, ipAddress, subnet, dns, gateway, mqttUsername, mqttPassword, hwVersion)
VALUES
	(_id, CONV(_macAddress,16,10), _name, _displayName, INET_ATON(_ipAddress), INET_ATON(_subnet), INET_ATON(_dns), INET_ATON(_gateway), _mqttUsername, _mqttPassword, _hwVersion)
ON DUPLICATE KEY UPDATE
	macAddress = CONV(_macAddress,16,10),
    name = _name,
    displayName = _displayName,
    ipAddress = INET_ATON(_ipAddress),
    subnet = INET_ATON(_subnet),
    dns = INET_ATON(_dns),
    gateway = INET_ATON(_gateway),
    mqttUsername = _mqttUsername,
    mqttPassword = _mqttPassword,
    hwVersion = _hwVersion;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure editControllerPins
-- -----------------------------------------------------

USE `firefly`;
DROP procedure IF EXISTS `firefly`.`editControllerPins`;

DELIMITER $$
USE `firefly`$$
CREATE PROCEDURE `editControllerPins`(IN _id int,
IN _hwVersion tinyint(1),
IN _pin tinyint(1),
IN _inputAllowed tinyint(1),
IN _binaryOutputAllowed tinyint(1),
IN _variableOutputAllowed tinyint(1))
BEGIN


INSERT INTO controllerPins(
id, hwVersion, pin, inputAllowed, binaryOutputAllowed, variableOutputAllowed)
VALUES
(_id, _hwVersion, _pin, _inputAllowed, _binaryOutputAllowed, _variableOutputAllowed)
ON DUPLICATE KEY UPDATE
	inputAllowed = _inputAllowed,
    binaryOutputAllowed = _binaryOutputAllowed, 
    variableOutputAllowed = _variableOutputAllowed;
    
END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure editControllerPorts
-- -----------------------------------------------------

USE `firefly`;
DROP procedure IF EXISTS `firefly`.`editControllerPorts`;

DELIMITER $$
USE `firefly`$$
CREATE PROCEDURE `editControllerPorts`(IN _id int, 
IN _hwVersion tinyint, 
IN _port tinyint, 
IN _inputAllowed tinyint, 
IN _outputAllowed tinyint)
BEGIN

INSERT INTO controllerPorts(id, hwVersion, port, inputAllowed, outputAllowed)
VALUES (_id, _hwVersion, _port, _inputAllowed, _outputAllowed)
ON DUPLICATE KEY UPDATE
	inputAllowed = _inputAllowed,
	outputAllowed = _outputAllowed;

END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure editFirmware
-- -----------------------------------------------------

USE `firefly`;
DROP procedure IF EXISTS `firefly`.`editFirmware`;

DELIMITER $$
USE `firefly`$$
CREATE PROCEDURE `editFirmware`(IN _id int, 
IN _deviceType ENUM('CONTROLLER','SWITCH'), 
IN _version int, 
IN _url varchar(255))
BEGIN

SET _url = trim(_url);

INSERT INTO firmware (id, deviceType, version, url)
VALUES (_id, _deviceType, _version, _url)
ON DUPLICATE KEY UPDATE
	deviceType = _deviceType,
    version = _version,
    url = _url;
    
END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure editInput
-- -----------------------------------------------------

USE `firefly`;
DROP procedure IF EXISTS `firefly`.`editInput`;

DELIMITER $$
USE `firefly`$$
CREATE PROCEDURE `editInput`(IN _id int,
IN _switchId int,
IN _port varchar(1),
IN _pin tinyint,
IN _colorId int,
IN _circuitType ENUM('NORMALLY_OPEN','NORMALLY_CLOSED'),
IN _displayName varchar(20),
IN _broadcastOnChange tinyint,
IN _enabled tinyint)
BEGIN

DECLARE _controllerId int;
DECLARE _port_ int;
DECLARE badPortCheck int;
DECLARE badPinCheck int;

SET _displayName = trim(_displayName);
SET _port = upper(trim(_port));

SELECT 
    controllerId
INTO _controllerId FROM
    switches
WHERE
    id = _switchId;

CASE
	WHEN _port = 'A' THEN
		SET _port_ = 1;
	WHEN _port = 'B' THEN 
		SET _port_ = 2;
	WHEN _port = 'C' THEN
		SET _port_ = 3;
	WHEN _port = 'D' THEN
		SET _port_ = 4;
	WHEN _port = 'E' THEN
		SET _port_ = 5;
	WHEN _port = 'F' THEN
		SET _port_ = 6;
	ELSE 
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid port.';
	
END CASE;


SELECT 
    COUNT(*)
INTO badPortCheck FROM
    inputs
WHERE
    switchId = _switchId AND port = _port_
        AND ID != IFNULL(_id, 0);

IF badPortCheck > 0 THEN
	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Port in use.';
END IF;

IF _pin IS NULL THEN

	SELECT GETNEXTINPUTPIN(_controllerId) INTO _pin;

END IF;
 
 
SELECT ISCONTROLLERPINAVAILABLE(_controllerId, _pin, _id, 'INPUT', NULL) INTO badPinCheck;
        
IF badPinCheck = 0 THEN
	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pin in use.';
END IF;


INSERT INTO inputs (
	id,
	switchId,
	port,
	pin,
	colorId, 
	circuitType,
    displayName,
	broadcastOnChange,
	enabled)
VALUES(
	_id,
	_switchId,
	_port_,
	_pin,
	_colorId,
	_circuitType,
    _displayName,
	_broadcastOnChange,
	true)
ON DUPLICATE KEY UPDATE
	switchId = _switchId,
	port = _port_,
	pin = _pin,
	colorId = _colorId,
	circuitType = _circuitType,
    displayName = _displayName,
	broadcastOnChange = _broadcastOnChange,
	enabled = _enabled;

END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure editOutput
-- -----------------------------------------------------

USE `firefly`;
DROP procedure IF EXISTS `firefly`.`editOutput`;

DELIMITER $$
USE `firefly`$$
CREATE PROCEDURE `editOutput`(
IN _id int,
IN _controllerId int,
IN _port tinyint,
IN _pin tinyint,
IN _name varchar(20),
IN _displayName varchar(20),
IN _outputType ENUM('BINARY','VARIABLE'),
IN _enabled tinyint,
IN _amperage tinyint,
IN _breakerId int)
BEGIN

DECLARE badOutputCount int;
DECLARE badPortCheck int;
DECLARE badPinCheck int;

SET _displayName = trim(_displayName);
SET _name = trim(_name);

IF _pin IS NULL THEN

	SELECT getNextOutputPin(_controllerId, _outputType) INTO _pin;

END IF;

SELECT 
    ISCONTROLLERPINAVAILABLE(_controllerId,
            _pin,
            _id,
            'OUTPUT',
            _outputType)
INTO badPinCheck;
        
IF badPinCheck = 0 THEN
	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pin in use.';
END IF;

SELECT ISCONTROLLERPORTAVAILABLE(_controllerId, _port, _id, 'OUTPUT') INTO badPortCheck;
        
IF badPortCheck = 0 THEN

	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Controller port in use.';

END IF;


IF _outputType = 'VARIABLE' THEN
	
    SELECT COUNT(*) INTO badOutputCount FROM actions WHERE outputId = _id AND actionType NOT IN ('INCREASE' , 'DECREASE');
    
    IF badOutputCount != 0 THEN

		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'One or more actions are not INCREASE or DECREASE for this variable output.';

	END IF;

END IF;

    
IF _outputType = 'BINARY' THEN
        
	SELECT COUNT(*) INTO badOutputCount FROM actions WHERE outputId = _id AND actionType NOT IN ('TOGGLE');
    
	IF badOutputCount != 0 THEN

		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'One or more actions are not TOGGLE for this binary output.';

	END IF;
    
END IF;

INSERT INTO outputs
	(id, controllerId, port, pin, name, displayName, outputType, enabled, amperage, breakerId)
VALUES
	(_id, _controllerId, _port, _pin, _name, _displayName, _outputType, true, IFNULL(_amperage, 0), breakerId)
ON DUPLICATE KEY UPDATE
	controllerId = _controllerId,
    port = _port,
    pin = _pin, 
    name = _name,
    displayName = _displayName,
    outputType = _outputType,
    enabled = _enabled,
    amperage = IFNULL(_amperage, 0),
    breakerId = _breakerId;
    

END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure editSetting
-- -----------------------------------------------------

USE `firefly`;
DROP procedure IF EXISTS `firefly`.`editSetting`;

DELIMITER $$
USE `firefly`$$
CREATE PROCEDURE `editSetting`(
IN _id int,
IN _value varchar(255)
)
BEGIN

SET _value = trim(_value);

UPDATE settings SET value = _value WHERE id = _id;
    
END$$
DELIMITER ;

-- -----------------------------------------------------
-- procedure editSwitch
-- -----------------------------------------------------

USE `firefly`;
DROP procedure IF EXISTS `firefly`.`editSwitch`;

DELIMITER $$
USE `firefly`$$
CREATE PROCEDURE `editSwitch`(
IN _id int,
IN _controllerId int,
IN _port int,
IN _macAddress varchar(17),
IN _hwVersion tinyint,
IN _name varchar(20),
IN _displayName varchar(20),
IN _mqttUsername varchar(20),
IN _mqttPassword varchar(255),
IN _firmwareId int
)
BEGIN

DECLARE _firmwareId_ int;
DECLARE controllerCount int;
DECLARE badPortCheck int;

SET _name = upper(replace(trim(_name), " ", ""));
SET _displayName = trim(_displayName);
SET _mqttUsername = trim(_mqttUsername);
SET _mqttPassword = trim(_mqttPassword);
SET _macAddress = trim(REPLACE(_macAddress, ':',''));

IF length(_mqttUsername) = 0 THEN
	SET _mqttUsername = NULL;
END IF;

IF length(_mqttPassword) = 0 THEN
	SET _mqttPassword = NULL;
END IF;

SELECT 
    COUNT(*)
INTO controllerCount FROM
    controllers
WHERE
    id = _controllerId;

IF controllerCount != 1 THEN

	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid controller.';

END IF;

IF _firmwareId IS NULL THEN
	SELECT id INTO _firmwareId FROM firmware
	INNER JOIN (SELECT deviceType AS deviceType, MAX(version) AS version FROM firmware GROUP BY deviceType) AS maxVal
	ON maxVal.deviceType=firmware.deviceType AND maxVal.version = firmware.version
	WHERE firmware.deviceType='SWITCH';

END IF;

IF _port IS NULL THEN

	SELECT getNextPort(_controllerId, 'INPUT') INTO _port;

END IF;

SELECT ISCONTROLLERPORTAVAILABLE(_controllerId, _port, _id, 'INPUT') INTO badPortCheck;
        
IF badPortCheck = 0 THEN

	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Controller port in use.';

END IF;

SELECT 
    id
INTO _firmwareId_ FROM
    firmware
WHERE
    id = _firmwareId
        AND deviceType = 'SWITCH';

IF _firmwareId_ is null THEN

	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid firmware.';

END IF;

INSERT INTO switches
	(id, controllerId, port, macAddress, hwVersion, name, displayName, firmwareId)
VALUES
	(_id, _controllerId, _port, CONV(_macAddress,16,10), _hwVersion, _name, _displayName, _firmwareId)
ON DUPLICATE KEY UPDATE
	controllerId = _controllerId,
    port = _port,
    macAddress = CONV(_macAddress,16,10),
    hwVersion = _hwVersion,
	name = _name,
    displayName = _displayName,
	mqttUsername = _mqttUsername,
	mqttPassword = _mqttPassword,
    firmwareId = _firmwareId;

END$$

DELIMITER ;

-- -----------------------------------------------------
-- function formatMacAddress
-- -----------------------------------------------------

USE `firefly`;
DROP function IF EXISTS `firefly`.`formatMacAddress`;

DELIMITER $$
USE `firefly`$$
CREATE FUNCTION `formatMacAddress`(macAddress bigint) RETURNS char(17) CHARSET utf8mb4
    DETERMINISTIC
BEGIN
	
    
    
	return CONCAT_WS(':',substring(hex(macAddress),1,2), substring(hex(macAddress),3,2), substring(hex(macAddress),5,2),
   substring(hex(macAddress),7,2), substring(hex(macAddress),9,2), substring(hex(macAddress),11,2));


END$$

DELIMITER ;

-- -----------------------------------------------------
-- function getButtonColorId
-- -----------------------------------------------------

USE `firefly`;
DROP function IF EXISTS `firefly`.`getButtonColorId`;

DELIMITER $$
USE `firefly`$$
CREATE FUNCTION `getButtonColorId`(_colorName varchar(20)) RETURNS int
    DETERMINISTIC
BEGIN

DECLARE returnValue int;


SET _colorName = UPPER(TRIM(_colorName));

SELECT 
    id
INTO returnValue FROM
    buttonColors
WHERE
    name = _colorName;

Return returnValue;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure getHeartbeat
-- -----------------------------------------------------

USE `firefly`;
DROP procedure IF EXISTS `firefly`.`getHeartbeat`;

DELIMITER $$
USE `firefly`$$
CREATE PROCEDURE `getHeartbeat`()
BEGIN

DECLARE rowCount int;

SELECT 
    COUNT(*)
INTO rowCount FROM
    settings;

IF rowCount > 0 THEN
	#Do nothing
    SELECT 'OK';

ELSE
	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Database Error.';
    
END IF;

END$$

DELIMITER ;

-- -----------------------------------------------------
-- function getMQTTPassword
-- -----------------------------------------------------

USE `firefly`;
DROP function IF EXISTS `firefly`.`getMQTTPassword`;

DELIMITER $$
USE `firefly`$$
CREATE FUNCTION `getMQTTPassword`(requestedMacAddress bigint) RETURNS varchar(20) CHARSET utf8mb4
    DETERMINISTIC
BEGIN
DECLARE returnValue varchar(20);

SELECT 
    mqttPassword
INTO returnValue FROM
    controllers
WHERE
    macAddress = requestedMacAddress;
    

IF isnull(returnValue) THEN
SELECT 
    mqttPassword
INTO
	returnValue
FROM
    switches
WHERE
    macAddress = requestedMacAddress;
END IF;
    

IF isnull(returnValue) THEN
	SELECT
		value
    INTO
		returnValue
    FROM
		settings
    WHERE
		name = 'MQTTPassword';
    
END IF;

RETURN returnValue;

END$$

DELIMITER ;

-- -----------------------------------------------------
-- function getMQTTUsername
-- -----------------------------------------------------

USE `firefly`;
DROP function IF EXISTS `firefly`.`getMQTTUsername`;

DELIMITER $$
USE `firefly`$$
CREATE FUNCTION `getMQTTUsername`(requestedMacAddress bigint) RETURNS varchar(20) CHARSET utf8mb4
    DETERMINISTIC
BEGIN
DECLARE returnValue varchar(20);

SELECT 
    mqttUsername
INTO returnValue FROM
    controllers
WHERE
    macAddress = requestedMacAddress;
    

IF isnull(returnValue) THEN
SELECT 
    mqttUsername
INTO
	returnValue
FROM
    switches
WHERE
    macAddress = requestedMacAddress;
END IF;
    

IF isnull(returnValue) THEN
	SELECT
		value
    INTO
		returnValue
    FROM
		settings
    WHERE
		name = 'MQTTUsername';
    
END IF;

RETURN returnValue;

END$$

DELIMITER ;

-- -----------------------------------------------------
-- function getNextInputPin
-- -----------------------------------------------------

USE `firefly`;
DROP function IF EXISTS `firefly`.`getNextInputPin`;

DELIMITER $$
USE `firefly`$$
CREATE FUNCTION `getNextInputPin`(_controllerId tinyint) RETURNS tinyint
    DETERMINISTIC
BEGIN

DECLARE returnValue tinyint;



SELECT 
    pin
INTO returnValue FROM
    firefly.getControllerPinsUnused
WHERE
    inputAllowed = TRUE
        AND controllerId = _controllerId
ORDER BY (binaryOutputAllowed * 10 + variableOutputAllowed * 10) ASC , pin ASC
LIMIT 1;

IF returnValue is null THEN

	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No pins available.';

END IF;

RETURN returnValue;

END$$

DELIMITER ;

-- -----------------------------------------------------
-- function getNextOutputPin
-- -----------------------------------------------------

USE `firefly`;
DROP function IF EXISTS `firefly`.`getNextOutputPin`;

DELIMITER $$
USE `firefly`$$
CREATE FUNCTION `getNextOutputPin`(_controllerId tinyint, _outputType ENUM('BINARY','VARIABLE')) RETURNS tinyint
    DETERMINISTIC
BEGIN

DECLARE returnValue tinyint;



IF _outputType = 'BINARY' THEN

	SELECT 
		pin
	INTO returnValue FROM
		firefly.getControllerPinsUnused
	WHERE
		binaryOutputAllowed = TRUE
			AND controllerId = _controllerId
	ORDER BY (binaryOutputAllowed * 10 + variableOutputAllowed * 10) ASC , pin ASC
	LIMIT 1;
END IF;

IF _outputType = 'VARIABLE' THEN
	SELECT 
		pin
	INTO returnValue FROM
		firefly.getControllerPinsUnused
	WHERE
		variableOutputAllowed = TRUE
			AND controllerId = _controllerId
	ORDER BY pin ASC
	LIMIT 1;

END IF;

IF returnValue is null THEN

	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No pins available.';

END IF;

RETURN returnValue;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- function getNextPort
-- -----------------------------------------------------

USE `firefly`;
DROP function IF EXISTS `firefly`.`getNextPort`;

DELIMITER $$
USE `firefly`$$
CREATE FUNCTION `getNextPort`(_controllerId int, _portType ENUM('INPUT', 'OUTPUT')) RETURNS tinyint
    DETERMINISTIC
BEGIN

DECLARE returnValue tinyint;

IF _portType = 'OUTPUT' THEN

	SELECT 
		port
	INTO returnValue FROM
		getControllerPortsUnused
	WHERE
		outputAllowed = TRUE
			AND controllerId = _controllerId
	ORDER BY inputAllowed ASC , port
	LIMIT 1;

END IF;

IF _portType = 'INPUT' THEN
	SELECT 
		port
	INTO returnValue FROM
		getControllerPortsUnused
	WHERE
		inputAllowed = TRUE
			AND controllerId = _controllerId
	ORDER BY outputAllowed ASC , port
	LIMIT 1;

END IF;

IF returnValue is null THEN

	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No ports available.';

END IF;

RETURN returnValue;

END$$

DELIMITER ;

-- -----------------------------------------------------
-- function getSetting
-- -----------------------------------------------------

USE `firefly`;
DROP function IF EXISTS `firefly`.`getSetting`;

DELIMITER $$
USE `firefly`$$
CREATE FUNCTION `getSetting`(requstedName varchar(20)) RETURNS varchar(255) CHARSET utf8mb4
    DETERMINISTIC
BEGIN


IF requstedName = 'mqttUsername' OR requstedName = 'mqttPassword' THEN

	return NULL;

END IF;

RETURN  (SELECT
    value
FROM
    settings
WHERE
    name = requstedName); 
    
END$$

DELIMITER ;

-- -----------------------------------------------------
-- function isControllerPinAvailable
-- -----------------------------------------------------

USE `firefly`;
DROP function IF EXISTS `firefly`.`isControllerPinAvailable`;

DELIMITER $$
USE `firefly`$$
CREATE FUNCTION `isControllerPinAvailable`(_controllerId int, _pin int, _id int, _pinType ENUM('INPUT','OUTPUT'), _outputType ENUM('BINARY','VARIABLE')) RETURNS tinyint(1)
    DETERMINISTIC
BEGIN

DECLARE countUnused int;
DECLARE countUsed int;

SET countUnused = -1;
SET countUsed = -1;

SET _id = IFNULL(_id, 0);

CASE
	WHEN _pinType = 'INPUT' THEN
    
		#Check if the pin is available and if input is allowed
		SELECT COUNT(*) INTO countUnused FROM getControllerPinsUnused WHERE controllerId = _controllerId AND pin = _pin AND inputAllowed = true;
		
		IF countUnused = 1 THEN
			#The pin is available for use and input is allowed
			RETURN TRUE;
		END IF;
        
		SELECT 
			COUNT(*)
		INTO countUsed FROM
			getControllerPinsUsed
		WHERE
			controllerId = _controllerId
				AND pin = _pin
				AND id = _id
				AND pinType = _pinType;
        
		IF countUsed = 1 THEN
			#This input is already using this pin, so allow it to continue
			RETURN TRUE;
		ELSE
			#This pin is occupied by someone else
			RETURN FALSE;
		END IF;
        
	WHEN _pinType = 'OUTPUT' THEN
        
		IF _outputType NOT IN ('BINARY','VARIABLE') OR _outputType IS NULL THEN
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid output type.';
        END IF;
        
		IF _outputType = 'BINARY' THEN
        
			#Check if the pin is available and if output is allowed
			SELECT COUNT(*) INTO countUnused FROM getControllerPinsUnused WHERE controllerId = _controllerId AND pin = _pin AND binaryOutputAllowed = true;
        
        END IF;
        
		IF _outputType = 'VARIABLE' THEN
        
			#Check if the pin is available and if output is allowed
			SELECT COUNT(*) INTO countUnused FROM getControllerPinsUnused WHERE controllerId = _controllerId AND pin = _pin AND variableOutputAllowed = true;
        
        END IF;
    
		IF countUnused = 1 THEN
			#The pin is available for use and the output type is allowed
			RETURN TRUE;
		END IF;
        
		SELECT 
			COUNT(*)
		INTO countUsed FROM
			getControllerPinsUsed
		WHERE
			controllerId = _controllerId
				AND pin = _pin
				AND id = _id
				AND pinType = _pinType;
        
		IF countUsed = 1 THEN
			#This output is already using this pin, so allow it to continue
			RETURN TRUE;
		ELSE
			#This pin is occupied by someone else OR the output type is not allowed
			RETURN FALSE;
		END IF;

	ELSE 
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid pin type.';
	
END CASE;

END$$

DELIMITER ;

-- -----------------------------------------------------
-- function isControllerPortAvailable
-- -----------------------------------------------------

USE `firefly`;
DROP function IF EXISTS `firefly`.`isControllerPortAvailable`;

DELIMITER $$
USE `firefly`$$
CREATE FUNCTION `isControllerPortAvailable`(_controllerId int, _port int, _id int, _portType ENUM('INPUT','OUTPUT')) RETURNS tinyint(1)
    DETERMINISTIC
BEGIN

DECLARE countUnused int;
DECLARE countUsed int;

SET countUnused = -1;
SET countUsed = -1;

SET _id = IFNULL(_id, 0);

CASE
	WHEN _portType = 'INPUT' THEN
    
		#Check if the port is available and if input is allowed
		SELECT COUNT(*) INTO countUnused FROM getControllerPortsUnused WHERE controllerId = _controllerId AND port = _port AND inputAllowed = true;
		
		IF countUnused = 1 THEN
			#The port is available for use and input is allowed
			RETURN TRUE;
		END IF;
        
		SELECT 
			COUNT(*)
		INTO countUsed FROM
			getControllerPortsUsed
		WHERE
			controllerId = _controllerId
				AND port = _port
				AND id = _id
				AND portType = _portType;
        
		IF countUsed = 1 THEN
			#This input is already using this port, so allow it to continue
			RETURN TRUE;
		ELSE
			#This port is occupied by someone else
			RETURN FALSE;
		END IF;
        
	WHEN _portType = 'OUTPUT' THEN
    
		#Check if the port is available and if output is allowed
		SELECT COUNT(*) INTO countUnused FROM getControllerPortsUnused WHERE controllerId = _controllerId AND port = _port AND outputAllowed = true;
        
		IF countUnused = 1 THEN
			#The port is available for use and the output type is allowed
			RETURN TRUE;
		END IF;
        
        
		SELECT 
			COUNT(*)
		INTO countUsed FROM
			getControllerPortsUsed
		WHERE
			controllerId = _controllerId
				AND port = _port
				AND id = _id
				AND portType = _portType;
        
		IF countUsed = 1 THEN
			#This output is already using this port, so allow it to continue
			RETURN TRUE;
		ELSE
			#This port is occupied by someone else OR the output type is not allowed
			RETURN FALSE;
		END IF;

	ELSE 
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid port type.';
	
END CASE;


END$$

DELIMITER ;

-- -----------------------------------------------------
-- View `firefly`.`getActionsJson`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`getActionsJson`;
DROP VIEW IF EXISTS `firefly`.`getActionsJson` ;
USE `firefly`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `firefly`.`getActionsJson` AS select json_arrayagg(json_object('output',`firefly`.`outputs`.`name`,'action',`firefly`.`actions`.`actionType`)) AS `json`,`firefly`.`actions`.`inputId` AS `inputId` from (`firefly`.`actions` join `firefly`.`outputs` on((`firefly`.`outputs`.`id` = `firefly`.`actions`.`outputId`))) group by `firefly`.`actions`.`inputId`;

-- -----------------------------------------------------
-- View `firefly`.`getBreakers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`getBreakers`;
DROP VIEW IF EXISTS `firefly`.`getBreakers` ;
USE `firefly`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `firefly`.`getBreakers` AS select `firefly`.`breakers`.`id` AS `id`,`firefly`.`breakers`.`name` AS `name`,`firefly`.`breakers`.`displayName` AS `displayName`,`firefly`.`breakers`.`amperage` AS `amperage`,json_object('id',`firefly`.`breakers`.`id`,'name',`firefly`.`breakers`.`name`,'displayName',`firefly`.`breakers`.`displayName`,'amperage',`firefly`.`breakers`.`amperage`) AS `json` from `firefly`.`breakers`;

-- -----------------------------------------------------
-- View `firefly`.`getBrightnessNames`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`getBrightnessNames`;
DROP VIEW IF EXISTS `firefly`.`getBrightnessNames` ;
USE `firefly`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `firefly`.`getBrightnessNames` AS select `firefly`.`brightnessNames`.`id` AS `id`,`firefly`.`brightnessNames`.`name` AS `name`,`firefly`.`brightnessNames`.`displayName` AS `displayName`,`firefly`.`brightnessNames`.`brightnessValue` AS `brightnessValue`,json_object('id',`firefly`.`brightnessNames`.`id`,'name',`firefly`.`brightnessNames`.`name`,'displayName',`firefly`.`brightnessNames`.`displayName`,'brightnessValue',`firefly`.`brightnessNames`.`brightnessValue`) AS `json` from `firefly`.`brightnessNames`;

-- -----------------------------------------------------
-- View `firefly`.`getButtonColors`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`getButtonColors`;
DROP VIEW IF EXISTS `firefly`.`getButtonColors` ;
USE `firefly`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `firefly`.`getButtonColors` AS select `firefly`.`buttonColors`.`id` AS `id`,`firefly`.`buttonColors`.`name` AS `name`,json_object('id',`firefly`.`buttonColors`.`id`,'name',`firefly`.`buttonColors`.`name`,'displayName',`firefly`.`buttonColors`.`displayName`,'hexValue',concat('#',`firefly`.`buttonColors`.`hexValue`),'brightnessMinimum',`firefly`.`buttonColors`.`brightnessMinimum`,'brightnessMaximum',`firefly`.`buttonColors`.`brightnessMaximum`) AS `json` from `firefly`.`buttonColors`;

-- -----------------------------------------------------
-- View `firefly`.`getColorBrightnessNames`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`getColorBrightnessNames`;
DROP VIEW IF EXISTS `firefly`.`getColorBrightnessNames` ;
USE `firefly`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `firefly`.`getColorBrightnessNames` AS select `firefly`.`buttonColors`.`id` AS `colorId`,json_object('color',`firefly`.`buttonColors`.`name`,'intensities',(select json_arrayagg(json_object('name',`firefly`.`brightnessNames`.`name`,'brightness',`ADJUSTBRIGHTNESSLEVELS`(`firefly`.`buttonColors`.`brightnessMinimum`,`firefly`.`buttonColors`.`brightnessMaximum`,`firefly`.`brightnessNames`.`brightnessValue`))) from `firefly`.`brightnessNames`)) AS `brightnessNames` from `firefly`.`buttonColors`;

-- -----------------------------------------------------
-- View `firefly`.`getControllerBootstraps`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`getControllerBootstraps`;
DROP VIEW IF EXISTS `firefly`.`getControllerBootstraps` ;
USE `firefly`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `firefly`.`getControllerBootstraps` AS select `firefly`.`controllers`.`id` AS `id`,`FORMATMACADDRESS`(`firefly`.`controllers`.`macAddress`) AS `macAddress`,hex(`firefly`.`controllers`.`macAddress`) AS `deviceName`,`firefly`.`controllers`.`name` AS `name`,`firefly`.`controllers`.`displayName` AS `displayName`,inet_ntoa(`firefly`.`controllers`.`ipAddress`) AS `ipAddress`,inet_ntoa(`firefly`.`controllers`.`subnet`) AS `subnet`,inet_ntoa(`firefly`.`controllers`.`dns`) AS `dns`,inet_ntoa(`firefly`.`controllers`.`gateway`) AS `gateway`,json_object('id',`firefly`.`controllers`.`id`,'name',`firefly`.`controllers`.`name`,'displayName',`firefly`.`controllers`.`displayName`,'network',json_object('macAddress',`FORMATMACADDRESS`(`firefly`.`controllers`.`macAddress`),'ipAddress',inet_ntoa(`firefly`.`controllers`.`ipAddress`),'subnet',inet_ntoa(`firefly`.`controllers`.`subnet`),'dns',inet_ntoa(`firefly`.`controllers`.`dns`),'gateway',inet_ntoa(`firefly`.`controllers`.`gateway`)),'mqtt',json_object('serverName',`GETSETTING`('mqttServer'),'port',cast(`GETSETTING`('mqttPort') as unsigned),'username',`GETMQTTUSERNAME`(`firefly`.`controllers`.`macAddress`),'password',`GETMQTTPASSWORD`(`firefly`.`controllers`.`macAddress`),'topics',json_object('client',`GETSETTING`('clientTopic'),'control',`GETSETTING`('controlTopic'),'event',`GETSETTING`('eventTopic')))) AS `json` from `firefly`.`controllers`;

-- -----------------------------------------------------
-- View `firefly`.`getControllerPinsUnused`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`getControllerPinsUnused`;
DROP VIEW IF EXISTS `firefly`.`getControllerPinsUnused` ;
USE `firefly`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `firefly`.`getControllerPinsUnused` AS select `controllers`.`id` AS `controllerId`,`controllerPins`.`pin` AS `pin`,`controllerPins`.`inputAllowed` AS `inputAllowed`,`controllerPins`.`binaryOutputAllowed` AS `binaryOutputAllowed`,`controllerPins`.`variableOutputAllowed` AS `variableOutputAllowed`,json_object('controllerId',`controllers`.`id`,'pin',`controllerPins`.`pin`,'inputAllowed',if((`controllerPins`.`inputAllowed` = '1'),cast(true as json),cast(false as json)),'binaryOutputAllowed',if((`controllerPins`.`binaryOutputAllowed` = '1'),cast(true as json),cast(false as json)),'variableOutputAllowed',if((`controllerPins`.`variableOutputAllowed` = '1'),cast(true as json),cast(false as json))) AS `json` from ((`controllerPins` join `controllers` on((`controllerPins`.`hwVersion` = `controllers`.`hwVersion`))) left join `getControllerPinsUsed` on(((`controllers`.`id` = `getControllerPinsUsed`.`controllerId`) and (`getControllerPinsUsed`.`pin` = `controllerPins`.`pin`)))) where (`getControllerPinsUsed`.`pinType` is null) order by `controllers`.`id`,`controllerPins`.`pin`;

-- -----------------------------------------------------
-- View `firefly`.`getControllerPinsUsed`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`getControllerPinsUsed`;
DROP VIEW IF EXISTS `firefly`.`getControllerPinsUsed` ;
USE `firefly`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `firefly`.`getControllerPinsUsed` AS select `usedPins`.`controllerId` AS `controllerId`,`usedPins`.`id` AS `id`,`usedPins`.`pin` AS `pin`,`usedPins`.`pinType` AS `pinType`,json_object('controllerId',`usedPins`.`controllerId`,'switchId',`usedPins`.`id`,'pin',`usedPins`.`pin`,'pinType',`usedPins`.`pinType`) AS `json` from (select `firefly`.`switches`.`controllerId` AS `controllerId`,`firefly`.`inputs`.`switchId` AS `id`,`firefly`.`inputs`.`pin` AS `pin`,'INPUT' AS `pinType` from ((`firefly`.`inputs` join `firefly`.`switches` on((`firefly`.`inputs`.`switchId` = `firefly`.`switches`.`id`))) join `firefly`.`controllers` on((`firefly`.`switches`.`controllerId` = `firefly`.`controllers`.`id`))) union select `firefly`.`outputs`.`controllerId` AS `controllerId`,`firefly`.`outputs`.`id` AS `id`,`firefly`.`outputs`.`pin` AS `pin`,'OUTPUT' AS `pinType` from `firefly`.`outputs`) `usedPins` order by `usedPins`.`controllerId`,`usedPins`.`pin`;

-- -----------------------------------------------------
-- View `firefly`.`getControllerPortsUnused`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`getControllerPortsUnused`;
DROP VIEW IF EXISTS `firefly`.`getControllerPortsUnused` ;
USE `firefly`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `firefly`.`getControllerPortsUnused` AS select `controllers`.`id` AS `controllerId`,`controllerPorts`.`port` AS `port`,`controllerPorts`.`inputAllowed` AS `inputAllowed`,`controllerPorts`.`outputAllowed` AS `outputAllowed`,json_object('controllerId',`controllers`.`id`,'port',`controllerPorts`.`port`,'inputAllowed',if((`controllerPorts`.`inputAllowed` = '1'),cast(true as json),cast(false as json)),'outputAllowed',if((`controllerPorts`.`outputAllowed` = '1'),cast(true as json),cast(false as json))) AS `json` from ((`controllerPorts` join `controllers` on((`controllers`.`hwVersion` = `controllerPorts`.`hwVersion`))) left join `getControllerPortsUsed` on(((`getControllerPortsUsed`.`controllerId` = `controllers`.`id`) and (`getControllerPortsUsed`.`port` = `controllerPorts`.`port`)))) where (`getControllerPortsUsed`.`portType` is null);

-- -----------------------------------------------------
-- View `firefly`.`getControllerPortsUsed`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`getControllerPortsUsed`;
DROP VIEW IF EXISTS `firefly`.`getControllerPortsUsed` ;
USE `firefly`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `firefly`.`getControllerPortsUsed` AS select `usedPorts`.`controllerId` AS `controllerId`,`usedPorts`.`id` AS `id`,`usedPorts`.`port` AS `port`,`usedPorts`.`portType` AS `portType`,json_object('controllerId',`usedPorts`.`controllerId`,'id',`usedPorts`.`id`,'port',`usedPorts`.`port`,'portType',`usedPorts`.`portType`) AS `json` from (select `firefly`.`switches`.`controllerId` AS `controllerId`,`firefly`.`switches`.`id` AS `id`,`firefly`.`switches`.`port` AS `port`,'INPUT' AS `portType` from `firefly`.`switches` union select `firefly`.`outputs`.`controllerId` AS `controllerId`,`firefly`.`outputs`.`id` AS `id`,`firefly`.`outputs`.`port` AS `port`,'OUTPUT' AS `portType` from `firefly`.`outputs`) `usedPorts`;

-- -----------------------------------------------------
-- View `firefly`.`getControllers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`getControllers`;
DROP VIEW IF EXISTS `firefly`.`getControllers` ;
USE `firefly`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `firefly`.`getControllers` AS select `firefly`.`controllers`.`id` AS `id`,`FORMATMACADDRESS`(`firefly`.`controllers`.`macAddress`) AS `macAddress`,`firefly`.`controllers`.`name` AS `name`,`firefly`.`controllers`.`displayName` AS `displayName`,inet_ntoa(`firefly`.`controllers`.`ipAddress`) AS `ipAddress`,inet_ntoa(`firefly`.`controllers`.`subnet`) AS `subnet`,inet_ntoa(`firefly`.`controllers`.`dns`) AS `dns`,inet_ntoa(`firefly`.`controllers`.`gateway`) AS `gateway`,`GETMQTTUSERNAME`(`firefly`.`controllers`.`macAddress`) AS `mqttUsername`,`GETMQTTPASSWORD`(`firefly`.`controllers`.`macAddress`) AS `mqttPassword`,`firefly`.`controllers`.`hwVersion` AS `hwVersion`,json_object('id',`firefly`.`controllers`.`id`,'macAddress',`FORMATMACADDRESS`(`firefly`.`controllers`.`macAddress`),'name',`firefly`.`controllers`.`name`,'displayName',`firefly`.`controllers`.`displayName`,'ipAddress',inet_ntoa(`firefly`.`controllers`.`ipAddress`),'subnet',inet_ntoa(`firefly`.`controllers`.`subnet`),'dns',inet_ntoa(`firefly`.`controllers`.`dns`),'gateway',inet_ntoa(`firefly`.`controllers`.`gateway`),'mqttUsername',`GETMQTTUSERNAME`(`firefly`.`controllers`.`macAddress`),'mqttPassword',`GETMQTTPASSWORD`(`firefly`.`controllers`.`macAddress`),'hwVersion',`firefly`.`controllers`.`hwVersion`) AS `json` from `firefly`.`controllers`;

-- -----------------------------------------------------
-- View `firefly`.`getFirmware`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`getFirmware`;
DROP VIEW IF EXISTS `firefly`.`getFirmware` ;
USE `firefly`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `firefly`.`getFirmware` AS select `firefly`.`firmware`.`id` AS `id`,`firefly`.`firmware`.`deviceType` AS `deviceType`,`firefly`.`firmware`.`version` AS `version`,`firefly`.`firmware`.`url` AS `url`,json_object('id',`firefly`.`firmware`.`id`,'deviceType',`firefly`.`firmware`.`deviceType`,'version',`firefly`.`firmware`.`version`,'url',`firefly`.`firmware`.`url`) AS `json` from `firefly`.`firmware`;

-- -----------------------------------------------------
-- View `firefly`.`getControllerInputs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`getControllerInputs`;
DROP VIEW IF EXISTS `firefly`.`getControllerInputs` ;
USE `firefly`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `firefly`.`getControllerInputs` AS select `firefly`.`switches`.`controllerId` AS `controllerId`,concat(`firefly`.`switches`.`name`,`firefly`.`inputs`.`port`) AS `name`,`firefly`.`inputs`.`displayName` AS `displayName`,`firefly`.`inputs`.`pin` AS `pin`,`firefly`.`inputs`.`circuitType` AS `circuitType`,if(`firefly`.`inputs`.`broadcastOnChange`,'TRUE','FALSE') AS `broadcastOnStateChange`,if(`firefly`.`inputs`.`enabled`,'TRUE','FALSE') AS `enabled`,json_object('name',concat(`firefly`.`switches`.`name`,`firefly`.`inputs`.`port`),'displayName',`firefly`.`inputs`.`displayName`,'pin',`firefly`.`inputs`.`pin`,'circuitType',`firefly`.`inputs`.`circuitType`,'broadcastOnStateChange',((0 <> `firefly`.`inputs`.`broadcastOnChange`) is true),'enabled',((0 <> `firefly`.`inputs`.`enabled`) is true)) AS `json` from (`firefly`.`inputs` join `firefly`.`switches` on((`firefly`.`inputs`.`switchId` = `firefly`.`switches`.`id`)));

-- -----------------------------------------------------
-- View `firefly`.`getInputs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`getInputs`;
DROP VIEW IF EXISTS `firefly`.`getInputs` ;
USE `firefly`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `firefly`.`getInputs` AS select `firefly`.`inputs`.`id` AS `id`,`firefly`.`inputs`.`switchId` AS `switchId`,(case when (`firefly`.`inputs`.`port` = 1) then 'A' when (`firefly`.`inputs`.`port` = 2) then 'B' when (`firefly`.`inputs`.`port` = 3) then 'C' when (`firefly`.`inputs`.`port` = 4) then 'D' when (`firefly`.`inputs`.`port` = 5) then 'E' when (`firefly`.`inputs`.`port` = 6) then 'F' end) AS `port`,`firefly`.`inputs`.`pin` AS `pin`,`firefly`.`inputs`.`colorId` AS `colorId`,`firefly`.`inputs`.`circuitType` AS `circuitType`,`firefly`.`inputs`.`displayName` AS `displayName`,if(`firefly`.`inputs`.`broadcastOnChange`,'true','false') AS `broadcastOnChange`,if(`firefly`.`inputs`.`enabled`,'true','false') AS `enabled`,json_object('id',`firefly`.`inputs`.`id`,'switchId',`firefly`.`inputs`.`switchId`,'port',(case when (`firefly`.`inputs`.`port` = 1) then 'A' when (`firefly`.`inputs`.`port` = 2) then 'B' when (`firefly`.`inputs`.`port` = 3) then 'C' when (`firefly`.`inputs`.`port` = 4) then 'D' when (`firefly`.`inputs`.`port` = 5) then 'E' when (`firefly`.`inputs`.`port` = 6) then 'F' end),'pin',`firefly`.`inputs`.`pin`,'colorId',`firefly`.`inputs`.`colorId`,'circuitType',`firefly`.`inputs`.`circuitType`,'displayName',`firefly`.`inputs`.`displayName`,'broadcastOnChange',cast(if(`firefly`.`inputs`.`broadcastOnChange`,'true','false') as json),'enabled',cast(if(`firefly`.`inputs`.`enabled`,'true','false') as json)) AS `json` from `firefly`.`inputs`;

-- -----------------------------------------------------
-- View `firefly`.`getOutputs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`getOutputs`;
DROP VIEW IF EXISTS `firefly`.`getOutputs` ;
USE `firefly`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `firefly`.`getOutputs` AS select `firefly`.`outputs`.`id` AS `outputId`,`firefly`.`outputs`.`controllerId` AS `controllerId`,`firefly`.`outputs`.`name` AS `name`,`firefly`.`outputs`.`displayName` AS `displayName`,`firefly`.`outputs`.`outputType` AS `outputType`,`firefly`.`outputs`.`pin` AS `pin`,`firefly`.`outputs`.`port` AS `port`,if((`firefly`.`outputs`.`outputType` = 'BINARY'),1,2) AS `position`,if(`firefly`.`outputs`.`enabled`,'TRUE','FALSE') AS `enabled`,`firefly`.`outputs`.`amperage` AS `amperage`,`firefly`.`outputs`.`breakerId` AS `breakerId`,json_object('name',`firefly`.`outputs`.`name`,'displayName',`firefly`.`outputs`.`displayName`,'outputType',`firefly`.`outputs`.`outputType`,'pin',`firefly`.`outputs`.`pin`,'enabled',((0 <> `firefly`.`outputs`.`enabled`) is true),'amperage',`firefly`.`outputs`.`amperage`,'breakerId',`firefly`.`outputs`.`breakerId`) AS `json` from `firefly`.`outputs`;

-- -----------------------------------------------------
-- View `firefly`.`getSettings`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`getSettings`;
DROP VIEW IF EXISTS `firefly`.`getSettings` ;
USE `firefly`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `firefly`.`getSettings` AS select `firefly`.`settings`.`id` AS `id`,`firefly`.`settings`.`name` AS `name`,`firefly`.`settings`.`displayName` AS `displayName`,`firefly`.`settings`.`value` AS `value`,json_object('id',`firefly`.`settings`.`id`,'name',`firefly`.`settings`.`name`,'displayName',`firefly`.`settings`.`displayName`,'value',`firefly`.`settings`.`value`) AS `json` from `firefly`.`settings`;

-- -----------------------------------------------------
-- View `firefly`.`getSwitchBootstraps`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`getSwitchBootstraps`;
DROP VIEW IF EXISTS `firefly`.`getSwitchBootstraps` ;
USE `firefly`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `firefly`.`getSwitchBootstraps` AS select `firefly`.`switches`.`id` AS `switchId`,`FORMATMACADDRESS`(`firefly`.`switches`.`macAddress`) AS `macAddress`,hex(`firefly`.`switches`.`macAddress`) AS `deviceName`,`firefly`.`firmware`.`version` AS `firmwareVersion`,`firefly`.`firmware`.`url` AS `firmwareURL`,`firefly`.`switches`.`name` AS `name`,`firefly`.`switches`.`name` AS `displayName`,json_object('id',`firefly`.`switches`.`id`,'name',`firefly`.`switches`.`name`,'displayName',`firefly`.`switches`.`displayName`,'firmware',json_object('url',`firefly`.`firmware`.`url`,'version',`firefly`.`firmware`.`version`,'refreshMilliseconds',cast(`GETSETTING`('firmwareRefreshMs') as unsigned)),'network',json_object('ssid',`GETSETTING`('wifiSSID'),'key',`GETSETTING`('wifiKey')),'mqtt',json_object('serverName',`GETSETTING`('mqttServer'),'port',`GETSETTING`('mqttPort'),'username',`GETMQTTUSERNAME`(`firefly`.`switches`.`macAddress`),'password',`GETMQTTPASSWORD`(`firefly`.`switches`.`macAddress`),'topics',json_object('control',`GETSETTING`('controlTopic'),'event',`GETSETTING`('eventTopic'),'client',`GETSETTING`('clientTopic'))),'buttons',ifnull(`getSwitchButtons`.`json`,json_array())) AS `json` from ((`firefly`.`switches` join `firefly`.`firmware` on((`firefly`.`switches`.`firmwareId` = `firefly`.`firmware`.`id`))) left join `firefly`.`getSwitchButtons` on((`getSwitchButtons`.`switchId` = `firefly`.`switches`.`id`)));

-- -----------------------------------------------------
-- View `firefly`.`getSwitchButtons`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`getSwitchButtons`;
DROP VIEW IF EXISTS `firefly`.`getSwitchButtons` ;
USE `firefly`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `firefly`.`getSwitchButtons` AS select `firefly`.`inputs`.`switchId` AS `switchId`,json_arrayagg(json_object('name',concat('B',`firefly`.`inputs`.`port`),'port',(case when (`firefly`.`inputs`.`port` = 1) then 'A' when (`firefly`.`inputs`.`port` = 2) then 'B' when (`firefly`.`inputs`.`port` = 3) then 'C' when (`firefly`.`inputs`.`port` = 4) then 'D' when (`firefly`.`inputs`.`port` = 5) then 'E' when (`firefly`.`inputs`.`port` = 6) then 'F' end),'led',`getColorBrightnessNames`.`brightnessNames`)) AS `json` from (((`firefly`.`inputs` join `firefly`.`buttonColors` on((`firefly`.`buttonColors`.`id` = `firefly`.`inputs`.`colorId`))) join `firefly`.`getColorBrightnessNames` on((`firefly`.`inputs`.`colorId` = `getColorBrightnessNames`.`colorId`))) join `firefly`.`switches` on((`firefly`.`inputs`.`switchId` = `firefly`.`switches`.`id`))) group by `firefly`.`inputs`.`switchId`;

-- -----------------------------------------------------
-- View `firefly`.`getSwitches`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `firefly`.`getSwitches`;
DROP VIEW IF EXISTS `firefly`.`getSwitches` ;
USE `firefly`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `firefly`.`getSwitches` AS select `firefly`.`switches`.`id` AS `id`,`firefly`.`switches`.`controllerId` AS `controllerId`,`firefly`.`switches`.`port` AS `port`,`FORMATMACADDRESS`(`firefly`.`switches`.`macAddress`) AS `macAddress`,`firefly`.`switches`.`hwVersion` AS `hwVersion`,`firefly`.`switches`.`firmwareId` AS `firmwareId`,`firefly`.`switches`.`name` AS `name`,`firefly`.`switches`.`displayName` AS `displayName`,`GETMQTTUSERNAME`(`firefly`.`switches`.`macAddress`) AS `mqttUsername`,`GETMQTTPASSWORD`(`firefly`.`switches`.`macAddress`) AS `mqttPassword`,json_object('id',`firefly`.`switches`.`id`,'controllerId',`firefly`.`switches`.`controllerId`,'controllerPort',`firefly`.`switches`.`port`,'macAddress',`FORMATMACADDRESS`(`firefly`.`switches`.`macAddress`),'hwVersion',`firefly`.`switches`.`hwVersion`,'firmwareId',`firefly`.`switches`.`firmwareId`,'name',`firefly`.`switches`.`name`,'displayName',`firefly`.`switches`.`displayName`,'mqttUsername',`GETMQTTUSERNAME`(`firefly`.`switches`.`macAddress`),'mqttPassword',`GETMQTTPASSWORD`(`firefly`.`switches`.`macAddress`)) AS `json` from `firefly`.`switches`;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
