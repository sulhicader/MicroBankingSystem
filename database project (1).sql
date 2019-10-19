

CREATE DATABASE CentralBank;
USE CentralBank;

CREATE TABLE CUSTOMER(
	customer_id INT(10) AUTO_INCREMENT , 
	first_name VARCHAR(40) NOT NULL,
	last_name VARCHAR(40) NOT NULL,
	telephone_mobile VARCHAR(10) NOT NULL,
	NIC VARCHAR(10) NOT NULL,
	dateofbirth date NOT NULL,
	address VARCHAR(60) NOT NULL,
	PRIMARY KEY(customer_id)	
	
	
);


CREATE TABLE AGENT (
	agent_id INT(10) AUTO_INCREMENT,
	first_name VARCHAR(40) NOT NULL,
	last_name VARCHAR(40) NOT NULL,
	telephone_mobile INT(10) NOT NULL,  
	NIC varchar(10) NOT NULL,
	dateofbirth date NOT NULL,
	address varchar(60) NOT NULL,
	PRIMARY KEY(agent_id),
		
);


CREATE TABLE ACCOUNT_TYPE(
	account_type_ID INT(4),     
	min_balance DECIMAL(10,2) NOT NULL,
	interest_per DECIMAL(5,2) NOT NULL,
	acc_type VARCHAR(15) NOT NULL,
	PRIMARY KEY(account_type_ID)
);


CREATE TABLE FD_type(
	fd_ID INT(1) ,
	months INT(2) NOT NULL,
	interest_rate DECIMAL(5,2) NOT NULL,
	PRIMARY KEY(FD_ID)
);


CREATE TABLE ACCOUNT(
	account_ID INT(10) AUTO_INCREMENT,     
	account_type_ID INT(4) NOT NULL,
	transaction_type ENUM ('1','0') ,
	password INT(8) NOT NULL,
	opened_date DATE NOT null,
	balance DECIMAL(25,2) NOT NULL,
	trans_type ENUM('ACTIVE','DEACTIVE'),
	last_modified DATE NOT NULL,
	PRIMARY KEY(account_ID),
	FOREIGN KEY (account_type_ID) REFERENCES ACCOUNT_TYPE (account_type_ID) ON UPDATE CASCADE 	
);


CREATE TABLE ACCOUNT_CUSTOMER(
	customer_id INT(10) NOT NULL,
	account_ID INT(4) NOT NULL,
	PRIMARY KEY(account_ID , customer_id ),
	FOREIGN KEY (customer_ID) REFERENCES CUSTOMER (customer_ID) ON UPDATE CASCADE ,
	FOREIGN KEY (account_ID) REFERENCES ACCOUNT (account_ID) ON UPDATE CASCADE 
);


CREATE TABLE ACCOUNT_AGENT( 
	agent_id INT(10) NOT NULL,
	account_ID INT(4) NOT NULL,
	PRIMARY KEY(agent_id,account_ID),
	FOREIGN KEY (agent_ID) REFERENCES AGENT (agent_ID) ON UPDATE CASCADE ,
	FOREIGN KEY (account_ID) REFERENCES ACCOUNT (account_ID) ON UPDATE CASCADE 
);


CREATE TABLE FIXED_DEPOSITE(
	deposite_ID INT(10) AUTO_INCREMENT,
	account_ID INT(10) NOT NULL,
	fd_ID INT(10) NOT NULL,
	amount FLOAT (15,2) NOT NULL,
	opening_date DATE NOT NULL,
	closing_date DATE NOT NULL,
	last_modified DATE NOT NULL,
	PRIMARY KEY(deposite_ID),
	FOREIGN KEY (account_ID) REFERENCES ACCOUNT (account_ID) ON UPDATE CASCADE ,
	FOREIGN KEY (fd_ID) REFERENCES FD_TYPE (fd_ID) ON UPDATE CASCADE 	
);


CREATE TABLE TRANSACTION(
	transaction_ID INT(20) AUTO_INCREMENT,
	account_ID INT(10) NOT NULL,
	agent_ID INT(6) NOT NULL,
	time TIME NOT NULL,
	date DATE NOT NULL,	
	amount DECIMAL(25,2) NOT NULL,
	trans_type ENUM('DEPOSITE','WITHDRAW','INTEREST','CHARGES') NOT NULL,
	PRIMARY KEY(transaction_ID),
	FOREIGN KEY (account_ID) REFERENCES ACCOUNT (account_ID) ON UPDATE CASCADE,
	FOREIGN KEY (agent_ID) REFERENCES AGENT (agent_ID) ON UPDATE CASCADE 	
);

