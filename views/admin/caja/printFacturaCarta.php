<div class="min-h-screen flex flex-col">
    <!-- Contenedor Factura -->
    <div class="flex-1">
        <!-- Invoice -->
        <div class="max-w-[85rem] px-16 mx-auto my-10">

            <!-- Grid -->
            <div class="flex justify-between">
                <div>
                    <div class="grid space-y-3">
                        <img class="w-auto h-24" src="/build/img/<?php echo $sucursal->logo??'Logoj2negro.png';?>" alt="logo">
                        <dl class="flex flex-col gap-y-3 text-sm pt-20">
                            <div class="font-medium text-gray-800 text-lg leading-normal">
                                <span class="block font-semibold uppercase">Facturado a</span>
                                <span class="not-italic font-normal text-gray-400"><?php echo $cliente->nombre.' '.$cliente->apellido;?></span>
                                <address class="not-italic font-normal text-gray-400">
                                    <span class="font-semibold">NIT/CC:</span><?php echo $cliente->identificacion;?>,<br>
                                    <span class="font-semibold uppercase">Email:</span><?php echo $cliente->email;?>,<br>
                                    <span class="font-semibold uppercase">Teléfono:</span><?php echo $cliente->telefono;?><br>
                                </address>
                            </div>
                            <div class="font-medium text-gray-800 text-lg leading-normal mt-5">
                                <span class="block font-semibold uppercase">Dirección de entrega</span>
                                <address class="not-italic font-normal text-gray-400">
                                    <?php echo $direccion->direccion;?>,<br>
                                    <?php echo $direccion->ciudad.' - '.$direccion->departamento;?><br>
                                </address>
                            </div>
                        </dl>
                    </div>
                </div>
                <!-- Col -->

                <div class="text-lg leading-normal">
                    <div class="grid font-medium text-gray-800 text-center text-lg leading-normal">
                        <span class="block font-semibold text-lg uppercase"><?php echo $factura->nombrecompania??' - ';?></span>
                        <address class="not-italic font-light">
                            <?php echo $sucursal->nombre;?>,<br>
                            <?php echo $sucursal->direccion;?>,<br>
                            Tel: <?php echo $sucursal->telefono;?>,<br>
                            <?php echo $sucursal->ciudad.' - '.$sucursal->departamento;?>,<br>
                            <?php echo $sucursal->email;?><br>
                            <?php //echo $sucursal->www;?><br>
                        </address>
                    </div>
                </div>
                <!-- Col -->

                <div>
                    <div class="grid space-y-3">
                        <div class="text-lg leading-normal">
                            <p class="min-w-36 max-w-[200px] text-gray-800 text-lg font-semibold">FACTURA #:</p>
                            <span class="text-gray-500"><?php echo $factura->prefijo.''.$factura->num_orden??'';?></span>
                        </div>
                        <div class="text-lg leading-normal">
                            <p class="min-w-36 max-w-[200px] text-gray-800 text-lg font-semibold">Vendedor:</p>
                            <span class="text-gray-500"><?php echo $vendedor->nombre.' '.($vendedor->apellido??'');?></span>
                        </div>

                        <div class="flex flex-col gap-x-1 pt-8 text-lg leading-normal">
                            <p class="font-medium min-w-36 max-w-[200px] text-gray-800">
                                <span class="uppercase"> Fecha y Hora de Factura</span> <br>
                                <span class=" font-normal text-gray-400"><?php echo $factura->fechapago??'';?></span>
                            </p>
                            <p class="font-medium text-gray-800 mt-4">
                                <span class="uppercase"> Medio de Pago</span> <br>
                                <span class="font-normal text-gray-400"><?php foreach($mediospago as $value): echo $value->mediopago.' '; endforeach; ?></span>
                            </p>
                        </div>
                    </div>
                </div>
                <!-- Col -->
            </div>
            <!-- End Grid -->
            <?php if($factura->estado == 'Eliminada'): ?>
                <div><p class="block font-semibold uppercase text-center">Factura eliminada. <span class="font-normal text-gray-400 text-base normal-case">Documento no valido</span></p></div>
            <?php endif; ?>
            <!-- Table -->
            <div class="mt-6 border border-gray-200 p-4 rounded-lg space-y-4 text-lg leading-normal">
                <div class="hidden sm:grid sm:grid-cols-5">
                    <div class="sm:col-span-2 text-base font-nomal text-gray-400 uppercase">Item</div>
                    <div class="text-start text-base font-nomal text-gray-400 uppercase">Cantidad</div>
                    <div class="text-start text-base font-nomal text-gray-400 uppercase">Vr. Unitario</div>
                    <div class="text-end text-base font-nomal text-gray-400 uppercase">Vr. Total</div>
                </div>
                <div class="hidden sm:block border-b border-gray-200"></div>

                <?php foreach($productos as $index=>$value): ?>
                    <div class="grid grid-cols-3 sm:grid-cols-5 gap-2">
                        <div class="col-span-full sm:col-span-2">
                            <p class="text-lg font-medium text-gray-800"><?php echo $value->nombreproducto??'';?></p>
                        </div>
                        <div>
                            <p class="text-lg text-gray-800"><?php echo $value->cantidad??'';?></p>
                        </div>
                        <div>
                            <p class="text-lg text-gray-800"><?php echo number_format($value->valorunidad??'', '0', ',', '.');?></p>
                        </div>
                        <div>
                            <p class="text-lg sm:text-end text-gray-800">$<?php echo number_format($value->total??'', '0', ',', '.');?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- End Table -->

            <!-- Totales -->
            <div class="mt-8 flex sm:justify-end">
                <div class="w-full max-w-2xl sm:text-end space-y-2">
                    <div class="grid grid-cols-2 sm:grid-cols-1 gap-3 sm:gap-2">
                        <dl class="grid sm:grid-cols-5 gap-x-3 text-sm">
                            <dt class="col-span-3 text-gray-400">Subotal:</dt>
                            <dd class="col-span-2 font-medium text-gray-800">$<?php echo number_format($factura->subtotal??'', '0', ',', '.');?></dd>
                        </dl>

                        <dl class="grid sm:grid-cols-5 gap-x-3 text-sm">
                            <dt class="col-span-3 text-gray-400">Descuento:</dt>
                            <dd class="col-span-2 font-medium text-gray-800">$<?php echo number_format($factura->descuento??'', '0', ',', '.');?></dd>
                        </dl>

                        <dl class="grid sm:grid-cols-5 gap-x-3 text-sm">
                            <dt class="col-span-3 text-gray-400">Impuesto:</dt>
                            <dd class="col-span-2 font-medium text-gray-800">$<?php echo number_format($factura->valorimpuestototal??'', '0', ',', '.');?></dd>
                        </dl>

                        <dl class="grid sm:grid-cols-5 gap-x-3 text-sm">
                            <dt class="col-span-3 text-gray-400">Tarifa envio:</dt>
                            <dd class="col-span-2 font-medium text-gray-800">$<?php echo number_format($factura->valortarifa??0, '0', ',', '.');?></dd>
                        </dl>

                        <dl class="grid sm:grid-cols-5 gap-x-3 text-sm">
                            <dt class="col-span-3 text-gray-400">Total:</dt>
                            <dd class="col-span-2 font-medium text-gray-800">$<?php echo number_format($factura->total??'','0', ',', '.');?></dd>
                        </dl>
                    </div>
                </div>
            </div>
            <!-- End Totales -->

            <!-- Observaciones -->
            <div class="mt-8">
                <div class="border border-gray-200 p-4 rounded-lg space-y-2 text-lg leading-normal">
                    <span class="block font-semibold uppercase text-gray-800">Observaciones</span>
                    <p class="text-gray-500">
                        <?php echo $factura->observaciones ?? 'Ninguna'; ?>
                    </p>
                </div>
            </div>
            <!-- End Observaciones -->
        </div>
        <!-- End Invoice -->
    </div>

    <!-- Footer -->
    <footer class="border-t border-gray-200 py-5 text-center text-sm text-gray-500 leading-snug">
        <p class="mb-1.5">
            Esta factura es un documento válido generado electrónicamente por 
            <span class="font-semibold text-gray-700"><?php echo $factura->nombrecompania??' - ';?></span> - NIT <?php echo $factura->nit ?? ' - ';?>.
        </p>
        <p class="mb-1">Gracias por su compra.</p>
        <p class="mb-1">
            Contáctanos: 
            <a href="mailto:correo@empresa.com" class="text-indigo-600 hover:underline"><?php echo $sucursal->email??'';?></a> 
            | Tel: <?php echo $sucursal->telefono??'';?>
        </p>
        <p class="mb-1">Dirección: <?php echo $sucursal->direccion??'';?>, <?php echo $sucursal->ciudad??'';?> - <?php echo $sucursal->departamento??'';?></p>
        <p class="mt-3 text-xs text-gray-400">
            © <?php echo date("Y"); ?> <?php echo $factura->nombrecompania??' - ';?>. Todos los derechos reservados.
        </p>
        <p class="mt-1 text-xs text-gray-400">
            Generado con <span class="text-indigo-500 font-semibold">J2 Software POS Multisucursal</span>
        </p>
    </footer>
    <!-- End Footer -->

</div>
