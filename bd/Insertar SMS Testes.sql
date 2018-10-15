SET @Porcentaje = CelPorcentaje('836104', 50);
SELECT @Porcentaje;
select InsertarSMS("Mensaje del SMS", "836104", "Test1", @Porcentaje);
-- select * from celulares ORDER BY RAND() limit 1