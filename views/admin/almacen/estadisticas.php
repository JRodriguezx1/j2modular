<div class="box p-10 rounded-lg">

    <!-- BUSCADOR -->
    <div class="mb-8">
        <input 
            type="text" 
            id="buscarProducto"
            placeholder="Buscar producto..."
            class="bg-gray-50 w-full border border-gray-300 text-gray-900 rounded-lg p-4 text-xl focus:outline-none focus:ring-1 focus:border-indigo-600"
        >
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">

        <!-- PRECIO -->
        <div class="bg-indigo-50 p-4 rounded-2xl shadow-md">
            <div class="flex justify-between items-center h-full">
                <div>
                    <p class="text-gray-500 text-base">Precio venta</p>
                    <h2 id="precio" class="text-4xl font-bold text-indigo-700">$0</h2>
                </div>
                <span class="text-3xl">💰</span>
            </div>
        </div>

        <!-- COSTO -->
        <div class="bg-gray-100 p-4 rounded-2xl shadow-md">
            <div class="flex justify-between items-center h-full">
                <div>
                    <p class="text-gray-500 text-base">Costo</p>
                    <h2 id="costo" class="text-4xl font-bold text-gray-700">$0</h2>
                    <p id="alertaCosto" class="text-sm font-semibold mt-2"></p>
                </div>
                <span class="text-3xl">🧾</span>
            </div>
        </div>

        <!-- UTILIDAD -->
        <div class="bg-emerald-50 p-4 rounded-2xl shadow-md">
            <div class="flex justify-between items-center h-full">
                <div>
                    <p class="text-gray-500 text-base">Utilidad</p>
                    <h2 id="utilidad" class="text-4xl font-bold text-emerald-600">$0</h2>
                    <p id="alertaUtilidad" class="text-sm font-semibold mt-2"></p>
                </div>
                <span class="text-3xl">📈</span>
            </div>
        </div>

        <!-- % UTILIDAD -->
        <div class="bg-yellow-50 p-4 rounded-2xl shadow-md">
            <div class="flex justify-between items-center h-full">
                <div>
                    <p class="text-gray-500 text-base">% Utilidad</p>
                    <h2 id="porcentaje" class="text-4xl font-bold text-yellow-600">0%</h2>
                </div>
                <span class="text-3xl">📊</span>
            </div>
        </div>

    </div>

    <!-- INDICADORES -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">

        <!-- CANTIDAD -->
        <div class="bg-sky-50 p-4 rounded-2xl shadow-md">
            <div class="flex justify-between items-center h-full">
                <div>
                    <p class="text-gray-500 text-base">Cantidad vendida</p>
                    <h2 id="cantidad" class="text-4xl font-bold text-sky-600">0</h2>
                </div>
                <span class="text-3xl">📦</span>
            </div>
        </div>

        <!-- INGRESOS -->
        <div class="bg-indigo-50 p-4 rounded-2xl shadow-md">
            <div class="flex justify-between items-center h-full">
                <div>
                    <p class="text-gray-500 text-base">Ingresos</p>
                    <h2 id="ingresos" class="text-4xl font-bold text-indigo-700">$0</h2>
                </div>
                <span class="text-3xl">💵</span>
            </div>
        </div>

        <!-- GANANCIA -->
        <div class="bg-emerald-50 p-4 rounded-2xl shadow-md">
            <div class="flex justify-between items-center h-full">
                <div>
                    <p class="text-gray-500 text-base">Ganancia total</p>
                    <h2 id="ganancia" class="text-4xl font-bold text-emerald-600">$0</h2>
                    <p id="alertaGanancia" class="text-sm font-semibold mt-2"></p>
                </div>
                <span class="text-3xl">🏆</span>
            </div>
        </div>

        <!-- ROTACIÓN -->
        <div class="bg-rose-50 p-4 rounded-2xl shadow-md">
            <div class="flex justify-between items-center h-full">
                <div>
                    <p class="text-gray-500 text-base">Rotación</p>
                    <h2 id="rotacion" class="text-4xl font-bold text-rose-600">-</h2>
                    <p id="alertaRotacion" class="text-sm font-semibold mt-2"></p>
                </div>
                <span class="text-3xl">🔄</span>
            </div>
        </div>

    </div>

    <!-- GRÁFICAS -->
    <div class="bg-white p-6 rounded-2xl shadow-md mb-8">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Ventas del producto</h3>
        <canvas id="graficaVentas"></canvas>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-md mb-8">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">
            Historial de costos vs precios
        </h3>
        <canvas id="graficaCostos"></canvas>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

let chartVentas;
let chartCostos;

