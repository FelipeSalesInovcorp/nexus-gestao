<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

type EntityRow = { id: number; name: string }
type ProductRow = { id: number; name: string; reference?: string | null; price: number | string; tax_rate_id?: number | null }
type TaxRateRow = { id: number; name: string; rate: number }

type ProposalItem = {
  product_id: number | null
  description: string
  qty: number | string
  unit_price: number | string
  tax_rate_id: number | null
  tax_rate: number | string
  supplier_id: number | null
  cost_price: number | string | null
}

const props = defineProps<{
  proposal: any
  clients: EntityRow[]
  suppliers: EntityRow[]
  products: ProductRow[]
  taxRates: TaxRateRow[]
}>()

function normalizeItems(items: any[]): ProposalItem[] {
  return (items ?? []).map((it) => ({
    product_id: it.product_id ?? null,
    description: it.description ?? '',
    qty: it.qty ?? 1,
    unit_price: it.unit_price ?? 0,
    tax_rate_id: it.tax_rate_id ?? null,
    tax_rate: it.tax_rate ?? 0,
    supplier_id: it.supplier_id ?? null,
    cost_price: it.cost_price ?? null,
  }))
}

const form = useForm({
  number: props.proposal.number ?? '',
  proposal_date: (props.proposal.proposal_date ?? '').slice(0, 10),
  valid_until: (props.proposal.valid_until ?? '').slice(0, 10),
  entity_id: props.proposal.entity_id ?? '',
  status: props.proposal.status ?? 'draft',
  notes: props.proposal.notes ?? '',
  items: normalizeItems(props.proposal.items ?? []),
})

function addItem() {
  form.items.push({
    product_id: null,
    description: '',
    qty: 1,
    unit_price: 0,
    tax_rate_id: null,
    tax_rate: 0,
    supplier_id: null,
    cost_price: null,
  })
}

function removeItem(index: number) {
  form.items.splice(index, 1)
}

function onPickProduct(index: number) {
  const pid = form.items[index].product_id
  const p = props.products.find((x) => x.id === pid)
  if (!p) return

  form.items[index].description = `${p.reference ? p.reference + ' - ' : ''}${p.name}`
  form.items[index].unit_price = Number(p.price ?? 0)

  if (p.tax_rate_id) {
    const tr = props.taxRates.find((t) => t.id === p.tax_rate_id)
    form.items[index].tax_rate_id = p.tax_rate_id
    form.items[index].tax_rate = tr ? tr.rate : (form.items[index].tax_rate ?? 0)
  }
}

function submit() {
  // garante que items vai sempre
  if (!Array.isArray(form.items)) form.items = []
  form.put(`/proposals/${props.proposal.id}`)
}

const totals = computed(() => {
  const subtotal = form.items.reduce((sum, it) => sum + Number(it.qty) * Number(it.unit_price), 0)
  const taxTotal = form.items.reduce((sum, it) => {
    const line = Number(it.qty) * Number(it.unit_price)
    return sum + line * (Number(it.tax_rate) / 100)
  }, 0)
  const total = subtotal + taxTotal
  return {
    subtotal: subtotal.toFixed(2),
    taxTotal: taxTotal.toFixed(2),
    total: total.toFixed(2),
  }
})
</script>