ALTER TABLE CUSTOMER AUTO_INCREMENT=1000000000;
ALTER TABLE ACCOUNT AUTO_INCREMENT=1000000000;
ALTER TABLE AGENT AUTO_INCREMENT=1000000000;

DELIMITER //

CREATE FUNCTION get_age (`date_of_birth` DATE) 
RETURNS INT(11)

BEGIN

RETURN ((YEAR(current_time) - YEAR(date_of_birth)) - ((DATE_FORMAT(current_time, '00-%m-%d') < DATE_FORMAT(date_of_birth, '00-%m-%d'))));
END//



/*timestampdiff(year,dateofbirth,curdate())*/

CREATE PROCEDURE new_transaction(IN account_ID1 INT(10),IN agent_ID1 INT(6),IN time1 TIME,IN trans_date1 DATE,IN AMONUT1 DECIMAL(25,2),
                                          IN trans_type1 ENUM('DEPOSITE','WITHDRAW'),OUT STATUS VARCHAR(100))
BEGIN
DECLARE new_balance DECIMAL(25,2);
DECLARE check_min DECIMAL(25,2);
START TRANSACTION;

    IF trans_type = 'DEPOSITE'
    THEN 
        SET new_balance=((SELECT balance FROM ACCOUNT WHERE ACCOUNT.account_ID=account_ID1)+AMONUT1);
        UPDATE ACCOUNT SET balance= new_balance WHERE account_ID=account_ID1;
        INSERT INTO TRANSACTION(account_ID, agent_ID, time, date, amount, trans_type) VALUES(account_ID1, agent_ID1, time1, trans_date1, amount1, trans_type1);
        SET STATUS ='TRANSACTION SUCCESSFULLY';
    ELSEIF trans_type = 'WITHDRAW'
    THEN
    SET check_min= (SELECT min_balance FROM ACCOUNT LEFT OUTER0 JOIN ACCOUNT_TYPE ON ACCOUNT.account_type_ID=ACCOUNT_TYPE.account_type_ID
                                       WHERE ACCOUNT.account_ID= account_ID );
    SET new_balance=((SELECT balance FROM ACCOUNT WHERE ACCOUNT.account_ID=account_ID)-AMONUT);
        IF new_balance>=check_min
        THEN 
            UPDATE ACCOUNT SET balance= new_balance;
            INSERT INTO TRANSACTION(account_ID, agent_ID, time, date, amount, trans_type) VALUES(account_ID, agent_ID, time, trans_date, amount, trans_type);
            SET STATUS ='TRANSACTION SUCCESSFULLY';
        ELSE
            SET STATUS ='NOT ENOUGH BALANCE';
        END IF;
    END IF;

COMMIT;

SELECT STATUS;
END//



CREATE PROCEDURE reduce_quick_transaction(IN account_ID1 INT(10), OUT STATUS VARCHAR(100))
BEGIN
DECLARE new_balance DECIMAL(25,2);
DECLARE quick_transaction_fee DECIMAL(25,2);
START TRANSACTION;
SET quick_transaction_fee=50.00;
INSERT INTO TRANSACTION values (NULL,account_ID1,'1',current_time,current_date,quick_transaction_fee,'CHARGES');
SET new_balance= ((SELECT balance FROM ACCOUNT WHERE ACCOUNT.account_ID=account_ID1)-quick_transaction_fee);
UPDATE ACCOUNT SET balance= new_balance where account_id = account_ID1;
COMMIT;
SET STATUS ='REDUCE SUCCESSFULLY';
SELECT STATUS;
END//





CREATE PROCEDURE update_balance(IN account_ID1 INT(10), IN last_modified DATE,IN interest_rate DECIMAL(5,2),IN balance DECIMAL(25,2), OUT STATUS VARCHAR(100))
BEGIN
DECLARE new_balance DECIMAL(25,2);
DECLARE interest DECIMAL(25,2);
START TRANSACTION;
SET interest = balance*(interest_rate/100)*( datediff(current_time, last_modified)/30);
SET new_balance=balance + interest;
INSERT INTO TRANSACTION values (NULL,account_ID1,'1',current_time,current_date,interest,'INTEREST');
UPDATE ACCOUNT SET balance= new_balance where account_id = account_ID1;
UPDATE ACCOUNT SET last_modified= current_time where account_id = account_ID1;
COMMIT;
SET STATUS ='UPDATE SUCCESSFULLY';
SELECT STATUS;
END//


