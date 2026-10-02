//animacion de botones de alternancia de flecha
if(document.querySelector('.btnrotated')){
  const arrow = document.querySelectorAll('#arrow');
  document.querySelectorAll('.btnrotated').forEach((btn, index) => {
    btn.addEventListener('click', (e) => {
      arrow[index].classList.toggle('rotated');
      const x = document.querySelector(`DIV[data-switch="${e.target.dataset.switch}"]`);
      if(x)x.classList.toggle('showacordeon');
    });
  });
}