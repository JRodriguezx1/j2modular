function l(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}async function A(e){let t=`/restaurant/api/occupations/active?resourceId=${encodeURIComponent(e)}`,n=await fetch(t,{headers:{Accept:"application/json"}}),o=await n.json();if(!n.ok||!o.success||!o.occupation)throw new Error(o.message??"No fue posible consultar la atenci\xF3n.");return o.occupation}async function O(e){let t=`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(e)}`,n=await fetch(t,{headers:{Accept:"application/json"}}),o=await n.json();if(!n.ok||!o.success||!o.data)throw new Error(o.message??"No fue posible consultar las cuentas.");return o.data}var u,T,v,j,R;function X(){window.clearTimeout(R),T.classList.remove("hidden"),u.inert=!1,requestAnimationFrame(()=>{u.classList.remove("translate-x-full")}),u.setAttribute("aria-hidden","false")}function E(){u.getAttribute("aria-hidden")!=="true"&&(++b,u.classList.add("translate-x-full"),u.setAttribute("aria-hidden","true"),u.inert=!0,R=window.setTimeout(()=>{T.classList.add("hidden")},300))}function Y(){v.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function Z(e){let t=e.clientName?l(e.clientName):"Sin cliente asociado",n=e.numberOfPeople!==null?String(e.numberOfPeople):"\u2014",o=e.reservationId!==null?`#${e.reservationId}`:"Atenci\xF3n directa",r=e.observations?l(e.observations):"Sin observaciones";v.innerHTML=`
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
                ${f("Cliente",t)}
                ${f("Personas",n)}
                ${f("Origen",o)}
                ${f("Inicio",q(e.startDate))}
                ${f("Fin estimado",e.estimatedEndDate?q(e.estimatedEndDate):"\u2014")}
                ${f("Observaciones",r)}
            </div>
        </div>`}function f(e,t){return`
        <div class="border-b border-slate-100 pb-4">
            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-400">
                ${e}
            </p>
            <p class="text-sm font-medium text-slate-800">
                ${t}
            </p>
        </div>`}function q(e){let t=new Date(e.replace(" ","T"));return Number.isNaN(t.getTime())?e:new Intl.DateTimeFormat("es",{dateStyle:"medium",timeStyle:"short"}).format(t)}function ee(e){let t=document.createElement("section");t.className="mt-8 border-t border-slate-200 pt-5",t.innerHTML=`
        <h3 class="mb-3 font-semibold text-slate-900">Cuentas</h3>
        ${e.length===0?'<p class="text-sm text-slate-500">Esta atenci\xF3n no tiene cuentas.</p>':e.map(n=>`
                <article class="mb-2 rounded-xl border border-slate-200 p-3">
                    <p class="font-medium text-slate-900">${l(n.name)}</p>
                    <p class="text-xs text-slate-500">
                        ${l(n.status)} \xB7 ${n.invoiceId===null?"Sin orden todav\xEDa":`Orden #${n.invoiceId}`}
                    </p>
                </article>
            `).join("")}`,v.appendChild(t)}function te(e){let t=document.createElement("p");t.className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700",t.textContent=e,v.appendChild(t)}var b=0;function B(){let e=document.getElementById("activeOccupationDrawer"),t=document.getElementById("activeOccupationOverlay"),n=document.getElementById("activeOccupationContent"),o=document.getElementById("activeOccupationActions");!e||!t||!n||!o||(u=e,T=t,v=n,j=o,u.inert=!0,u.addEventListener("click",r=>{r.target.closest("[data-close-active-occupation]")&&E()}),T.addEventListener("click",E),document.addEventListener("keydown",r=>{r.key==="Escape"&&u.getAttribute("aria-hidden")==="false"&&E()}))}async function W(e){if(!e)return;let t=++b;X(),Y(),j.classList.add("hidden");try{let n=await A(e);if(t!==b)return;Z(n);try{let o=await O(n.occupationId);if(t!==b)return;ee(o.accounts)}catch(o){if(t!==b)return;te(o instanceof Error?o.message:"No fue posible consultar las cuentas.")}}catch(n){if(t!==b)return;v.innerHTML=`
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                ${l(n instanceof Error?n.message:"Ocurri\xF3 un error inesperado.")}
            </div>`}}async function _(){let t=await fetch("/admin/api/inventory/getAllcategorias");if(!t.ok)throw new Error("No fue posible consultar las categorias.");let n=await t.json();if(!Array.isArray(n))throw new Error("La respuesta de categor\xEDas no tiene el formato esperado.");return n}async function V(e=1,t=null,n=""){let o=new URLSearchParams({page:String(e)});t!=null&&o.set("categoriaId",String(t)),n&&o.set("search",n);let r=`/admin/api/inventory/listSimpleProducts?${o.toString()}`,s=await fetch(r),a=await s.json();if(!s.ok||!a.success||!a.data||!Array.isArray(a.data.products))throw new Error("No fue posible consultar las productos.");return a.data}var c,C,ne,U,L=0,x=0,G,p=[],h=null,D=0,N=null,I=!1,H=!1,$="",F,M=document.querySelector("#atencionMesaText");function oe(){window.clearTimeout(G),C.classList.remove("hidden"),c.inert=!1,c.setAttribute("aria-hidden","false"),requestAnimationFrame(()=>c.classList.remove("translate-x-full"))}function y(){if(c.getAttribute("aria-hidden")==="true")return;++L;let e=document.activeElement;e instanceof HTMLElement&&c.contains(e)&&e.blur(),c.classList.add("translate-x-full"),c.inert=!0,c.setAttribute("aria-hidden","true"),G=window.setTimeout(()=>C.classList.add("hidden"),300)}function ae(e){let t=p.find(o=>o.id===h)??null,n=p.map(o=>`
        <option value="${o.id}" ${o.id===h?"selected":""}>
            ${l(o.name)}
        </option>
    `).join("")}async function z(e,t){if(!e||!c)return;let n=++L;p=[],h=null,U.textContent=t,oe();try{let o=await A(e);if(n!==L)return;let r=await O(o.occupationId);if(n!==L)return;p=r.accounts,h=p.find(s=>s.status==="abierta")?.id??p[0]?.id??null,M.classList.add("bg-emerald-50","text-emerald-700"),M.textContent="En atenci\xF3n",ae(o)}catch(o){if(n!==L)return;let r=o instanceof Error?o.message:"No fue posible consultar la mesa.";M.classList.add("bg-red-50","text-red-700"),M.textContent=`${l(r)}`}}function J(){let e=document.getElementById("orderWorkspaceDrawer"),t=document.getElementById("orderWorkspaceOverlay"),n=document.getElementById("orderWorkspaceContent"),o=document.getElementById("orderWorkspaceTitle"),r=document.querySelector("#catalogLoadMore");if(!e||!t||!n||!o)return;re(),c=e,C=t,ne=n,U=o,c.inert=!0,c.addEventListener("click",a=>{a.target.closest("[data-close-order-workspace]")&&y()}),c.addEventListener("change",a=>{let i=a.target;if(!i.matches("[data-order-workspace-account]"))return;h=Number(i.value);let m=p.find(w=>w.id===h),d=c.querySelector("[data-order-workspace-account-info]");m&&d&&(d.textContent=`Estado: ${m.status} \xB7 ${m.invoiceId===null?"Sin orden todav\xEDa":`Orden #${m.invoiceId}`}`)}),C.addEventListener("click",y),document.addEventListener("keydown",a=>{a.key==="Escape"&&c.getAttribute("aria-hidden")==="false"&&y()});let s=document.querySelector("#searchItem");s?.addEventListener("input",()=>{let a=s.value.trim();a!==$&&($=a,window.clearTimeout(F),++x,H=!0,r&&(r.disabled=!0),F=window.setTimeout(()=>{k(N)},350))}),r?.addEventListener("click",()=>{se()}),k()}async function re(){let e=await _(),t=document.querySelector("#divCategorias");t.innerHTML=`
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
    `,t.addEventListener("click",n=>{let o=n.target.closest("button[data-categoria-id]");if(!o||!t.contains(o))return;let r=o.dataset.categoriaId,s=r==="all"?null:Number(r);s!==null&&(!Number.isInteger(s)||s<=0)||(t.querySelectorAll("button[data-categoria-id]").forEach(a=>{let i=a===o;a.setAttribute("aria-pressed",String(i)),a.classList.toggle("border-indigo-600",i),a.classList.toggle("bg-indigo-600",i),a.classList.toggle("text-white",i),a.classList.toggle("border-slate-200",!i),a.classList.toggle("bg-white",!i),a.classList.toggle("text-slate-600",!i),a.classList.toggle("hover:bg-slate-50",!i)}),k(s))})}async function se(){H||!I||await K(D+1,N,!1)}async function k(e=null){N=e,D=0,I=!1,++x,await K(1,e,!0)}async function K(e,t,n){let o=document.querySelector("#catalogoPaginador"),r=document.querySelector("#catalogProductGrid"),s=document.querySelector("#catalogProductCount"),a=document.querySelector("#catalogProductStatus"),i=document.querySelector("#catalogLoadMore");if(!r||!s||!a||!i)return;let m=++x;H=!0,i.disabled=!0,i.classList.add("hidden"),n?(r.replaceChildren(),s.textContent="Cargando...",a.textContent="Cargando productos..."):a.textContent="Cargando m\xE1s productos...";try{let d=await V(e,t,$);if(m!==x)return;let w=d.products.map(g=>{let P=l(g.nombre),S=g.foto&&g.foto!=="null"&&g.foto!=="undefined"?g.foto:null,Q=Number(g.precioVenta).toLocaleString();return`
                <article class="group overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-indigo-300 hover:shadow-md">
                    <div class="h-32 bg-slate-100 ${S!=null?"":"grid place-items-center"}">
                        ${S!=null?`<img src="/build/img/${l(S)}" alt="${P}" loading="lazy" class="h-full w-full object-cover">`:'<span class=" material-symbols-outlined text-5xl">restaurant</span>'}
                    </div>
                    <div class="p-3">
                        <h4 class="text-base font-semibold text-slate-900">${P}</h4>
                        <div class="mt-3 flex items-center justify-between gap-2">
                            <span class="text-lg font-bold text-slate-900">$${l(Q)}</span>
                            <button
                                type="button"
                                aria-label="Agregar ${P}"
                                class="flex size-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white"
                            >
                                <span class="material-symbols-outlined">add</span>
                            </button>
                        </div>
                    </div>
                </article>`}).join("");n?(r.innerHTML=w,o?.scrollTo({top:0})):r.insertAdjacentHTML("beforeend",w),D=d.page,I=d.hasMore,s.textContent=`${d.products.length} en esta p\xE1gina`,a.textContent=d.products.length>0?`Mostrando p\xE1gina ${d.page}`:"No hay productos vendibles en esta sucursal.",i.classList.toggle("hidden",!I)}catch(d){if(m!==x)return;n&&(s.textContent="Sin cargar"),a.textContent=d instanceof Error?d.message:"No fue posible cargar los productos.",n||i.classList.remove("hidden")}finally{m===x&&(H=!1,i.disabled=!1)}}function Le(){B(),J(),document.addEventListener("click",e=>{let n=e.target.closest("[data-open-active-detail], [data-open-order-workspace]"),o=n?.closest("[data-table-id]");if(!n||!o)return;let r=Number(o.dataset.resourceId);r&&(n.hasAttribute("data-open-active-detail")?(y(),W(r)):(E(),z(r,o.dataset.tableName??"Mesa")))})}export{Le as initTables};
//# sourceMappingURL=tables-HGCGDRCE.js.map