CREATE PROCEDURE update_fd_balance(IN account_ID1 INT(10), IN last_modified DATE,IN interest_rate DECIMAL(5,2),IN amount DECIMAL(25,2), IN deposit_id1 INT(10), OUT STATUS VARCHAR(100))
BEGIN
DECLARE new_balance DECIMAL(25,2);
DECLARE interest DECIMAL(25,2);
START TRANSACTION;
SET interest = amount*(interest_rate/100)*( datediff(current_time, last_modified)/30);
SET new_balance=(SELECT balance FORM ACCOUNT WHERE account_ID=account_ID1) + interest;
INSERT INTO TRANSACTION values (NULL,account_ID1,'1',current_time,current_date,interest,'INTEREST');
UPDATE ACCOUNT SET ACCOUNT.balance= new_balance  where ACCOUNT.account_id = account_ID1;
UPDATE FIXED_DEPOSITE SET last_modified= current_time where deposite_ID=deposit_id1;
COMMIT;
SET STATUS ='UPDATE SUCCESSFULLY';
SELECT STATUS;
END//







CREATE PROCEDURE account_create  ( IN customer_ID_1 INT(10),IN customer_ID_2 INT(10),
                            	   IN  account_type_ID1 INT(4) ,IN transaction_type ENUM ('1','0') ,IN password INT(8) ,IN amount DECIMAL(25,2) ,
                             	  IN agent_id INT(10), OUT STATUS VARCHAR(100) )
                          	  

BEGIN
DECLARE account_ID INT(10);

START TRANSACTION;
IF LENGTH(password)<8
THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'YOUR PASSWORD IS TOO SHORT';
    
ELSEIF LENGTH(password)>8
THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'YOUR PASSWORD IS TOO LONG';

ELSEIF (SELECT min_balance FROM ACCOUNT_TYPE where account_type_id = account_type_ID1)> amount
 THEN 
     SIGNAL SQLSTATE '45000'
     SET MESSAGE_TEXT = 'NOT ENOUGH AMOUNT';
    
ELSE 

    INSERT INTO ACCOUNT(  account_type_ID, transaction_type, password, opened_date, balance, trans_type, last_modified) VALUES
                   ( account_type_ID1, transaction_type, password, current_date, amount,'ACTIVE', current_date);
    SET account_ID=  LAST_INSERT_ID();
    INSERT INTO TRANSACTION values (NULL,account_ID,'1',current_time,current_date,amount,'DEPOSIT');
    INSERT INTO ACCOUNT_CUSTOMER(customer_ID,account_ID) VALUES 
                                (customer_ID_1,account_ID);
    IF customer_ID_2 !=0
    THEN
        INSERT INTO ACCOUNT_CUSTOMER(customer_ID,account_ID) VALUES 
                                (customer_ID_2,account_ID);
    END IF;
    
    IF transaction_type='0'
    THEN
        INSERT INTO ACCOUNT_AGENT(agent_id,account_ID) VALUES 
                                (agent_id,account_ID);
    END IF;
    
END IF;
COMMIT;
SET STATUS ='INSERT SUCCESSFULLY';
SELECT STATUS;
END//



CREATE TRIGGER customer_insert
BEFORE INSERT ON CUSTOMER
FOR EACH ROW 
BEGIN
    DECLARE nic_exsits INT(1);
    SET  nic_exsits=(SELECT count(*) FROM CUSTOMER WHERE CUSTOMER.NIC= NEW.NIC);
    
    IF LENGTH(NEW.NIC)!=10
    THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'INVALID NIC NUMBER';
    
    ELSEIF nic_exsits!=0
    THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ALREADY EXSITS NIC NUMBER';
          
    ELSEIF LENGTH(NEW.telephone_mobile)!=10
    THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'INVALID TELEPHONE MOBILE NUMBER';
    
    END IF;
END;
//



