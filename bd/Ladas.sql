SELECT DISTINCT
	SUBSTRING(celular, 1, 3 ) as Lada
	, 
	CONCAT(
					COALESCE((select clave from ladas where ladas.clave like CONCAT('%',Lada,'%') limit 0, 1)),  ", ",
					COALESCE((select clave from ladas where ladas.clave like CONCAT('%',Lada,'%') limit 1, 1)),  ", ",
					COALESCE((select clave from ladas where ladas.clave like CONCAT('%',Lada,'%') limit 2, 1)),  ", ",
					COALESCE((select clave from ladas where ladas.clave like CONCAT('%',Lada,'%') limit 3, 1)),  ", ",
					COALESCE((select clave from ladas where ladas.clave like CONCAT('%',Lada,'%') limit 4, 1)),   ", ",
					COALESCE((select clave from ladas where ladas.clave like CONCAT('%',Lada,'%') limit 5, 1)),   ", ",
					COALESCE((select clave from ladas where ladas.clave like CONCAT('%',Lada,'%') limit 6, 1)),   ", ",
					COALESCE((select clave from ladas where ladas.clave like CONCAT('%',Lada,'%') limit 7, 1)),   ", "
				)	
	as Lugar,
	(select count(*) from celulares where celular like CONCAT('',Lada,'%')) as n
	
FROM
	celulares 

order by n desc