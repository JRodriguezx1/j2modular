function d(t){let n=document.createElement("div");return n.textContent=t,n.innerHTML}function r(t,n){let o=document.getElementById(t);o&&(o.textContent=n)}async function I(t){let n=`/restaurant/api/occupations/active?resourceId=${encodeURIComponent(t)}`,o=await fetch(n,{headers:{Accept:"application/json"}}),e=await o.json();if(!o.ok||!e.success)throw new Error(e.message??"No fue posible consultar la atenci\xF3n.");if(e.occupation===void 0)throw new Error("La respuesta de la atenci\xF3n est\xE1 incompleta.");return e.occupation}async function T(t){let n=`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(t)}`,o=await fetch(n,{headers:{Accept:"application/json"}}),e=await o.json();if(!o.ok||!e.success||!e.data)throw new Error(e.message??"No fue posible consultar las cuentas.");return e.data}var g,O,v,K,U;function mt(){window.clearTimeout(U),O.classList.remove("hidden"),g.inert=!1,requestAnimationFrame(()=>{g.classList.remove("translate-x-full")}),g.setAttribute("aria-hidden","false")}function w(){g.getAttribute("aria-hidden")!=="true"&&(++A,g.classList.add("translate-x-full"),g.setAttribute("aria-hidden","true"),g.inert=!0,U=window.setTimeout(()=>{O.classList.add("hidden")},300))}function pt(){v.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function gt(t){let n=t.clientName?d(t.clientName):"Sin cliente asociado",o=t.numberOfPeople!==null?String(t.numberOfPeople):"\u2014",e=t.reservationId!==null?`#${t.reservationId}`:"Atenci\xF3n directa",a=t.observations?d(t.observations):"Sin observaciones";v.innerHTML=`
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
                ${x("Cliente",n)}
                ${x("Personas",o)}
                ${x("Origen",e)}
                ${x("Inicio",_(t.startDate))}
                ${x("Fin estimado",t.estimatedEndDate?_(t.estimatedEndDate):"\u2014")}
                ${x("Observaciones",a)}
            </div>
        </div>`}function x(t,n){return`
        <div class="border-b border-slate-100 pb-4">
            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-400">
                ${t}
            </p>
            <p class="text-sm font-medium text-slate-800">
                ${n}
            </p>
        </div>`}function _(t){let n=new Date(t.replace(" ","T"));return Number.isNaN(n.getTime())?t:new Intl.DateTimeFormat("es",{dateStyle:"medium",timeStyle:"short"}).format(n)}function ft(t){let n=document.createElement("section");n.className="mt-8 border-t border-slate-200 pt-5",n.innerHTML=`
        <h3 class="mb-3 font-semibold text-slate-900">Cuentas</h3>
        ${t.length===0?'<p class="text-sm text-slate-500">Esta atenci\xF3n no tiene cuentas.</p>':t.map(o=>`
                <article class="mb-2 rounded-xl border border-slate-200 p-3">
                    <p class="font-medium text-slate-900">${d(o.name)}</p>
                    <p class="text-xs text-slate-500">
                        ${d(o.status)} \xB7 ${o.invoiceId===null?"Sin orden todav\xEDa":`Orden #${o.invoiceId}`}
                    </p>
                </article>
            `).join("")}`,v.appendChild(n)}function bt(t){let n=document.createElement("p");n.className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700",n.textContent=t,v.appendChild(n)}var A=0;function z(){let t=document.getElementById("activeOccupationDrawer"),n=document.getElementById("activeOccupationOverlay"),o=document.getElementById("activeOccupationContent"),e=document.getElementById("activeOccupationActions");!t||!n||!o||!e||(g=t,O=n,v=o,K=e,g.inert=!0,g.addEventListener("click",a=>{a.target.closest("[data-close-active-occupation]")&&w()}),O.addEventListener("click",w),document.addEventListener("keydown",a=>{a.key==="Escape"&&g.getAttribute("aria-hidden")==="false"&&w()}))}async function Q(t){if(!t)return;let n=++A;mt(),pt(),K.classList.add("hidden");try{let o=await I(t);if(n!==A)return;if(o===null){v.textContent="La mesa no tiene una atenci\xF3n activa.";return}gt(o);try{let e=await T(o.occupationId);if(n!==A)return;ft(e.accounts)}catch(e){if(n!==A)return;bt(e instanceof Error?e.message:"No fue posible consultar las cuentas.")}}catch(o){if(n!==A)return;v.innerHTML=`
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                ${d(o instanceof Error?o.message:"Ocurri\xF3 un error inesperado.")}
            </div>`}}function G(){let t=[];function n(i){if(!Number.isFinite(i)||i<=0)throw new Error("La cantidad debe ser un n\xFAmero mayor que cero.")}function o(i,u=1){n(u);let p=Number(i.precioVenta);if(!Number.isInteger(i.id)||i.id<=0)throw new Error("El producto no es v\xE1lido.");if(!Number.isFinite(p)||p<0)throw new Error("El precio del producto no es v\xE1lido.");let l=t.find(b=>b.productId===i.id);if(l){let b=l.quantity+u;n(b),l.quantity=b;return}t.push({productId:i.id,name:i.nombre,unitPrice:p,quantity:u})}function e(i,u){n(u);let p=t.find(l=>l.productId===i);if(!p)throw new Error("El producto no est\xE1 en el carrito.");p.quantity=u}function a(i){t=t.filter(u=>u.productId!==i)}function c(){return t.map(i=>({...i}))}function s(){t=[]}return{addProduct:o,updateQuantity:e,removeProduct:a,getItems:c,clear:s}}async function J(){let n=await fetch("/admin/api/inventory/getAllcategorias");if(!n.ok)throw new Error("No fue posible consultar las categorias.");let o=await n.json();if(!Array.isArray(o))throw new Error("La respuesta de categor\xEDas no tiene el formato esperado.");return o}async function X(t=1,n=null,o=""){let e=new URLSearchParams({page:String(t)});n!=null&&e.set("categoriaId",String(n)),o&&e.set("search",o);let a=`/admin/api/inventory/listSimpleProducts?${e.toString()}`,c=await fetch(a),s=await c.json();if(!c.ok||!s.success||!s.data||!Array.isArray(s.data.products))throw new Error("No fue posible consultar las productos.");return s.data}async function Y(t,n){if(!Number.isInteger(t)||t<=0)throw new Error("El ID de la cuenta no es v\xE1lido.");let o=`/restaurant/api/accounts/summary?accountId=${encodeURIComponent(String(t))}`,e=await fetch(o,{headers:{Accept:"application/json"},signal:n}),a=await e.json();if(!e.ok||!a.success||!a.data)throw new Error(a.message??"No fue posible consultar la cuenta.");if(!Array.isArray(a.data.consumos)||!a.data.totales||typeof a.data.totales!="object"||Array.isArray(a.data.totales))throw new Error("La respuesta de la cuenta no tiene el formato esperado.");return a.data}var C=0,S=null,vt=new Intl.NumberFormat("es-CO",{style:"currency",currency:"COP",minimumFractionDigits:0,maximumFractionDigits:2}),yt=new Intl.NumberFormat("es-CO",{maximumFractionDigits:2});function f(t,n){let o=document.getElementById(t);if(!o)return;let e=document.createElement("p");e.className="text-sm text-slate-500",e.textContent=n,o.replaceChildren(e)}function y(t){let n=Number(t);return Number.isFinite(n)?vt.format(n):"\u2014"}function Z(t){let n=Number(t);return Number.isFinite(n)?yt.format(n):"\u2014"}function xt(t){if(t.length===0){f("accountPanelConsumptions","Sin productos registrados.");return}let n=document.getElementById("accountPanelConsumptions"),o=document.getElementById("accountConsumptionTemplate");if(!n||!(o instanceof HTMLTemplateElement))throw new Error("No se encontr\xF3 la vista de productos registrados.");let e=document.createDocumentFragment();for(let a of t){let c=o.content.firstElementChild?.cloneNode(!0);if(!(c instanceof HTMLElement))throw new Error("La vista de productos registrados no es v\xE1lida.");let s=c.querySelector("[data-consumption-quantity]"),i=c.querySelector("[data-consumption-name]"),u=c.querySelector("[data-consumption-unit-price]"),p=c.querySelector("[data-consumption-total]");if(!s||!i||!u||!p)throw new Error("La vista de productos registrados est\xE1 incompleta.");s.textContent=Z(a.cantidad),i.textContent=a.nombreproducto,u.textContent=`${y(a.valorunidad)} c/u`,p.textContent=y(a.total),e.append(c)}n.replaceChildren(e)}function At(t){r("accountPanelName",t.name);let n=Z(t.totales.totalunidades);r("accountPanelRegisteredCount",`${n} ${n==="1"?"producto":"productos"}`),xt(t.consumos);let o=y(t.totales.total);r("accountPanelRegisteredAmount",o),r("accountPanelDiscount",y(t.totales.descuento)),r("accountPanelTax",`${y(t.totales.valorimpuestototal)} incluidos`),r("accountPanelTotal",o),r("accountPanelPayTotal",o)}function h(){++C,S?.abort(),S=null,document.getElementById("accountPanel")?.setAttribute("aria-busy","false"),r("atencionNoActiva","Atenci\xF3n por consultar"),r("atencionEstadoResumen","Mesa sin atencion"),r("atencionEstadoCuenta","Listo para ordenar"),r("accountPanelOccupation","Atenci\xF3n por consultar"),r("accountPanelName","Cuenta por consultar"),r("accountPanelClientName","Consumidor final"),r("accountPanelRegisteredCount","\u2014 \xEDtems"),f("accountPanelConsumptions","Los productos registrados aparecer\xE1n aqu\xED."),r("accountPanelPendingCount","0 item sin guardar"),r("accountPanelRegisteredAmount","\u2014"),r("accountPanelPendingAmount",y(0)),r("accountPanelDiscount","\u2014"),r("accountPanelTax","\u2014"),r("accountPanelTotal","\u2014"),r("accountPanelPayTotal","\u2014"),f("accountPanelPendingProducts","No hay productos nuevos.")}function tt(t){h(),r("atencionEstadoCuenta","Cuenta no disponible"),f("accountPanelConsumptions","No fue posible consultar los productos registrados.")}async function et(t,n){h();let o=C;if(r("accountPanelOccupation",`Atenci\xF3n #${t.occupationId}`),r("accountPanelClientName",t.clientName?.trim()||(t.clientId===null?"Consumidor final":"Cliente por consultar")),n.length===0)return{status:"empty"};let e=n.filter(s=>s.occupationId===t.occupationId&&s.status==="abierta");if(e.length===0)return r("atencionEstadoCuenta","Sin cuenta abierta"),f("accountPanelConsumptions","Sin cuenta para consultar productos."),{status:"closed"};if(e.length>1)return r("atencionEstadoCuenta","Varias cuentas abiertas"),f("accountPanelConsumptions","Selecciona una cuenta cuando esta opci\xF3n est\xE9 disponible."),{status:"selection-required"};let a=e[0];r("atencionEstadoCuenta","Cuenta abierta"),r("accountPanelName",a.name),f("accountPanelConsumptions","Cargando productos registrados..."),document.getElementById("accountPanel")?.setAttribute("aria-busy","true");let c=new AbortController;S=c;try{let s=await Y(a.id,c.signal);if(o!==C)return{status:"cancelled"};if(s.accountId!==a.id||s.occupationId!==t.occupationId)throw new Error("El resumen recibido no corresponde a esta cuenta.");return At(s),{status:"ready",account:a}}catch{if(o!==C||c.signal.aborted)return{status:"cancelled"};f("accountPanelConsumptions","No fue posible consultar los productos registrados.")}finally{o===C&&(S=null,document.getElementById("accountPanel")?.setAttribute("aria-busy","false"))}return{status:"error"}}function D(t){h(),r("atencionNoActiva","Sin atenci\xF3n registrada"),r("atencionEstadoResumen","Mesa sin atencion"),r("atencionEstadoCuenta","Listo para ordenar"),r("accountPanelOccupation",t?`Atenci\xF3n #${t.occupationId}`:"Sin atenci\xF3n registrada"),r("accountPanelName","Cuenta por crear"),r("accountPanelClientName",t?.clientName?.trim()||"Sin cliente seleccionado"),r("accountPanelRegisteredCount","0 \xEDtems"),r("accountPanelRegisteredAmount",y(0)),f("accountPanelConsumptions","Sin productos registrados.")}var R=new Intl.NumberFormat("es-CO",{style:"currency",currency:"COP",minimumFractionDigits:0,maximumFractionDigits:2}),ht=new Intl.NumberFormat("es-CO",{maximumFractionDigits:2});function nt(t){let n=document.getElementById("accountPanelPendingProducts");if(!n)return;let o=t.length;if(r("accountPanelPendingCount",`${o} ${o===1?"\xEDtem":"\xEDtems"} sin guardar`),r("accountPanelPendingAmount",o===0?R.format(0):"Por calcular"),o===0){n.innerHTML='<p class="text-sm text-slate-500">No hay productos nuevos.</p>';return}n.innerHTML=t.map(e=>{let a=d(e.name),c=ht.format(e.quantity),s=R.format(e.unitPrice),i=R.format(e.quantity*e.unitPrice);return`
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
                </div>`}).join("")}var m,N,ct,P=0,E=0,st,V=0,W=null,$=!1,H=!1,j="",ot,at=new Map,q=null,rt=new Map,M=document.querySelector("#catalogProductGrid"),Ut=document.getElementById("accountPanelPendingProducts");function F(t,n,o){let e;o!==null?e=`account:${o}`:n!==null?e=`occupation:${n}`:e=`table:${t}`;let a=at.get(e);a||(a=G(),at.set(e,a)),q={resourceId:t,occupationId:n,accountId:o,cartKey:e,cart:a},nt(a.getItems())}function Pt(){window.clearTimeout(st),N.classList.remove("hidden"),m.inert=!1,m.setAttribute("aria-hidden","false"),requestAnimationFrame(()=>m.classList.remove("translate-x-full"))}function L(){if(m.getAttribute("aria-hidden")==="true")return;++P,q=null,h();let t=document.activeElement;t instanceof HTMLElement&&m.contains(t)&&t.blur(),m.classList.add("translate-x-full"),m.inert=!0,m.setAttribute("aria-hidden","true"),st=window.setTimeout(()=>N.classList.add("hidden"),300)}async function it(t,n){if(!t||!m)return;let o=++P;q=null,h(),ct.textContent=n,Pt();try{let e=await I(t);if(o!==P)return;if(e===null){D(null),F(t,null,null);return}if(e.resourceId!==t||e.status!=="en_uso")throw new Error("La atenci\xF3n recibida no est\xE1 activa para esta mesa.");r("atencionNoActiva",`Atenci\xF3n #${e.occupationId}`),r("atencionEstadoResumen",`Atenci\xF3n #${e.occupationId} \xB7 En uso`);let a=await T(e.occupationId);if(o!==P)return;if(a.occupationId!==e.occupationId||a.occupationStatus!=="en_uso"||a.accounts.some(s=>s.occupationId!==e.occupationId))throw new Error("Las cuentas recibidas no corresponden a una atenci\xF3n activa.");let c=await et(e,a.accounts);if(o!==P)return;if(c.status==="ready"){F(t,e.occupationId,c.account.id);return}if(c.status==="empty"){D(e),F(t,e.occupationId,null);return}}catch(e){if(o!==P)return;q=null,tt(e instanceof Error?e.message:"No fue posible consultar la mesa.")}}function ut(){let t=document.getElementById("orderWorkspaceDrawer"),n=document.getElementById("orderWorkspaceOverlay"),o=document.getElementById("orderWorkspaceTitle"),e=document.querySelector("#catalogLoadMore");if(!t||!n||!o)return;Et(),m=t,N=n,ct=o,m.inert=!0,m.addEventListener("click",c=>{c.target.closest("[data-close-order-workspace]")&&L()}),N.addEventListener("click",L),document.addEventListener("keydown",c=>{c.key==="Escape"&&m.getAttribute("aria-hidden")==="false"&&L()});let a=document.querySelector("#searchItem");a?.addEventListener("input",()=>{let c=a.value.trim();c!==j&&(j=c,window.clearTimeout(ot),++E,H=!0,e&&(e.disabled=!0),ot=window.setTimeout(()=>{B(W)},350))}),e?.addEventListener("click",()=>{wt()}),B()}async function Et(){let t=await J(),n=document.querySelector("#divCategorias");n.innerHTML=`
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
                data-categoria-id="${d(o.id)}"
                aria-pressed="false"
                class="btncateogira shrink-0 rounded-lg border border-slate-200 bg-white px-3 py-2 font-medium text-slate-600 hover:bg-slate-50">
                ${d(o.nombre)}
            </button>
        `).join("")}
    `,n.addEventListener("click",o=>{let e=o.target.closest("button[data-categoria-id]");if(!e||!n.contains(e))return;let a=e.dataset.categoriaId,c=a==="all"?null:Number(a);c!==null&&(!Number.isInteger(c)||c<=0)||(n.querySelectorAll("button[data-categoria-id]").forEach(s=>{let i=s===e;s.setAttribute("aria-pressed",String(i)),s.classList.toggle("border-indigo-600",i),s.classList.toggle("bg-indigo-600",i),s.classList.toggle("text-white",i),s.classList.toggle("border-slate-200",!i),s.classList.toggle("bg-white",!i),s.classList.toggle("text-slate-600",!i),s.classList.toggle("hover:bg-slate-50",!i)}),B(c))})}async function wt(){H||!$||await lt(V+1,W,!1)}async function B(t=null){W=t,V=0,$=!1,++E,await lt(1,t,!0)}async function lt(t,n,o){let e=document.querySelector("#catalogoPaginador"),a=document.querySelector("#catalogProductCount"),c=document.querySelector("#catalogProductStatus"),s=document.querySelector("#catalogLoadMore");if(!M||!a||!c||!s)return;let i=++E;H=!0,s.disabled=!0,s.classList.add("hidden"),o?(rt.clear(),M.replaceChildren(),a.textContent="Cargando...",c.textContent="Cargando productos..."):c.textContent="Cargando m\xE1s productos...";try{let u=await X(t,n,j);if(i!==E)return;for(let l of u.products)rt.set(l.id,{...l,precioVenta:Number(l.precioVenta)});let p=u.products.map(l=>{let b=d(l.nombre),k=l.foto&&l.foto!=="null"&&l.foto!=="undefined"?l.foto:null,dt=Number(l.precioVenta).toLocaleString();return`
                <article 
                    data-product-id="${l.id}"
                    class="cursor-pointer group overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-indigo-300 hover:shadow-md">
                    <div class="h-32 bg-slate-100 ${k!=null?"":"grid place-items-center"}">
                        ${k!=null?`<img src="/build/img/${d(k)}" alt="${b}" loading="lazy" class="h-full w-full object-cover">`:'<span class=" material-symbols-outlined text-5xl">restaurant</span>'}
                    </div>
                    <div class="p-3">
                        <h4 class="text-base font-semibold text-slate-900">${b}</h4>
                        <div class="mt-3 flex items-center justify-between gap-2">
                            <span class="text-lg font-bold text-slate-900">$${d(dt)}</span>
                            <button
                                type="button"
                                aria-label="Agregar ${b}"
                                class="flex size-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white"
                            >
                                <span class="material-symbols-outlined">add</span>
                            </button>
                        </div>
                    </div>
                </article>`}).join("");o?(M.innerHTML=p,e?.scrollTo({top:0})):M.insertAdjacentHTML("beforeend",p),V=u.page,$=u.hasMore,a.textContent=`${u.products.length} en esta p\xE1gina`,c.textContent=u.products.length>0?`Mostrando p\xE1gina ${u.page}`:"No hay productos vendibles en esta sucursal.",s.classList.toggle("hidden",!$)}catch(u){if(i!==E)return;o&&(a.textContent="Sin cargar"),c.textContent=u instanceof Error?u.message:"No fue posible cargar los productos.",o||s.classList.remove("hidden")}finally{i===E&&(H=!1,s.disabled=!1)}}function Jt(){z(),ut(),document.addEventListener("click",t=>{let o=t.target.closest("[data-open-active-detail], [data-open-order-workspace]"),e=o?.closest("[data-table-id]");if(!o||!e)return;let a=Number(e.dataset.resourceId);a&&(o.hasAttribute("data-open-active-detail")?(L(),Q(a)):(w(),it(a,e.dataset.tableName??"Mesa")))})}export{Jt as initTables};
//# sourceMappingURL=tables-KRLE2ETF.js.map
