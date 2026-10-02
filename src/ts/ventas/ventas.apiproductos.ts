(()=>{
  if(!document.querySelector('.ventas'))return;

  const productosAPI = {

    async getProductosAPI(){
      try {
          const url = "/admin/api/allproducts"; //llamado a la API REST
          const respuesta = await fetch(url); 
          const products = await respuesta.json();
          return products;
      } catch (error) {
          console.log(error);
      }
    }
    
  };

  (window as any).POS.productosAPI = productosAPI;


})();