<script setup lang="ts">
import { Line, Bar } from 'vue-chartjs'
import type { ActivityDataPoint } from '~/types'

const props = defineProps<{
  data: ActivityDataPoint[]
  type?: 'line' | 'bar'
}>()

const labels = computed(() => props.data.map((d) => d.date))

const chartData = computed(() => ({
  labels: labels.value,
  datasets: [
    {
      label: 'Workflows',
      data: props.data.map((d) => d.workflows),
      borderColor: '#3b82f6',
      backgroundColor: 'rgba(59, 130, 246, 0.08)',
      fill: true,
      tension: 0.4,
      pointRadius: 3,
      pointHoverRadius: 5,
    },
    {
      label: 'Automations',
      data: props.data.map((d) => d.automations),
      borderColor: '#8b5cf6',
      backgroundColor: 'rgba(139, 92, 246, 0.08)',
      fill: true,
      tension: 0.4,
      pointRadius: 3,
      pointHoverRadius: 5,
    },
    {
      label: 'AI Queries',
      data: props.data.map((d) => d.aiQueries),
      borderColor: '#06b6d4',
      backgroundColor: 'rgba(6, 182, 212, 0.08)',
      fill: true,
      tension: 0.4,
      pointRadius: 3,
      pointHoverRadius: 5,
    },
  ],
}))

const chartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  interaction: {
    mode: 'index' as const,
    intersect: false,
  },
  plugins: {
    legend: {
      position: 'top' as const,
      labels: {
        color: '#94a3b8',
        font: { size: 11, family: 'Inter' },
        boxWidth: 12,
        padding: 16,
      },
    },
    tooltip: {
      backgroundColor: '#10101a',
      borderColor: '#1e1e35',
      borderWidth: 1,
      titleColor: '#e2e8f0',
      bodyColor: '#94a3b8',
      padding: 10,
    },
  },
  scales: {
    x: {
      grid: { color: 'rgba(30, 30, 53, 0.8)' },
      ticks: { color: '#64748b', font: { size: 10 } },
    },
    y: {
      grid: { color: 'rgba(30, 30, 53, 0.8)' },
      ticks: { color: '#64748b', font: { size: 10 }, stepSize: 1 },
      beginAtZero: true,
    },
  },
}))
</script>

<template>
  <ClientOnly>
    <Line v-if="type !== 'bar'" :data="chartData" :options="chartOptions" />
    <Bar v-else :data="chartData" :options="chartOptions" />
    <template #fallback>
      <div class="h-full flex items-center justify-center">
        <LoadingSpinner size="sm" />
      </div>
    </template>
  </ClientOnly>
</template>
