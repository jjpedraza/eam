  SET @Mensaje = "Prueba";
	SET @QLada = "833";
	SET @BrigadaN = "Test";
	
	INSERT INTO sms (
		SELECT	
			' ' as id
			,celular as celular
			,@Mensaje as mensaje
			,'ADMIN' as envia
			,CURDATE() as fecha
			,CURTIME() as hora
			,'0' as estado
			,' ' as comentarios
			,' ' as fecha_envio
			,' ' as hora_envio			
			,@BrigadaN as brigada
			,' ' as dispositivo

		FROM
			celulares
		
		WHERE celular like CONCAT('',@QLada,'%')
	 );