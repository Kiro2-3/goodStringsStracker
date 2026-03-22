<template>
  <div class="w-full h-full flex items-center justify-center">
    <div v-if="!hasData" class="text-sm text-base-content/50">Add transactions to see the chart</div>
    <Line v-else :data="chartData" :options="chartOptions" />
  </div>
</template>
<script setup>
import { Line } from 'vue-chartjs';
import { computed } from 'vue';
import 'chart.js/auto';  // auto-registers all Chart.js components so we don't need manual registration

/**
 * Creates a vertical linear gradient for a dataset fill area.
 * Falls back to a solid colour before the chart area is measured on first render.
 */
function makeGradient(context, startColor, endColor) {
  const chart = context.chart;
  const { ctx, chartArea } = chart;

  if (!chartArea) {
    // Chart hasn't rendered yet; return a plain colour as a safe default
    return startColor;
  }

  const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
  gradient.addColorStop(0, startColor);
  gradient.addColorStop(1, endColor);

  return gradient;
}

const props = defineProps({
  // data: array of { date, income, expense } objects from the server
  data: {
    type: Array,
    required: true
  }
});

// Determines whether there is any non-zero income or expense to render;
// shows a placeholder message when false
const hasData = computed(() => {
  if (!props.data || props.data.length === 0) {
    return false;
  }

  return props.data.some(item => {
    const income = Number(item.income || 0);
    const expense = Number(item.expense || 0);
    return income !== 0 || expense !== 0;
  });
});

// Builds the Chart.js dataset config from the prop array;
// gradient fills are created lazily by the callback signature Chart.js provides
const chartData = computed(() => ({
  labels: props.data.map(item => item.date || item.label),
  datasets: [
    {
      label: 'Income',
      data: props.data.map(item => item.income),
      borderColor: '#22c55e',
      backgroundColor: (context) => makeGradient(context, 'rgba(34, 197, 94, 0.28)', 'rgba(34, 197, 94, 0.02)'),
      tension: 0.4,
      fill: true,
      borderWidth: 3,
      pointRadius: 0,           // hides dots on the line; they still appear on hover
      pointHoverRadius: 6,
      pointHoverBorderWidth: 3,
      pointHoverBackgroundColor: '#ffffff',
      pointHoverBorderColor: '#22c55e'
    },
    {
      label: 'Expense',
      data: props.data.map(item => item.expense),
      borderColor: '#ef4444',
      backgroundColor: (context) => makeGradient(context, 'rgba(239, 68, 68, 0.24)', 'rgba(239, 68, 68, 0.02)'),
      tension: 0.4,
      fill: true,
      borderWidth: 3,
      pointRadius: 0,
      pointHoverRadius: 6,
      pointHoverBorderWidth: 3,
      pointHoverBackgroundColor: '#ffffff',
      pointHoverBorderColor: '#ef4444'
    }
  ]
}));

// Static chart options (not reactive) — defined outside computed to avoid re-creation on each render
const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  interaction: {
    mode: 'index',       // tooltip shows both series values when hovering any point in the column
    intersect: false,
  },
  plugins: {
    legend: {
      display: false,    // hidden because the parent card renders its own legend badges
    },
    title: { display: false },
    tooltip: {
      backgroundColor: 'rgba(15, 23, 42, 0.92)',
      titleColor: '#f8fafc',
      bodyColor: '#e2e8f0',
      padding: 12,
      cornerRadius: 14,
      displayColors: true,
      callbacks: {
        // Formats each tooltip line as "₱1,234.56" instead of a plain number
        label(context) {
          const value = Number(context.parsed.y || 0).toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
          });

          return `${context.dataset.label}: ₱${value}`;
        },
      },
    },
  },
  scales: {
    x: {
      grid: {
        display: false,  // removes vertical grid lines for a cleaner look
      },
      ticks: {
        color: '#94a3b8',
      },
    },
    y: {
      beginAtZero: true,
      border: {
        display: false,  // removes the axis border line
      },
      grid: {
        color: 'rgba(148, 163, 184, 0.16)',
        drawTicks: false,
      },
      ticks: {
        color: '#94a3b8',
        // Prefixes each y-axis tick with the Philippine Peso symbol
        callback(value) {
          return `₱${Number(value).toLocaleString('en-PH')}`;
        },
      },
    },
  },
  elements: {
    line: {
      cubicInterpolationMode: 'monotone',  // smooth curves that respect data ordering
    },
  },
};
</script>
<style scoped>
</style>
