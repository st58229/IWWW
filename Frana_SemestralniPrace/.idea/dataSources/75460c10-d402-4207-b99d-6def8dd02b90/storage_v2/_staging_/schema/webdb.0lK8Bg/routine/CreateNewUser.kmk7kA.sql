create
    definer = root@localhost function CreateNewUser(login varchar(50), passwd varchar(255), nam varchar(50),
                                                    surname varchar(50), email varchar(50), phoneprefix varchar(5),
                                                    phone int, street varchar(50), numbr smallint, postalCode tinyint,
                                                    town varchar(50), state varchar(3)) returns char(4)
BEGIN
    INSERT INTO address VALUES (street, numbr, postalCode, town, state);
    INSERT INTO users VALUES (login, passwd, nam, surname, email, phoneprefix, phone, NOW(), 'USER', LAST_INSERT_ID() );
    RETURN '1234';
END;