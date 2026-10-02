(function(){
  if(document.querySelector('.reportes')){
    const graficaventa = document.querySelectorAll<HTMLButtonElement>('.graficaventa');
    const chartventas = (document.getElementById('chartventas') as HTMLCanvasElement)?.getContext('2d');
    const chartutilidad = (document.getElementById('chartutilidad') as HTMLCanvasElement)?.getContext('2d');

    let chartVentasInstance: any | null = null;

    const formatoMoneda = new Intl.NumberFormat('es-CO', {
      maximumFractionDigits: 0,
      minimumFractionDigits: 0
    });

    const setGraficaActiva = (botonActivo: HTMLButtonElement | null) => {
      graficaventa.forEach((boton) => boton.classList.toggle('active', boton === botonActivo));
    };

    setGraficaActiva(document.getElementById('graficaVentaMensual') as HTMLButtonElement | null);
    callapiventasgrafica('/admin/api/ventasGraficaMensual');

    graficaventa.forEach((btngrafica, index) =>{
      btngrafica.addEventListener('click', () =>{
        let url:string = '/admin/api/ventasGraficaMensual';
        if(index == 1){
          url = '/admin/api/ventasGraficaDiario';
        }
        setGraficaActiva(btngrafica);
        callapiventasgrafica(url);
      });
    });

    async function callapiventasgrafica(url:string){
      try {
        const respuesta = await fetch(url);
        const resultado = await respuesta.json();
        graficaVentas(resultado);
      } catch (error) {
        console.log(error);
      }
    }

    function graficaVentas(resultado:{label:string[], datos:string[]}){
      if(!chartventas)return;
      if (chartVentasInstance)chartVentasInstance.destroy();

      const gradient = chartventas.createLinearGradient(0, 0, 0, 360);
      gradient.addColorStop(0, 'rgba(79, 70, 229, .92)');
      gradient.addColorStop(.58, 'rgba(6, 182, 212, .55)');
      gradient.addColorStop(1, 'rgba(6, 182, 212, .16)');

      chartVentasInstance = new Chart(chartventas, {
        type: 'bar',
        data: {
          labels: resultado.label,
          datasets: [{
            label: 'Total Ventas',
            data: resultado.datos,
            backgroundColor: gradient,
            borderColor: 'rgba(79, 70, 229, .95)',
            borderRadius: 10,
            borderSkipped: false,
            borderWidth: 1,
            hoverBackgroundColor: 'rgba(79, 70, 229, .95)',
            maxBarThickness: 58
          }]
        },
        options: {
          animation: {
            duration: 700,
            easing: 'easeOutQuart'
          },
          maintainAspectRatio: false,
          responsive: true,
          scales: {
            y: {
              beginAtZero: true,
              border: {display: false},
              grid: {
                color: 'rgba(148, 163, 184, .18)',
                drawBorder: false
              },
              ticks: {
                color: '#64748b',
                font: {
                  size: 12,
                  weight: '600'
                },
                callback: (value:any) => formatoMoneda.format(Number(value))
              }
            },
            x: {
              border: {display: false},
              grid: {display: false},
              ticks: {
                color: '#64748b',
                font: {
                  size: 12,
                  weight: '600'
                }
              }
            }
          },
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              backgroundColor: '#14233b',
              bodyColor: '#fff',
              borderColor: 'rgba(79, 70, 229, .28)',
              borderWidth: 1,
              cornerRadius: 10,
              displayColors: false,
              padding: 12,
              titleColor: '#e0f2fe',
              callbacks: {
                label: (context:any) => `Ventas: $${formatoMoneda.format(Number(context.parsed.y || 0))}`
              }
            }
          }
        }
      });
    }

    (async ()=>{
      try {
        const url = '/admin/api/graficaValorInventario';
        const respuesta = await fetch(url);
        const resultado = await respuesta.json();
        if(!chartutilidad)return;

        new Chart(chartutilidad, {
          type: 'doughnut',
          data: {
            labels: ['Ventas total', 'Costo', 'Utilidad'],
            datasets: [{
              label: 'Inventario',
              data: [Number(resultado.valorventa), Number(resultado.costoinv), (Number(resultado.valorventa) - Number(resultado.costoinv))],
              backgroundColor: ['#4f46e5', '#06b6d4', '#22c55e'],
              borderColor: '#ffffff',
              borderRadius: 8,
              borderWidth: 5,
              hoverOffset: 8
            }]
          },
          options: {
            cutout: '66%',
            maintainAspectRatio: false,
            responsive: true,
            plugins: {
              legend: {
                align: 'center',
                labels: {
                  boxHeight: 10,
                  boxWidth: 10,
                  color: '#475569',
                  font: {
                    size: 12,
                    weight: '700'
                  },
                  padding: 14,
                  usePointStyle: true,
                  generateLabels: (chart:any) => {
                    const data = chart.data.datasets[0].data;
                    const labels = chart.data.labels;
                    return labels.map((label:any, i:any) => ({
                      text: `${label} $${formatoMoneda.format(Number(data[i] || 0))}`,
                      fillStyle: chart.data.datasets[0].backgroundColor[i],
                      strokeStyle: chart.data.datasets[0].backgroundColor[i],
                      index: i
                    }));
                  }
                }
              },
              tooltip: {
                backgroundColor: '#14233b',
                bodyColor: '#fff',
                borderColor: 'rgba(6, 182, 212, .25)',
                borderWidth: 1,
                cornerRadius: 10,
                displayColors: true,
                padding: 12,
                titleColor: '#e0f2fe',
                callbacks: {
                  label: (context:any) => `${context.label}: $${formatoMoneda.format(Number(context.parsed || 0))}`
                }
              }
            }
          }
        });
      } catch (error) {
        console.log(error);
      }
    })();
  }
})();
