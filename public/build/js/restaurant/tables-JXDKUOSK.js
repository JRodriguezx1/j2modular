function m(t){let n=document.createElement("div");return n.textContent=t,n.innerHTML}function c(t,n){let o=document.getElementById(t);o&&(o.textContent=n)}async function M(t){let n=`/restaurant/api/occupations/active?resourceId=${encodeURIComponent(t)}`,o=await fetch(n,{headers:{Accept:"application/json"}}),e=await o.json();if(!o.ok||!e.success)throw new Error(e.message??"No fue posible consultar la atenci\xF3n.");if(e.occupation===void 0)throw new Error("La respuesta de la atenci\xF3n est\xE1 incompleta.");return e.occupation}async function N(t){let n=`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(t)}`,o=await fetch(n,{headers:{Accept:"application/json"}}),e=await o.json();if(!o.ok||!e.success||!e.data)throw new Error(e.message??"No fue posible consultar las cuentas.");return e.data}var f,H,x,J,X;function vt(){window.clearTimeout(X),H.classList.remove("hidden"),f.inert=!1,requestAnimationFrame(()=>{f.classList.remove("translate-x-full")}),f.setAttribute("aria-hidden","false")}function I(){f.getAttribute("aria-hidden")!=="true"&&(++P,f.classList.add("translate-x-full"),f.setAttribute("aria-hidden","true"),f.inert=!0,X=window.setTimeout(()=>{H.classList.add("hidden")},300))}function yt(){x.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function xt(t){let n=t.clientName?m(t.clientName):"Sin cliente asociado",o=t.numberOfPeople!==null?String(t.numberOfPeople):"\u2014",e=t.reservationId!==null?`#${t.reservationId}`:"Atenci\xF3n directa",a=t.observations?m(t.observations):"Sin observaciones";x.innerHTML=`
        <div class="space-y-6">
            <div class="rounded-2xl bg-emerald-50 p-4">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-emerald-600">
                        restaurant
                    </span>

                    <div>
                        <p class="text-xs font-medium uppercase text-emerald-700">
                            Estado
                        </p>
                        <p class="font-semibold text-emerald-900">
                            Atenci\xF3n en curso
                        </p>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                ${E("Cliente",n)}
                ${E("Personas",o)}
                ${E("Origen",e)}
                ${E("Inicio",G(t.startDate))}
                ${E("Fin estimado",t.estimatedEndDate?G(t.estimatedEndDate):"\u2014")}
                ${E("Observaciones",a)}
            </div>
        </div>`}function E(t,n){return`
        <div class="border-b border-slate-100 pb-4">
            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-400">
                ${t}
            </p>
            <p class="text-sm font-medium text-slate-800">
                ${n}
            </p>
        </div>`}function G(t){let n=new Date(t.replace(" ","T"));return Number.isNaN(n.getTime())?t:new Intl.DateTimeFormat("es",{dateStyle:"medium",timeStyle:"short"}).format(n)}function At(t){let n=document.createElement("section");n.className="mt-8 border-t border-slate-200 pt-5",n.innerHTML=`
        <h3 class="mb-3 font-semibold text-slate-900">Cuentas</h3>
        ${t.length===0?'<p class="text-sm text-slate-500">Esta atenci\xF3n no tiene cuentas.</p>':t.map(o=>`
                <article class="mb-2 rounded-xl border border-slate-200 p-3">
                    <p class="font-medium text-slate-900">${m(o.name)}</p>
                    <p class="text-xs text-slate-500">
                        ${m(o.status)} \xB7 ${o.invoiceId===null?"Sin orden todav\xEDa":`Orden #${o.invoiceId}`}
                    </p>
                </article>
            `).join("")}`,x.appendChild(n)}function Et(t){let n=document.createElement("p");n.className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700",n.textContent=t,x.appendChild(n)}var P=0;function Y(){let t=document.getElementById("activeOccupationDrawer"),n=document.getElementById("activeOccupationOverlay"),o=document.getElementById("activeOccupationContent"),e=document.getElementById("activeOccupationActions");!t||!n||!o||!e||(f=t,H=n,x=o,J=e,f.inert=!0,f.addEventListener("click",a=>{a.target.closest("[data-close-active-occupation]")&&I()}),H.addEventListener("click",I),document.addEventListener("keydown",a=>{a.key==="Escape"&&f.getAttribute("aria-hidden")==="false"&&I()}))}async function Z(t){if(!t)return;let n=++P;vt(),yt(),J.classList.add("hidden");try{let o=await M(t);if(n!==P)return;if(o===null){x.textContent="La mesa no tiene una atenci\xF3n activa.";return}xt(o);try{let e=await N(o.occupationId);if(n!==P)return;At(e.accounts)}catch(e){if(n!==P)return;Et(e instanceof Error?e.message:"No fue posible consultar las cuentas.")}}catch(o){if(n!==P)return;x.innerHTML=`
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                ${m(o instanceof Error?o.message:"Ocurri\xF3 un error inesperado.")}
            </div>`}}function tt(){let t=[];function n(s){if(!Number.isFinite(s)||s<=0)throw new Error("La cantidad debe ser un n\xFAmero mayor que cero.")}function o(s,u=1){n(u);let d=Number(s.precioVenta);if(!Number.isInteger(s.id)||s.id<=0)throw new Error("El producto no es v\xE1lido.");if(!Number.isFinite(d)||d<0)throw new Error("El precio del producto no es v\xE1lido.");let l=t.find(p=>p.productId===s.id);if(l){let p=l.quantity+u;n(p),l.quantity=p;return}t.push({productId:s.id,name:s.nombre,unitPrice:d,quantity:u})}function e(s,u){n(u);let d=t.find(l=>l.productId===s);if(!d)throw new Error("El producto no est\xE1 en el carrito.");d.quantity=u}function a(s){t=t.filter(u=>u.productId!==s)}function i(){return t.map(s=>({...s}))}function r(){t=[]}return{addProduct:o,updateQuantity:e,removeProduct:a,getItems:i,clear:r}}async function et(){let n=await fetch("/admin/api/inventory/getAllcategorias");if(!n.ok)throw new Error("No fue posible consultar las categorias.");let o=await n.json();if(!Array.isArray(o))throw new Error("La respuesta de categor\xEDas no tiene el formato esperado.");return o}async function nt(t=1,n=null,o=""){let e=new URLSearchParams({page:String(t)});n!=null&&e.set("categoriaId",String(n)),o&&e.set("search",o);let a=`/admin/api/inventory/listSimpleProducts?${e.toString()}`,i=await fetch(a),r=await i.json();if(!i.ok||!r.success||!r.data||!Array.isArray(r.data.products))throw new Error("No fue posible consultar las productos.");return r.data}async function ot(t,n){if(!Number.isInteger(t)||t<=0)throw new Error("El ID de la cuenta no es v\xE1lido.");let o=`/restaurant/api/accounts/summary?accountId=${encodeURIComponent(String(t))}`,e=await fetch(o,{headers:{Accept:"application/json"},signal:n}),a=await e.json();if(!e.ok||!a.success||!a.data)throw new Error(a.message??"No fue posible consultar la cuenta.");if(!Array.isArray(a.data.consumos)||!a.data.totales||typeof a.data.totales!="object"||Array.isArray(a.data.totales))throw new Error("La respuesta de la cuenta no tiene el formato esperado.");return a.data}var T=0,k=null,Pt=new Intl.NumberFormat("es-CO",{style:"currency",currency:"COP",minimumFractionDigits:0,maximumFractionDigits:2}),ht=new Intl.NumberFormat("es-CO",{maximumFractionDigits:2});function y(t,n){let o=document.getElementById(t);if(!o)return;let e=document.createElement("p");e.className="text-sm text-slate-500",e.textContent=n,o.replaceChildren(e)}function A(t){let n=Number(t);return Number.isFinite(n)?Pt.format(n):"\u2014"}function at(t){let n=Number(t);return Number.isFinite(n)?ht.format(n):"\u2014"}function Ct(t){if(t.length===0){y("accountPanelConsumptions","Sin productos registrados.");return}let n=document.getElementById("accountPanelConsumptions"),o=document.getElementById("accountConsumptionTemplate");if(!n||!(o instanceof HTMLTemplateElement))throw new Error("No se encontr\xF3 la vista de productos registrados.");let e=document.createDocumentFragment();for(let a of t){let i=o.content.firstElementChild?.cloneNode(!0);if(!(i instanceof HTMLElement))throw new Error("La vista de productos registrados no es v\xE1lida.");let r=i.querySelector("[data-consumption-quantity]"),s=i.querySelector("[data-consumption-name]"),u=i.querySelector("[data-consumption-unit-price]"),d=i.querySelector("[data-consumption-total]");if(!r||!s||!u||!d)throw new Error("La vista de productos registrados est\xE1 incompleta.");r.textContent=at(a.cantidad),s.textContent=a.nombreproducto,u.textContent=`${A(a.valorunidad)} c/u`,d.textContent=A(a.total),e.append(i)}n.replaceChildren(e)}function wt(t){c("accountPanelName",t.name);let n=at(t.totales.totalunidades);c("accountPanelRegisteredCount",`${n} ${n==="1"?"producto":"productos"}`),Ct(t.consumos);let o=A(t.totales.total);c("accountPanelRegisteredAmount",o),c("accountPanelDiscount",A(t.totales.descuento)),c("accountPanelTax",`${A(t.totales.valorimpuestototal)} incluidos`),c("accountPanelTotal",o),c("accountPanelPayTotal",o)}function h(){++T,k?.abort(),k=null,document.getElementById("accountPanel")?.setAttribute("aria-busy","false"),c("atencionNoActiva","Atenci\xF3n por consultar"),c("atencionEstadoResumen","Mesa sin atencion"),c("atencionEstadoCuenta","Listo para ordenar"),c("accountPanelOccupation","Atenci\xF3n por consultar"),c("accountPanelName","Cuenta por consultar"),c("accountPanelClientName","Consumidor final"),c("accountPanelRegisteredCount","\u2014 \xEDtems"),y("accountPanelConsumptions","Los productos registrados aparecer\xE1n aqu\xED."),c("accountPanelPendingCount","0 item sin guardar"),c("accountPanelRegisteredAmount","\u2014"),c("accountPanelPendingAmount",A(0)),c("accountPanelDiscount","\u2014"),c("accountPanelTax","\u2014"),c("accountPanelTotal","\u2014"),c("accountPanelPayTotal","\u2014"),y("accountPanelPendingProducts","No hay productos nuevos.")}function rt(t){h(),c("atencionEstadoCuenta","Cuenta no disponible"),y("accountPanelConsumptions","No fue posible consultar los productos registrados.")}async function ct(t,n){h();let o=T;if(c("accountPanelOccupation",`Atenci\xF3n #${t.occupationId}`),c("accountPanelClientName",t.clientName?.trim()||(t.clientId===null?"Consumidor final":"Cliente por consultar")),n.length===0)return{status:"empty"};let e=n.filter(r=>r.occupationId===t.occupationId&&r.status==="abierta");if(e.length===0)return c("atencionEstadoCuenta","Sin cuenta abierta"),y("accountPanelConsumptions","Sin cuenta para consultar productos."),{status:"closed"};if(e.length>1)return c("atencionEstadoCuenta","Varias cuentas abiertas"),y("accountPanelConsumptions","Selecciona una cuenta cuando esta opci\xF3n est\xE9 disponible."),{status:"selection-required"};let a=e[0];c("atencionEstadoCuenta","Cuenta abierta"),c("accountPanelName",a.name),y("accountPanelConsumptions","Cargando productos registrados..."),document.getElementById("accountPanel")?.setAttribute("aria-busy","true");let i=new AbortController;k=i;try{let r=await ot(a.id,i.signal);if(o!==T)return{status:"cancelled"};if(r.accountId!==a.id||r.occupationId!==t.occupationId)throw new Error("El resumen recibido no corresponde a esta cuenta.");return wt(r),{status:"ready",account:a}}catch{if(o!==T||i.signal.aborted)return{status:"cancelled"};y("accountPanelConsumptions","No fue posible consultar los productos registrados.")}finally{o===T&&(k=null,document.getElementById("accountPanel")?.setAttribute("aria-busy","false"))}return{status:"error"}}function B(t){h(),c("atencionNoActiva","Sin atenci\xF3n registrada"),c("atencionEstadoResumen","Mesa sin atencion"),c("atencionEstadoCuenta","Listo para ordenar"),c("accountPanelOccupation",t?`Atenci\xF3n #${t.occupationId}`:"Sin atenci\xF3n registrada"),c("accountPanelName","Cuenta por crear"),c("accountPanelClientName",t?.clientName?.trim()||"Sin cliente seleccionado"),c("accountPanelRegisteredCount","0 \xEDtems"),c("accountPanelRegisteredAmount",A(0)),y("accountPanelConsumptions","Sin productos registrados.")}var j=new Intl.NumberFormat("es-CO",{style:"currency",currency:"COP",minimumFractionDigits:0,maximumFractionDigits:2}),Lt=new Intl.NumberFormat("es-CO",{maximumFractionDigits:2});function V(t){let n=document.getElementById("accountPanelPendingProducts");if(!n)return;let o=t.length;if(c("accountPanelPendingCount",`${o} ${o===1?"\xEDtem":"\xEDtems"} sin guardar`),c("accountPanelPendingAmount",o===0?j.format(0):"Por calcular"),o===0){n.innerHTML='<p class="text-sm text-slate-500">No hay productos nuevos.</p>';return}n.innerHTML=t.map(e=>{let a=m(e.name),i=Lt.format(e.quantity),r=j.format(e.unitPrice),s=j.format(e.quantity*e.unitPrice);return`
                <div data-cart-product-id="${e.productId}" class="rounded-xl border border-slate-200 bg-white p-4">
                    <!-- Encabezado -->
                    <div class="flex items-center justify-between gap-3">
                        <p class="m-0 nombreproducto text-xl font-semibold text-slate-800 leading-7 break-words">${a}</p>
                        <button data-cart-action="remove" class="eliminarProducto flex justify-center items-center size-7 rounded-lg border border-red-200 bg-red-50 text-red-500 transition-all duration-300 hover:bg-red-600 hover:text-white  hover:border-red-600 hover:shadow-md">
                            <i class="fa-solid fa-trash-can text-base"></i>
                        </button>
                    </div>

                    <!-- Detalles -->
                    <div class="mt-1 flex items-end justify-between gap-3">
                        <!-- Cantidad -->
                        <div class="flex flex-col gap-1">
                            <span class="text-base font-extrabold uppercase text-slate-500">Cantidad</span>
                            <div class="flex items-center gap-1">
                                <button data-cart-action="decrease" type="button" class="size-9 shrink-0 bg-indigo-700 text-white rounded-full"><span class="menos material-symbols-outlined text-base">remove</span></button>
                                <input
                                    data-cart-action="quantity"
                                    inputmode="decimal"
                                    type="text"
                                    class="inputcantidad w-16 max-w-[12ch] h-9 px-2 rounded-lg border border-slate-300 text-center font-medium text-xl outline-none focus:border-indigo-500"
                                    value="${i}"
                                >
                                <button data-cart-action="increase" type="button" class="size-9 shrink-0 bg-indigo-700 text-white rounded-full"><span class="mas material-symbols-outlined text-base">add</span></button>
                            </div>
                        </div>
                        <!-- Precios -->
                        <div class="flex flex-col items-end text-right">
                            <div class="flex items-baseline gap-1">
                                <span class="text-base font-extrabold uppercase text-slate-500">Unitario:</span>
                                <span class="text-lg font-medium text-slate-500">${r}</span>
                            </div>
                            <div class="flex items-baseline gap-1">
                                <span class="text-base font-extrabold uppercase text-slate-500">Total</span>
                                <span class="text-2xl font-black text-slate-900">${s}</span>
                            </div>
                        </div>
                    </div>
                </div>`}).join("")}function st({productosGrid:t,cartContainer:n,catalogProducts:o,ActiveOrderE:e,renderCartPanelE:a}){t?.addEventListener("click",i=>{let r=i.target;if(!(r instanceof Element))return;let s=r.closest("article[data-product-id]");if(!s||!t.contains(s))return;let u=e();if(u===null){c("catalogProductStatus","El pedido a\xFAn no est\xE1 disponible. Revisa el estado del panel.");return}let d=Number(s.dataset.productId),l=o.get(d);if(l)try{u.cart.addProduct(l),a(u.cart.getItems()),console.log("Cuenta activa:",u.accountId),console.log("Cuenta en MySQL:",u.accountId),console.table(u.cart.getItems())}catch(p){c("catalogProductStatus",p instanceof Error?p.message:"No fue posible agregar el producto.")}}),n?.addEventListener("click",i=>{let r=i.target;if(!(r instanceof Element))return;let s=r.closest("button[data-cart-action]");if(!s||!n.contains(s))return;let u=e();if(u===null)return;let d=s.closest("[data-cart-product-id]");if(!d)return;let l=Number(d.dataset.cartProductId);if(!Number.isSafeInteger(l)||l<=0)return;let p=s.dataset.cartAction,b=u.cart;try{switch(p){case"increase":{let v=o.get(l);if(!v)return;b.addProduct(v);break}case"decrease":{let v=b.getItems().find(F=>F.productId===l);if(!v||v.quantity<=1)return;b.updateQuantity(l,Math.round((v.quantity-1)*100)/100);break}case"remove":{b.removeProduct(l);break}default:return}a(b.getItems()),console.table(u.cart.getItems())}catch(v){console.error("Error al modificar el carrito:",v)}})}var It=document.querySelector("#addcliente"),Tt=document.querySelector("#miDialogoAddCliente");function it(){It?.addEventListener("click",t=>{Tt.showModal(),$("#selectCliente").select2({dropdownParent:$("#miDialogoAddCliente"),placeholder:"Seleccionar el cliente",maximumSelectionLength:1})})}var g,q,mt,C=0,w=0,pt,U=0,z=null,D=!1,R=!1,W="",ut,lt=new Map,S=null,_=new Map,L=document.querySelector("#catalogProductGrid"),dt=document.getElementById("accountPanelPendingProducts");function K(t,n,o){let e;o!==null?e=`account:${o}`:n!==null?e=`occupation:${n}`:e=`table:${t}`;let a=lt.get(e);a||(a=tt(),lt.set(e,a)),S={resourceId:t,occupationId:n,accountId:o,cartKey:e,cart:a},V(a.getItems())}function Ot(){window.clearTimeout(pt),q.classList.remove("hidden"),g.inert=!1,g.setAttribute("aria-hidden","false"),requestAnimationFrame(()=>g.classList.remove("translate-x-full"))}function O(){if(g.getAttribute("aria-hidden")==="true")return;++C,S=null,h();let t=document.activeElement;t instanceof HTMLElement&&g.contains(t)&&t.blur(),g.classList.add("translate-x-full"),g.inert=!0,g.setAttribute("aria-hidden","true"),pt=window.setTimeout(()=>q.classList.add("hidden"),300)}async function gt(t,n){if(!t||!g)return;let o=++C;S=null,h(),mt.textContent=n,Ot();try{let e=await M(t);if(o!==C)return;if(e===null){B(null),K(t,null,null);return}if(e.resourceId!==t||e.status!=="en_uso")throw new Error("La atenci\xF3n recibida no est\xE1 activa para esta mesa.");c("atencionNoActiva",`Atenci\xF3n #${e.occupationId}`),c("atencionEstadoResumen",`Atenci\xF3n #${e.occupationId} \xB7 En uso`);let a=await N(e.occupationId);if(o!==C)return;if(a.occupationId!==e.occupationId||a.occupationStatus!=="en_uso"||a.accounts.some(r=>r.occupationId!==e.occupationId))throw new Error("Las cuentas recibidas no corresponden a una atenci\xF3n activa.");let i=await ct(e,a.accounts);if(o!==C)return;if(i.status==="ready"){K(t,e.occupationId,i.account.id);return}if(i.status==="empty"){B(e),K(t,e.occupationId,null);return}}catch(e){if(o!==C)return;S=null,rt(e instanceof Error?e.message:"No fue posible consultar la mesa.")}}function ft(){let t=document.getElementById("orderWorkspaceDrawer"),n=document.getElementById("orderWorkspaceOverlay"),o=document.getElementById("orderWorkspaceTitle"),e=document.querySelector("#catalogLoadMore");if(!t||!n||!o)return;St(),g=t,q=n,mt=o,g.inert=!0,g.addEventListener("click",r=>{r.target.closest("[data-close-order-workspace]")&&O()}),q.addEventListener("click",O),document.addEventListener("keydown",r=>{r.key==="Escape"&&g.getAttribute("aria-hidden")==="false"&&(O(),document.querySelector("#miDialogoAddCliente")?.close())});let a=document.querySelector("#searchItem");a?.addEventListener("input",()=>{let r=a.value.trim();r!==W&&(W=r,window.clearTimeout(ut),++w,R=!0,e&&(e.disabled=!0),ut=window.setTimeout(()=>{Q(z)},350))}),e?.addEventListener("click",()=>{Mt()}),L&&dt&&st({productosGrid:L,cartContainer:dt,catalogProducts:_,ActiveOrderE:()=>S,renderCartPanelE:V}),it(),Q()}async function St(){let t=await et(),n=document.querySelector("#divCategorias");n.innerHTML=`
        <button
            type="button"
            data-categoria-id="all"
            aria-pressed="true"
            class="btncateogira shrink-0 rounded-lg border border-indigo-600 bg-indigo-600 px-3 py-2 font-medium text-white">
            Todas
        </button>
        ${t.map(o=>`
            <button
                type="button"
                data-categoria-id="${m(o.id)}"
                aria-pressed="false"
                class="btncateogira shrink-0 rounded-lg border border-slate-200 bg-white px-3 py-2 font-medium text-slate-600 hover:bg-slate-50">
                ${m(o.nombre)}
            </button>
        `).join("")}
    `,n.addEventListener("click",o=>{let e=o.target.closest("button[data-categoria-id]");if(!e||!n.contains(e))return;let a=e.dataset.categoriaId,i=a==="all"?null:Number(a);i!==null&&(!Number.isInteger(i)||i<=0)||(n.querySelectorAll("button[data-categoria-id]").forEach(r=>{let s=r===e;r.setAttribute("aria-pressed",String(s)),r.classList.toggle("border-indigo-600",s),r.classList.toggle("bg-indigo-600",s),r.classList.toggle("text-white",s),r.classList.toggle("border-slate-200",!s),r.classList.toggle("bg-white",!s),r.classList.toggle("text-slate-600",!s),r.classList.toggle("hover:bg-slate-50",!s)}),Q(i))})}async function Mt(){R||!D||await bt(U+1,z,!1)}async function Q(t=null){z=t,U=0,D=!1,++w,await bt(1,t,!0)}async function bt(t,n,o){let e=document.querySelector("#catalogoPaginador"),a=document.querySelector("#catalogProductCount"),i=document.querySelector("#catalogProductStatus"),r=document.querySelector("#catalogLoadMore");if(!L||!a||!i||!r)return;let s=++w;R=!0,r.disabled=!0,r.classList.add("hidden"),o?(_.clear(),L.replaceChildren(),a.textContent="Cargando...",i.textContent="Cargando productos..."):i.textContent="Cargando m\xE1s productos...";try{let u=await nt(t,n,W);if(s!==w)return;for(let l of u.products)_.set(l.id,{...l,precioVenta:Number(l.precioVenta)});let d=u.products.map(l=>{let p=m(l.nombre),b=l.foto&&l.foto!=="null"&&l.foto!=="undefined"?l.foto:null,F=Number(l.precioVenta).toLocaleString();return`
                <article 
                    data-product-id="${l.id}"
                    class="cursor-pointer group overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-indigo-300 hover:shadow-md">
                    <div class="h-32 bg-slate-100 ${b!=null?"":"grid place-items-center"}">
                        ${b!=null?`<img src="/build/img/${m(b)}" alt="${p}" loading="lazy" class="h-full w-full object-cover">`:'<span class=" material-symbols-outlined text-5xl">restaurant</span>'}
                    </div>
                    <div class="p-3">
                        <h4 class="text-base font-semibold text-slate-900">${p}</h4>
                        <div class="mt-3 flex items-center justify-between gap-2">
                            <span class="text-lg font-bold text-slate-900">$${m(F)}</span>
                            <button
                                type="button"
                                aria-label="Agregar ${p}"
                                class="flex size-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white"
                            >
                                <span class="material-symbols-outlined">add</span>
                            </button>
                        </div>
                    </div>
                </article>`}).join("");o?(L.innerHTML=d,e?.scrollTo({top:0})):L.insertAdjacentHTML("beforeend",d),U=u.page,D=u.hasMore,a.textContent=`${u.products.length} en esta p\xE1gina`,i.textContent=u.products.length>0?`Mostrando p\xE1gina ${u.page}`:"No hay productos vendibles en esta sucursal.",r.classList.toggle("hidden",!D)}catch(u){if(s!==w)return;o&&(a.textContent="Sin cargar"),i.textContent=u instanceof Error?u.message:"No fue posible cargar los productos.",o||r.classList.remove("hidden")}finally{s===w&&(R=!1,r.disabled=!1)}}function ce(){Y(),ft(),document.addEventListener("click",t=>{let o=t.target.closest("[data-open-active-detail], [data-open-order-workspace]"),e=o?.closest("[data-table-id]");if(!o||!e)return;let a=Number(e.dataset.resourceId);a&&(o.hasAttribute("data-open-active-detail")?(O(),Z(a)):(I(),gt(a,e.dataset.tableName??"Mesa")))})}export{ce as initTables};
//# sourceMappingURL=tables-JXDKUOSK.js.map
