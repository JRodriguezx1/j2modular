function d(t){let e=document.createElement("div");return e.textContent=t,e.innerHTML}async function T(t){let e=`/restaurant/api/occupations/active?resourceId=${encodeURIComponent(t)}`,n=await fetch(e,{headers:{Accept:"application/json"}}),o=await n.json();if(!n.ok||!o.success||!o.occupation)throw new Error(o.message??"No fue posible consultar la atenci\xF3n.");return o.occupation}async function I(t){let e=`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(t)}`,n=await fetch(e,{headers:{Accept:"application/json"}}),o=await n.json();if(!n.ok||!o.success||!o.data)throw new Error(o.message??"No fue posible consultar las cuentas.");return o.data}var m,O,y,_,U;function st(){window.clearTimeout(U),O.classList.remove("hidden"),m.inert=!1,requestAnimationFrame(()=>{m.classList.remove("translate-x-full")}),m.setAttribute("aria-hidden","false")}function C(){m.getAttribute("aria-hidden")!=="true"&&(++A,m.classList.add("translate-x-full"),m.setAttribute("aria-hidden","true"),m.inert=!0,U=window.setTimeout(()=>{O.classList.add("hidden")},300))}function it(){y.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function ut(t){let e=t.clientName?d(t.clientName):"Sin cliente asociado",n=t.numberOfPeople!==null?String(t.numberOfPeople):"\u2014",o=t.reservationId!==null?`#${t.reservationId}`:"Atenci\xF3n directa",a=t.observations?d(t.observations):"Sin observaciones";y.innerHTML=`
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
                ${v("Cliente",e)}
                ${v("Personas",n)}
                ${v("Origen",o)}
                ${v("Inicio",V(t.startDate))}
                ${v("Fin estimado",t.estimatedEndDate?V(t.estimatedEndDate):"\u2014")}
                ${v("Observaciones",a)}
            </div>
        </div>`}function v(t,e){return`
        <div class="border-b border-slate-100 pb-4">
            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-400">
                ${t}
            </p>
            <p class="text-sm font-medium text-slate-800">
                ${e}
            </p>
        </div>`}function V(t){let e=new Date(t.replace(" ","T"));return Number.isNaN(e.getTime())?t:new Intl.DateTimeFormat("es",{dateStyle:"medium",timeStyle:"short"}).format(e)}function lt(t){let e=document.createElement("section");e.className="mt-8 border-t border-slate-200 pt-5",e.innerHTML=`
        <h3 class="mb-3 font-semibold text-slate-900">Cuentas</h3>
        ${t.length===0?'<p class="text-sm text-slate-500">Esta atenci\xF3n no tiene cuentas.</p>':t.map(n=>`
                <article class="mb-2 rounded-xl border border-slate-200 p-3">
                    <p class="font-medium text-slate-900">${d(n.name)}</p>
                    <p class="text-xs text-slate-500">
                        ${d(n.status)} \xB7 ${n.invoiceId===null?"Sin orden todav\xEDa":`Orden #${n.invoiceId}`}
                    </p>
                </article>
            `).join("")}`,y.appendChild(e)}function dt(t){let e=document.createElement("p");e.className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700",e.textContent=t,y.appendChild(e)}var A=0;function G(){let t=document.getElementById("activeOccupationDrawer"),e=document.getElementById("activeOccupationOverlay"),n=document.getElementById("activeOccupationContent"),o=document.getElementById("activeOccupationActions");!t||!e||!n||!o||(m=t,O=e,y=n,_=o,m.inert=!0,m.addEventListener("click",a=>{a.target.closest("[data-close-active-occupation]")&&C()}),O.addEventListener("click",C),document.addEventListener("keydown",a=>{a.key==="Escape"&&m.getAttribute("aria-hidden")==="false"&&C()}))}async function Q(t){if(!t)return;let e=++A;st(),it(),_.classList.add("hidden");try{let n=await T(t);if(e!==A)return;ut(n);try{let o=await I(n.occupationId);if(e!==A)return;lt(o.accounts)}catch(o){if(e!==A)return;dt(o instanceof Error?o.message:"No fue posible consultar las cuentas.")}}catch(n){if(e!==A)return;y.innerHTML=`
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                ${d(n instanceof Error?n.message:"Ocurri\xF3 un error inesperado.")}
            </div>`}}async function z(){let e=await fetch("/admin/api/inventory/getAllcategorias");if(!e.ok)throw new Error("No fue posible consultar las categorias.");let n=await e.json();if(!Array.isArray(n))throw new Error("La respuesta de categor\xEDas no tiene el formato esperado.");return n}async function J(t=1,e=null,n=""){let o=new URLSearchParams({page:String(t)});e!=null&&o.set("categoriaId",String(e)),n&&o.set("search",n);let a=`/admin/api/inventory/listSimpleProducts?${o.toString()}`,s=await fetch(a),r=await s.json();if(!s.ok||!r.success||!r.data||!Array.isArray(r.data.products))throw new Error("No fue posible consultar las productos.");return r.data}async function K(t,e){if(!Number.isInteger(t)||t<=0)throw new Error("El ID de la cuenta no es v\xE1lido.");let n=`/restaurant/api/accounts/summary?accountId=${encodeURIComponent(String(t))}`,o=await fetch(n,{headers:{Accept:"application/json"},signal:e}),a=await o.json();if(!o.ok||!a.success||!a.data)throw new Error(a.message??"No fue posible consultar la cuenta.");if(!Array.isArray(a.data.consumos)||!a.data.totales||typeof a.data.totales!="object"||Array.isArray(a.data.totales))throw new Error("La respuesta de la cuenta no tiene el formato esperado.");return a.data}var P=0,S=null,mt=new Intl.NumberFormat("es-CO",{style:"currency",currency:"COP",minimumFractionDigits:0,maximumFractionDigits:2}),pt=new Intl.NumberFormat("es-CO",{maximumFractionDigits:2});function c(t,e){let n=document.getElementById(t);n&&(n.textContent=e)}function f(t){let e=document.getElementById("accountPanelStatus");e&&(e.textContent=t,e.classList.toggle("hidden",t===""))}function p(t,e){let n=document.getElementById(t);if(!n)return;let o=document.createElement("p");o.className="text-sm text-slate-500",o.textContent=e,n.replaceChildren(o)}function x(t){let e=Number(t);return Number.isFinite(e)?mt.format(e):"\u2014"}function X(t){let e=Number(t);return Number.isFinite(e)?pt.format(e):"\u2014"}function gt(t){if(t.length===0){p("accountPanelConsumptions","Sin productos registrados.");return}let e=document.getElementById("accountPanelConsumptions"),n=document.getElementById("accountConsumptionTemplate");if(!e||!(n instanceof HTMLTemplateElement))throw new Error("No se encontr\xF3 la vista de productos registrados.");let o=document.createDocumentFragment();for(let a of t){let s=n.content.firstElementChild?.cloneNode(!0);if(!(s instanceof HTMLElement))throw new Error("La vista de productos registrados no es v\xE1lida.");let r=s.querySelector("[data-consumption-quantity]"),i=s.querySelector("[data-consumption-name]"),g=s.querySelector("[data-consumption-unit-price]"),l=s.querySelector("[data-consumption-total]");if(!r||!i||!g||!l)throw new Error("La vista de productos registrados est\xE1 incompleta.");r.textContent=X(a.cantidad),i.textContent=a.nombreproducto,g.textContent=`${x(a.valorunidad)} c/u`,l.textContent=x(a.total),o.append(s)}e.replaceChildren(o)}function ft(t){c("accountPanelName",t.name);let e=X(t.totales.totalunidades);c("accountPanelProductCount",`${e} ${e==="1"?"producto":"productos"}`);let n=t.consumos.length;c("accountPanelRegisteredCount",`${n} ${n===1?"\xEDtem":"\xEDtems"}`),gt(t.consumos);let o=x(t.totales.total);c("accountPanelRegisteredAmount",o),c("accountPanelDiscount",x(t.totales.descuento)),c("accountPanelTax",`${x(t.totales.valorimpuestototal)} incluidos`),c("accountPanelTotal",o),c("accountPanelPayTotal",o),f(t.consumos.length===0?"Cuenta abierta sin productos registrados.":"")}function M(){++P,S?.abort(),S=null,document.getElementById("accountPanel")?.setAttribute("aria-busy","false"),c("atencionEstadoCuenta","Cuenta por consultar"),c("accountPanelOccupation","Atenci\xF3n por consultar"),c("accountPanelName","Cuenta por consultar"),c("accountPanelClientName","Por consultar"),c("accountPanelProductCount","\u2014 productos"),c("accountPanelRegisteredCount","\u2014 \xEDtems"),c("accountPanelRegisteredAmount","\u2014"),c("accountPanelPendingCount","0 sin guardar"),c("accountPanelPendingAmount",x(0)),c("accountPanelDiscount","\u2014"),c("accountPanelTax","\u2014"),c("accountPanelTotal","\u2014"),c("accountPanelPayTotal","\u2014"),p("accountPanelConsumptions","Los productos registrados aparecer\xE1n aqu\xED."),p("accountPanelPendingProducts","No hay productos nuevos."),f("Cuenta por consultar.")}function Y(t){M(),c("atencionEstadoCuenta","Cuenta no disponible"),f(t),p("accountPanelConsumptions","No fue posible consultar los productos registrados.")}async function Z(t,e){M();let n=P;c("accountPanelOccupation",`Atenci\xF3n #${t.occupationId}`),c("accountPanelClientName",t.clientName?.trim()||(t.clientId===null?"Consumidor final":"Cliente por consultar"));let o=e.filter(r=>r.occupationId===t.occupationId&&r.status==="abierta");if(o.length===0){c("atencionEstadoCuenta","Sin cuenta abierta"),f("Esta atenci\xF3n a\xFAn no tiene una cuenta abierta."),p("accountPanelConsumptions","Sin cuenta para consultar productos.");return}if(o.length>1){c("atencionEstadoCuenta","Varias cuentas abiertas"),f("Esta atenci\xF3n tiene varias cuentas abiertas. La selecci\xF3n de cuenta est\xE1 pendiente."),p("accountPanelConsumptions","Selecciona una cuenta cuando esta opci\xF3n est\xE9 disponible.");return}let a=o[0];c("atencionEstadoCuenta","Cuenta abierta"),c("accountPanelName",a.name),f("Consultando productos y total de la cuenta..."),p("accountPanelConsumptions","Cargando productos registrados..."),document.getElementById("accountPanel")?.setAttribute("aria-busy","true");let s=new AbortController;S=s;try{let r=await K(a.id,s.signal);if(n!==P)return;if(r.accountId!==a.id||r.occupationId!==t.occupationId)throw new Error("El resumen recibido no corresponde a esta cuenta.");ft(r)}catch(r){if(n!==P||s.signal.aborted)return;f(r instanceof Error?r.message:"No fue posible consultar la cuenta."),p("accountPanelConsumptions","No fue posible consultar los productos registrados.")}finally{n===P&&(S=null,document.getElementById("accountPanel")?.setAttribute("aria-busy","false"))}}var u,N,et,w=0,h=0,nt,j=0,F=null,$=!1,k=!1,R="",tt,E=document.querySelector("#atencionMesaText");function H(t,e){let n=document.getElementById(t);n&&(n.textContent=e)}function bt(){window.clearTimeout(nt),N.classList.remove("hidden"),u.inert=!1,u.setAttribute("aria-hidden","false"),requestAnimationFrame(()=>u.classList.remove("translate-x-full"))}function L(){if(u.getAttribute("aria-hidden")==="true")return;++w,M();let t=document.activeElement;t instanceof HTMLElement&&u.contains(t)&&t.blur(),u.classList.add("translate-x-full"),u.inert=!0,u.setAttribute("aria-hidden","true"),nt=window.setTimeout(()=>N.classList.add("hidden"),300)}async function ot(t,e){if(!t||!u)return;let n=++w;et.textContent=e,E.classList.remove("bg-emerald-50","text-emerald-700","bg-red-50","text-red-700"),E.textContent="Consultando atenci\xF3n...",H("atencionNoActiva","Atenci\xF3n por consultar"),H("atencionEstadoResumen","Atenci\xF3n por consultar"),bt();try{let o=await T(t);if(n!==w)return;E.classList.add("bg-emerald-50","text-emerald-700"),E.textContent="En atenci\xF3n",H("atencionNoActiva",`Atenci\xF3n #${o.occupationId}`),H("atencionEstadoResumen",`Atenci\xF3n #${o.occupationId} \xB7 En uso`);let a=await I(o.occupationId);if(n!==w)return;Z(o,a.accounts)}catch(o){if(n!==w)return;let a=o instanceof Error?o.message:"No fue posible consultar la mesa.";E.classList.remove("bg-emerald-50","text-emerald-700"),E.classList.add("bg-red-50","text-red-700"),Y(a)}}function at(){let t=document.getElementById("orderWorkspaceDrawer"),e=document.getElementById("orderWorkspaceOverlay"),n=document.getElementById("orderWorkspaceContent"),o=document.getElementById("orderWorkspaceTitle"),a=document.querySelector("#catalogLoadMore");if(!t||!e||!n||!o)return;vt(),u=t,N=e,et=o,u.inert=!0,u.addEventListener("click",r=>{r.target.closest("[data-close-order-workspace]")&&L()}),u.addEventListener("change",r=>{if(!r.target.matches("[data-order-workspace-account]"))return;let g=u.querySelector("[data-order-workspace-account-info]")}),N.addEventListener("click",L),document.addEventListener("keydown",r=>{r.key==="Escape"&&u.getAttribute("aria-hidden")==="false"&&L()});let s=document.querySelector("#searchItem");s?.addEventListener("input",()=>{let r=s.value.trim();r!==R&&(R=r,window.clearTimeout(tt),++h,k=!0,a&&(a.disabled=!0),tt=window.setTimeout(()=>{B(F)},350))}),a?.addEventListener("click",()=>{At()}),B()}async function vt(){let t=await z(),e=document.querySelector("#divCategorias");e.innerHTML=`
        <button
            type="button"
            data-categoria-id="all"
            aria-pressed="true"
            class="btncateogira shrink-0 rounded-lg border border-indigo-600 bg-indigo-600 px-3 py-2 font-medium text-white">
            Todas
        </button>
        ${t.map(n=>`
            <button
                type="button"
                data-categoria-id="${d(n.id)}"
                aria-pressed="false"
                class="btncateogira shrink-0 rounded-lg border border-slate-200 bg-white px-3 py-2 font-medium text-slate-600 hover:bg-slate-50">
                ${d(n.nombre)}
            </button>
        `).join("")}
    `,e.addEventListener("click",n=>{let o=n.target.closest("button[data-categoria-id]");if(!o||!e.contains(o))return;let a=o.dataset.categoriaId,s=a==="all"?null:Number(a);s!==null&&(!Number.isInteger(s)||s<=0)||(e.querySelectorAll("button[data-categoria-id]").forEach(r=>{let i=r===o;r.setAttribute("aria-pressed",String(i)),r.classList.toggle("border-indigo-600",i),r.classList.toggle("bg-indigo-600",i),r.classList.toggle("text-white",i),r.classList.toggle("border-slate-200",!i),r.classList.toggle("bg-white",!i),r.classList.toggle("text-slate-600",!i),r.classList.toggle("hover:bg-slate-50",!i)}),B(s))})}async function At(){k||!$||await rt(j+1,F,!1)}async function B(t=null){F=t,j=0,$=!1,++h,await rt(1,t,!0)}async function rt(t,e,n){let o=document.querySelector("#catalogoPaginador"),a=document.querySelector("#catalogProductGrid"),s=document.querySelector("#catalogProductCount"),r=document.querySelector("#catalogProductStatus"),i=document.querySelector("#catalogLoadMore");if(!a||!s||!r||!i)return;let g=++h;k=!0,i.disabled=!0,i.classList.add("hidden"),n?(a.replaceChildren(),s.textContent="Cargando...",r.textContent="Cargando productos..."):r.textContent="Cargando m\xE1s productos...";try{let l=await J(t,e,R);if(g!==h)return;let W=l.products.map(b=>{let q=d(b.nombre),D=b.foto&&b.foto!=="null"&&b.foto!=="undefined"?b.foto:null,ct=Number(b.precioVenta).toLocaleString();return`
                <article class="group overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-indigo-300 hover:shadow-md">
                    <div class="h-32 bg-slate-100 ${D!=null?"":"grid place-items-center"}">
                        ${D!=null?`<img src="/build/img/${d(D)}" alt="${q}" loading="lazy" class="h-full w-full object-cover">`:'<span class=" material-symbols-outlined text-5xl">restaurant</span>'}
                    </div>
                    <div class="p-3">
                        <h4 class="text-base font-semibold text-slate-900">${q}</h4>
                        <div class="mt-3 flex items-center justify-between gap-2">
                            <span class="text-lg font-bold text-slate-900">$${d(ct)}</span>
                            <button
                                type="button"
                                aria-label="Agregar ${q}"
                                class="flex size-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white"
                            >
                                <span class="material-symbols-outlined">add</span>
                            </button>
                        </div>
                    </div>
                </article>`}).join("");n?(a.innerHTML=W,o?.scrollTo({top:0})):a.insertAdjacentHTML("beforeend",W),j=l.page,$=l.hasMore,s.textContent=`${l.products.length} en esta p\xE1gina`,r.textContent=l.products.length>0?`Mostrando p\xE1gina ${l.page}`:"No hay productos vendibles en esta sucursal.",i.classList.toggle("hidden",!$)}catch(l){if(g!==h)return;n&&(s.textContent="Sin cargar"),r.textContent=l instanceof Error?l.message:"No fue posible cargar los productos.",n||i.classList.remove("hidden")}finally{g===h&&(k=!1,i.disabled=!1)}}function Dt(){G(),at(),document.addEventListener("click",t=>{let n=t.target.closest("[data-open-active-detail], [data-open-order-workspace]"),o=n?.closest("[data-table-id]");if(!n||!o)return;let a=Number(o.dataset.resourceId);a&&(n.hasAttribute("data-open-active-detail")?(L(),Q(a)):(C(),ot(a,o.dataset.tableName??"Mesa")))})}export{Dt as initTables};
//# sourceMappingURL=tables-BFMMRA7X.js.map
