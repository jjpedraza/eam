select  DISTINCT SUBSTRING(celular, 1, 3 ) as Lada,

SUBSTRING(celular, 4, 3 ) as Rama



from celulares
order by Rama

-- where SUBSTRING(celular, 1, 3 ) = '833'