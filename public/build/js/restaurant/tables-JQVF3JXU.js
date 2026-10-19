async function A(e){let t=`/restaurant/api/occupations/active?resourceId=${encodeURIComponent(e)}`,n=await fetch(t,{headers:{Accept:"application/json"}}),o=await n.json();if(!n.ok||!o.success||!o.occupation)throw new Error(o.message??"No fue posible consultar la atenci\xF3n.");return o.occupation}async function w(e){let t=await fetch(`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(e)}`,{headers:{Accept:"application/json"}}),n=await t.json();if(!t.ok||!n.success||!n.data)throw new Error(n.message??"No fue posible consultar las cuentas.");return n.data}var d,O,b,q,j;function X(){window.clearTimeout(j),O.classList.remove("hidden"),d.inert=!1,requestAnimationFrame(()=>{d.classList.remove("translate-x-full")}),d.setAttribute("aria-hidden","false")}function h(){d.getAttribute("aria-hidden")!=="true"&&(++f,d.classList.add("translate-x-full"),d.setAttribute("aria-hidden","true"),d.inert=!0,j=window.setTimeout(()=>{O.classList.add("hidden")},300))}function Y(){b.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function E(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}function Z(e){let t=e.clientName?E(e.clientName):"Sin cliente asociado",n=e.numberOfPeople!==null?String(e.numberOfPeople):"\u2014",o=e.reservationId!==null?`#${e.reservationId}`:"Atenci\xF3n directa",a=e.observations?E(e.observations):"Sin observaciones";b.innerHTML=`
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
                ${g("Observaciones",a)}
            </div>
        </div>`}function g(e,t){return`
        <div class="border-b border-slate-100 pb-4">
            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-400">
                ${e}
            </p>
            <p class="text-sm font-medium text-slate-800">
                ${t}
            </p>
        </div>`}function D(e){let t=new Date(e.replace(" ","T"));return Number.isNaN(t.getTime())?e:new Intl.DateTimeFormat("es",{dateStyle:"medium",timeStyle:"short"}).format(t)}function ee(e){let t=document.createElement("section");t.className="mt-8 border-t border-slate-200 pt-5",t.innerHTML=`
        <h3 class="mb-3 font-semibold text-slate-900">Cuentas</h3>
        ${e.length===0?'<p class="text-sm text-slate-500">Esta atenci\xF3n no tiene cuentas.</p>':e.map(n=>`
                <article class="mb-2 rounded-xl border border-slate-200 p-3">
                    <p class="font-medium text-slate-900">${E(n.name)}</p>
                    <p class="text-xs text-slate-500">
                        ${E(n.status)} \xB7 ${n.invoiceId===null?"Sin orden todav\xEDa":`Orden #${n.invoiceId}`}
                    </p>
                </article>
            `).join("")}`,b.appendChild(t)}function te(e){let t=document.createElement("p");t.className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700",t.textContent=e,b.appendChild(t)}var f=0;function R(){let e=document.getElementById("activeOccupationDrawer"),t=document.getElementById("activeOccupationOverlay"),n=document.getElementById("activeOccupationContent"),o=document.getElementById("activeOccupationActions");!e||!t||!n||!o||(d=e,O=t,b=n,q=o,d.inert=!0,d.addEventListener("click",a=>{a.target.closest("[data-close-active-occupation]")&&h()}),O.addEventListener("click",h),document.addEventListener("keydown",a=>{a.key==="Escape"&&d.getAttribute("aria-hidden")==="false"&&h()}))}async function B(e){if(!e)return;let t=++f;X(),Y(),q.classList.add("hidden");try{let n=await A(e);if(t!==f)return;Z(n);try{let o=await w(n.occupationId);if(t!==f)return;ee(o.accounts)}catch(o){if(t!==f)return;te(o instanceof Error?o.message:"No fue posible consultar las cuentas.")}}catch(n){if(t!==f)return;b.innerHTML=`
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                ${E(n instanceof Error?n.message:"Ocurri\xF3 un error inesperado.")}
            </div>`}}function u(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}async function W(){let t=await fetch("/admin/api/inventory/getAllcategorias");if(!t.ok)throw new Error("No fue posible consultar las categorias.");let n=await t.json();if(!Array.isArray(n))throw new Error("La respuesta de categor\xEDas no tiene el formato esperado.");return n}async function _(e=1,t=null){let n=new URLSearchParams({page:String(e)});t!=null&&n.set("categoriaId",String(t));let o=`/admin/api/inventory/listSimpleProducts?${n.toString()}`,a=await fetch(o),r=await a.json();if(!a.ok||!r.success||!r.data||!Array.isArray(r.data.products))throw new Error("No fue posible consultar las productos.");return r.data}var c,C,ne,V,y=0,M=0,F,m=[],v=null,k=0,U=null,H=!1,S=!1,T=document.querySelector("#atencionMesaText");function oe(){window.clearTimeout(F),C.classList.remove("hidden"),c.inert=!1,c.setAttribute("aria-hidden","false"),requestAnimationFrame(()=>c.classList.remove("translate-x-full"))}function L(){if(c.getAttribute("aria-hidden")==="true")return;++y;let e=document.activeElement;e instanceof HTMLElement&&c.contains(e)&&e.blur(),c.classList.add("translate-x-full"),c.inert=!0,c.setAttribute("aria-hidden","true"),F=window.setTimeout(()=>C.classList.add("hidden"),300)}function ae(e){let t=m.find(o=>o.id===v)??null,n=m.map(o=>`
        <option value="${o.id}" ${o.id===v?"selected":""}>
            ${u(o.name)}
        </option>
    `).join("")}async function z(e,t){if(!e||!c)return;let n=++y;m=[],v=null,V.textContent=t,oe();try{let o=await A(e);if(n!==y)return;let a=await w(o.occupationId);if(n!==y)return;m=a.accounts,v=m.find(r=>r.status==="abierta")?.id??m[0]?.id??null,T.classList.add("bg-emerald-50","text-emerald-700"),T.textContent="En atenci\xF3n",ae(o)}catch(o){if(n!==y)return;let a=o instanceof Error?o.message:"No fue posible consultar la mesa.";T.classList.add("bg-red-50","text-red-700"),T.textContent=`${u(a)}`}}function G(){let e=document.getElementById("orderWorkspaceDrawer"),t=document.getElementById("orderWorkspaceOverlay"),n=document.getElementById("orderWorkspaceContent"),o=document.getElementById("orderWorkspaceTitle"),a=document.querySelector("#catalogLoadMore");!e||!t||!n||!o||(re(),c=e,C=t,ne=n,V=o,c.inert=!0,c.addEventListener("click",r=>{r.target.closest("[data-close-order-workspace]")&&L()}),c.addEventListener("change",r=>{let s=r.target;if(!s.matches("[data-order-workspace-account]"))return;v=Number(s.value);let i=m.find(l=>l.id===v),x=c.querySelector("[data-order-workspace-account-info]");i&&x&&(x.textContent=`Estado: ${i.status} \xB7 ${i.invoiceId===null?"Sin orden todav\xEDa":`Orden #${i.invoiceId}`}`)}),C.addEventListener("click",L),document.addEventListener("keydown",r=>{r.key==="Escape"&&c.getAttribute("aria-hidden")==="false"&&L()}),a?.addEventListener("click",()=>{se()}),J())}async function re(){let e=await W(),t=document.querySelector("#divCategorias");t.innerHTML=`
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
                data-categoria-id="${u(n.id)}"
                aria-pressed="false"
                class="btncateogira shrink-0 rounded-lg border border-slate-200 bg-white px-3 py-2 font-medium text-slate-600 hover:bg-slate-50">
                ${u(n.nombre)}
            </button>
        `).join("")}
    `,t.addEventListener("click",n=>{let o=n.target.closest("button[data-categoria-id]");if(!o||!t.contains(o))return;let a=o.dataset.categoriaId,r=a==="all"?null:Number(a);r!==null&&(!Number.isInteger(r)||r<=0)||(t.querySelectorAll("button[data-categoria-id]").forEach(s=>{let i=s===o;s.setAttribute("aria-pressed",String(i)),s.classList.toggle("border-indigo-600",i),s.classList.toggle("bg-indigo-600",i),s.classList.toggle("text-white",i),s.classList.toggle("border-slate-200",!i),s.classList.toggle("bg-white",!i),s.classList.toggle("text-slate-600",!i),s.classList.toggle("hover:bg-slate-50",!i)}),J(r))})}async function se(){S||!H||await K(k+1,U,!1)}async function J(e=null){U=e,k=0,H=!1,++M,await K(1,e,!0)}async function K(e,t,n){let o=document.querySelector("#catalogoPaginador"),a=document.querySelector("#catalogProductGrid"),r=document.querySelector("#catalogProductCount"),s=document.querySelector("#catalogProductStatus"),i=document.querySelector("#catalogLoadMore");if(!a||!r||!s||!i)return;let x=++M;S=!0,i.disabled=!0,i.classList.add("hidden"),n?(a.replaceChildren(),r.textContent="Cargando...",s.textContent="Cargando productos..."):s.textContent="Cargando m\xE1s productos...";try{let l=await _(e,t);if(x!==M)return;r.textContent=`${l.products.length} en esta p\xE1gina`,s.textContent=l.products.length>0?`Mostrando p\xE1gina ${l.page}`:"No hay productos vendibles en esta sucursal.";let N=l.products.map(p=>{let $=u(p.nombre),I=p.foto&&p.foto!=="null"&&p.foto!=="undefined"?p.foto:null,Q=Number(p.precioVenta).toLocaleString();return`
                <article class="group overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-indigo-300 hover:shadow-md">
                    <div class="h-32 bg-slate-100 ${I!=null?"":"grid place-items-center"}">
                        ${I!=null?`<img src="/build/img/${u(I)}" alt="${$}" loading="lazy" class="h-full w-full object-cover">`:'<span class=" material-symbols-outlined text-5xl">restaurant</span>'}
                    </div>
                    <div class="p-3">
                        <h4 class="text-base font-semibold text-slate-900">${$}</h4>
                        <div class="mt-3 flex items-center justify-between gap-2">
                            <span class="text-lg font-bold text-slate-900">$${u(Q)}</span>
                            <button
                                type="button"
                                aria-label="Agregar ${$}"
                                class="flex size-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white"
                            >
                                <span class="material-symbols-outlined">add</span>
                            </button>
                        </div>
                    </div>
                </article>`}).join("");n?a.innerHTML=N:a.insertAdjacentHTML("beforeend",N),k=l.page,H=l.products.length>0&&l.products.length===24;let P=a.querySelectorAll("article").length;r.textContent=`${P} productos mostrados`,s.textContent=P>0?`Mostrando ${P} productos`:"No hay productos vendibles en esta sucursal.",i.classList.toggle("hidden",!H)}catch(l){r.textContent="Sin cargar",s.textContent=l instanceof Error?l.message:"No fue posible cargar los productos."}finally{x===M&&(S=!1,i.disabled=!1)}}function Ee(){R(),G(),document.addEventListener("click",e=>{let n=e.target.closest("[data-open-active-detail], [data-open-order-workspace]"),o=n?.closest("[data-table-id]");if(!n||!o)return;let a=Number(o.dataset.resourceId);a&&(n.hasAttribute("data-open-active-detail")?(L(),B(a)):(h(),z(a,o.dataset.tableName??"Mesa")))})}export{Ee as initTables};
//# sourceMappingURL=tables-JQVF3JXU.js.map
