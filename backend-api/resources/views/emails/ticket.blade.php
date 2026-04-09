<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { background-color: #ffffff; max-width: 600px; margin: 0 auto; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background-color: #020617; color: #ffffff; padding: 30px; text-align: center; }
        .content { padding: 30px; line-height: 1.6; color: #333333; }
        .ticket-info { background-color: #f8fafc; border: 1px solid #e1e4e8; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .qr-code { text-align: center; margin: 20px 0; }
        .footer { text-align: center; font-size: 12px; color: #718096; padding: 20px; }
        .btn { display: inline-block; padding: 12px 24px; background-color: #3b82f6; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: bold; }
        .seat-tag { display: inline-block; background: #e2e8f0; padding: 4px 8px; border-radius: 4px; margin-right: 5px; font-size: 14px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Entrades huevostyle</h1>
            <p>La teva reserva s'ha realitzat amb èxit!</p>
        </div>
        <div class="content">
            <p>Hola <strong>{{ $name }}</strong>,</p>
            <p>Aquí tens el resum de la teva compra per a l'esdeveniment:</p>
            
            <div class="ticket-info">
                <h2 style="margin-top: 0; color: #1a202c;">{{ $seients->first()->esdeveniment->nom }}</h2>
                <p>📍 {{ $seients->first()->esdeveniment->recinte }}</p>
                <p>📅 {{ \Carbon\Carbon::parse($seients->first()->esdeveniment->data_hora)->format('d/m/Y H:i') }}</p>
                <p>💺 Seients: 
                    @foreach($seients as $s)
                        <span class="seat-tag">Fila {{ $s->fila }} - Núm {{ $s->numero }}</span>
                    @endforeach
                </p>
            </div>

            <div class="qr-code">
                <p>Presenta aquest codi QR a l'entrada:</p>
                <img src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=ORDER-{{ $seients->first()->id }}-{{ time() }}&choe=UTF-8" alt="QR Code">
            </div>

            <p style="text-align: center;">
                <a href="#" class="btn">Descarregar PDF</a>
            </p>
        </div>
        <div class="footer">
            <p>&copy; 2026 Entrades huevostyle. Tots els drets reservats.</p>
        </div>
    </div>
</body>
</html>
