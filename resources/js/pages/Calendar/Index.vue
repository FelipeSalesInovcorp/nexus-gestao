<script setup lang="ts">
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import type { DateSelectArg, EventClickArg } from '@fullcalendar/interaction';
import timeGridPlugin from '@fullcalendar/timegrid';
import FullCalendar from '@fullcalendar/vue3';
import { computed, reactive, ref } from 'vue';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogFooter,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Opt = { id: number; name: string };
type TypeOpt = { id: number; name: string; color?: string | null };

const props = defineProps<{
    users: Opt[];
    entities: Opt[];
    types: TypeOpt[];
    actions: Opt[];
    statusOptions: string[];
}>();

const calRef = ref<any>(null);

const filters = reactive({
    user_id: '' as string | number,
    entity_id: '' as string | number,
});

const isOpen = ref(false);
const editingId = ref<number | null>(null);

const form = reactive({
    user_id: '' as string | number,
    entity_id: '' as string | number,
    type_id: '' as string | number,
    action_id: '' as string | number,
    date: '',
    time: '',
    duration: '' as string | number,
    shared: false,
    acknowledged: false,
    status: 'scheduled',
    description: '',
});

function csrf() {
    const el = document.querySelector(
        'meta[name="csrf-token"]',
    ) as HTMLMetaElement | null;
    return el?.content ?? '';
}

function getCookie(name: string) {
    const v = document.cookie
        .split('; ')
        .find((row) => row.startsWith(name + '='));
    return v ? decodeURIComponent(v.split('=')[1]) : '';
}

async function ensureCsrfCookie() {
    // Se já existir XSRF-TOKEN cookie, não precisa refazer
    if (document.cookie.includes('XSRF-TOKEN=')) return;

    // Se teu projeto não tiver Sanctum, este endpoint pode não existir.
    // Como você já reportou que o fix funcionou antes, mantemos aqui.
    await fetch('/sanctum/csrf-cookie', {
        method: 'GET',
        credentials: 'same-origin',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
    });
}

async function fetchJson(url: string) {
    const res = await fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
    });
    if (!res.ok) throw new Error('Erro ao carregar eventos');
    return await res.json();
}

function refreshEvents() {
    const api = calRef.value?.getApi?.();
    api?.refetchEvents();
}

function buildEventsUrl(info: any) {
    const p = new URLSearchParams();
    p.set('start', info.startStr);
    p.set('end', info.endStr);

    if (filters.user_id) p.set('user_id', String(filters.user_id));
    if (filters.entity_id) p.set('entity_id', String(filters.entity_id));

    return `/calendar/events?${p.toString()}`;
}

/**
 * ✅ Helpers para construir título “bonito”
 */
function nameById(
    list: Opt[] | TypeOpt[],
    id: number | string | null | undefined,
) {
    if (!id) return '';
    const n = Number(id);
    const found = (list as any[]).find((x) => Number(x.id) === n);
    return found?.name ?? '';
}

function buildPrettyTitle(ex: any) {
    const typeName = nameById(props.types, ex?.type_id) || 'Atividade';
    const actionName = nameById(props.actions, ex?.action_id);
    const entityName = nameById(props.entities, ex?.entity_id);

    // Ex: "Reunião · Videochamada · EFD Soluções"
    return [typeName, actionName, entityName].filter(Boolean).join(' · ');
}

function resetForm() {
    editingId.value = null;
    form.user_id = props.users?.[0]?.id ?? '';
    form.entity_id = '';
    form.type_id = '';
    form.action_id = '';
    form.date = '';
    form.time = '';
    form.duration = '';
    form.shared = false;
    form.acknowledged = false;
    form.status = 'scheduled';
    form.description = '';
}

function openCreateFromSelection(sel: DateSelectArg) {
    resetForm();

    const start = sel.start;
    const yyyy = start.getFullYear();
    const mm = String(start.getMonth() + 1).padStart(2, '0');
    const dd = String(start.getDate()).padStart(2, '0');
    const hh = String(start.getHours()).padStart(2, '0');
    const mi = String(start.getMinutes()).padStart(2, '0');

    form.date = `${yyyy}-${mm}-${dd}`;
    form.time = `${hh}:${mi}`;

    isOpen.value = true;
}

