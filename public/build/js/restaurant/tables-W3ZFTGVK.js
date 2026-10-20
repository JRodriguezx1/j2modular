function u(t){let e=document.createElement("div");return e.textContent=t,e.innerHTML}async function P(t){let e=`/restaurant/api/occupations/active?resourceId=${encodeURIComponent(t)}`,n=await fetch(e,{headers:{Accept:"application/json"}}),o=await n.json();if(!n.ok||!o.success||!o.occupation)throw new Error(o.message??"No fue posible consultar la atenci\xF3n.");return o.occupation}async function w(t){let e=`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(t)}`,n=await fetch(e,{headers:{Accept:"application/json"}}),o=await n.json();if(!n.ok||!o.success||!o.data)throw new Error(o.message??"No fue posible consultar las cuentas.");return o.data}var m,L,v,D,R;function Z(){window.clearTimeout(R),L.classList.remove("hidden"),m.inert=!1,requestAnimationFrame(()=>{m.classList.remove("translate-x-full")}),m.setAttribute("aria-hidden","false")}function y(){m.getAttribute("aria-hidden")!=="true"&&(++b,m.classList.add("translate-x-full"),m.setAttribute("aria-hidden","true"),m.inert=!0,R=window.setTimeout(()=>{L.classList.add("hidden")},300))}function tt(){v.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function et(t){let e=t.clientName?u(t.clientName):"Sin cliente asociado",n=t.numberOfPeople!==null?String(t.numberOfPeople):"\u2014",o=t.reservationId!==null?`#${t.reservationId}`:"Atenci\xF3n directa",r=t.observations?u(t.observations):"Sin observaciones";v.innerHTML=`
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
                ${f("Cliente",e)}
                ${f("Personas",n)}
                ${f("Origen",o)}
                ${f("Inicio",q(t.startDate))}
                ${f("Fin estimado",t.estimatedEndDate?q(t.estimatedEndDate):"\u2014")}
                ${f("Observaciones",r)}
            </div>
        </div>`}function f(t,e){return`
        <div class="border-b border-slate-100 pb-4">
            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-400">
                ${t}
            </p>
            <p class="text-sm font-medium text-slate-800">
                ${e}
            </p>
        </div>`}function q(t){let e=new Date(t.replace(" ","T"));return Number.isNaN(e.getTime())?t:new Intl.DateTimeFormat("es",{dateStyle:"medium",timeStyle:"short"}).format(e)}function nt(t){let e=document.createElement("section");e.className="mt-8 border-t border-slate-200 pt-5",e.innerHTML=`
        <h3 class="mb-3 font-semibold text-slate-900">Cuentas</h3>
        ${t.length===0?'<p class="text-sm text-slate-500">Esta atenci\xF3n no tiene cuentas.</p>':t.map(n=>`
                <article class="mb-2 rounded-xl border border-slate-200 p-3">
                    <p class="font-medium text-slate-900">${u(n.name)}</p>
                    <p class="text-xs text-slate-500">
                        ${u(n.status)} \xB7 ${n.invoiceId===null?"Sin orden todav\xEDa":`Orden #${n.invoiceId}`}
                    </p>
                </article>
            `).join("")}`,v.appendChild(e)}function ot(t){let e=document.createElement("p");e.className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700",e.textContent=t,v.appendChild(e)}var b=0;function B(){let t=document.getElementById("activeOccupationDrawer"),e=document.getElementById("activeOccupationOverlay"),n=document.getElementById("activeOccupationContent"),o=document.getElementById("activeOccupationActions");!t||!e||!n||!o||(m=t,L=e,v=n,D=o,m.inert=!0,m.addEventListener("click",r=>{r.target.closest("[data-close-active-occupation]")&&y()}),L.addEventListener("click",y),document.addEventListener("keydown",r=>{r.key==="Escape"&&m.getAttribute("aria-hidden")==="false"&&y()}))}async function j(t){if(!t)return;let e=++b;Z(),tt(),D.classList.add("hidden");try{let n=await P(t);if(e!==b)return;et(n);try{let o=await w(n.occupationId);if(e!==b)return;nt(o.accounts)}catch(o){if(e!==b)return;ot(o instanceof Error?o.message:"No fue posible consultar las cuentas.")}}catch(n){if(e!==b)return;v.innerHTML=`
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                ${u(n instanceof Error?n.message:"Ocurri\xF3 un error inesperado.")}
            </div>`}}async function F(){let e=await fetch("/admin/api/inventory/getAllcategorias");if(!e.ok)throw new Error("No fue posible consultar las categorias.");let n=await e.json();if(!Array.isArray(n))throw new Error("La respuesta de categor\xEDas no tiene el formato esperado.");return n}async function W(t=1,e=null,n=""){let o=new URLSearchParams({page:String(t)});e!=null&&o.set("categoriaId",String(e)),n&&o.set("search",n);let r=`/admin/api/inventory/listSimpleProducts?${o.toString()}`,c=await fetch(r),a=await c.json();if(!c.ok||!a.success||!a.data||!Array.isArray(a.data.products))throw new Error("No fue posible consultar las productos.");return a.data}var at=0,V=null,rt=new Intl.NumberFormat("es-CO",{style:"currency",currency:"COP",minimumFractionDigits:0,maximumFractionDigits:2}),Et=new Intl.NumberFormat("es-CO",{maximumFractionDigits:2});function l(t,e){let n=document.getElementById(t);n&&(n.textContent=e)}function ct(t){let e=document.getElementById("accountPanelStatus");e&&(e.textContent=t,e.classList.toggle("hidden",t===""))}function _(t,e){let n=document.getElementById(t);if(!n)return;let o=document.createElement("p");o.className="text-sm text-slate-500",o.textContent=e,n.replaceChildren(o)}function st(t){let e=Number(t);return Number.isFinite(e)?rt.format(e):"\u2014"}function U(){++at,V?.abort(),V=null,document.getElementById("accountPanel")?.setAttribute("aria-busy","false"),l("atencionEstadoCuenta","Cuenta por consultar"),l("accountPanelOccupation","Atenci\xF3n por consultar"),l("accountPanelName","Cuenta por consultar"),l("accountPanelClientName","Por consultar"),l("accountPanelProductCount","\u2014 productos"),l("accountPanelRegisteredCount","\u2014 \xEDtems"),l("accountPanelRegisteredAmount","\u2014"),l("accountPanelPendingCount","0 sin guardar"),l("accountPanelPendingAmount",st(0)),l("accountPanelDiscount","\u2014"),l("accountPanelTax","\u2014"),l("accountPanelTotal","\u2014"),l("accountPanelPayTotal","\u2014"),_("accountPanelConsumptions","Los productos registrados aparecer\xE1n aqu\xED."),_("accountPanelPendingProducts","No hay productos nuevos."),ct("Cuenta por consultar.")}var i,T,Q,E=0,A=0,z,$=0,k=null,I=!1,O=!1,H="",G,x=document.querySelector("#atencionMesaText");function it(){window.clearTimeout(z),T.classList.remove("hidden"),i.inert=!1,i.setAttribute("aria-hidden","false"),requestAnimationFrame(()=>i.classList.remove("translate-x-full"))}function h(){if(i.getAttribute("aria-hidden")==="true")return;++E,U();let t=document.activeElement;t instanceof HTMLElement&&i.contains(t)&&t.blur(),i.classList.add("translate-x-full"),i.inert=!0,i.setAttribute("aria-hidden","true"),z=window.setTimeout(()=>T.classList.add("hidden"),300)}async function J(t,e){if(!t||!i)return;let n=++E;Q.textContent=e,x.classList.remove("bg-emerald-50","text-emerald-700","bg-red-50","text-red-700"),it();try{let o=await P(t);if(n!==E)return;let r=await w(o.occupationId);if(n!==E)return;accounts=r.accounts,selectedAccountId=accounts.find(c=>c.status==="abierta")?.id??accounts[0]?.id??null,x.classList.add("bg-emerald-50","text-emerald-700"),x.textContent="En atenci\xF3n",renderWorkspace(o)}catch(o){if(n!==E)return;let r=o instanceof Error?o.message:"No fue posible consultar la mesa.";x.classList.add("bg-red-50","text-red-700"),x.textContent=`${u(r)}`}}function K(){let t=document.getElementById("orderWorkspaceDrawer"),e=document.getElementById("orderWorkspaceOverlay"),n=document.getElementById("orderWorkspaceContent"),o=document.getElementById("orderWorkspaceTitle"),r=document.querySelector("#catalogLoadMore");if(!t||!e||!n||!o)return;ut(),i=t,T=e,content=n,Q=o,i.inert=!0,i.addEventListener("click",a=>{a.target.closest("[data-close-order-workspace]")&&h()}),i.addEventListener("change",a=>{let s=a.target;if(!s.matches("[data-order-workspace-account]"))return;selectedAccountId=Number(s.value);let p=accounts.find(C=>C.id===selectedAccountId),d=i.querySelector("[data-order-workspace-account-info]");p&&d&&(d.textContent=`Estado: ${p.status} \xB7 ${p.invoiceId===null?"Sin orden todav\xEDa":`Orden #${p.invoiceId}`}`)}),T.addEventListener("click",h),document.addEventListener("keydown",a=>{a.key==="Escape"&&i.getAttribute("aria-hidden")==="false"&&h()});let c=document.querySelector("#searchItem");c?.addEventListener("input",()=>{let a=c.value.trim();a!==H&&(H=a,window.clearTimeout(G),++A,O=!0,r&&(r.disabled=!0),G=window.setTimeout(()=>{N(k)},350))}),r?.addEventListener("click",()=>{lt()}),N()}async function ut(){let t=await F(),e=document.querySelector("#divCategorias");e.innerHTML=`
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
                data-categoria-id="${u(n.id)}"
                aria-pressed="false"
                class="btncateogira shrink-0 rounded-lg border border-slate-200 bg-white px-3 py-2 font-medium text-slate-600 hover:bg-slate-50">
                ${u(n.nombre)}
            </button>
        `).join("")}
    `,e.addEventListener("click",n=>{let o=n.target.closest("button[data-categoria-id]");if(!o||!e.contains(o))return;let r=o.dataset.categoriaId,c=r==="all"?null:Number(r);c!==null&&(!Number.isInteger(c)||c<=0)||(e.querySelectorAll("button[data-categoria-id]").forEach(a=>{let s=a===o;a.setAttribute("aria-pressed",String(s)),a.classList.toggle("border-indigo-600",s),a.classList.toggle("bg-indigo-600",s),a.classList.toggle("text-white",s),a.classList.toggle("border-slate-200",!s),a.classList.toggle("bg-white",!s),a.classList.toggle("text-slate-600",!s),a.classList.toggle("hover:bg-slate-50",!s)}),N(c))})}async function lt(){O||!I||await X($+1,k,!1)}async function N(t=null){k=t,$=0,I=!1,++A,await X(1,t,!0)}async function X(t,e,n){let o=document.querySelector("#catalogoPaginador"),r=document.querySelector("#catalogProductGrid"),c=document.querySelector("#catalogProductCount"),a=document.querySelector("#catalogProductStatus"),s=document.querySelector("#catalogLoadMore");if(!r||!c||!a||!s)return;let p=++A;O=!0,s.disabled=!0,s.classList.add("hidden"),n?(r.replaceChildren(),c.textContent="Cargando...",a.textContent="Cargando productos..."):a.textContent="Cargando m\xE1s productos...";try{let d=await W(t,e,H);if(p!==A)return;let C=d.products.map(g=>{let S=u(g.nombre),M=g.foto&&g.foto!=="null"&&g.foto!=="undefined"?g.foto:null,Y=Number(g.precioVenta).toLocaleString();return`
                <article class="group overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-indigo-300 hover:shadow-md">
                    <div class="h-32 bg-slate-100 ${M!=null?"":"grid place-items-center"}">
                        ${M!=null?`<img src="/build/img/${u(M)}" alt="${S}" loading="lazy" class="h-full w-full object-cover">`:'<span class=" material-symbols-outlined text-5xl">restaurant</span>'}
                    </div>
                    <div class="p-3">
                        <h4 class="text-base font-semibold text-slate-900">${S}</h4>
                        <div class="mt-3 flex items-center justify-between gap-2">
                            <span class="text-lg font-bold text-slate-900">$${u(Y)}</span>
                            <button
                                type="button"
                                aria-label="Agregar ${S}"
                                class="flex size-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white"
                            >
                                <span class="material-symbols-outlined">add</span>
                            </button>
                        </div>
                    </div>
                </article>`}).join("");n?(r.innerHTML=C,o?.scrollTo({top:0})):r.insertAdjacentHTML("beforeend",C),$=d.page,I=d.hasMore,c.textContent=`${d.products.length} en esta p\xE1gina`,a.textContent=d.products.length>0?`Mostrando p\xE1gina ${d.page}`:"No hay productos vendibles en esta sucursal.",s.classList.toggle("hidden",!I)}catch(d){if(p!==A)return;n&&(c.textContent="Sin cargar"),a.textContent=d instanceof Error?d.message:"No fue posible cargar los productos.",n||s.classList.remove("hidden")}finally{p===A&&(O=!1,s.disabled=!1)}}function Nt(){B(),K(),document.addEventListener("click",t=>{let n=t.target.closest("[data-open-active-detail], [data-open-order-workspace]"),o=n?.closest("[data-table-id]");if(!n||!o)return;let r=Number(o.dataset.resourceId);r&&(n.hasAttribute("data-open-active-detail")?(h(),j(r)):(y(),J(r,o.dataset.tableName??"Mesa")))})}export{Nt as initTables};
//# sourceMappingURL=tables-W3ZFTGVK.js.map
