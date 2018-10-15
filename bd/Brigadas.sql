SELECT DISTINCT
	( brigada ) as Brig,
	(select count(*) from sms where sms.estado = 0 and brigada = Brig) as SentNOT,
	(select count(*) from sms where sms.estado = 1 and  brigada = Brig) as SentOK
	
FROM
	sms