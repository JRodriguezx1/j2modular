function r(){let e=document.querySelector("#restaurantReservationsData");if(!e)return{zones:[]};try{return JSON.parse(e.textContent??"{}")}catch(t){return console.error("Restaurant \u2192 Error leyendo datos de reservas",t),{zones:[]}}}function s(){console.log("Restaurant \u2192 Reservations cargado");let e=r();}export{s as initReservations};
//# sourceMappingURL=reservations-YI2Q2Q7I.js.map
