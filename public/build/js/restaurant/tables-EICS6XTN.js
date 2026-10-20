function d(t){let e=document.createElement("div");return e.textContent=t,e.innerHTML}async function P(t){let e=`/restaurant/api/occupations/active?resourceId=${encodeURIComponent(t)}`,n=await fetch(e,{headers:{Accept:"application/json"}}),o=await n.json();if(!n.ok||!o.success||!o.occupation)throw new Error(o.message??"No fue posible consultar la atenci\xF3n.");return o.occupation}async function w(t){let e=`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(t)}`,n=await fetch(e,{headers:{Accept:"application/json"}}),o=await n.json();if(!n.ok||!o.success||!o.data)throw new Error(o.message??"No fue posible consultar las cuentas.");return o.data}var m,L,v,j,F;function nt(){window.clearTimeout(F),L.classList.remove("hidden"),m.inert=!1,requestAnimationFrame(()=>{m.classList.remove("translate-x-full")}),m.setAttribute("aria-hidden","false")}function x(){m.getAttribute("aria-hidden")!=="true"&&(++b,m.classList.add("translate-x-full"),m.setAttribute("aria-hidden","true"),m.inert=!0,F=window.setTimeout(()=>{L.classList.add("hidden")},300))}function ot(){v.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function at(t){let e=t.clientName?d(t.clientName):"Sin cliente asociado",n=t.numberOfPeople!==null?String(t.numberOfPeople):"\u2014",o=t.reservationId!==null?`#${t.reservationId}`:"Atenci\xF3n directa",r=t.observations?d(t.observations):"Sin observaciones";v.innerHTML=`
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
                ${f("Inicio",B(t.startDate))}
                ${f("Fin estimado",t.estimatedEndDate?B(t.estimatedEndDate):"\u2014")}
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
        </div>`}function B(t){let e=new Date(t.replace(" ","T"));return Number.isNaN(e.getTime())?t:new Intl.DateTimeFormat("es",{dateStyle:"medium",timeStyle:"short"}).format(e)}function rt(t){let e=document.createElement("section");e.className="mt-8 border-t border-slate-200 pt-5",e.innerHTML=`
        <h3 class="mb-3 font-semibold text-slate-900">Cuentas</h3>
        ${t.length===0?'<p class="text-sm text-slate-500">Esta atenci\xF3n no tiene cuentas.</p>':t.map(n=>`
                <article class="mb-2 rounded-xl border border-slate-200 p-3">
                    <p class="font-medium text-slate-900">${d(n.name)}</p>
                    <p class="text-xs text-slate-500">
                        ${d(n.status)} \xB7 ${n.invoiceId===null?"Sin orden todav\xEDa":`Orden #${n.invoiceId}`}
                    </p>
                </article>
            `).join("")}`,v.appendChild(e)}function ct(t){let e=document.createElement("p");e.className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700",e.textContent=t,v.appendChild(e)}var b=0;function W(){let t=document.getElementById("activeOccupationDrawer"),e=document.getElementById("activeOccupationOverlay"),n=document.getElementById("activeOccupationContent"),o=document.getElementById("activeOccupationActions");!t||!e||!n||!o||(m=t,L=e,v=n,j=o,m.inert=!0,m.addEventListener("click",r=>{r.target.closest("[data-close-active-occupation]")&&x()}),L.addEventListener("click",x),document.addEventListener("keydown",r=>{r.key==="Escape"&&m.getAttribute("aria-hidden")==="false"&&x()}))}async function V(t){if(!t)return;let e=++b;nt(),ot(),j.classList.add("hidden");try{let n=await P(t);if(e!==b)return;at(n);try{let o=await w(n.occupationId);if(e!==b)return;rt(o.accounts)}catch(o){if(e!==b)return;ct(o instanceof Error?o.message:"No fue posible consultar las cuentas.")}}catch(n){if(e!==b)return;v.innerHTML=`
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                ${d(n instanceof Error?n.message:"Ocurri\xF3 un error inesperado.")}
            </div>`}}async function _(){let e=await fetch("/admin/api/inventory/getAllcategorias");if(!e.ok)throw new Error("No fue posible consultar las categorias.");let n=await e.json();if(!Array.isArray(n))throw new Error("La respuesta de categor\xEDas no tiene el formato esperado.");return n}async function U(t=1,e=null,n=""){let o=new URLSearchParams({page:String(t)});e!=null&&o.set("categoriaId",String(e)),n&&o.set("search",n);let r=`/admin/api/inventory/listSimpleProducts?${o.toString()}`,s=await fetch(r),a=await s.json();if(!s.ok||!a.success||!a.data||!Array.isArray(a.data.products))throw new Error("No fue posible consultar las productos.");return a.data}var st=0,G=null,it=new Intl.NumberFormat("es-CO",{style:"currency",currency:"COP",minimumFractionDigits:0,maximumFractionDigits:2}),Ct=new Intl.NumberFormat("es-CO",{maximumFractionDigits:2});function u(t,e){let n=document.getElementById(t);n&&(n.textContent=e)}function Q(t){let e=document.getElementById("accountPanelStatus");e&&(e.textContent=t,e.classList.toggle("hidden",t===""))}function N(t,e){let n=document.getElementById(t);if(!n)return;let o=document.createElement("p");o.className="text-sm text-slate-500",o.textContent=e,n.replaceChildren(o)}function ut(t){let e=Number(t);return Number.isFinite(e)?it.format(e):"\u2014"}function $(){++st,G?.abort(),G=null,document.getElementById("accountPanel")?.setAttribute("aria-busy","false"),u("atencionEstadoCuenta","Cuenta por consultar"),u("accountPanelOccupation","Atenci\xF3n por consultar"),u("accountPanelName","Cuenta por consultar"),u("accountPanelClientName","Por consultar"),u("accountPanelProductCount","\u2014 productos"),u("accountPanelRegisteredCount","\u2014 \xEDtems"),u("accountPanelRegisteredAmount","\u2014"),u("accountPanelPendingCount","0 sin guardar"),u("accountPanelPendingAmount",ut(0)),u("accountPanelDiscount","\u2014"),u("accountPanelTax","\u2014"),u("accountPanelTotal","\u2014"),u("accountPanelPayTotal","\u2014"),N("accountPanelConsumptions","Los productos registrados aparecer\xE1n aqu\xED."),N("accountPanelPendingProducts","No hay productos nuevos."),Q("Cuenta por consultar.")}function z(t){$(),u("atencionEstadoCuenta","Cuenta no disponible"),Q(t),N("accountPanelConsumptions","No fue posible consultar los productos registrados.")}var i,I,K,E=0,y=0,X,D=0,R=null,O=!1,S=!1,k="",J,A=document.querySelector("#atencionMesaText");function T(t,e){let n=document.getElementById(t);n&&(n.textContent=e)}function lt(){window.clearTimeout(X),I.classList.remove("hidden"),i.inert=!1,i.setAttribute("aria-hidden","false"),requestAnimationFrame(()=>i.classList.remove("translate-x-full"))}function h(){if(i.getAttribute("aria-hidden")==="true")return;++E,$();let t=document.activeElement;t instanceof HTMLElement&&i.contains(t)&&t.blur(),i.classList.add("translate-x-full"),i.inert=!0,i.setAttribute("aria-hidden","true"),X=window.setTimeout(()=>I.classList.add("hidden"),300)}async function Y(t,e){if(!t||!i)return;let n=++E;K.textContent=e,A.classList.remove("bg-emerald-50","text-emerald-700","bg-red-50","text-red-700"),A.textContent="Consultando atenci\xF3n...",T("atencionNoActiva","Atenci\xF3n por consultar"),T("atencionEstadoResumen","Atenci\xF3n por consultar"),lt();try{let o=await P(t);if(n!==E)return;A.classList.add("bg-emerald-50","text-emerald-700"),A.textContent="En atenci\xF3n",T("atencionNoActiva",`Atenci\xF3n #${o.occupationId}`),T("atencionEstadoResumen",`Atenci\xF3n #${o.occupationId} \xB7 En uso`);let r=await w(o.occupationId);if(n!==E)return}catch(o){if(n!==E)return;let r=o instanceof Error?o.message:"No fue posible consultar la mesa.";A.classList.remove("bg-emerald-50","text-emerald-700"),A.classList.add("bg-red-50","text-red-700"),z(r)}}function Z(){let t=document.getElementById("orderWorkspaceDrawer"),e=document.getElementById("orderWorkspaceOverlay"),n=document.getElementById("orderWorkspaceContent"),o=document.getElementById("orderWorkspaceTitle"),r=document.querySelector("#catalogLoadMore");if(!t||!e||!n||!o)return;dt(),i=t,I=e,content=n,K=o,i.inert=!0,i.addEventListener("click",a=>{a.target.closest("[data-close-order-workspace]")&&h()}),i.addEventListener("change",a=>{let c=a.target;if(!c.matches("[data-order-workspace-account]"))return;selectedAccountId=Number(c.value);let p=accounts.find(C=>C.id===selectedAccountId),l=i.querySelector("[data-order-workspace-account-info]");p&&l&&(l.textContent=`Estado: ${p.status} \xB7 ${p.invoiceId===null?"Sin orden todav\xEDa":`Orden #${p.invoiceId}`}`)}),I.addEventListener("click",h),document.addEventListener("keydown",a=>{a.key==="Escape"&&i.getAttribute("aria-hidden")==="false"&&h()});let s=document.querySelector("#searchItem");s?.addEventListener("input",()=>{let a=s.value.trim();a!==k&&(k=a,window.clearTimeout(J),++y,S=!0,r&&(r.disabled=!0),J=window.setTimeout(()=>{q(R)},350))}),r?.addEventListener("click",()=>{mt()}),q()}async function dt(){let t=await _(),e=document.querySelector("#divCategorias");e.innerHTML=`
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
    `,e.addEventListener("click",n=>{let o=n.target.closest("button[data-categoria-id]");if(!o||!e.contains(o))return;let r=o.dataset.categoriaId,s=r==="all"?null:Number(r);s!==null&&(!Number.isInteger(s)||s<=0)||(e.querySelectorAll("button[data-categoria-id]").forEach(a=>{let c=a===o;a.setAttribute("aria-pressed",String(c)),a.classList.toggle("border-indigo-600",c),a.classList.toggle("bg-indigo-600",c),a.classList.toggle("text-white",c),a.classList.toggle("border-slate-200",!c),a.classList.toggle("bg-white",!c),a.classList.toggle("text-slate-600",!c),a.classList.toggle("hover:bg-slate-50",!c)}),q(s))})}async function mt(){S||!O||await tt(D+1,R,!1)}async function q(t=null){R=t,D=0,O=!1,++y,await tt(1,t,!0)}async function tt(t,e,n){let o=document.querySelector("#catalogoPaginador"),r=document.querySelector("#catalogProductGrid"),s=document.querySelector("#catalogProductCount"),a=document.querySelector("#catalogProductStatus"),c=document.querySelector("#catalogLoadMore");if(!r||!s||!a||!c)return;let p=++y;S=!0,c.disabled=!0,c.classList.add("hidden"),n?(r.replaceChildren(),s.textContent="Cargando...",a.textContent="Cargando productos..."):a.textContent="Cargando m\xE1s productos...";try{let l=await U(t,e,k);if(p!==y)return;let C=l.products.map(g=>{let M=d(g.nombre),H=g.foto&&g.foto!=="null"&&g.foto!=="undefined"?g.foto:null,et=Number(g.precioVenta).toLocaleString();return`
                <article class="group overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-indigo-300 hover:shadow-md">
                    <div class="h-32 bg-slate-100 ${H!=null?"":"grid place-items-center"}">
                        ${H!=null?`<img src="/build/img/${d(H)}" alt="${M}" loading="lazy" class="h-full w-full object-cover">`:'<span class=" material-symbols-outlined text-5xl">restaurant</span>'}
                    </div>
                    <div class="p-3">
                        <h4 class="text-base font-semibold text-slate-900">${M}</h4>
                        <div class="mt-3 flex items-center justify-between gap-2">
                            <span class="text-lg font-bold text-slate-900">$${d(et)}</span>
                            <button
                                type="button"
                                aria-label="Agregar ${M}"
                                class="flex size-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white"
                            >
                                <span class="material-symbols-outlined">add</span>
                            </button>
                        </div>
                    </div>
                </article>`}).join("");n?(r.innerHTML=C,o?.scrollTo({top:0})):r.insertAdjacentHTML("beforeend",C),D=l.page,O=l.hasMore,s.textContent=`${l.products.length} en esta p\xE1gina`,a.textContent=l.products.length>0?`Mostrando p\xE1gina ${l.page}`:"No hay productos vendibles en esta sucursal.",c.classList.toggle("hidden",!O)}catch(l){if(p!==y)return;n&&(s.textContent="Sin cargar"),a.textContent=l instanceof Error?l.message:"No fue posible cargar los productos.",n||c.classList.remove("hidden")}finally{p===y&&(S=!1,c.disabled=!1)}}function $t(){W(),Z(),document.addEventListener("click",t=>{let n=t.target.closest("[data-open-active-detail], [data-open-order-workspace]"),o=n?.closest("[data-table-id]");if(!n||!o)return;let r=Number(o.dataset.resourceId);r&&(n.hasAttribute("data-open-active-detail")?(h(),V(r)):(x(),Y(r,o.dataset.tableName??"Mesa")))})}export{$t as initTables};
//# sourceMappingURL=tables-EICS6XTN.js.map
