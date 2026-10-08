var A=null;async function k(r){A?.abort(),A=new AbortController;let s=new FormData;s.append("startDate",r.startDate),s.append("endDate",r.endDate),s.append("numberOfPeople",String(r.numberOfPeople));let c=await fetch("/restaurant/api/reservations/available-tables",{method:"POST",body:s,signal:A.signal}),u=await c.json();if(!c.ok)throw new Error(u.message??"No fue posible consultar disponibilidad.");return u.tables??[]}function j(r){let s=document.querySelector("#btnNewReservation"),c=document.querySelector("#btnCloseReservationDrawer"),u=document.querySelector("#btnCancelReservation"),v=document.querySelector("#reservationDrawer"),f=document.querySelector("#reservationDrawerOverlay"),h=document.querySelector("#reservationDate"),y=document.querySelector("#reservationStartTime"),b=document.querySelector("#reservationEndTime"),S=document.querySelector("#reservationPeople"),C=document.querySelector("#reservationPeopleValue"),H=document.querySelector("#btnIncreasePeople"),M=document.querySelector("#btnDecreasePeople"),x=document.querySelector("#availableTables"),i=document.querySelector("#availableTablesCounter"),p=document.querySelector("#reservationResourceId");if(!s||!v||!f||!h||!y||!b||!S||!C||!x||!i||!p)return;function L(){f?.classList.remove("hidden"),requestAnimationFrame(()=>{v?.classList.remove("translate-x-full")}),document.body.classList.add("overflow-hidden")}function g(){v?.classList.add("translate-x-full"),document.body.classList.remove("overflow-hidden"),window.setTimeout(()=>{f?.classList.add("hidden")},300)}let a=l=>{let m=Math.max(1,l);S.value=String(m),C.textContent=String(m),n()};function t(l,m){return`${l} ${m}:00`}let n=async()=>{let l=h?.value,m=y?.value,d=b?.value,T=Number(S?.value);if(p.value="",!l||!m||!d||T<1){e();return}o();try{let E=await k({startDate:t(l,m),endDate:t(l,d),numberOfPeople:T});N(E)}catch(E){if(E instanceof DOMException&&E.name==="AbortError")return;w(E instanceof Error?E.message:"No fue posible consultar disponibilidad.")}},e=()=>{i.textContent="",x.innerHTML=`
            <div class="flex min-h-[70px] flex-col items-center justify-center text-center">
                <span class="material-symbols-outlined text-5xl text-slate-400">table_restaurant</span>
                <p class="mt-1 text-base text-slate-400">Selecciona fecha y horario.</p>
            </div>`},o=()=>{i.textContent="Consultando...",x.innerHTML=`
            <div class="flex min-h-[70px] items-center justify-center gap-2 text-xs text-slate-400">
                <span class="material-symbols-outlined animate-spin text-3xl">progress_activity</span>
                Buscando mesas disponibles...
            </div>`},w=l=>{i.textContent="",x.innerHTML="";let m=document.createElement("div");m.className="flex min-h-[70px] items-center justify-center gap-2 text-center text-base text-rose-500";let d=document.createElement("span");d.className="material-symbols-outlined text-[18px]",d.textContent="error";let T=document.createElement("span");T.textContent=l,m.append(d,T),x?.append(m)};function R(l){return l===null?"Sin zona":r.zones.find(d=>Number(d.id)===Number(l))?.name??"Sin zona"}let N=l=>{if(x.innerHTML="",p.value="",i.textContent=`${l.length} ${l.length===1?"disponible":"disponibles"}`,l.length===0){let d=document.createElement("div");d.className="flex min-h-[80px] flex-col items-center justify-center text-center",d.innerHTML=`
                <span class="material-symbols-outlined text-[22px] text-amber-400">event_busy</span>
                <p class="mt-1 text-xs font-medium text-slate-600">No hay mesas disponibles</p>
                <p class="mt-0.5 text-[11px] text-slate-400">Puedes guardar la reserva sin asignar mesa.</p>`,x.append(d);return}let m=new Map;for(let d of l){let T=R(d.zoneId),E=m.get(T)??[];E.push(d),m.set(T,E)}for(let[d,T]of m)_(d,T)};function _(l,m){let d=document.createElement("div");d.className="mb-4 last:mb-0";let T=document.createElement("p");T.className="mb-2 text-xl font-bold uppercase tracking-[0.12em] text-slate-500",T.textContent=l;let E=document.createElement("div");E.className="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-2";for(let I of m){let D=document.createElement("button");D.type="button",D.dataset.resourceId=String(I.resourceId),D.className="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-left transition hover:border-indigo-300 hover:bg-indigo-50/40";let P=document.createElement("span");P.className="block truncate text-lg font-bold text-slate-700",P.textContent=I.name;let B=document.createElement("span");B.className="mt-0.5 block text-xl text-slate-400",B.textContent=`${I.capacity} personas`,D.append(P,B),D.addEventListener("click",()=>{V(D,I.resourceId)}),E.append(D)}d.append(T,E),x?.append(d)}let V=(l,m)=>{x.querySelectorAll("button[data-resource-id]").forEach(d=>{d.classList.remove("border-indigo-500","bg-indigo-50","ring-1","ring-indigo-500")}),l.classList.add("border-indigo-500","bg-indigo-50","ring-1","ring-indigo-500"),p.value=String(m)};s.addEventListener("click",L),c?.addEventListener("click",g),u?.addEventListener("click",g),f.addEventListener("click",g),H?.addEventListener("click",()=>{a(Number(S.value)+1)}),M?.addEventListener("click",()=>{a(Number(S.value)-1)}),h.addEventListener("change",n),y.addEventListener("change",n),b.addEventListener("change",n),document.addEventListener("keydown",l=>{l.key==="Escape"&&!f.classList.contains("hidden")&&g()})}var O=null;async function G(r){O?.abort();let s=r.trim();if(s.length<2)return[];O=new AbortController;let c=new URLSearchParams({q:s}),u=await fetch(`/restaurant/api/customers/search?${c}`,{method:"GET",signal:O.signal,headers:{Accept:"application/json"}}),v=await u.json();if(!u.ok)throw new Error(v.message??"No fue posible buscar clientes.");return v.customers??[]}function $(){let r=document.querySelector("#customerSearchInput"),s=document.querySelector("#customerSearchResults"),c=document.querySelector("#customerSearchLoader"),u=document.querySelector("#customerSearch"),v=document.querySelector("#selectedCustomer"),f=document.querySelector("#reservationClientId"),h=document.querySelector("#selectedCustomerName"),y=document.querySelector("#selectedCustomerInfo"),b=document.querySelector("#selectedCustomerInitials"),S=document.querySelector("#btnChangeCustomer");if(!r||!s||!c||!u||!v||!f||!h||!y||!b)return;let C=null;r.addEventListener("input",()=>{C!==null&&window.clearTimeout(C);let n=r.value.trim();if(n.length<2){L(),c.classList.add("hidden");return}C=window.setTimeout(()=>{H(n)},300)});let H=async n=>{c.classList.remove("hidden");try{let e=await G(n);if(r.value.trim()!==n)return;M(e)}catch(e){if(e instanceof DOMException&&e.name==="AbortError")return;t(e instanceof Error?e.message:"No fue posible buscar clientes.")}finally{c.classList.add("hidden")}},M=n=>{if(s.innerHTML="",s.classList.remove("hidden"),n.length===0){let e=document.createElement("div");e.className="px-4 py-4 text-center",e.innerHTML=`
                <span class="material-symbols-outlined text-[22px] text-slate-300">
                    person_search
                </span>
                <p class="mt-1 text-xs text-slate-500">No encontramos clientes.</p>`,s.append(e);return}for(let e of n){let o=x(e);s.append(o)}};function x(n){let e=document.createElement("button");e.type="button",e.className="flex w-full items-center gap-3 border-b border-slate-100 px-3 py-2.5 text-left transition last:border-b-0 hover:bg-slate-50";let o=document.createElement("div");o.className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full p-6 bg-slate-200 text-xl font-bold text-slate-600",o.textContent=g(n.fullName);let w=document.createElement("div");w.className="min-w-0 flex-1";let R=document.createElement("p");R.className="m-0 truncate text-lg font-bold text-slate-700",R.textContent=n.fullName;let N=document.createElement("p");return N.className="m-0 truncate text-base text-slate-400",N.textContent=a(n),w.append(R,N),e.append(o,w),e.addEventListener("click",()=>{i(n)}),e}let i=n=>{f.value=String(n.id),h.textContent=n.fullName,y.textContent=a(n),b.textContent=g(n.fullName),u.classList.add("hidden"),v.classList.remove("hidden"),r.value="",L()},p=()=>{f.value="",h.textContent="",y.textContent="",b.textContent="",v.classList.add("hidden"),u.classList.remove("hidden"),r.value="",L(),requestAnimationFrame(()=>{r.focus()})};S?.addEventListener("click",p);let L=()=>{s.classList.add("hidden"),s.innerHTML=""};function g(n){let e=n.trim().split(/\s+/).filter(Boolean);return e.length===0?"?":e.length===1?e[0].substring(0,2).toUpperCase():(e[0][0]+e[e.length-1][0]).toUpperCase()}function a(n){let e=[];return n.identification&&e.push(n.identification),n.phone&&e.push(n.phone),e.join(" \xB7 ")}let t=n=>{s.innerHTML="",s.classList.remove("hidden");let e=document.createElement("div");e.className="px-4 py-3 text-center text-xs text-rose-500",e.textContent=n,s.append(e)}}async function U(r){let s=new FormData;s.append("clientId",String(r.clientId)),s.append("numberOfPeople",String(r.numberOfPeople)),s.append("startDate",r.startDate),s.append("endDate",r.endDate),r.resourceId!==null&&s.append("resourceId",String(r.resourceId)),r.observations&&s.append("observations",r.observations);let c=await fetch("/restaurant/api/reservations",{method:"POST",body:s,headers:{Accept:"application/json"}}),u=await c.json();if(!c.ok)throw new Error(u.message??"No fue posible crear la reserva.");return u}function z(){let r=document.querySelector("#reservationForm");if(!r)return;let s=r.querySelector("#reservationClientId"),c=r.querySelector("#reservationDate"),u=r.querySelector("#reservationStartTime"),v=r.querySelector("#reservationEndTime"),f=r.querySelector("#reservationPeople"),h=r.querySelector("#reservationResourceId"),y=r.querySelector("#reservationObservations"),b=r.querySelector('[type="submit"]');if(!s||!c||!u||!v||!f||!h||!b)return;r.addEventListener("submit",async i=>{console.log(123),i.preventDefault();let p=Number(s.value),L=Number(f.value);if(!p){M("Debes seleccionar un cliente.");return}if(!c.value||!u.value||!v.value){M("Debes seleccionar fecha y horario.");return}let g={clientId:p,numberOfPeople:L,startDate:C(c.value,u.value),endDate:C(c.value,v.value),resourceId:h.value?Number(h.value):null,observations:y?.value.trim()||null};await S(g,b)});async function S(i,p){H(p,!0);try{let L=await U(i);x(L.message??"Reserva creada correctamente."),window.setTimeout(()=>{window.location.reload()},700)}catch(L){M(L instanceof Error?L.message:"No fue posible crear la reserva.")}finally{H(p,!1)}}function C(i,p){return`${i} ${p}:00`}function H(i,p){if(i.disabled=p,p){i.dataset.originalText=i.textContent??"",i.innerHTML=`
                <span class="material-symbols-outlined animate-spin text-[18px]">
                    progress_activity
                </span>
                Guardando...`;return}i.textContent=i.dataset.originalText??"Guardar reserva"}function M(i){if(typeof Swal<"u"){Swal.fire({icon:"error",title:"No se pudo crear la reserva",text:i,confirmButtonText:"Aceptar"});return}console.error(i)}function x(i){if(typeof Swal<"u"){Swal.fire({icon:"success",title:"Reserva creada",text:i,showConfirmButton:!1,timer:1200});return}console.log(i)}}async function q(r){let s=await fetch(`/restaurant/api/reservations/detail?id=${r}`,{headers:{Accept:"application/json"}}),c=await s.json();if(!s.ok||!c.success||!c.reservation)throw new Error(c.message??"No fue posible consultar la reserva.");return c.reservation}function F(){let r=document.querySelector("#reservationDetailDrawer"),s=document.querySelector("#reservationDetailOverlay");if(!r||!s)return;let c=r.querySelectorAll("[data-reservation-detail-close]");document.addEventListener("click",async a=>{let n=a.target.closest("[data-reservation-id]");if(!n)return;let e=Number(n.dataset.reservationId);e&&await u(e)}),c.forEach(a=>{a.addEventListener("click",f)}),s.addEventListener("click",f);async function u(a){v(),h();try{let t=await q(a);y(t),b(t)}catch(t){f(),Swal.fire({icon:"error",title:"No se pudo cargar la reserva",text:t instanceof Error?t.message:"Ocurri\xF3 un error inesperado."})}}let v=()=>{s.classList.remove("hidden"),r.classList.remove("translate-x-full")};function f(){s?.classList.add("hidden"),r?.classList.add("translate-x-full")}let h=()=>{let a=r.querySelector("#reservationDetailContent");a&&(a.innerHTML=`
            <div class="flex items-center justify-center py-16">
                <span class="material-symbols-outlined animate-spin">
                    progress_activity
                </span>
                <span class="ml-2 text-sm text-slate-500">
                    Cargando reserva...
                </span>
            </div>
        `)},y=a=>{let t=r.querySelector("#reservationDetailContent");if(!t)return;let n=a.resources.length>0?a.resources.map(e=>`
                        <div class="rounded-xl border border-slate-200 p-3">
                            <div class="font-semibold text-slate-800">
                                ${g(e.name)}
                            </div>
                            <div class="mt-1 text-base text-slate-500">
                                ${g(e.zoneName??"Sin zona")}
                                \xB7 Capacidad ${e.capacity}
                            </div>
                        </div>`).join(""):`
                    <div class="rounded-xl bg-slate-50 p-3 text-sm text-slate-500">
                        Sin mesa asignada
                    </div>`;t.innerHTML=`
            <div class="space-y-6">
                <div>
                    <div class="text-base font-semibold uppercase text-slate-400">Cliente</div>
                    <div class="text-lg font-semibold">${g(a.clientName)}</div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-base font-semibold uppercase text-slate-400">Personas</div>
                        <div class="font-medium"> ${a.numberOfPeople}</div>
                    </div>
                    <div>
                        <div class="text-base font-semibold uppercase text-slate-400">Estado</div>
                        <div class="font-medium capitalize">${g(a.status)}</div>
                    </div>
                </div>

                <div>
                    <div class="text-base font-semibold uppercase text-slate-400">Horario</div>
                    <div class="mt-1 font-medium">
                        ${p(a.startDate)} \u2014 ${L(a.endDate)}
                    </div>
                </div>

                <div>
                    <div class="mb-2 text-base font-semibold uppercase text-slate-400">Mesa</div>
                    <div class="space-y-2">${n}</div>
                </div>

                <div>
                    <div class="text-base font-semibold uppercase text-slate-400">Observaciones</div>
                    <div class="mt-1 text-lg text-slate-700">
                        ${g(a.observations??"Sin observaciones")}
                    </div>
                </div>
            </div>`};function b(a){let t=r?.querySelector("#reservationDetailActions");if(t){if(t.innerHTML="",a.status==="pendiente"){t.innerHTML=`
            <div class="space-y-2">
                <button
                    type="button"
                    data-confirm-reservation="${a.id}"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                    Confirmar reserva
                </button>
                <button
                    type="button"
                    data-cancel-reservation="${a.id}"
                    class="flex w-full items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-medium text-red-600 transition hover:bg-red-50"
                >
                <span class="material-symbols-outlined text-[20px]">cancel</span>
                    Cancelar reserva
                </button>
            </div>`,t.classList.remove("hidden");return}if(a.status==="confirmada"){t.innerHTML=`
                <div class="space-y-2">
                    <button
                        type="button"
                        data-start-occupation="${a.id}"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <span class="material-symbols-outlined text-2xl">restaurant</span>
                        Iniciar atenci\xF3n
                    </button>

                    <button
                        type="button"
                        data-cancel-reservation="${a.id}"
                        class="flex w-full items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-medium text-red-600 transition hover:bg-red-50"
                    >
                    <span class="material-symbols-outlined text-2xl">cancel</span>
                        Cancelar reserva
                    </button>
                </div>`,t.classList.remove("hidden");return}t.classList.add("hidden")}}r.addEventListener("click",async a=>{let t=a.target,n=t.closest("[data-confirm-reservation]"),e=t.closest("[data-cancel-reservation]"),o=t.closest("[data-start-occupation]"),w=0;n&&(w=Number(n.dataset.confirmReservation)),e&&(w=Number(e.dataset.cancelReservation)),o&&(w=Number(o.dataset.startOccupation)),w&&(n&&await S(w,n),e&&await H(w,e),o&&await x(w,o))});async function S(a,t){if(!(await Swal.fire({icon:"question",title:"\xBFConfirmar reserva?",text:"La reserva cambiar\xE1 a estado confirmada.",showCancelButton:!0,confirmButtonText:"S\xED, confirmar",cancelButtonText:"Cancelar"})).isConfirmed)return;t.disabled=!0;let e=t.innerHTML;t.innerHTML=`
            <span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
            Confirmando...`;try{await C(a),await Swal.fire({icon:"success",title:"Reserva confirmada",text:"La reserva fue confirmada correctamente.",timer:1200,showConfirmButton:!1});let o=await q(a);y(o),b(o)}catch(o){Swal.fire({icon:"error",title:"No se pudo confirmar",text:o instanceof Error?o.message:"Ocurri\xF3 un error inesperado."}),t.disabled=!1,t.innerHTML=e}}async function C(a){let t=new FormData;t.append("reservationId",String(a));let n=await fetch("/restaurant/api/reservations/confirm",{method:"POST",body:t,headers:{Accept:"application/json"}}),e=await n.json();if(!n.ok||!e.success)throw new Error(e.message??"No fue posible confirmar la reserva.")}async function H(a,t){if(!(await Swal.fire({icon:"warning",title:"\xBFCancelar reserva?",text:"La mesa asignada quedar\xE1 disponible nuevamente para este horario.",showCancelButton:!0,confirmButtonText:"S\xED, cancelar reserva",cancelButtonText:"Volver"})).isConfirmed)return;t.disabled=!0;let e=t.innerHTML;t.innerHTML=`
            <span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
            Cancelando...`;try{await M(a),await Swal.fire({icon:"success",title:"Reserva cancelada",text:"La reserva fue cancelada correctamente.",timer:1200,showConfirmButton:!1});let o=await q(a);y(o),b(o)}catch(o){await Swal.fire({icon:"error",title:"No se pudo cancelar",text:o instanceof Error?o.message:"Ocurri\xF3 un error inesperado."}),t.disabled=!1,t.innerHTML=e}}async function M(a){let t=new FormData;t.append("reservationId",String(a));let n=await fetch("/restaurant/api/reservations/cancel",{method:"POST",body:t,headers:{Accept:"application/json"}}),e=await n.json();if(!n.ok||!e.success)throw new Error(e.message??"No fue posible cancelar la reserva.")}async function x(a,t){if(!(await Swal.fire({icon:"question",title:"\xBFIniciar atenci\xF3n?",text:"La mesa pasar\xE1 a estar ocupada y se iniciar\xE1 la atenci\xF3n de esta reserva.",showCancelButton:!0,confirmButtonText:"S\xED, iniciar",cancelButtonText:"Volver"})).isConfirmed)return;t.disabled=!0;let e=t.innerHTML;t.innerHTML=`
            <span class="material-symbols-outlined animate-spin text-[20px]">
                progress_activity
            </span>
            Iniciando...`;try{await i(a),await Swal.fire({icon:"success",title:"Atenci\xF3n iniciada",text:"La mesa ahora se encuentra ocupada.",timer:1200,showConfirmButton:!1});let o=await q(a);y(o),b(o)}catch(o){await Swal.fire({icon:"error",title:"No se pudo iniciar la atenci\xF3n",text:o instanceof Error?o.message:"Ocurri\xF3 un error inesperado."}),t.disabled=!1,t.innerHTML=e}}async function i(a){let t=new FormData;t.append("reservationId",String(a));let n=await fetch("/restaurant/api/reservations/start-occupation",{method:"POST",body:t,headers:{Accept:"application/json"}}),e=await n.json();if(!n.ok||!e.success||!e.occupationId)throw new Error(e.message??"No fue posible iniciar la atenci\xF3n.");return e.occupationId}function p(a){return new Date(a.replace(" ","T")).toLocaleString("es",{dateStyle:"medium",timeStyle:"short"})}function L(a){return new Date(a.replace(" ","T")).toLocaleTimeString("es",{hour:"2-digit",minute:"2-digit"})}function g(a){let t=document.createElement("div");return t.textContent=a,t.innerHTML}}function re(){console.log("Restaurant \u2192 Reservations cargado");let r=Z();j(r),$(),z(),F()}function Z(){let r=document.querySelector("#restaurantReservationsData");if(!r)return{zones:[]};try{return JSON.parse(r.textContent??"{}")}catch(s){return console.error("Restaurant \u2192 Error leyendo datos de reservas",s),{zones:[]}}}export{re as initReservations};
//# sourceMappingURL=reservations-TQILFWZI.js.map
