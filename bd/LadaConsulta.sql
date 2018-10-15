SELECT DISTINCT
	SUBSTRING(celular, 1, 3 ) as Lada
	, 
(SELECT  GROUP_CONCAT(clave) FROM  ladas WHERE  clave like  CONCAT('%',Lada,'%'))	as Lugar,
	(select count(*) from celulares where celular like CONCAT('',Lada,'%')) as n
	
FROM
	celulares 

order by n desc