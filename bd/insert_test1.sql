CREATE PROCEDURE proceso()
BEGIN
-- DECLARE mensaje CHAR(160);
-- DECLARE brigada CHAR(160);
-- mensaje = "Mensaje de prueba";
-- brigada = "Test";
DECLARE hecho INT DEFAULT 0;
DECLARE a CHAR(20);
DECLARE cursor1 cursor FOR SELECT celular FROM celulares WHERE celular like '%834%';
DECLARE continue handler FOR sqlstate '02000' SET hecho = 1;
OPEN cursor1;
repeat
fetch cursor1 INTO a;
	IF NOT hecho THEN
			INSERT INTO sms VALUES(a, 'mensaje', 'ADMIN', CURDATE(), CURTIME(), '0', '','','', 'brigada');
	END IF;
until hecho END repeat;
close cursor1;
END;
