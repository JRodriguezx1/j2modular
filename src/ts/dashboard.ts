(function():void{
  if(document.querySelector('.inicio')){
    const formatoMoneda = new Intl.NumberFormat('es-CO', {
      maximumFractionDigits: 0,
      minimumFractionDigits: 0
    });

    const normalizarNumero = (valor:string | number | null | undefined):number => {
      const numero = Number(valor ?? 0);
      return Number.isFinite(numero) ? numero : 0;
    };

    const crearGradiente = (ctx:CanvasRenderingContext2D, colorInicio:string, colorFin:string):CanvasGradient => {
      const gradient = ctx.createLinearGradient(0, 0, 0, 320);
      gradient.addColorStop(0, colorInicio);
      gradient.addColorStop(1, colorFin);
      return gradient;
    };

    const opcionesBase = {
      animation: {
        duration: 750,
        easing: 'easeOutQuart'
      },
      maintainAspectRatio: false,
      responsive: true,
      plugins: {
        legend: {
          labels: {
            boxHeight: 10,
            boxWidth: 10,
            color: '#475569',
            font: {
              size: 12,
              weight: '700'
            },
            padding: 14,
            usePointStyle: true
          }
        },
        tooltip: {
          backgroundColor: '#14233b',
          bodyColor: '#fff',
          borderColor: 'rgba(79, 70, 229, .25)',
          borderWidth: 1,
          cornerRadius: 10,
          displayColors: true,
          padding: 12,
          titleColor: '#e0f2fe'
        }
      }
    } as any;

    async function ventasgraficas($url:string, $dato:string){
      try{
        const respuesta = await fetch($url);
        const resultado = await respuesta.json();
        if($dato == 'ventasVsGastos')graficaVentasVsGastos(resultado);
        if($dato == 'ultimos7dias')ventas7dias(resultado);
      }catch(error){
        console.log(error);
      }
    }

    ventasgraficas('/admin/api/ventasVsGastos', 'ventasVsGastos');
    ventasgraficas('/admin/api/ultimos7dias', 'ultimos7dias');

    function ventas7dias(resultado:{dia:string, ventas_totales:string}[]){
      const canvas = document.getElementById('chartIngresosDias') as HTMLCanvasElement | null;
      const ctxIngresosDias = canvas?.getContext('2d');
      if(!ctxIngresosDias)return;

      const gradienteIngresos = crearGradiente(ctxIngresosDias, 'rgba(6, 182, 212, .92)', 'rgba(79, 70, 229, .16)');

      new Chart(ctxIngresosDias, {
        type: 'bar',
        data: {
          labels: resultado.map(x=>x.dia),
          datasets: [{
            label: 'Ingresos',
            data: resultado.map(x=>normalizarNumero(x.ventas_totales)),
            backgroundColor: gradienteIngresos,
            borderColor: 'rgba(6, 182, 212, .92)',
            borderRadius: 10,
            borderSkipped: false,
            borderWidth: 1,
            hoverBackgroundColor: 'rgba(79, 70, 229, .86)',
            maxBarThickness: 58
          }]
        },
        options: {
          ...opcionesBase,
          plugins: {
            ...opcionesBase.plugins,
            legend: {display: false},
            tooltip: {
              ...opcionesBase.plugins.tooltip,
              displayColors: false,
              callbacks: {
                label: (context:any) => `Ingresos: $${formatoMoneda.format(normalizarNumero(context.parsed.y))}`
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              border: {display: false},
              grid: {color: 'rgba(148, 163, 184, .16)', drawBorder: false},
              ticks: {
                color: '#64748b',
                font: {size: 12, weight: '600'},
                callback: (value:any) => formatoMoneda.format(normalizarNumero(value))
              }
            },
            x: {
              border: {display: false},
              grid: {display: false},
              ticks: {color: '#64748b', font: {size: 12, weight: '600'}}
            }
          }
        }
      });
    }

    function graficaVentasVsGastos(resultado:{periodo:string[], ventastotales:string[], gastostotales:string[]}){
      const canvas = document.getElementById('chartVentasGastos') as HTMLCanvasElement | null;
      const ctxVG = canvas?.getContext('2d');
      if(!ctxVG)return;

      const gradienteVentas = crearGradiente(ctxVG, 'rgba(79, 70, 229, .9)', 'rgba(79, 70, 229, .14)');
      const gradienteGastos = crearGradiente(ctxVG, 'rgba(6, 182, 212, .18)', 'rgba(6, 182, 212, .02)');

      new Chart(ctxVG, {
        data: {
          labels: resultado.periodo,
          datasets: [
            {
              type: 'bar',
              label: 'Ventas',
              data: resultado.ventastotales.map(x=>normalizarNumero(x)),
              backgroundColor: gradienteVentas,
              borderColor: 'rgba(79, 70, 229, .92)',
              borderRadius: 10,
              borderSkipped: false,
              borderWidth: 1,
              maxBarThickness: 48,
              order: 2
            },
            {
              type: 'line',
              label: 'Gastos',
              data: resultado.gastostotales.map(x=>normalizarNumero(x)),
              borderColor: '#06b6d4',
              backgroundColor: gradienteGastos,
              borderWidth: 3,
              fill: true,
              pointBackgroundColor: '#fff',
              pointBorderColor: '#06b6d4',
              pointBorderWidth: 2,
              pointHoverRadius: 6,
              pointRadius: 4,
              tension: 0.35,
              yAxisID: 'y1',
              order: 1
            }
          ]
        },
        options: {
          ...opcionesBase,
          plugins: {
            ...opcionesBase.plugins,
            legend: {
              ...opcionesBase.plugins.legend,
              position: 'bottom'
            },
            tooltip: {
              ...opcionesBase.plugins.tooltip,
              callbacks: {
                label: (context:any) => `${context.dataset.label}: $${formatoMoneda.format(normalizarNumero(context.parsed.y))}`
              }
            }
          },
          interaction: {
            intersect: false,
            mode: 'index'
          },
          scales: {
            y: {
              beginAtZero: true,
              border: {display: false},
              grid: {color: 'rgba(148, 163, 184, .16)', drawBorder: false},
              position: 'left',
              ticks: {
                color: '#64748b',
                font: {size: 12, weight: '600'},
                callback: (value:any) => formatoMoneda.format(normalizarNumero(value))
              },
              title: {display: false}
            },
            y1: {
              beginAtZero: true,
              border: {display: false},
              grid: {drawOnChartArea: false},
              position: 'right',
              ticks: {
                color: '#64748b',
                font: {size: 12, weight: '600'},
                callback: (value:any) => formatoMoneda.format(normalizarNumero(value))
              },
              title: {display: false}
            },
            x: {
              border: {display: false},
              grid: {display: false},
              ticks: {color: '#64748b', font: {size: 12, weight: '600'}}
            }
          }
        }
      });
    }
  }
})();
