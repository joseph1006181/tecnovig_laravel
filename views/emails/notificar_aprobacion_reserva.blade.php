<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Estado de tu Reserva</title>
</head>
<body>
    @php
        $estadoTexto = '';
        $estadoEmoji = '';

        switch($confirmacion) {
            case 0:
            case 1:
                $estadoTexto = '¡Tu reserva ha sido autorizada!';
                $estadoEmoji = '✅';
                break;
            case 2:
                $estadoTexto = 'Tu reserva ha sido rechazada';
                $estadoEmoji = '❌';
                break;
          
            case 3:
                $estadoTexto = 'Tu reserva ha sido cancelada';
                $estadoEmoji = '❌';
                break;
            default:
                $estadoTexto = 'Tu reserva está pendiente de autorización';
                $estadoEmoji = '⏳';
                break;
        }
    @endphp

    <p>{{ $estadoEmoji }} <strong>{{ $estadoTexto }}</strong></p>

    <p>Hola {{ $nombre }},</p>

    <p>
        Tu reserva para <strong>{{ $espacio }}</strong> tiene el siguiente estado:
    </p>
    <p>
        {{$estadoTexto}}
    </p>
    <p>
        📅 <strong>Fecha:</strong> {{ $fecha }}<br>
        🕒 <strong>Hora:</strong> {{ $horaInicio }} – {{ $horaFin }}<br>
        📍 <strong>Ubicación:</strong> {{ $espacio }} del conjunto
    </p>

    @if (!empty($notaAutorizacion))
        <p>
            📝 <strong>Nota de autorización:</strong><br>
            {{ $notaAutorizacion }}
        </p>
    @endif

    @if ($confirmacion == 1)
        <p>Por favor, preséntate 5 minutos antes y lleva tu documento de identificación.</p>
        <p>¡Gracias por utilizar nuestro sistema de reservas!</p>
    @elseif ($confirmacion == 2 && $confirmacion == 3)
        <p>Para más información puedes comunicarte con la administración.</p>
    @endif
</body>
</html>