CREATE TRIGGER cosmoter_update
BEFORE UPDATE ON CUSTOMER
FOR EACH ROW 
BEGIN
    DECLARE nic_exsits INT(1);
    SET  nic_exsits=(SELECT count(*) FROM CUSTOMER WHERE CUSTOMER.NIC= NEW.NIC);
    
    IF LENGTH(NEW.NIC)!=10
    THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'INVALID NIC NUMBER';
    
    ELSEIF nic_exsits!=1
    THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ALREADY EXSITS NIC NUMBER';
          
    ELSEIF LENGTH(NEW.telephone_mobile)!=10
    THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'INVALID TELEPHONE MOBILE NUMBER';
        

    END IF;
END;
//



CREATE TRIGGER account_update
BEFORE UPDATE ON ACCOUNT
FOR EACH ROW 
BEGIN
    IF NEW.password!=null
    THEN
        IF LENGTH(password)<8
        THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'YOUR PASSWORD IS TOO SHORT';
        
        ELSEIF LENGTH(password)>8
        THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'YOUR PASSWORD IS TOO LONG';
        END IF;    

    END IF;
END;
//




CREATE TRIGGER agent_insert
BEFORE INSERT ON AGENT
FOR EACH ROW 
BEGIN
    DECLARE nic_exsits INT(1);
    SET  nic_exsits=(SELECT count(*) FROM AGENT WHERE AGENT.NIC= NEW.NIC);
    
    IF LENGTH(NEW.NIC)!=10
    THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'INVALID NIC NUMBER';
    
    ELSEIF nic_exsits!=0
    THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ALREADY EXSITS NIC NUMBER';
          
    ELSEIF LENGTH(NEW.telephone_mobile)!=10
    THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'INVALID TELEPHONE MOBILE NUMBER';
    
    END IF;
END;
//


CREATE TRIGGER agent_update
BEFORE UPDATE ON AGENT
FOR EACH ROW 
BEGIN
    DECLARE nic_exsits INT(1);
    SET  nic_exsits=(SELECT count(*) FROM AGENT WHERE AGENT.NIC= NEW.NIC);
    
    IF LENGTH(NEW.NIC)!=10
    THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'INVALID NIC NUMBER';
    
    ELSEIF nic_exsits!=1
    THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ALREADY EXSITS NIC NUMBER';
          
    ELSEIF LENGTH(NEW.telephone_mobile)!=10
    THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'INVALID TELEPHONE MOBILE NUMBER';
        

    END IF;
END;
//

CREATE VIEW ACCOUNT_VIEW AS SELECT account_ID, account_type_ID, transaction_type, balance, trans_type, last_modified FROM ACCOUNT;


CREATE USER 'manager1'@'localhost' identified by 'manager1123';


CREATE USER 'recieption1'@'localhost' identified by 'recieption1123';
-- CREATE USER recieption2@localhost identified by 'recieption2123';

CREATE ROLE MANAGER ,GRANT MANAGER TO 'manager1';
CREATE ROLE RECIEPTION ,GRANT RECIEPTION TO 'recieption1';


GRANT SELECT, INSERT, UPDATE ON CUSTOMER  TO MANAGER;
GRANT SELECT, INSERT, UPDATE ON CUSTOMER  TO RECIEPTION;

GRANT SELECT, INSERT, UPDATE ON AGENT  TO MANAGER;
GRANT SELECT, INSERT, UPDATE ON AGENT  TO RECIEPTION;

GRANT SELECT, INSERT, UPDATE ON ACCOUNT_TYPE  TO MANAGER;
GRANT SELECT ON ACCOUNT_TYPE  TO RECIEPTION;

GRANT INSERT ON ACCOUNT  TO MANAGER;
GRANT SELECT, UPDATE ON ACCOUNT_VIEW  TO MANAGER;
GRANT SELECT, INSERT, UPDATE ON ACCOUNT  TO MANAGER;

GRANT SELECT, INSERT, UPDATE ON FD_TYPE  TO MANAGER;
GRANT SELECT ON FD_TYPE  TO RECIEPTION;

GRANT SELECT, INSERT ON ACCOUNT_TYPE  TO MANAGER;
GRANT SELECT, INSERT ON ACCOUNT_TYPE  TO MANAGER;

create idex index_account on account on account (transaction_type);







