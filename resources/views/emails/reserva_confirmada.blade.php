<h2>¡Tu reserva ha sido confirmada!</h2>
<p>Detalles de la reserva:</p>
<ul>
    <li>Vehículo: {{ $reserva->vehiculo->modelo }}</li>
    <li>Fecha inicio: {{ $reserva->fecha_inicio->format('d/m/Y') }}</li>
    <li>Fecha fin: {{ $reserva->fecha_fin->format('d/m/Y') }}</li>
    <li>Precio total: {{ $reserva->precio_total }} €</li>
    <li>Depósito pagado: {{ $reserva->deposito }} €</li>
</ul>
<p>¡Gracias por confiar en nosotros!</p>