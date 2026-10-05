var A=null;async function O(e){A?.abort(),A=new AbortController;let t=new FormData;t.append("startDate",e.startDate),t.append("endDate",e.endDate),t.append("numberOfPeople",String(e.numberOfPeople));let a=await fetch("/restaurant/api/reservations/available-tables",{method:"POST",body:t,signal:A.signal}),d=await a.json();if(!a.ok)throw new Error(d.message??"No fue posible consultar disponibilidad.");return d.tables??[]}function $(e){let t=document.querySelector("#btnNewReservation"),a=document.querySelector("#btnCloseReservationDrawer"),d=document.querySelector("#btnCancelReservation"),v=document.querySelector("#reservationDrawer"),f=document.querySelector("#reservationDrawerOverlay"),g=document.querySelector("#reservationDate"),T=document.querySelector("#reservationStartTime"),L=document.querySelector("#reservationEndTime"),h=document.querySelector("#reservationPeople"),b=document.querySelector("#reservationPeopleValue"),o=document.querySelector("#btnIncreasePeople"),c=document.querySelector("#btnDecreasePeople"),m=document.querySelector("#availableTables"),r=document.querySelector("#availableTablesCounter"),p=document.querySelector("#reservationResourceId");if(!t||!v||!f||!g||!T||!L||!h||!b||!m||!r||!p)return;function E(){f?.classList.remove("hidden"),requestAnimationFrame(()=>{v?.classList.remove("translate-x-full")}),document.body.classList.add("overflow-hidden")}function S(){v?.classList.add("translate-x-full"),document.body.classList.remove("overflow-hidden"),window.setTimeout(()=>{f?.classList.add("hidden")},300)}let D=i=>{let u=Math.max(1,i);h.value=String(u),b.textContent=String(u),s()};function I(i,u){return`${i} ${u}:00`}let s=async()=>{let i=g?.value,u=T?.value,l=L?.value,x=Number(h?.value);if(p.value="",!i||!u||!l||x<1){n();return}C();try{let y=await O({startDate:I(i,u),endDate:I(i,l),numberOfPeople:x});R(y)}catch(y){if(y instanceof DOMException&&y.name==="AbortError")return;M(y instanceof Error?y.message:"No fue posible consultar disponibilidad.")}},n=()=>{r.textContent="",m.innerHTML=`
            <div class="flex min-h-[70px] flex-col items-center justify-center text-center">
                <span class="material-symbols-outlined text-5xl text-slate-400">table_restaurant</span>
                <p class="mt-1 text-base text-slate-400">Selecciona fecha y horario.</p>
            </div>`},C=()=>{r.textContent="Consultando...",m.innerHTML=`
            <div class="flex min-h-[70px] items-center justify-center gap-2 text-xs text-slate-400">
                <span class="material-symbols-outlined animate-spin text-3xl">progress_activity</span>
                Buscando mesas disponibles...
            </div>`},M=i=>{r.textContent="",m.innerHTML="";let u=document.createElement("div");u.className="flex min-h-[70px] items-center justify-center gap-2 text-center text-base text-rose-500";let l=document.createElement("span");l.className="material-symbols-outlined text-[18px]",l.textContent="error";let x=document.createElement("span");x.textContent=i,u.append(l,x),m?.append(u)};function H(i){return i===null?"Sin zona":e.zones.find(l=>Number(l.id)===Number(i))?.name??"Sin zona"}let R=i=>{if(m.innerHTML="",p.value="",r.textContent=`${i.length} ${i.length===1?"disponible":"disponibles"}`,i.length===0){let l=document.createElement("div");l.className="flex min-h-[80px] flex-col items-center justify-center text-center",l.innerHTML=`
                <span class="material-symbols-outlined text-[22px] text-amber-400">event_busy</span>
                <p class="mt-1 text-xs font-medium text-slate-600">No hay mesas disponibles</p>
                <p class="mt-0.5 text-[11px] text-slate-400">Puedes guardar la reserva sin asignar mesa.</p>`,m.append(l);return}let u=new Map;for(let l of i){let x=H(l.zoneId),y=u.get(x)??[];y.push(l),u.set(x,y)}for(let[l,x]of u)F(l,x)};function F(i,u){let l=document.createElement("div");l.className="mb-4 last:mb-0";let x=document.createElement("p");x.className="mb-2 text-xl font-bold uppercase tracking-[0.12em] text-slate-500",x.textContent=i;let y=document.createElement("div");y.className="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-2";for(let N of u){let w=document.createElement("button");w.type="button",w.dataset.resourceId=String(N.resourceId),w.className="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-left transition hover:border-indigo-300 hover:bg-indigo-50/40";let q=document.createElement("span");q.className="block truncate text-lg font-bold text-slate-700",q.textContent=N.name;let P=document.createElement("span");P.className="mt-0.5 block text-xl text-slate-400",P.textContent=`${N.capacity} personas`,w.append(q,P),w.addEventListener("click",()=>{_(w,N.resourceId)}),y.append(w)}l.append(x,y),m?.append(l)}let _=(i,u)=>{m.querySelectorAll("button[data-resource-id]").forEach(l=>{l.classList.remove("border-indigo-500","bg-indigo-50","ring-1","ring-indigo-500")}),i.classList.add("border-indigo-500","bg-indigo-50","ring-1","ring-indigo-500"),p.value=String(u)};t.addEventListener("click",E),a?.addEventListener("click",S),d?.addEventListener("click",S),f.addEventListener("click",S),o?.addEventListener("click",()=>{D(Number(h.value)+1)}),c?.addEventListener("click",()=>{D(Number(h.value)-1)}),g.addEventListener("change",s),T.addEventListener("change",s),L.addEventListener("change",s),document.addEventListener("keydown",i=>{i.key==="Escape"&&!f.classList.contains("hidden")&&S()})}var k=null;async function G(e){k?.abort();let t=e.trim();if(t.length<2)return[];k=new AbortController;let a=new URLSearchParams({q:t}),d=await fetch(`/restaurant/api/customers/search?${a}`,{method:"GET",signal:k.signal,headers:{Accept:"application/json"}}),v=await d.json();if(!d.ok)throw new Error(v.message??"No fue posible buscar clientes.");return v.customers??[]}function B(){let e=document.querySelector("#customerSearchInput"),t=document.querySelector("#customerSearchResults"),a=document.querySelector("#customerSearchLoader"),d=document.querySelector("#customerSearch"),v=document.querySelector("#selectedCustomer"),f=document.querySelector("#reservationClientId"),g=document.querySelector("#selectedCustomerName"),T=document.querySelector("#selectedCustomerInfo"),L=document.querySelector("#selectedCustomerInitials"),h=document.querySelector("#btnChangeCustomer");if(!e||!t||!a||!d||!v||!f||!g||!T||!L)return;let b=null;e.addEventListener("input",()=>{b!==null&&window.clearTimeout(b);let s=e.value.trim();if(s.length<2){E(),a.classList.add("hidden");return}b=window.setTimeout(()=>{o(s)},300)});let o=async s=>{a.classList.remove("hidden");try{let n=await G(s);if(e.value.trim()!==s)return;c(n)}catch(n){if(n instanceof DOMException&&n.name==="AbortError")return;I(n instanceof Error?n.message:"No fue posible buscar clientes.")}finally{a.classList.add("hidden")}},c=s=>{if(t.innerHTML="",t.classList.remove("hidden"),s.length===0){let n=document.createElement("div");n.className="px-4 py-4 text-center",n.innerHTML=`
                <span class="material-symbols-outlined text-[22px] text-slate-300">
                    person_search
                </span>
                <p class="mt-1 text-xs text-slate-500">No encontramos clientes.</p>`,t.append(n);return}for(let n of s){let C=m(n);t.append(C)}};function m(s){let n=document.createElement("button");n.type="button",n.className="flex w-full items-center gap-3 border-b border-slate-100 px-3 py-2.5 text-left transition last:border-b-0 hover:bg-slate-50";let C=document.createElement("div");C.className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-[10px] font-bold text-slate-600",C.textContent=S(s.fullName);let M=document.createElement("div");M.className="min-w-0 flex-1";let H=document.createElement("p");H.className="truncate text-xs font-semibold text-slate-700",H.textContent=s.fullName;let R=document.createElement("p");return R.className="mt-0.5 truncate text-[10px] text-slate-400",R.textContent=D(s),M.append(H,R),n.append(C,M),n.addEventListener("click",()=>{r(s)}),n}let r=s=>{f.value=String(s.id),g.textContent=s.fullName,T.textContent=D(s),L.textContent=S(s.fullName),d.classList.add("hidden"),v.classList.remove("hidden"),e.value="",E()},p=()=>{f.value="",g.textContent="",T.textContent="",L.textContent="",v.classList.add("hidden"),d.classList.remove("hidden"),e.value="",E(),requestAnimationFrame(()=>{e.focus()})};h?.addEventListener("click",p);let E=()=>{t.classList.add("hidden"),t.innerHTML=""};function S(s){let n=s.trim().split(/\s+/).filter(Boolean);return n.length===0?"?":n.length===1?n[0].substring(0,2).toUpperCase():(n[0][0]+n[n.length-1][0]).toUpperCase()}function D(s){let n=[];return s.identification&&n.push(s.identification),s.phone&&n.push(s.phone),n.join(" \xB7 ")}let I=s=>{t.innerHTML="",t.classList.remove("hidden");let n=document.createElement("div");n.className="px-4 py-3 text-center text-xs text-rose-500",n.textContent=s,t.append(n)}}async function U(e){let t=new FormData;t.append("clientId",String(e.clientId)),t.append("numberOfPeople",String(e.numberOfPeople)),t.append("startDate",e.startDate),t.append("endDate",e.endDate),e.resourceId!==null&&t.append("resourceId",String(e.resourceId)),e.observations&&t.append("observations",e.observations);let a=await fetch("/restaurant/api/reservations",{method:"POST",body:t,headers:{Accept:"application/json"}}),d=await a.json();if(!a.ok)throw new Error(d.message??"No fue posible crear la reserva.");return d}function j(){let e=document.querySelector("#reservationForm");if(!e)return;let t=e.querySelector("#reservationClientId"),a=e.querySelector("#reservationDate"),d=e.querySelector("#reservationStartTime"),v=e.querySelector("#reservationEndTime"),f=e.querySelector("#reservationPeople"),g=e.querySelector("#reservationResourceId"),T=e.querySelector("#reservationObservations"),L=e.querySelector('[type="submit"]');if(!t||!a||!d||!v||!f||!g||!L)return;e.addEventListener("submit",async r=>{console.log(123),r.preventDefault();let p=Number(t.value),E=Number(f.value);if(!p){c("Debes seleccionar un cliente.");return}if(!a.value||!d.value||!v.value){c("Debes seleccionar fecha y horario.");return}let S={clientId:p,numberOfPeople:E,startDate:b(a.value,d.value),endDate:b(a.value,v.value),resourceId:g.value?Number(g.value):null,observations:T?.value.trim()||null};await h(S,L)});async function h(r,p){o(p,!0);try{let E=await U(r);m(E.message??"Reserva creada correctamente."),window.setTimeout(()=>{window.location.reload()},700)}catch(E){c(E instanceof Error?E.message:"No fue posible crear la reserva.")}finally{o(p,!1)}}function b(r,p){return`${r} ${p}:00`}function o(r,p){if(r.disabled=p,p){r.dataset.originalText=r.textContent??"",r.innerHTML=`
                <span class="material-symbols-outlined animate-spin text-[18px]">
                    progress_activity
                </span>
                Guardando...`;return}r.textContent=r.dataset.originalText??"Guardar reserva"}function c(r){if(typeof Swal<"u"){Swal.fire({icon:"error",title:"No se pudo crear la reserva",text:r,confirmButtonText:"Aceptar"});return}console.error(r)}function m(r){if(typeof Swal<"u"){Swal.fire({icon:"success",title:"Reserva creada",text:r,showConfirmButton:!1,timer:1200});return}console.log(r)}}async function V(e){let t=await fetch(`/restaurant/api/reservations/detail?id=${e}`,{headers:{Accept:"application/json"}}),a=await t.json();if(!t.ok||!a.success||!a.reservation)throw new Error(a.message??"No fue posible consultar la reserva.");return a.reservation}function z(){let e=document.querySelector("#reservationDetailDrawer"),t=document.querySelector("#reservationDetailOverlay");if(!e||!t)return;let a=e.querySelectorAll("[data-reservation-detail-close]");document.addEventListener("click",async o=>{let m=o.target.closest("[data-reservation-id]");if(!m)return;let r=Number(m.dataset.reservationId);r&&await d(r)}),a.forEach(o=>{o.addEventListener("click",f)}),t.addEventListener("click",f);async function d(o){v(),g();try{let c=await V(o);T(c)}catch(c){f(),Swal.fire({icon:"error",title:"No se pudo cargar la reserva",text:c instanceof Error?c.message:"Ocurri\xF3 un error inesperado."})}}let v=()=>{t.classList.remove("hidden"),e.classList.remove("translate-x-full")};function f(){t?.classList.add("hidden"),e?.classList.add("translate-x-full")}let g=()=>{let o=e.querySelector("#reservationDetailContent");o&&(o.innerHTML=`
            <div class="flex items-center justify-center py-16">
                <span class="material-symbols-outlined animate-spin">
                    progress_activity
                </span>

                <span class="ml-2 text-sm text-slate-500">
                    Cargando reserva...
                </span>
            </div>
        `)},T=o=>{let c=e.querySelector("#reservationDetailContent");if(!c)return;let m=o.resources.length>0?o.resources.map(r=>`
                        <div class="rounded-xl border border-slate-200 p-3">
                            <div class="font-semibold text-slate-800">
                                ${b(r.name)}
                            </div>

                            <div class="mt-1 text-xs text-slate-500">
                                ${b(r.zoneName??"Sin zona")}
                                \xB7 Capacidad ${r.capacity}
                            </div>
                        </div>`).join(""):`
                    <div class="rounded-xl bg-slate-50 p-3 text-sm text-slate-500">
                        Sin mesa asignada
                    </div>`;c.innerHTML=`
            <div class="space-y-6">
                <div>
                    <div class="text-xs font-medium uppercase text-slate-400">Cliente</div>
                    <div class="mt-1 text-lg font-semibold">${b(o.clientName)}</div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-xs text-slate-400">Personas</div>
                        <div class="font-medium"> ${o.numberOfPeople}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-400">Estado</div>
                        <div class="font-medium capitalize">${b(o.status)}</div>
                    </div>
                </div>

                <div>
                    <div class="text-xs text-slate-400">Horario</div>
                    <div class="mt-1 font-medium">
                        ${L(o.startDate)}
                        \u2014
                        ${h(o.endDate)}
                    </div>
                </div>

                <div>
                    <div class="mb-2 text-xs text-slate-400">Mesa</div>
                    <div class="space-y-2">${m}</div>
                </div>

                <div>
                    <div class="text-xs text-slate-400">Observaciones</div>
                    <div class="mt-1 text-sm text-slate-700">
                        ${b(o.observations??"Sin observaciones")}
                    </div>
                </div>
            </div>`};function L(o){return new Date(o.replace(" ","T")).toLocaleString("es",{dateStyle:"medium",timeStyle:"short"})}function h(o){return new Date(o.replace(" ","T")).toLocaleTimeString("es",{hour:"2-digit",minute:"2-digit"})}function b(o){let c=document.createElement("div");return c.textContent=o,c.innerHTML}}function se(){console.log("Restaurant \u2192 Reservations cargado");let e=Z();$(e),B(),j(),z()}function Z(){let e=document.querySelector("#restaurantReservationsData");if(!e)return{zones:[]};try{return JSON.parse(e.textContent??"{}")}catch(t){return console.error("Restaurant \u2192 Error leyendo datos de reservas",t),{zones:[]}}}export{se as initReservations};
//# sourceMappingURL=reservations-K6J4ORE3.js.map
