async function A(e){let t=await fetch(`/restaurant/api/occupations/active?resourceId=${e}`,{headers:{Accept:"application/json"}}),n=await t.json();if(!t.ok||!n.success||!n.occupation)throw new Error(n.message??"No fue posible consultar la atenci\xF3n.");return n.occupation}async function y(e){let t=`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(e)}`,n=await fetch(t,{method:"GET",headers:{Accept:"application/json"}}),s=await n.json();if(!n.ok||!s.success||!s.data)throw new Error(s.message??"No fue posible consultar las cuentas.");return s.data}var c,o,u,v;function w(){o.classList.remove("hidden"),requestAnimationFrame(()=>{c.classList.remove("translate-x-full")}),c.setAttribute("aria-hidden","false")}function l(){c.classList.add("translate-x-full"),c.setAttribute("aria-hidden","true"),window.setTimeout(()=>{o.classList.add("hidden")},300)}function I(){u.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function d(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}function L(e){let t=e.clientName?d(e.clientName):"Sin cliente asociado",n=e.numberOfPeople!==null?String(e.numberOfPeople):"\u2014",s=e.reservationId!==null?`#${e.reservationId}`:"Atenci\xF3n directa",a=e.observations?d(e.observations):"Sin observaciones";u.innerHTML=`
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
                ${i("Inicio",f(e.startDate))}
                ${i("Fin estimado",e.estimatedEndDate?f(e.estimatedEndDate):"\u2014")}
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
        </div>`}function f(e){let t=new Date(e.replace(" ","T"));return Number.isNaN(t.getTime())?e:new Intl.DateTimeFormat("es",{dateStyle:"medium",timeStyle:"short"}).format(t)}var g=0;function b(){let e=document.getElementById("activeOccupationDrawer"),t=document.getElementById("activeOccupationOverlay"),n=document.getElementById("activeOccupationContent"),s=document.getElementById("activeOccupationActions");!e||!t||!n||!s||(c=e,o=t,u=n,v=s,document.addEventListener("click",async a=>{let m=a.target.closest("[data-active-occupation]");if(!m)return;let p=Number(m.dataset.resourceId);if(!p)return;let O=++g;w(),I(),v.classList.add("hidden");try{let r=await A(p);if(O!==g)return;L(r);try{let x=await y(r.occupationId)}catch{}}catch(r){u.innerHTML=`
                <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                    ${d(r instanceof Error?r.message:"Ocurri\xF3 un error inesperado.")}
                </div>`}}),c.addEventListener("click",a=>{a.target.closest("[data-close-active-occupation]")&&l()}),o.addEventListener("click",l),document.addEventListener("keydown",a=>{a.key==="Escape"&&c.getAttribute("aria-hidden")==="false"&&l()}))}function D(){console.log("Restaurant \u2192 Tables cargado x"),b()}export{D as initTables};
//# sourceMappingURL=tables-FDOLNNHZ.js.map
