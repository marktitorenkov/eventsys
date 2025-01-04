CREATE TABLE `event` (
  `idEvent` INT IDENTITY(1,1) PRIMARY KEY,
  `admin` VARCHAR(50) REFERENCES `users`(`username`),
  `nameEvent` VARCHAR(50) NOT NULL,
  `dateEvent` DATE NOT NULL,
  `isRepeated` BIT
);

CREATE TRIGGER trBirthdayDefault
ON `users` 
AFTER INSERT 
AS 
INSERT INTO `event` (`admin`,`nameEvent`,`dateEvent`,`isRepeated`) VALUES ((SELECT `username` FROM inserted),'Birthday'+(SELECT `username` FROM inserted),(SELECT `birthdate` FROM inserted),1)
GO

CREATE TABLE `hideEvent`(
	`eventid` INT REFERENCES `event`(`idEvent`),
	`personId` INT REFERENCES `users`(`id`),
    CONSTRAINT `eventid` FOREIGN KEY (`eventid`) REFERENCES `event`(`idEvent`)
);

CREATE TABLE `groupsRelated`(
	`eventid` INT REFERENCES `event`(`idEvent`),
	`personId` INT REFERENCES `users`(`id`)
    CONSTRAINT `eventid` FOREIGN KEY (`eventid`) REFERENCES `event`(`idEvent`)
);