# **CS3380 Final Project**

## **Project Group Members**
* Gordon Casey
* Chavinda Munasinghe
* Theodore Choma

## **Description of the Application**

We created a database application for an IT/Technology Services department to use to inventory their electronic devices (Computers, printers, monitors, TVs, etc.). This application allows for the creation of a record for an electronic device that stores the device's serial number, brand name, model, type of device, the department that the device belongs to, and the mocode that the device was purchased by. Each device record also allows us to assign the device to a user, by pawprint. A device does not have t have a user, but it can, and it it does, they the main screen also displays information about where that device is located based on the user's data. The user's data is stored in another table, and that information includes the user's pawprint, name, and office location. We can update a device's information by using the update button, for example if the device is reassigned to another user, and we can delete a device from the DB, for example if we get rid of the device. We also provide a search bar at the top, where a user can search for devices in the table based off of any of the device's fields, and one only has to enter a partial string, not a full string (Searching "deskt" will produce all records of type "Desktop," and all other records that match this string in other fields). This provides a very dynamic and flexible inventory system.

One note: If the device is not assigned to a user, then it will not show any location info on the home screen. This is by design. This application was developed because I actually work in technology services, and wanted to write an inventory system to replace our current system, and in our department the standard is that a device must always be assigned to a user, except for when the device is in the possession of tech services, in which case we leave the location blank to indicate that it is in or possession, rather than assigning it to us, only to reassign it out later. Also, we allow you, the DB user, to add users to the second table currently, however if this system were implemented in a UM system, the table would pull from a list of UM users, so we do not offer the capability to view the users table, because this functionality would be useless in the actual implementation of this app.

## **Schema**
* This application uses two tables, Devices and Users.

### __Devices__

ID integer NOT NULL AUTO_INCREMENT
SerialNumber varchar(45) NOT NULL
Brand varchar(45) NOT NULL
Model varchar(45)
Owner integer
DepartmentOwner varchar(45) NOT NULL
MoCodePurchasedBy varchar(45) NOT NULL
Type varchar(45) NOT NULL
PRIMARY KEY(ID)

### __Users__

ID integer NOT NULL AUTO_INCREMENT
FirstName varchar(45)
LastName varchar(45)
Pawprint varchar(45) NOT NULL
OfficeNumber integer
OfficeLetter char
PRIMARY KEY(ID)# CS3380FinalProject

## Entity Relationship Diagram
![](https://github.com/Gordon-Casey/CS3380FinalProject/blob/master/CS3380%20Final%20Project%20ERD.png)