function openEdit(ev: EventClickArg) {
    resetForm();
    editingId.value = Number(ev.event.id);

    const start = ev.event.start!;
    const yyyy = start.getFullYear();
    const mm = String(start.getMonth() + 1).padStart(2, '0');
    const dd = String(start.getDate()).padStart(2, '0');
    const hh = String(start.getHours()).padStart(2, '0');
    const mi = String(start.getMinutes()).padStart(2, '0');

    const ex: any = ev.event.extendedProps ?? {};

    form.user_id = ex.user_id ?? '';
    form.entity_id = ex.entity_id ?? '';
    form.type_id = ex.type_id ?? '';
    form.action_id = ex.action_id ?? '';
    form.date = `${yyyy}-${mm}-${dd}`;
    form.time = `${hh}:${mi}`;
    form.duration = ex.duration ?? '';
    form.shared = !!ex.shared;
    form.acknowledged = !!ex.acknowledged;
    form.status = ex.status ?? 'scheduled';
    form.description = ex.description ?? '';

    isOpen.value = true;
}

async function save() {
    await ensureCsrfCookie();

    const payload = {
        user_id: Number(form.user_id),
        entity_id: form.entity_id ? Number(form.entity_id) : null,
        type_id: form.type_id ? Number(form.type_id) : null,
        action_id: form.action_id ? Number(form.action_id) : null,
        date: form.date,
        time: form.time,
        duration: form.duration ? Number(form.duration) : null,
        shared: !!form.shared,
        acknowledged: !!form.acknowledged,
        status: form.status,
        description: form.description || null,
    };

    const url = editingId.value
        ? `/calendar/events/${editingId.value}`
        : `/calendar/events`;
    const method = editingId.value ? 'PUT' : 'POST';

    const res = await fetch(url, {
        method,
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            // CSRF via cookie XSRF-TOKEN (mais estável para fetch)
            'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
            // fallback (não atrapalha)
            'X-CSRF-TOKEN': csrf(),
        },
        credentials: 'same-origin',
        body: JSON.stringify(payload),
    });

    if (!res.ok) {
        const txt = await res.text();
        alert(`Erro ao guardar.\n${txt}`);
        return;
    }

    isOpen.value = false;
    refreshEvents();
}

async function removeEvent() {
    if (!editingId.value) return;
    if (!confirm('Apagar esta atividade?')) return;

    await ensureCsrfCookie();

    const res = await fetch(`/calendar/events/${editingId.value}`, {
        method: 'DELETE',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
            'X-CSRF-TOKEN': csrf(),
        },
        credentials: 'same-origin',
    });

    if (!res.ok) {
        const txt = await res.text();
        alert(`Erro ao apagar.\n${txt}`);
        return;
    }

    isOpen.value = false;
    refreshEvents();
}

const calendarOptions = computed(() => ({
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
    initialView: 'dayGridMonth',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay',
    },
    selectable: true,
    selectMirror: true,
    select: openCreateFromSelection,
    eventClick: openEdit,
    height: 'auto',

    // ✅ fallback (caso algum evento venha sem color)
    eventColor: '#3b82f6',

    // ✅ título mais claro (Tipo · Ação · Entidade) + (done/canceled)
    eventContent: (arg: any) => {
        const ex = arg.event.extendedProps ?? {};
        const title = buildPrettyTitle(ex);

        const status = ex.status ? String(ex.status) : '';
        const suffix = status && status !== 'scheduled' ? ` (${status})` : '';

        return { html: `<div class="fc-title">${title}${suffix}</div>` };
    },

    events: async (info: any, successCallback: any, failureCallback: any) => {
        try {
            const url = buildEventsUrl(info);
            const data = await fetchJson(url);
            successCallback(data);
        } catch (e) {
            failureCallback(e);
        }
    },
}));
</script>

