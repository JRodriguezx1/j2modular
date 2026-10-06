var A=null;async function O(n){A?.abort(),A=new AbortController;let a=new FormData;a.append("startDate",n.startDate),a.append("endDate",n.endDate),a.append("numberOfPeople",String(n.numberOfPeople));let i=await fetch("/restaurant/api/reservations/available-tables",{method:"POST",body:a,signal:A.signal}),p=await i.json();if(!i.ok)throw new Error(p.message??"No fue posible consultar disponibilidad.");return p.tables??[]}function j(n){let a=document.querySelector("#btnNewReservation"),i=document.querySelector("#btnCloseReservationDrawer"),p=document.querySelector("#btnCancelReservation"),f=document.querySelector("#reservationDrawer"),b=document.querySelector("#reservationDrawerOverlay"),L=document.querySelector("#reservationDate"),T=document.querySelector("#reservationStartTime"),g=document.querySelector("#reservationEndTime"),h=document.querySelector("#reservationPeople"),w=document.querySelector("#reservationPeopleValue"),S=document.querySelector("#btnIncreasePeople"),C=document.querySelector("#btnDecreasePeople"),x=document.querySelector("#availableTables"),o=document.querySelector("#availableTablesCounter"),c=document.querySelector("#reservationResourceId");if(!a||!f||!b||!L||!T||!g||!h||!w||!x||!o||!c)return;function e(){b?.classList.remove("hidden"),requestAnimationFrame(()=>{f?.classList.remove("translate-x-full")}),document.body.classList.add("overflow-hidden")}function t(){f?.classList.add("translate-x-full"),document.body.classList.remove("overflow-hidden"),window.setTimeout(()=>{b?.classList.add("hidden")},300)}let d=u=>{let v=Math.max(1,u);h.value=String(v),w.textContent=String(v),r()};function l(u,v){return`${u} ${v}:00`}let r=async()=>{let u=L?.value,v=T?.value,m=g?.value,y=Number(h?.value);if(c.value="",!u||!v||!m||y<1){s();return}M();try{let E=await O({startDate:l(u,v),endDate:l(u,m),numberOfPeople:y});N(E)}catch(E){if(E instanceof DOMException&&E.name==="AbortError")return;D(E instanceof Error?E.message:"No fue posible consultar disponibilidad.")}},s=()=>{o.textContent="",x.innerHTML=`
            <div class="flex min-h-[70px] flex-col items-center justify-center text-center">
                <span class="material-symbols-outlined text-5xl text-slate-400">table_restaurant</span>
                <p class="mt-1 text-base text-slate-400">Selecciona fecha y horario.</p>
            </div>`},M=()=>{o.textContent="Consultando...",x.innerHTML=`
            <div class="flex min-h-[70px] items-center justify-center gap-2 text-xs text-slate-400">
                <span class="material-symbols-outlined animate-spin text-3xl">progress_activity</span>
                Buscando mesas disponibles...
            </div>`},D=u=>{o.textContent="",x.innerHTML="";let v=document.createElement("div");v.className="flex min-h-[70px] items-center justify-center gap-2 text-center text-base text-rose-500";let m=document.createElement("span");m.className="material-symbols-outlined text-[18px]",m.textContent="error";let y=document.createElement("span");y.textContent=u,v.append(m,y),x?.append(v)};function R(u){return u===null?"Sin zona":n.zones.find(m=>Number(m.id)===Number(u))?.name??"Sin zona"}let N=u=>{if(x.innerHTML="",c.value="",o.textContent=`${u.length} ${u.length===1?"disponible":"disponibles"}`,u.length===0){let m=document.createElement("div");m.className="flex min-h-[80px] flex-col items-center justify-center text-center",m.innerHTML=`
                <span class="material-symbols-outlined text-[22px] text-amber-400">event_busy</span>
                <p class="mt-1 text-xs font-medium text-slate-600">No hay mesas disponibles</p>
                <p class="mt-0.5 text-[11px] text-slate-400">Puedes guardar la reserva sin asignar mesa.</p>`,x.append(m);return}let v=new Map;for(let m of u){let y=R(m.zoneId),E=v.get(y)??[];E.push(m),v.set(y,E)}for(let[m,y]of v)_(m,y)};function _(u,v){let m=document.createElement("div");m.className="mb-4 last:mb-0";let y=document.createElement("p");y.className="mb-2 text-xl font-bold uppercase tracking-[0.12em] text-slate-500",y.textContent=u;let E=document.createElement("div");E.className="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-2";for(let q of v){let H=document.createElement("button");H.type="button",H.dataset.resourceId=String(q.resourceId),H.className="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-left transition hover:border-indigo-300 hover:bg-indigo-50/40";let I=document.createElement("span");I.className="block truncate text-lg font-bold text-slate-700",I.textContent=q.name;let P=document.createElement("span");P.className="mt-0.5 block text-xl text-slate-400",P.textContent=`${q.capacity} personas`,H.append(I,P),H.addEventListener("click",()=>{G(H,q.resourceId)}),E.append(H)}m.append(y,E),x?.append(m)}let G=(u,v)=>{x.querySelectorAll("button[data-resource-id]").forEach(m=>{m.classList.remove("border-indigo-500","bg-indigo-50","ring-1","ring-indigo-500")}),u.classList.add("border-indigo-500","bg-indigo-50","ring-1","ring-indigo-500"),c.value=String(v)};a.addEventListener("click",e),i?.addEventListener("click",t),p?.addEventListener("click",t),b.addEventListener("click",t),S?.addEventListener("click",()=>{d(Number(h.value)+1)}),C?.addEventListener("click",()=>{d(Number(h.value)-1)}),L.addEventListener("change",r),T.addEventListener("change",r),g.addEventListener("change",r),document.addEventListener("keydown",u=>{u.key==="Escape"&&!b.classList.contains("hidden")&&t()})}var B=null;async function U(n){B?.abort();let a=n.trim();if(a.length<2)return[];B=new AbortController;let i=new URLSearchParams({q:a}),p=await fetch(`/restaurant/api/customers/search?${i}`,{method:"GET",signal:B.signal,headers:{Accept:"application/json"}}),f=await p.json();if(!p.ok)throw new Error(f.message??"No fue posible buscar clientes.");return f.customers??[]}function $(){let n=document.querySelector("#customerSearchInput"),a=document.querySelector("#customerSearchResults"),i=document.querySelector("#customerSearchLoader"),p=document.querySelector("#customerSearch"),f=document.querySelector("#selectedCustomer"),b=document.querySelector("#reservationClientId"),L=document.querySelector("#selectedCustomerName"),T=document.querySelector("#selectedCustomerInfo"),g=document.querySelector("#selectedCustomerInitials"),h=document.querySelector("#btnChangeCustomer");if(!n||!a||!i||!p||!f||!b||!L||!T||!g)return;let w=null;n.addEventListener("input",()=>{w!==null&&window.clearTimeout(w);let r=n.value.trim();if(r.length<2){e(),i.classList.add("hidden");return}w=window.setTimeout(()=>{S(r)},300)});let S=async r=>{i.classList.remove("hidden");try{let s=await U(r);if(n.value.trim()!==r)return;C(s)}catch(s){if(s instanceof DOMException&&s.name==="AbortError")return;l(s instanceof Error?s.message:"No fue posible buscar clientes.")}finally{i.classList.add("hidden")}},C=r=>{if(a.innerHTML="",a.classList.remove("hidden"),r.length===0){let s=document.createElement("div");s.className="px-4 py-4 text-center",s.innerHTML=`
                <span class="material-symbols-outlined text-[22px] text-slate-300">
                    person_search
                </span>
                <p class="mt-1 text-xs text-slate-500">No encontramos clientes.</p>`,a.append(s);return}for(let s of r){let M=x(s);a.append(M)}};function x(r){let s=document.createElement("button");s.type="button",s.className="flex w-full items-center gap-3 border-b border-slate-100 px-3 py-2.5 text-left transition last:border-b-0 hover:bg-slate-50";let M=document.createElement("div");M.className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-[10px] font-bold text-slate-600",M.textContent=t(r.fullName);let D=document.createElement("div");D.className="min-w-0 flex-1";let R=document.createElement("p");R.className="truncate text-xs font-semibold text-slate-700",R.textContent=r.fullName;let N=document.createElement("p");return N.className="mt-0.5 truncate text-[10px] text-slate-400",N.textContent=d(r),D.append(R,N),s.append(M,D),s.addEventListener("click",()=>{o(r)}),s}let o=r=>{b.value=String(r.id),L.textContent=r.fullName,T.textContent=d(r),g.textContent=t(r.fullName),p.classList.add("hidden"),f.classList.remove("hidden"),n.value="",e()},c=()=>{b.value="",L.textContent="",T.textContent="",g.textContent="",f.classList.add("hidden"),p.classList.remove("hidden"),n.value="",e(),requestAnimationFrame(()=>{n.focus()})};h?.addEventListener("click",c);let e=()=>{a.classList.add("hidden"),a.innerHTML=""};function t(r){let s=r.trim().split(/\s+/).filter(Boolean);return s.length===0?"?":s.length===1?s[0].substring(0,2).toUpperCase():(s[0][0]+s[s.length-1][0]).toUpperCase()}function d(r){let s=[];return r.identification&&s.push(r.identification),r.phone&&s.push(r.phone),s.join(" \xB7 ")}let l=r=>{a.innerHTML="",a.classList.remove("hidden");let s=document.createElement("div");s.className="px-4 py-3 text-center text-xs text-rose-500",s.textContent=r,a.append(s)}}async function V(n){let a=new FormData;a.append("clientId",String(n.clientId)),a.append("numberOfPeople",String(n.numberOfPeople)),a.append("startDate",n.startDate),a.append("endDate",n.endDate),n.resourceId!==null&&a.append("resourceId",String(n.resourceId)),n.observations&&a.append("observations",n.observations);let i=await fetch("/restaurant/api/reservations",{method:"POST",body:a,headers:{Accept:"application/json"}}),p=await i.json();if(!i.ok)throw new Error(p.message??"No fue posible crear la reserva.");return p}function z(){let n=document.querySelector("#reservationForm");if(!n)return;let a=n.querySelector("#reservationClientId"),i=n.querySelector("#reservationDate"),p=n.querySelector("#reservationStartTime"),f=n.querySelector("#reservationEndTime"),b=n.querySelector("#reservationPeople"),L=n.querySelector("#reservationResourceId"),T=n.querySelector("#reservationObservations"),g=n.querySelector('[type="submit"]');if(!a||!i||!p||!f||!b||!L||!g)return;n.addEventListener("submit",async o=>{console.log(123),o.preventDefault();let c=Number(a.value),e=Number(b.value);if(!c){C("Debes seleccionar un cliente.");return}if(!i.value||!p.value||!f.value){C("Debes seleccionar fecha y horario.");return}let t={clientId:c,numberOfPeople:e,startDate:w(i.value,p.value),endDate:w(i.value,f.value),resourceId:L.value?Number(L.value):null,observations:T?.value.trim()||null};await h(t,g)});async function h(o,c){S(c,!0);try{let e=await V(o);x(e.message??"Reserva creada correctamente."),window.setTimeout(()=>{window.location.reload()},700)}catch(e){C(e instanceof Error?e.message:"No fue posible crear la reserva.")}finally{S(c,!1)}}function w(o,c){return`${o} ${c}:00`}function S(o,c){if(o.disabled=c,c){o.dataset.originalText=o.textContent??"",o.innerHTML=`
                <span class="material-symbols-outlined animate-spin text-[18px]">
                    progress_activity
                </span>
                Guardando...`;return}o.textContent=o.dataset.originalText??"Guardar reserva"}function C(o){if(typeof Swal<"u"){Swal.fire({icon:"error",title:"No se pudo crear la reserva",text:o,confirmButtonText:"Aceptar"});return}console.error(o)}function x(o){if(typeof Swal<"u"){Swal.fire({icon:"success",title:"Reserva creada",text:o,showConfirmButton:!1,timer:1200});return}console.log(o)}}async function k(n){let a=await fetch(`/restaurant/api/reservations/detail?id=${n}`,{headers:{Accept:"application/json"}}),i=await a.json();if(!a.ok||!i.success||!i.reservation)throw new Error(i.message??"No fue posible consultar la reserva.");return i.reservation}function F(){let n=document.querySelector("#reservationDetailDrawer"),a=document.querySelector("#reservationDetailOverlay");if(!n||!a)return;let i=n.querySelectorAll("[data-reservation-detail-close]");document.addEventListener("click",async e=>{let d=e.target.closest("[data-reservation-id]");if(!d)return;let l=Number(d.dataset.reservationId);l&&await p(l)}),i.forEach(e=>{e.addEventListener("click",b)}),a.addEventListener("click",b);async function p(e){f(),L();try{let t=await k(e);T(t),g(t)}catch(t){b(),Swal.fire({icon:"error",title:"No se pudo cargar la reserva",text:t instanceof Error?t.message:"Ocurri\xF3 un error inesperado."})}}let f=()=>{a.classList.remove("hidden"),n.classList.remove("translate-x-full")};function b(){a?.classList.add("hidden"),n?.classList.add("translate-x-full")}let L=()=>{let e=n.querySelector("#reservationDetailContent");e&&(e.innerHTML=`
            <div class="flex items-center justify-center py-16">
                <span class="material-symbols-outlined animate-spin">
                    progress_activity
                </span>
                <span class="ml-2 text-sm text-slate-500">
                    Cargando reserva...
                </span>
            </div>
        `)},T=e=>{let t=n.querySelector("#reservationDetailContent");if(!t)return;let d=e.resources.length>0?e.resources.map(l=>`
                        <div class="rounded-xl border border-slate-200 p-3">
                            <div class="font-semibold text-slate-800">
                                ${c(l.name)}
                            </div>

                            <div class="mt-1 text-xs text-slate-500">
                                ${c(l.zoneName??"Sin zona")}
                                \xB7 Capacidad ${l.capacity}
                            </div>
                        </div>`).join(""):`
                    <div class="rounded-xl bg-slate-50 p-3 text-sm text-slate-500">
                        Sin mesa asignada
                    </div>`;t.innerHTML=`
            <div class="space-y-6">
                <div>
                    <div class="text-xs font-medium uppercase text-slate-400">Cliente</div>
                    <div class="mt-1 text-lg font-semibold">${c(e.clientName)}</div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-xs text-slate-400">Personas</div>
                        <div class="font-medium"> ${e.numberOfPeople}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-400">Estado</div>
                        <div class="font-medium capitalize">${c(e.status)}</div>
                    </div>
                </div>

                <div>
                    <div class="text-xs text-slate-400">Horario</div>
                    <div class="mt-1 font-medium">
                        ${x(e.startDate)} \u2014 ${o(e.endDate)}
                    </div>
                </div>

                <div>
                    <div class="mb-2 text-xs text-slate-400">Mesa</div>
                    <div class="space-y-2">${d}</div>
                </div>

                <div>
                    <div class="text-xs text-slate-400">Observaciones</div>
                    <div class="mt-1 text-sm text-slate-700">
                        ${c(e.observations??"Sin observaciones")}
                    </div>
                </div>
            </div>`};function g(e){let t=n?.querySelector("#reservationDetailActions");if(t){if(t.innerHTML="",e.status==="pendiente"){t.innerHTML=`
            <div class="space-y-2">
                <button
                    type="button"
                    data-confirm-reservation="${e.id}"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                    Confirmar reserva
                </button>
                <button
                    type="button"
                    data-cancel-reservation="${e.id}"
                    class="flex w-full items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-medium text-red-600 transition hover:bg-red-50"
                >
                <span class="material-symbols-outlined text-[20px]">cancel</span>
                    Cancelar reserva
                </button>
            </div>`,t.classList.remove("hidden");return}if(e.status==="confirmada"){t.innerHTML=`
                <button
                    type="button"
                    data-cancel-reservation="${e.id}"
                    class="flex w-full items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-medium text-red-600 transition hover:bg-red-50"
                >
                <span class="material-symbols-outlined text-[20px]">cancel</span>
                    Cancelar reserva
                </button>`,t.classList.remove("hidden");return}t.classList.add("hidden")}}n.addEventListener("click",async e=>{let t=e.target,d=t.closest("[data-confirm-reservation]"),l=t.closest("[data-cancel-reservation]"),r=0;if(d)r=Number(d.dataset.confirmReservation);else if(l)r=Number(l.dataset.cancelReservation);else return;r&&(d&&await h(r,d),l&&await S(r,l))});async function h(e,t){if(!(await Swal.fire({icon:"question",title:"\xBFConfirmar reserva?",text:"La reserva cambiar\xE1 a estado confirmada.",showCancelButton:!0,confirmButtonText:"S\xED, confirmar",cancelButtonText:"Cancelar"})).isConfirmed)return;t.disabled=!0;let l=t.innerHTML;t.innerHTML=`
            <span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
            Confirmando...`;try{await w(e),await Swal.fire({icon:"success",title:"Reserva confirmada",text:"La reserva fue confirmada correctamente.",timer:1200,showConfirmButton:!1});let r=await k(e);T(r),g(r)}catch(r){Swal.fire({icon:"error",title:"No se pudo confirmar",text:r instanceof Error?r.message:"Ocurri\xF3 un error inesperado."}),t.disabled=!1,t.innerHTML=l}}async function w(e){let t=new FormData;t.append("reservationId",String(e));let d=await fetch("/restaurant/api/reservations/confirm",{method:"POST",body:t,headers:{Accept:"application/json"}}),l=await d.json();if(!d.ok||!l.success)throw new Error(l.message??"No fue posible confirmar la reserva.")}async function S(e,t){if(!(await Swal.fire({icon:"warning",title:"\xBFCancelar reserva?",text:"La mesa asignada quedar\xE1 disponible nuevamente para este horario.",showCancelButton:!0,confirmButtonText:"S\xED, cancelar reserva",cancelButtonText:"Volver"})).isConfirmed)return;t.disabled=!0;let l=t.innerHTML;t.innerHTML=`
            <span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
            Cancelando...`;try{await C(e),await Swal.fire({icon:"success",title:"Reserva cancelada",text:"La reserva fue cancelada correctamente.",timer:1200,showConfirmButton:!1});let r=await k(e);T(r),g(r)}catch(r){await Swal.fire({icon:"error",title:"No se pudo cancelar",text:r instanceof Error?r.message:"Ocurri\xF3 un error inesperado."}),t.disabled=!1,t.innerHTML=l}}async function C(e){let t=new FormData;t.append("reservationId",String(e));let d=await fetch("/restaurant/api/reservations/cancel",{method:"POST",body:t,headers:{Accept:"application/json"}}),l=await d.json();if(!d.ok||!l.success)throw new Error(l.message??"No fue posible cancelar la reserva.")}function x(e){return new Date(e.replace(" ","T")).toLocaleString("es",{dateStyle:"medium",timeStyle:"short"})}function o(e){return new Date(e.replace(" ","T")).toLocaleTimeString("es",{hour:"2-digit",minute:"2-digit"})}function c(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}}function ae(){console.log("Restaurant \u2192 Reservations cargado");let n=Z();j(n),$(),z(),F()}function Z(){let n=document.querySelector("#restaurantReservationsData");if(!n)return{zones:[]};try{return JSON.parse(n.textContent??"{}")}catch(a){return console.error("Restaurant \u2192 Error leyendo datos de reservas",a),{zones:[]}}}export{ae as initReservations};
//# sourceMappingURL=reservations-T672P72M.js.map
