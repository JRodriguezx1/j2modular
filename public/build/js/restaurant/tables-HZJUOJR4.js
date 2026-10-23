function p(t){let n=document.createElement("div");return n.textContent=t,n.innerHTML}function r(t,n){let o=document.getElementById(t);o&&(o.textContent=n)}async function O(t){let n=`/restaurant/api/occupations/active?resourceId=${encodeURIComponent(t)}`,o=await fetch(n,{headers:{Accept:"application/json"}}),e=await o.json();if(!o.ok||!e.success)throw new Error(e.message??"No fue posible consultar la atenci\xF3n.");if(e.occupation===void 0)throw new Error("La respuesta de la atenci\xF3n est\xE1 incompleta.");return e.occupation}async function M(t){let n=`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(t)}`,o=await fetch(n,{headers:{Accept:"application/json"}}),e=await o.json();if(!o.ok||!e.success||!e.data)throw new Error(e.message??"No fue posible consultar las cuentas.");return e.data}var v,S,y,U,z;function gt(){window.clearTimeout(z),S.classList.remove("hidden"),v.inert=!1,requestAnimationFrame(()=>{v.classList.remove("translate-x-full")}),v.setAttribute("aria-hidden","false")}function L(){v.getAttribute("aria-hidden")!=="true"&&(++E,v.classList.add("translate-x-full"),v.setAttribute("aria-hidden","true"),v.inert=!0,z=window.setTimeout(()=>{S.classList.add("hidden")},300))}function ft(){y.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function bt(t){let n=t.clientName?p(t.clientName):"Sin cliente asociado",o=t.numberOfPeople!==null?String(t.numberOfPeople):"\u2014",e=t.reservationId!==null?`#${t.reservationId}`:"Atenci\xF3n directa",a=t.observations?p(t.observations):"Sin observaciones";y.innerHTML=`
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
                ${A("Cliente",n)}
                ${A("Personas",o)}
                ${A("Origen",e)}
                ${A("Inicio",Q(t.startDate))}
                ${A("Fin estimado",t.estimatedEndDate?Q(t.estimatedEndDate):"\u2014")}
                ${A("Observaciones",a)}
            </div>
        </div>`}function A(t,n){return`
        <div class="border-b border-slate-100 pb-4">
            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-400">
                ${t}
            </p>
            <p class="text-sm font-medium text-slate-800">
                ${n}
            </p>
        </div>`}function Q(t){let n=new Date(t.replace(" ","T"));return Number.isNaN(n.getTime())?t:new Intl.DateTimeFormat("es",{dateStyle:"medium",timeStyle:"short"}).format(n)}function vt(t){let n=document.createElement("section");n.className="mt-8 border-t border-slate-200 pt-5",n.innerHTML=`
        <h3 class="mb-3 font-semibold text-slate-900">Cuentas</h3>
        ${t.length===0?'<p class="text-sm text-slate-500">Esta atenci\xF3n no tiene cuentas.</p>':t.map(o=>`
                <article class="mb-2 rounded-xl border border-slate-200 p-3">
                    <p class="font-medium text-slate-900">${p(o.name)}</p>
                    <p class="text-xs text-slate-500">
                        ${p(o.status)} \xB7 ${o.invoiceId===null?"Sin orden todav\xEDa":`Orden #${o.invoiceId}`}
                    </p>
                </article>
            `).join("")}`,y.appendChild(n)}function yt(t){let n=document.createElement("p");n.className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700",n.textContent=t,y.appendChild(n)}var E=0;function G(){let t=document.getElementById("activeOccupationDrawer"),n=document.getElementById("activeOccupationOverlay"),o=document.getElementById("activeOccupationContent"),e=document.getElementById("activeOccupationActions");!t||!n||!o||!e||(v=t,S=n,y=o,U=e,v.inert=!0,v.addEventListener("click",a=>{a.target.closest("[data-close-active-occupation]")&&L()}),S.addEventListener("click",L),document.addEventListener("keydown",a=>{a.key==="Escape"&&v.getAttribute("aria-hidden")==="false"&&L()}))}async function J(t){if(!t)return;let n=++E;gt(),ft(),U.classList.add("hidden");try{let o=await O(t);if(n!==E)return;if(o===null){y.textContent="La mesa no tiene una atenci\xF3n activa.";return}bt(o);try{let e=await M(o.occupationId);if(n!==E)return;vt(e.accounts)}catch(e){if(n!==E)return;yt(e instanceof Error?e.message:"No fue posible consultar las cuentas.")}}catch(o){if(n!==E)return;y.innerHTML=`
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                ${p(o instanceof Error?o.message:"Ocurri\xF3 un error inesperado.")}
            </div>`}}function X(){let t=[];function n(i){if(!Number.isFinite(i)||i<=0)throw new Error("La cantidad debe ser un n\xFAmero mayor que cero.")}function o(i,u=1){n(u);let l=Number(i.precioVenta);if(!Number.isInteger(i.id)||i.id<=0)throw new Error("El producto no es v\xE1lido.");if(!Number.isFinite(l)||l<0)throw new Error("El precio del producto no es v\xE1lido.");let d=t.find(m=>m.productId===i.id);if(d){let m=d.quantity+u;n(m),d.quantity=m;return}t.push({productId:i.id,name:i.nombre,unitPrice:l,quantity:u})}function e(i,u){n(u);let l=t.find(d=>d.productId===i);if(!l)throw new Error("El producto no est\xE1 en el carrito.");l.quantity=u}function a(i){t=t.filter(u=>u.productId!==i)}function c(){return t.map(i=>({...i}))}function s(){t=[]}return{addProduct:o,updateQuantity:e,removeProduct:a,getItems:c,clear:s}}async function Y(){let n=await fetch("/admin/api/inventory/getAllcategorias");if(!n.ok)throw new Error("No fue posible consultar las categorias.");let o=await n.json();if(!Array.isArray(o))throw new Error("La respuesta de categor\xEDas no tiene el formato esperado.");return o}async function Z(t=1,n=null,o=""){let e=new URLSearchParams({page:String(t)});n!=null&&e.set("categoriaId",String(n)),o&&e.set("search",o);let a=`/admin/api/inventory/listSimpleProducts?${e.toString()}`,c=await fetch(a),s=await c.json();if(!c.ok||!s.success||!s.data||!Array.isArray(s.data.products))throw new Error("No fue posible consultar las productos.");return s.data}async function tt(t,n){if(!Number.isInteger(t)||t<=0)throw new Error("El ID de la cuenta no es v\xE1lido.");let o=`/restaurant/api/accounts/summary?accountId=${encodeURIComponent(String(t))}`,e=await fetch(o,{headers:{Accept:"application/json"},signal:n}),a=await e.json();if(!e.ok||!a.success||!a.data)throw new Error(a.message??"No fue posible consultar la cuenta.");if(!Array.isArray(a.data.consumos)||!a.data.totales||typeof a.data.totales!="object"||Array.isArray(a.data.totales))throw new Error("La respuesta de la cuenta no tiene el formato esperado.");return a.data}var I=0,N=null,xt=new Intl.NumberFormat("es-CO",{style:"currency",currency:"COP",minimumFractionDigits:0,maximumFractionDigits:2}),At=new Intl.NumberFormat("es-CO",{maximumFractionDigits:2});function et(t,n){let o=document.getElementById(t);if(!o)return;let e=document.createElement("p");e.className="text-sm text-slate-500",e.textContent=n,o.replaceChildren(e)}function x(t){let n=Number(t);return Number.isFinite(n)?xt.format(n):"\u2014"}function nt(t){let n=Number(t);return Number.isFinite(n)?At.format(n):"\u2014"}function Et(t){if(t.length===0){et("accountPanelConsumptions","Sin productos registrados.");return}let n=document.getElementById("accountPanelConsumptions"),o=document.getElementById("accountConsumptionTemplate");if(!n||!(o instanceof HTMLTemplateElement))throw new Error("No se encontr\xF3 la vista de productos registrados.");let e=document.createDocumentFragment();for(let a of t){let c=o.content.firstElementChild?.cloneNode(!0);if(!(c instanceof HTMLElement))throw new Error("La vista de productos registrados no es v\xE1lida.");let s=c.querySelector("[data-consumption-quantity]"),i=c.querySelector("[data-consumption-name]"),u=c.querySelector("[data-consumption-unit-price]"),l=c.querySelector("[data-consumption-total]");if(!s||!i||!u||!l)throw new Error("La vista de productos registrados est\xE1 incompleta.");s.textContent=nt(a.cantidad),i.textContent=a.nombreproducto,u.textContent=`${x(a.valorunidad)} c/u`,l.textContent=x(a.total),e.append(c)}n.replaceChildren(e)}function ht(t){r("accountPanelName",t.name);let n=nt(t.totales.totalunidades);r("accountPanelRegisteredCount",`${n} ${n==="1"?"producto":"productos"}`),Et(t.consumos);let o=x(t.totales.total);r("accountPanelRegisteredAmount",o),r("accountPanelDiscount",x(t.totales.descuento)),r("accountPanelTax",`${x(t.totales.valorimpuestototal)} incluidos`),r("accountPanelTotal",o),r("accountPanelPayTotal",o)}function h(){++I,N?.abort(),N=null,document.getElementById("accountPanel")?.setAttribute("aria-busy","false"),r("atencionNoActiva","Atenci\xF3n por consultar"),r("atencionEstadoResumen","Mesa sin atencion"),r("atencionEstadoCuenta","Listo para ordenar"),r("accountPanelOccupation","Atenci\xF3n por consultar"),r("accountPanelName","Cuenta por consultar"),r("accountPanelClientName","Consumidor final"),r("accountPanelRegisteredCount","\u2014 \xEDtems"),r("accountPanelPendingCount","0 item sin guardar"),r("accountPanelRegisteredAmount","\u2014"),r("accountPanelPendingAmount",x(0)),r("accountPanelDiscount","\u2014"),r("accountPanelTax","\u2014"),r("accountPanelTotal","\u2014"),r("accountPanelPayTotal","\u2014"),et("accountPanelPendingProducts","No hay productos nuevos.")}function ot(t){h(),r("atencionEstadoCuenta","Cuenta no disponible")}async function at(t,n){h();let o=I;if(r("accountPanelOccupation",`Atenci\xF3n #${t.occupationId}`),r("accountPanelClientName",t.clientName?.trim()||(t.clientId===null?"Consumidor final":"Cliente por consultar")),n.length===0)return{status:"empty"};let e=n.filter(s=>s.occupationId===t.occupationId&&s.status==="abierta");if(e.length===0)return r("atencionEstadoCuenta","Sin cuenta abierta"),{status:"closed"};if(e.length>1)return r("atencionEstadoCuenta","Varias cuentas abiertas"),{status:"selection-required"};let a=e[0];r("atencionEstadoCuenta","Cuenta abierta"),r("accountPanelName",a.name),document.getElementById("accountPanel")?.setAttribute("aria-busy","true");let c=new AbortController;N=c;try{let s=await tt(a.id,c.signal);if(o!==I)return{status:"cancelled"};if(s.accountId!==a.id||s.occupationId!==t.occupationId)throw new Error("El resumen recibido no corresponde a esta cuenta.");return ht(s),{status:"ready",account:a}}catch{if(o!==I||c.signal.aborted)return{status:"cancelled"}}finally{o===I&&(N=null,document.getElementById("accountPanel")?.setAttribute("aria-busy","false"))}return{status:"error"}}function R(t){h(),r("atencionNoActiva","Sin atenci\xF3n registrada"),r("atencionEstadoResumen","Mesa sin atencion"),r("atencionEstadoCuenta","Listo para ordenar"),r("accountPanelOccupation",t?`Atenci\xF3n #${t.occupationId}`:"Sin atenci\xF3n registrada"),r("accountPanelName","Cuenta por crear"),r("accountPanelClientName",t?.clientName?.trim()||"Sin cliente seleccionado"),r("accountPanelRegisteredCount","0 \xEDtems"),r("accountPanelRegisteredAmount",x(0))}var F=new Intl.NumberFormat("es-CO",{style:"currency",currency:"COP",minimumFractionDigits:0,maximumFractionDigits:2}),Pt=new Intl.NumberFormat("es-CO",{maximumFractionDigits:2});function $(t){let n=document.getElementById("accountPanelPendingProducts");if(!n)return;let o=t.length;if(r("accountPanelPendingCount",`${o} ${o===1?"\xEDtem":"\xEDtems"} sin guardar`),r("accountPanelPendingAmount",o===0?F.format(0):"Por calcular"),o===0){n.innerHTML='<p class="text-sm text-slate-500">No hay productos nuevos.</p>';return}n.innerHTML=t.map(e=>{let a=p(e.name),c=Pt.format(e.quantity),s=F.format(e.unitPrice),i=F.format(e.quantity*e.unitPrice);return`
                <div data-cart-product-id="${e.productId}" class="rounded-xl border border-slate-200 bg-white p-4">
                    <!-- Encabezado -->
                    <div class="flex items-center justify-between gap-3">
                        <p class="m-0 nombreproducto text-xl font-semibold text-slate-800 leading-7 break-words">${a}</p>
                        <button data-cart-action="remove" class="eliminarProducto size-7 rounded-lg border border-red-200 bg-red-50 text-red-500 transition-all duration-300 hover:bg-red-600 hover:text-white  hover:border-red-600 hover:shadow-md">
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
                                    value="${c}"
                                >
                                <button data-cart-action="increase" type="button" class="size-9 shrink-0 bg-indigo-700 text-white rounded-full"><span class="mas material-symbols-outlined text-base">add</span></button>
                            </div>
                        </div>
                        <!-- Precios -->
                        <div class="flex flex-col items-end text-right">
                            <div class="flex items-baseline gap-1">
                                <span class="text-base font-extrabold uppercase text-slate-500">Unitario:</span>
                                <span class="text-lg font-medium text-slate-500">${s}</span>
                            </div>
                            <div class="flex items-baseline gap-1">
                                <span class="text-base font-extrabold uppercase text-slate-500">Total</span>
                                <span class="text-2xl font-black text-slate-900">${i}</span>
                            </div>
                        </div>
                    </div>
                </div>`}).join("")}var g,H,it,P=0,w=0,ut,W=0,_=null,k=!1,q=!1,j="",rt,ct=new Map,b=null,D=new Map,C=document.querySelector("#catalogProductGrid"),st=document.getElementById("accountPanelPendingProducts");function B(t,n,o){let e;o!==null?e=`account:${o}`:n!==null?e=`occupation:${n}`:e=`table:${t}`;let a=ct.get(e);a||(a=X(),ct.set(e,a)),b={resourceId:t,occupationId:n,accountId:o,cartKey:e,cart:a},$(a.getItems())}function wt(){window.clearTimeout(ut),H.classList.remove("hidden"),g.inert=!1,g.setAttribute("aria-hidden","false"),requestAnimationFrame(()=>g.classList.remove("translate-x-full"))}function T(){if(g.getAttribute("aria-hidden")==="true")return;++P,b=null,h();let t=document.activeElement;t instanceof HTMLElement&&g.contains(t)&&t.blur(),g.classList.add("translate-x-full"),g.inert=!0,g.setAttribute("aria-hidden","true"),ut=window.setTimeout(()=>H.classList.add("hidden"),300)}async function lt(t,n){if(!t||!g)return;let o=++P;b=null,h(),it.textContent=n,wt();try{let e=await O(t);if(o!==P)return;if(e===null){R(null),B(t,null,null);return}if(e.resourceId!==t||e.status!=="en_uso")throw new Error("La atenci\xF3n recibida no est\xE1 activa para esta mesa.");r("atencionNoActiva",`Atenci\xF3n #${e.occupationId}`),r("atencionEstadoResumen",`Atenci\xF3n #${e.occupationId} \xB7 En uso`);let a=await M(e.occupationId);if(o!==P)return;if(a.occupationId!==e.occupationId||a.occupationStatus!=="en_uso"||a.accounts.some(s=>s.occupationId!==e.occupationId))throw new Error("Las cuentas recibidas no corresponden a una atenci\xF3n activa.");let c=await at(e,a.accounts);if(o!==P)return;if(c.status==="ready"){B(t,e.occupationId,c.account.id);return}if(c.status==="empty"){R(e),B(t,e.occupationId,null);return}}catch(e){if(o!==P)return;b=null,ot(e instanceof Error?e.message:"No fue posible consultar la mesa.")}}function dt(){let t=document.getElementById("orderWorkspaceDrawer"),n=document.getElementById("orderWorkspaceOverlay"),o=document.getElementById("orderWorkspaceTitle"),e=document.querySelector("#catalogLoadMore");if(!t||!n||!o)return;Ct(),g=t,H=n,it=o,g.inert=!0,g.addEventListener("click",c=>{c.target.closest("[data-close-order-workspace]")&&T()}),H.addEventListener("click",T),document.addEventListener("keydown",c=>{c.key==="Escape"&&g.getAttribute("aria-hidden")==="false"&&T()});let a=document.querySelector("#searchItem");a?.addEventListener("input",()=>{let c=a.value.trim();c!==j&&(j=c,window.clearTimeout(rt),++w,q=!0,e&&(e.disabled=!0),rt=window.setTimeout(()=>{V(_)},350))}),e?.addEventListener("click",()=>{Lt()}),C?.addEventListener("click",c=>{let s=c.target;if(!(s instanceof Element))return;let i=s.closest("article[data-product-id]");if(!i||!C.contains(i))return;if(b===null){r("catalogProductStatus","El pedido a\xFAn no est\xE1 disponible. Revisa el estado del panel.");return}let u=Number(i.dataset.productId),l=D.get(u);if(l)try{b.cart.addProduct(l),$(b.cart.getItems()),console.log("Cuenta activa:",b.accountId),console.log("Cuenta en MySQL:",b.accountId),console.table(b.cart.getItems())}catch(d){r("catalogProductStatus",d instanceof Error?d.message:"No fue posible agregar el producto.")}}),st?.addEventListener("click",c=>{let s=c.target;if(!(s instanceof Element))return;let i=s.closest("button[data-cart-action]");if(!i||!st.contains(i)||b===null)return;let u=i.closest("[data-cart-product-id]");if(!u)return;let l=Number(u.dataset.cartProductId);if(!Number.isSafeInteger(l)||l<=0)return;let d=i.dataset.cartAction,m=b.cart;try{switch(d){case"increase":{let f=D.get(l);if(!f)return;m.addProduct(f);break}case"decrease":{let f=m.getItems().find(K=>K.productId===l);if(!f||f.quantity<=1)return;m.updateQuantity(l,Math.round((f.quantity-1)*100)/100);break}case"remove":{m.removeProduct(l);break}default:return}$(m.getItems())}catch(f){console.error("Error al modificar el carrito:",f)}}),V()}async function Ct(){let t=await Y(),n=document.querySelector("#divCategorias");n.innerHTML=`
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
                data-categoria-id="${p(o.id)}"
                aria-pressed="false"
                class="btncateogira shrink-0 rounded-lg border border-slate-200 bg-white px-3 py-2 font-medium text-slate-600 hover:bg-slate-50">
                ${p(o.nombre)}
            </button>
        `).join("")}
    `,n.addEventListener("click",o=>{let e=o.target.closest("button[data-categoria-id]");if(!e||!n.contains(e))return;let a=e.dataset.categoriaId,c=a==="all"?null:Number(a);c!==null&&(!Number.isInteger(c)||c<=0)||(n.querySelectorAll("button[data-categoria-id]").forEach(s=>{let i=s===e;s.setAttribute("aria-pressed",String(i)),s.classList.toggle("border-indigo-600",i),s.classList.toggle("bg-indigo-600",i),s.classList.toggle("text-white",i),s.classList.toggle("border-slate-200",!i),s.classList.toggle("bg-white",!i),s.classList.toggle("text-slate-600",!i),s.classList.toggle("hover:bg-slate-50",!i)}),V(c))})}async function Lt(){q||!k||await mt(W+1,_,!1)}async function V(t=null){_=t,W=0,k=!1,++w,await mt(1,t,!0)}async function mt(t,n,o){let e=document.querySelector("#catalogoPaginador"),a=document.querySelector("#catalogProductCount"),c=document.querySelector("#catalogProductStatus"),s=document.querySelector("#catalogLoadMore");if(!C||!a||!c||!s)return;let i=++w;q=!0,s.disabled=!0,s.classList.add("hidden"),o?(D.clear(),C.replaceChildren(),a.textContent="Cargando...",c.textContent="Cargando productos..."):c.textContent="Cargando m\xE1s productos...";try{let u=await Z(t,n,j);if(i!==w)return;for(let d of u.products)D.set(d.id,{...d,precioVenta:Number(d.precioVenta)});let l=u.products.map(d=>{let m=p(d.nombre),f=d.foto&&d.foto!=="null"&&d.foto!=="undefined"?d.foto:null,pt=Number(d.precioVenta).toLocaleString();return`
                <article 
                    data-product-id="${d.id}"
                    class="cursor-pointer group overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-indigo-300 hover:shadow-md">
                    <div class="h-32 bg-slate-100 ${f!=null?"":"grid place-items-center"}">
                        ${f!=null?`<img src="/build/img/${p(f)}" alt="${m}" loading="lazy" class="h-full w-full object-cover">`:'<span class=" material-symbols-outlined text-5xl">restaurant</span>'}
                    </div>
                    <div class="p-3">
                        <h4 class="text-base font-semibold text-slate-900">${m}</h4>
                        <div class="mt-3 flex items-center justify-between gap-2">
                            <span class="text-lg font-bold text-slate-900">$${p(pt)}</span>
                            <button
                                type="button"
                                aria-label="Agregar ${m}"
                                class="flex size-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white"
                            >
                                <span class="material-symbols-outlined">add</span>
                            </button>
                        </div>
                    </div>
                </article>`}).join("");o?(C.innerHTML=l,e?.scrollTo({top:0})):C.insertAdjacentHTML("beforeend",l),W=u.page,k=u.hasMore,a.textContent=`${u.products.length} en esta p\xE1gina`,c.textContent=u.products.length>0?`Mostrando p\xE1gina ${u.page}`:"No hay productos vendibles en esta sucursal.",s.classList.toggle("hidden",!k)}catch(u){if(i!==w)return;o&&(a.textContent="Sin cargar"),c.textContent=u instanceof Error?u.message:"No fue posible cargar los productos.",o||s.classList.remove("hidden")}finally{i===w&&(q=!1,s.disabled=!1)}}function Jt(){G(),dt(),document.addEventListener("click",t=>{let o=t.target.closest("[data-open-active-detail], [data-open-order-workspace]"),e=o?.closest("[data-table-id]");if(!o||!e)return;let a=Number(e.dataset.resourceId);a&&(o.hasAttribute("data-open-active-detail")?(T(),J(a)):(L(),lt(a,e.dataset.tableName??"Mesa")))})}export{Jt as initTables};
//# sourceMappingURL=tables-HZJUOJR4.js.map
