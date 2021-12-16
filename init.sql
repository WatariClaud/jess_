CREATE TABLE users (id SERIAL NOT NULL , name VARCHAR(50) NOT NULL , email VARCHAR(100) NOT NULL , phone VARCHAR(50) NOT NULL , password TEXT NOT NULL);

CREATE TABLE parking (id SERIAL NOT NULL , space VARCHAR(100) NOT NULL , spots INT NOT NULL , priceperspot VARCHAR(50) NOT NULL);

CREATE TABLE tickets (id SERIAL PRIMARY KEY, userid integer, datecreated VARCHAR (50), status VARCHAR (11), ispaid boolean, amount VARCHAR (50), spaceid VARCHAR(100), datepaid VARCHAR (50));
