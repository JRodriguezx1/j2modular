function l(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}async function y(e){let t=`/restaurant/api/occupations/active?resourceId=${encodeURIComponent(e)}`,n=await fetch(t,{headers:{Accept:"application/json"}}),o=await n.json();if(!n.ok||!o.success||!o.occupation)throw new Error(o.message??"No fue posible consultar la atenci\xF3n.");return o.occupation}async function w(e){let t=`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(e)}`,n=await fetch(t,{headers:{Accept:"application/json"}}),o=await n.json();if(!n.ok||!o.success||!o.data)throw new Error(o.message??"No fue posible consultar las cuentas.");return o.data}var u,A,b,N,q;function K(){window.clearTimeout(q),A.classList.remove("hidden"),u.inert=!1,requestAnimationFrame(()=>{u.classList.remove("translate-x-full")}),u.setAttribute("aria-hidden","false")}function x(){u.getAttribute("aria-hidden")!=="true"&&(++f,u.classList.add("translate-x-full"),u.setAttribute("aria-hidden","true"),u.inert=!0,q=window.setTimeout(()=>{A.classList.add("hidden")},300))}function Q(){b.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function X(e){let t=e.clientName?l(e.clientName):"Sin cliente asociado",n=e.numberOfPeople!==null?String(e.numberOfPeople):"\u2014",o=e.reservationId!==null?`#${e.reservationId}`:"Atenci\xF3n directa",r=e.observations?l(e.observations):"Sin observaciones";b.innerHTML=`
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
                ${g("Cliente",t)}
                ${g("Personas",n)}
                ${g("Origen",o)}
                ${g("Inicio",D(e.startDate))}
                ${g("Fin estimado",e.estimatedEndDate?D(e.estimatedEndDate):"\u2014")}
                ${g("Observaciones",r)}
            </div>
        </div>`}function g(e,t){return`
        <div class="border-b border-slate-100 pb-4">
            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-400">
                ${e}
            </p>
            <p class="text-sm font-medium text-slate-800">
                ${t}
            </p>
        </div>`}function D(e){let t=new Date(e.replace(" ","T"));return Number.isNaN(t.getTime())?e:new Intl.DateTimeFormat("es",{dateStyle:"medium",timeStyle:"short"}).format(t)}function Y(e){let t=document.createElement("section");t.className="mt-8 border-t border-slate-200 pt-5",t.innerHTML=`
        <h3 class="mb-3 font-semibold text-slate-900">Cuentas</h3>
        ${e.length===0?'<p class="text-sm text-slate-500">Esta atenci\xF3n no tiene cuentas.</p>':e.map(n=>`
                <article class="mb-2 rounded-xl border border-slate-200 p-3">
                    <p class="font-medium text-slate-900">${l(n.name)}</p>
                    <p class="text-xs text-slate-500">
                        ${l(n.status)} \xB7 ${n.invoiceId===null?"Sin orden todav\xEDa":`Orden #${n.invoiceId}`}
                    </p>
                </article>
            `).join("")}`,b.appendChild(t)}function Z(e){let t=document.createElement("p");t.className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700",t.textContent=e,b.appendChild(t)}var f=0;function R(){let e=document.getElementById("activeOccupationDrawer"),t=document.getElementById("activeOccupationOverlay"),n=document.getElementById("activeOccupationContent"),o=document.getElementById("activeOccupationActions");!e||!t||!n||!o||(u=e,A=t,b=n,N=o,u.inert=!0,u.addEventListener("click",r=>{r.target.closest("[data-close-active-occupation]")&&x()}),A.addEventListener("click",x),document.addEventListener("keydown",r=>{r.key==="Escape"&&u.getAttribute("aria-hidden")==="false"&&x()}))}async function j(e){if(!e)return;let t=++f;K(),Q(),N.classList.add("hidden");try{let n=await y(e);if(t!==f)return;X(n);try{let o=await w(n.occupationId);if(t!==f)return;Y(o.accounts)}catch(o){if(t!==f)return;Z(o instanceof Error?o.message:"No fue posible consultar las cuentas.")}}catch(n){if(t!==f)return;b.innerHTML=`
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                ${l(n instanceof Error?n.message:"Ocurri\xF3 un error inesperado.")}
            </div>`}}async function B(){let t=await fetch("/admin/api/inventory/getAllcategorias");if(!t.ok)throw new Error("No fue posible consultar las categorias.");let n=await t.json();if(!Array.isArray(n))throw new Error("La respuesta de categor\xEDas no tiene el formato esperado.");return n}async function W(e=1,t=null,n=""){let o=new URLSearchParams({page:String(e)});t!=null&&o.set("categoriaId",String(t)),n&&o.set("search",n);let r=`/admin/api/inventory/listSimpleProducts?${o.toString()}`,s=await fetch(r),a=await s.json();if(!s.ok||!a.success||!a.data||!Array.isArray(a.data.products))throw new Error("No fue posible consultar las productos.");return a.data}var c,T,V,h=0,v=0,F,k=0,$=null,M=!1,I=!1,H="",_,O=document.querySelector("#atencionMesaText");function ee(){window.clearTimeout(F),T.classList.remove("hidden"),c.inert=!1,c.setAttribute("aria-hidden","false"),requestAnimationFrame(()=>c.classList.remove("translate-x-full"))}function E(){if(c.getAttribute("aria-hidden")==="true")return;++h;let e=document.activeElement;e instanceof HTMLElement&&c.contains(e)&&e.blur(),c.classList.add("translate-x-full"),c.inert=!0,c.setAttribute("aria-hidden","true"),F=window.setTimeout(()=>T.classList.add("hidden"),300)}async function U(e,t){if(!e||!c)return;let n=++h;accounts=[],selectedAccountId=null,V.textContent=t,ee();try{let o=await y(e);if(n!==h)return;let r=await w(o.occupationId);if(n!==h)return;accounts=r.accounts,selectedAccountId=accounts.find(s=>s.status==="abierta")?.id??accounts[0]?.id??null,O.classList.add("bg-emerald-50","text-emerald-700"),O.textContent="En atenci\xF3n",renderWorkspace(o)}catch(o){if(n!==h)return;let r=o instanceof Error?o.message:"No fue posible consultar la mesa.";O.classList.add("bg-red-50","text-red-700"),O.textContent=`${l(r)}`}}function G(){let e=document.getElementById("orderWorkspaceDrawer"),t=document.getElementById("orderWorkspaceOverlay"),n=document.getElementById("orderWorkspaceContent"),o=document.getElementById("orderWorkspaceTitle"),r=document.querySelector("#catalogLoadMore");if(!e||!t||!n||!o)return;te(),c=e,T=t,content=n,V=o,c.inert=!0,c.addEventListener("click",a=>{a.target.closest("[data-close-order-workspace]")&&E()}),c.addEventListener("change",a=>{let i=a.target;if(!i.matches("[data-order-workspace-account]"))return;selectedAccountId=Number(i.value);let m=accounts.find(L=>L.id===selectedAccountId),d=c.querySelector("[data-order-workspace-account-info]");m&&d&&(d.textContent=`Estado: ${m.status} \xB7 ${m.invoiceId===null?"Sin orden todav\xEDa":`Orden #${m.invoiceId}`}`)}),T.addEventListener("click",E),document.addEventListener("keydown",a=>{a.key==="Escape"&&c.getAttribute("aria-hidden")==="false"&&E()});let s=document.querySelector("#searchItem");s?.addEventListener("input",()=>{let a=s.value.trim();a!==H&&(H=a,window.clearTimeout(_),++v,I=!0,r&&(r.disabled=!0),_=window.setTimeout(()=>{S($)},350))}),r?.addEventListener("click",()=>{ne()}),S()}async function te(){let e=await B(),t=document.querySelector("#divCategorias");t.innerHTML=`
        <button
            type="button"
            data-categoria-id="all"
            aria-pressed="true"
            class="btncateogira shrink-0 rounded-lg border border-indigo-600 bg-indigo-600 px-3 py-2 font-medium text-white">
            Todas
        </button>
        ${e.map(n=>`
            <button
                type="button"
                data-categoria-id="${l(n.id)}"
                aria-pressed="false"
                class="btncateogira shrink-0 rounded-lg border border-slate-200 bg-white px-3 py-2 font-medium text-slate-600 hover:bg-slate-50">
                ${l(n.nombre)}
            </button>
        `).join("")}
    `,t.addEventListener("click",n=>{let o=n.target.closest("button[data-categoria-id]");if(!o||!t.contains(o))return;let r=o.dataset.categoriaId,s=r==="all"?null:Number(r);s!==null&&(!Number.isInteger(s)||s<=0)||(t.querySelectorAll("button[data-categoria-id]").forEach(a=>{let i=a===o;a.setAttribute("aria-pressed",String(i)),a.classList.toggle("border-indigo-600",i),a.classList.toggle("bg-indigo-600",i),a.classList.toggle("text-white",i),a.classList.toggle("border-slate-200",!i),a.classList.toggle("bg-white",!i),a.classList.toggle("text-slate-600",!i),a.classList.toggle("hover:bg-slate-50",!i)}),S(s))})}async function ne(){I||!M||await z(k+1,$,!1)}async function S(e=null){$=e,k=0,M=!1,++v,await z(1,e,!0)}async function z(e,t,n){let o=document.querySelector("#catalogoPaginador"),r=document.querySelector("#catalogProductGrid"),s=document.querySelector("#catalogProductCount"),a=document.querySelector("#catalogProductStatus"),i=document.querySelector("#catalogLoadMore");if(!r||!s||!a||!i)return;let m=++v;I=!0,i.disabled=!0,i.classList.add("hidden"),n?(r.replaceChildren(),s.textContent="Cargando...",a.textContent="Cargando productos..."):a.textContent="Cargando m\xE1s productos...";try{let d=await W(e,t,H);if(m!==v)return;let L=d.products.map(p=>{let C=l(p.nombre),P=p.foto&&p.foto!=="null"&&p.foto!=="undefined"?p.foto:null,J=Number(p.precioVenta).toLocaleString();return`
                <article class="group overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-indigo-300 hover:shadow-md">
                    <div class="h-32 bg-slate-100 ${P!=null?"":"grid place-items-center"}">
                        ${P!=null?`<img src="/build/img/${l(P)}" alt="${C}" loading="lazy" class="h-full w-full object-cover">`:'<span class=" material-symbols-outlined text-5xl">restaurant</span>'}
                    </div>
                    <div class="p-3">
                        <h4 class="text-base font-semibold text-slate-900">${C}</h4>
                        <div class="mt-3 flex items-center justify-between gap-2">
                            <span class="text-lg font-bold text-slate-900">$${l(J)}</span>
                            <button
                                type="button"
                                aria-label="Agregar ${C}"
                                class="flex size-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white"
                            >
                                <span class="material-symbols-outlined">add</span>
                            </button>
                        </div>
                    </div>
                </article>`}).join("");n?(r.innerHTML=L,o?.scrollTo({top:0})):r.insertAdjacentHTML("beforeend",L),k=d.page,M=d.hasMore,s.textContent=`${d.products.length} en esta p\xE1gina`,a.textContent=d.products.length>0?`Mostrando p\xE1gina ${d.page}`:"No hay productos vendibles en esta sucursal.",i.classList.toggle("hidden",!M)}catch(d){if(m!==v)return;n&&(s.textContent="Sin cargar"),a.textContent=d instanceof Error?d.message:"No fue posible cargar los productos.",n||i.classList.remove("hidden")}finally{m===v&&(I=!1,i.disabled=!1)}}function ve(){R(),G(),document.addEventListener("click",e=>{let n=e.target.closest("[data-open-active-detail], [data-open-order-workspace]"),o=n?.closest("[data-table-id]");if(!n||!o)return;let r=Number(o.dataset.resourceId);r&&(n.hasAttribute("data-open-active-detail")?(E(),j(r)):(x(),U(r,o.dataset.tableName??"Mesa")))})}export{ve as initTables};
//# sourceMappingURL=tables-YLXEISIJ.js.map