<template>
    <div class="max-w-6xl space-y-6 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Calendário</h1>
        </div>

        <Card>
            <CardHeader
                class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
            >
                <CardTitle>Filtros</CardTitle>

                <div class="flex flex-col gap-3 md:flex-row md:items-center">
                    <div class="flex items-center gap-2">
                        <Label>Utilizador</Label>
                        <select
                            class="h-10 rounded-md border bg-background px-3 text-sm"
                            v-model="filters.user_id"
                            @change="refreshEvents"
                        >
                            <option value="">Todos</option>
                            <option
                                v-for="u in props.users"
                                :key="u.id"
                                :value="u.id"
                            >
                                {{ u.name }}
                            </option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <Label>Entidade</Label>
                        <select
                            class="h-10 rounded-md border bg-background px-3 text-sm"
                            v-model="filters.entity_id"
                            @change="refreshEvents"
                        >
                            <option value="">Todas</option>
                            <option
                                v-for="e in props.entities"
                                :key="e.id"
                                :value="e.id"
                            >
                                {{ e.name }}
                            </option>
                        </select>
                    </div>

                    <Button variant="outline" @click="refreshEvents"
                        >Atualizar</Button
                    >
                </div>
            </CardHeader>

            <CardContent>
                <FullCalendar ref="calRef" :options="calendarOptions" />
            </CardContent>
        </Card>

        <Dialog v-model:open="isOpen">
            <DialogContent class="max-w-2xl">
                <DialogHeader>
                    <DialogTitle>
                        {{ editingId ? 'Editar atividade' : 'Nova atividade' }}
                    </DialogTitle>
                </DialogHeader>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="space-y-2">
                        <Label>Utilizador</Label>
                        <select
                            class="h-10 w-full rounded-md border bg-background px-3 text-sm"
                            v-model="form.user_id"
                        >
                            <option
                                v-for="u in props.users"
                                :key="u.id"
                                :value="u.id"
                            >
                                {{ u.name }}
                            </option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <Label>Entidade</Label>
                        <select
                            class="h-10 w-full rounded-md border bg-background px-3 text-sm"
                            v-model="form.entity_id"
                        >
                            <option value="">—</option>
                            <option
                                v-for="e in props.entities"
                                :key="e.id"
                                :value="e.id"
                            >
                                {{ e.name }}
                            </option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <Label>Tipo</Label>
                        <select
                            class="h-10 w-full rounded-md border bg-background px-3 text-sm"
                            v-model="form.type_id"
                        >
                            <option value="">—</option>
                            <option
                                v-for="t in props.types"
                                :key="t.id"
                                :value="t.id"
                            >
                                {{ t.name }}
                            </option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <Label>Acção</Label>
                        <select
                            class="h-10 w-full rounded-md border bg-background px-3 text-sm"
                            v-model="form.action_id"
                        >
                            <option value="">—</option>
                            <option
                                v-for="a in props.actions"
                                :key="a.id"
                                :value="a.id"
                            >
                                {{ a.name }}
                            </option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <Label>Data</Label>
                        <Input type="date" v-model="form.date" />
                    </div>

                    <div class="space-y-2">
                        <Label>Hora</Label>
                        <Input type="time" v-model="form.time" />
                    </div>

                    <div class="space-y-2">
                        <Label>Duração (min)</Label>
                        <Input
                            type="number"
                            min="1"
                            max="1440"
                            v-model="form.duration"
                        />
                    </div>

                    <div class="space-y-2">
                        <Label>Estado</Label>
                        <select
                            class="h-10 w-full rounded-md border bg-background px-3 text-sm"
                            v-model="form.status"
                        >
                            <option
                                v-for="s in props.statusOptions"
                                :key="s"
                                :value="s"
                            >
                                {{ s }}
                            </option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <Checkbox
                            :checked="form.shared"
                            @update:checked="(v: any) => (form.shared = !!v)"
                        />
                        <Label>Partilha</Label>
                    </div>

                    <div class="flex items-center gap-2">
                        <Checkbox
                            :checked="form.acknowledged"
                            @update:checked="
                                (v: any) => (form.acknowledged = !!v)
                            "
                        />
                        <Label>Conhecimento</Label>
                    </div>
                </div>

                <div class="space-y-2">
                    <Label>Descrição</Label>
                    <textarea
                        rows="4"
                        v-model="form.description"
                        class="w-full rounded-md border bg-background px-3 py-2 text-sm"
                    />
                </div>

                <DialogFooter class="flex items-center justify-between gap-2">
                    <div>
                        <Button
                            v-if="editingId"
                            variant="destructive"
                            type="button"
                            @click="removeEvent"
                            >Apagar</Button
                        >
                    </div>

                    <div class="flex gap-2">
                        <Button
                            variant="outline"
                            type="button"
                            @click="isOpen = false"
                            >Cancelar</Button
                        >
                        <Button type="button" @click="save">Guardar</Button>
                    </div>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>

<style>
.fc .fc-daygrid-event {
    border-radius: 6px;
    padding: 2px 6px;
}
.fc .fc-event-title,
.fc .fc-title {
    font-weight: 600;
    font-size: 12px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
