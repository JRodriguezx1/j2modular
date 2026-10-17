async function b(e){let t=await fetch(`/restaurant/api/occupations/active?resourceId=${e}`,{headers:{Accept:"application/json"}}),n=await t.json();if(!t.ok||!n.success||!n.occupation)throw new Error(n.message??"No fue posible consultar la atenci\xF3n.");return n.occupation}async function x(e){let t=`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(e)}`,n=await fetch(t,{method:"GET",headers:{Accept:"application/json"}}),s=await n.json();if(!n.ok||!s.success||!s.data)throw new Error(s.message??"No fue posible consultar las cuentas.");return s.data}var c,d,r,f;function E(){d.classList.remove("hidden"),requestAnimationFrame(()=>{c.classList.remove("translate-x-full")}),c.setAttribute("aria-hidden","false")}function m(){c.classList.add("translate-x-full"),c.setAttribute("aria-hidden","true"),window.setTimeout(()=>{d.classList.add("hidden")},300)}function O(){r.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function o(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}function A(e){let t=e.clientName?o(e.clientName):"Sin cliente asociado",n=e.numberOfPeople!==null?String(e.numberOfPeople):"\u2014",s=e.reservationId!==null?`#${e.reservationId}`:"Atenci\xF3n directa",a=e.observations?o(e.observations):"Sin observaciones";r.innerHTML=`
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
                ${i("Cliente",t)}
                ${i("Personas",n)}
                ${i("Origen",s)}
                ${i("Inicio",v(e.startDate))}
                ${i("Fin estimado",e.estimatedEndDate?v(e.estimatedEndDate):"\u2014")}
                ${i("Observaciones",a)}
            </div>
        </div>`}function i(e,t){return`
        <div class="border-b border-slate-100 pb-4">
            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-400">
                ${e}
            </p>
            <p class="text-sm font-medium text-slate-800">
                ${t}
            </p>
        </div>`}function v(e){let t=new Date(e.replace(" ","T"));return Number.isNaN(t.getTime())?e:new Intl.DateTimeFormat("es",{dateStyle:"medium",timeStyle:"short"}).format(t)}function y(e){let t=document.createElement("section");t.className="mt-8 border-t border-slate-200 pt-5",t.innerHTML=`
        <h3 class="mb-3 font-semibold text-slate-900">Cuentas</h3>
        ${e.length===0?'<p class="text-sm text-slate-500">Esta atenci\xF3n no tiene cuentas.</p>':e.map(n=>`
                <article class="mb-2 rounded-xl border border-slate-200 p-3">
                    <p class="font-medium text-slate-900">${o(n.name)}</p>
                    <p class="text-xs text-slate-500">
                        ${o(n.status)} \xB7 ${n.invoiceId===null?"Sin orden todav\xEDa":`Orden #${n.invoiceId}`}
                    </p>
                </article>
            `).join("")}`,r.appendChild(t)}function L(e){let t=document.createElement("p");t.className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700",t.textContent=e,r.appendChild(t)}var l=0;function g(){let e=document.getElementById("activeOccupationDrawer"),t=document.getElementById("activeOccupationOverlay"),n=document.getElementById("activeOccupationContent"),s=document.getElementById("activeOccupationActions");!e||!t||!n||!s||(c=e,d=t,r=n,f=s,document.addEventListener("click",async a=>{let u=a.target,p=u.closest("[data-active-occupation]");p&&(u.classList.contains("panel")||h(p))}),c.addEventListener("click",a=>{a.target.closest("[data-close-active-occupation]")&&m()}),d.addEventListener("click",m),document.addEventListener("keydown",a=>{a.key==="Escape"&&c.getAttribute("aria-hidden")==="false"&&m()}))}async function h(e){let t=Number(e.dataset.resourceId);if(!t)return;let n=++l;E(),O(),f.classList.add("hidden");try{let s=await b(t);if(n!==l)return;A(s);try{let a=await x(s.occupationId);if(n!==l)return;y(a.accounts)}catch(a){if(n!==l)return;L(a instanceof Error?a.message:"No fue posible consultar las cuentas.")}}catch(s){r.innerHTML=`
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                ${o(s instanceof Error?s.message:"Ocurri\xF3 un error inesperado.")}
            </div>`}}function T(){console.log("Restaurant \u2192 Tables cargado x"),g()}export{T as initTables};
//# sourceMappingURL=tables-6CCMRNTY.js.map
