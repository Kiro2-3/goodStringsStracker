<template>
  <div class="w-full h-full">
    <Doughnut :data="chartData" :options="chartOptions" />
  </div>
</template>
<script setup>
import { Doughnut } from 'vue-chartjs';
import { computed } from 'vue';
import {
  Chart,
  ArcElement,
  Tooltip,
  Legend,
  Filler
} from 'chart.js';

// Manually register only the Chart.js modules needed (tree-shakeable approach)
Chart.register(ArcElement, Tooltip, Legend, Filler);

const props = defineProps({
  // data: array of { label, value } objects — one slice per entry
  data: {
    type: Array,
    required: true
  },
  // colors: optional array of hex/rgba strings; falls back to a built-in palette
  colors: {
    type: Array,
    required: false,
    default: () => []
  }
});

// Builds Chart.js dataset from the prop array
const chartData = computed(() => ({
  labels: props.data.map(item => item.label),
  datasets: [
    {
      data: props.data.map(item => item.value),
      // Use caller-supplied colours when provided; otherwise fall back to the default palette
      backgroundColor: props.colors.length ? props.colors : [
        '#6366f1', '#a21caf', '#f59e42', '#22c55e', '#ef4444', '#0ea5e9', '#fbbf24', '#eab308', '#f472b6', '#818cf8'
      ],
      borderWidth: 0,
      borderRadius: 12,  // rounds the arc ends for a modern pill look
      spacing: 4,        // gap between slices
      hoverOffset: 6,
    }
  ]
}));

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  cutout: '68%',  // controls the doughnut hole size; higher % = thinner ring
  plugins: {
    legend: {
      display: false,  // hidden because the parent card renders a custom legend list
    },
    title: { display: false },
    tooltip: {
      backgroundColor: 'rgba(15, 23, 42, 0.92)',
      titleColor: '#f8fafc',
      bodyColor: '#e2e8f0',
      padding: 12,
      cornerRadius: 14,
      callbacks: {
        // Formats tooltip values as "Category: ₱1,234.56"
        label(context) {
          const value = Number(context.parsed || 0).toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
          });

          return `${context.label}: ₱${value}`;
        },
      },
    },
  }
};
</script>
<style scoped>
</style>
