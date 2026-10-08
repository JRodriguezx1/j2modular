async function x(e){let t=await fetch(`/restaurant/api/occupations/active?resourceId=${e}`,{headers:{Accept:"application/json"}}),n=await t.json();if(!t.ok||!n.success||!n.occupation)throw new Error(n.message??"No fue posible consultar la atenci\xF3n.");return n.occupation}var i,o,l,f;function E(){o.classList.remove("hidden"),requestAnimationFrame(()=>{i.classList.remove("translate-x-full")}),i.setAttribute("aria-hidden","false")}function u(){i.classList.add("translate-x-full"),i.setAttribute("aria-hidden","true"),window.setTimeout(()=>{o.classList.add("hidden")},300)}function y(){l.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function m(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}function L(e){let t=e.clientName?m(e.clientName):"Sin cliente asociado",n=e.numberOfPeople!==null?String(e.numberOfPeople):"\u2014",r=e.reservationId!==null?`#${e.reservationId}`:"Atenci\xF3n directa",s=e.observations?m(e.observations):"Sin observaciones";l.innerHTML=`
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
                ${a("Cliente",t)}
                ${a("Personas",n)}
                ${a("Origen",r)}
                ${a("Inicio",g(e.startDate))}
                ${a("Fin estimado",e.estimatedEndDate?g(e.estimatedEndDate):"\u2014")}
                ${a("Observaciones",s)}
            </div>
        </div>`}function a(e,t){return`
        <div class="border-b border-slate-100 pb-4">
            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-400">
                ${e}
            </p>
            <p class="text-sm font-medium text-slate-800">
                ${t}
            </p>
        </div>`}function g(e){let t=new Date(e.replace(" ","T"));return Number.isNaN(t.getTime())?e:new Intl.DateTimeFormat("es",{dateStyle:"medium",timeStyle:"short"}).format(t)}function b(){let e=document.getElementById("activeOccupationDrawer"),t=document.getElementById("activeOccupationOverlay"),n=document.getElementById("activeOccupationContent"),r=document.getElementById("activeOccupationActions");!e||!t||!n||!r||(i=e,o=t,l=n,f=r,document.addEventListener("click",async s=>{let d=s.target;console.log(d);let p=d.closest("[data-active-occupation]");if(!p)return;let v=Number(p.dataset.resourceId);if(v){E(),y(),f.classList.add("hidden");try{let c=await x(v);L(c)}catch(c){l.innerHTML=`
                <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                    ${m(c instanceof Error?c.message:"Ocurri\xF3 un error inesperado.")}
                </div>`}}}),i.addEventListener("click",s=>{s.target.closest("[data-close-active-occupation]")&&u()}),o.addEventListener("click",u),document.addEventListener("keydown",s=>{s.key==="Escape"&&i.getAttribute("aria-hidden")==="false"&&u()}))}function w(){console.log("Restaurant \u2192 Tables cargado x"),b()}export{w as initTables};
//# sourceMappingURL=tables-J3VX4FIY.js.map
