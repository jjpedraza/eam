SET @QLada = '836104'; SET @Porcentaje = 16;

SELECT count(*) from celulares WHERE celular like CONCAT('',@QLada,'%')
INTO @Total;

	SET @Resultado = @Total * CONCAT('.',@Porcentaje);
	
SELECT @Resultado as R