<template>
  <div class="max-w-5xl space-y-6 p-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Editar proposta</h1>

      <Link href="/proposals">
        <Button variant="outline">Voltar</Button>
      </Link>
    </div>

    <Card>
      <CardHeader>
        <CardTitle>Dados base</CardTitle>
      </CardHeader>

      <CardContent>
        <form class="space-y-4" @submit.prevent="submit">
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
              <Label>Cliente *</Label>
              <select v-model="form.entity_id" class="w-full rounded-md border px-3 py-2">
                <option :value="''">—</option>
                <option v-for="c in props.clients" :key="c.id" :value="c.id">
                  {{ c.name }}
                </option>
              </select>
              <p v-if="form.errors.entity_id" class="text-sm text-red-600">{{ form.errors.entity_id }}</p>
            </div>

            <div class="space-y-2">
              <Label>Estado</Label>
              <select v-model="form.status" class="w-full rounded-md border px-3 py-2">
                <option value="draft">Rascunho</option>
                <option value="closed">Fechado</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-3 gap-4">
            <div class="space-y-2">
              <Label>Número</Label>
              <Input v-model="form.number" placeholder="PROP-0001 (opcional)" />
            </div>

            <div class="space-y-2">
              <Label>Data da Proposta</Label>
              <Input v-model="form.proposal_date" type="date" />
              <p v-if="form.errors.proposal_date" class="text-sm text-red-600">{{ form.errors.proposal_date }}</p>
            </div>

            <div class="space-y-2">
              <Label>Validade</Label>
              <Input v-model="form.valid_until" type="date" />
              <p v-if="form.errors.valid_until" class="text-sm text-red-600">{{ form.errors.valid_until }}</p>
            </div>
          </div>

          <div class="space-y-2">
            <Label>Observações</Label>
            <textarea v-model="form.notes" rows="3" class="w-full rounded-md border px-3 py-2" />
          </div>

          <div class="flex items-center justify-between pt-2">
            <h2 class="text-lg font-semibold">Linhas de artigos</h2>
            <Button type="button" variant="outline" @click="addItem">+ Adicionar linha</Button>
          </div>

          <div class="overflow-hidden rounded-xl border">
            <table class="w-full text-sm">
              <thead class="bg-gray-100 dark:bg-gray-800">
                <tr>
                  <th class="p-2 text-left">Artigo</th>
                  <th class="p-2 text-left">Descrição</th>
                  <th class="p-2 text-right">Qtd</th>
                  <th class="p-2 text-right">Preço</th>
                  <th class="p-2 text-right">IVA %</th>
                  <th class="p-2 text-left">Fornecedor</th>
                  <th class="p-2 text-right">Custo</th>
                  <th class="p-2 text-right"></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(it, idx) in form.items" :key="idx" class="border-t">
                  <td class="p-2">
                    <select v-model="it.product_id" class="w-full rounded-md border px-2 py-1"
                      @change="onPickProduct(idx)">
                      <option :value="null">—</option>
                      <option v-for="p in props.products" :key="p.id" :value="p.id">
                        {{ p.reference ? p.reference + ' - ' : '' }}{{ p.name }}
                      </option>
                    </select>
                  </td>

                  <td class="p-2">
                    <Input v-model="it.description" placeholder="Descrição da linha" />
                    <p v-if="form.errors[`items.${idx}.description` as any]" class="text-xs text-red-600">
                      {{ (form.errors as any)[`items.${idx}.description`] }}
                    </p>
                  </td>

                  <td class="p-2 text-right">
                    <Input v-model="it.qty" type="number" min="0" step="1" class="text-right" />
                  </td>

                  <td class="p-2 text-right">
                    <Input v-model="it.unit_price" type="number" min="0" step="0.01" class="text-right" />
                  </td>

                  <td class="p-2 text-right">
                    <Input v-model="it.tax_rate" type="number" min="0" step="0.01" class="text-right" />
                  </td>

                  <td class="p-2">
                    <select v-model="it.supplier_id" class="w-full rounded-md border px-2 py-1">
                      <option :value="null">—</option>
                      <option v-for="s in props.suppliers" :key="s.id" :value="s.id">
                        {{ s.name }}
                      </option>
                    </select>
                  </td>

                  <td class="p-2 text-right">
                    <Input v-model="it.cost_price" type="number" min="0" step="0.01" class="text-right" />
                  </td>

                  <td class="p-2 text-right">
                    <button type="button" class="text-red-600 underline" @click="removeItem(idx)">Remover</button>
                  </td>
                </tr>

                <tr v-if="form.items.length === 0" class="border-t">
                  <td colspan="8" class="p-4 text-center text-muted-foreground">
                    Sem linhas. Clique em “Adicionar linha”.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="flex justify-end gap-6 text-sm">
            <div>Subtotal: <b>{{ totals.subtotal }}</b></div>
            <div>IVA: <b>{{ totals.taxTotal }}</b></div>
            <div>Total: <b>{{ totals.total }}</b></div>
          </div>

          <div class="flex justify-end gap-2">
            <Button type="submit" :disabled="form.processing">Guardar</Button>
            <!--<Link :href="`/proposals/${props.proposal.id}/pdf`" target="_blank">
              <Button type="button" variant="outline">PDF</Button>
            </Link>-->

          <a :href="`/proposals/${props.proposal.id}/pdf`" target="_blank" rel="noopener noreferrer">
              <Button type="button" variant="outline">PDF</Button>
            </a>
          
          </div>
        </form>
      </CardContent>
    </Card>
  </div>
</template>