const dataProducto = {
    precio: 20000,
    costo: 12000,
    cantidad: 80,
    ventas: [10, 20, 15, 30, 25, 40],
    fechas: ["Ene", "Feb", "Mar", "Abr", "May", "Jun"],
    costos: [10000, 11000, 12000, 13000, 12000, 12500],
    precios: [18000, 19000, 20000, 20000, 21000, 22000]
};

function cargarEstadisticas(data) {

    let utilidad = data.precio - data.costo;
    let porcentaje = ((utilidad / data.costo) * 100).toFixed(2);
    let ingresos = data.precio * data.cantidad;
    let ganancia = utilidad * data.cantidad;

    let rotacion = "Baja";
    if(data.cantidad > 50) rotacion = "Media";
    if(data.cantidad > 100) rotacion = "Alta";

    document.getElementById("precio").innerText = "$" + data.precio.toLocaleString();
    document.getElementById("costo").innerText = "$" + data.costo.toLocaleString();
    document.getElementById("utilidad").innerText = "$" + utilidad.toLocaleString();
    document.getElementById("porcentaje").innerText = porcentaje + "%";

    document.getElementById("cantidad").innerText = data.cantidad;
    document.getElementById("ingresos").innerText = "$" + ingresos.toLocaleString();
    document.getElementById("ganancia").innerText = "$" + ganancia.toLocaleString();
    document.getElementById("rotacion").innerText = rotacion;

    evaluarAlertas(data, utilidad, porcentaje, ganancia);

    renderGraficaVentas(data);
    renderGraficaCostos(data);
}

// 🔥 ALERTAS INTELIGENTES
function evaluarAlertas(data, utilidad, porcentaje, ganancia){

    // UTILIDAD
    let alertaUtilidad = document.getElementById("alertaUtilidad");

    if(porcentaje > 50){
        alertaUtilidad.innerText = "🟢 Excelente margen";
        alertaUtilidad.className = "text-green-600 text-sm font-semibold mt-2";
    } else if(porcentaje > 20){
        alertaUtilidad.innerText = "🟡 Margen aceptable";
        alertaUtilidad.className = "text-yellow-600 text-sm font-semibold mt-2";
    } else{
        alertaUtilidad.innerText = "🔴 Margen bajo";
        alertaUtilidad.className = "text-rose-600 text-sm font-semibold mt-2";
    }

    // ROTACIÓN
    let alertaRotacion = document.getElementById("alertaRotacion");

    if(data.cantidad > 100){
        alertaRotacion.innerText = "🟢 Alta demanda";
    } else if(data.cantidad > 50){
        alertaRotacion.innerText = "🟡 Rotación normal";
    } else{
        alertaRotacion.innerText = "🔴 Producto estancado";
    }

    // GANANCIA
    let alertaGanancia = document.getElementById("alertaGanancia");

    if(ganancia > 500000){
        alertaGanancia.innerText = "🟢 Producto rentable";
        alertaGanancia.className = "text-green-600 text-sm font-semibold mt-2";
    } else{
        alertaGanancia.innerText = "🟡 Puede mejorar";
        alertaGanancia.className = "text-yellow-600 text-sm font-semibold mt-2";
    }

    // COSTO (comparación simple)
    let alertaCosto = document.getElementById("alertaCosto");

    let ultimoCosto = data.costos[data.costos.length - 1];
    let primerCosto = data.costos[0];

    if(ultimoCosto > primerCosto){
        alertaCosto.innerText = "🔴 Costo en aumento";
        alertaCosto.className = "text-rose-600 text-sm font-semibold mt-2";
    } else{
        alertaCosto.innerText = "🟢 Costo estable";
        alertaCosto.className = "text-green-600 text-sm font-semibold mt-2";
    }
}

function renderGraficaVentas(data){
    if(chartVentas) chartVentas.destroy();

    chartVentas = new Chart(document.getElementById("graficaVentas"), {
        type: 'line',
        data: {
            labels: data.fechas,
            datasets: [{
                label: 'Ventas',
                data: data.ventas,
                borderWidth: 2,
                tension: 0.3
            }]
        }
    });
}

function renderGraficaCostos(data){
    if(chartCostos) chartCostos.destroy();

    chartCostos = new Chart(document.getElementById("graficaCostos"), {
        type: 'line',
        data: {
            labels: data.fechas,
            datasets: [
                { label: 'Costo', data: data.costos, borderWidth: 2, tension: 0.3 },
                { label: 'Precio', data: data.precios, borderWidth: 2, tension: 0.3 }
            ]
        }
    });
}

document.getElementById("buscarProducto").addEventListener("keyup", function(){
    cargarEstadisticas(dataProducto);
});

cargarEstadisticas(dataProducto);

</script>