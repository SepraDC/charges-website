<template>
  <div class="max-h-full flex justify-center">
    <canvas ref="chartRef"/>
  </div>
</template>

<script lang="ts" setup>
import { ref, onMounted, watch } from 'vue';
import Chart from 'chart.js/auto';
import type { Charge } from '@type/Charge';

const props = defineProps<{
  charges: Charge[]
}>();

const chartRef = ref<HTMLCanvasElement | null>(null);
let chart: Chart | null = null;

const createChart = () => {
  if (!chartRef.value) return;

  const categoryTotals = props.charges.reduce((acc, charge) => {
    const category = charge.chargeType.name;
    acc[category] = (acc[category] || 0) + charge.amount;
    return acc;
  }, {} as Record<string, number>);

  const data = {
    labels: Object.keys(categoryTotals),
    datasets: [{
      data: Object.values(categoryTotals),
      backgroundColor: [
        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
      ],
    }]
  };

  chart = new Chart(chartRef.value, {
    type: 'pie',
    data: data,
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'right',
          display: false
        },
        title: {
          display: true,
          text: 'Répartition par catégorie',
          color: "#FDBA74"
        },
        tooltip: {
          
        },
      }
    }
  });
};

onMounted(() => {
  createChart();
});

watch(() => props.charges, () => {
  if (chart) {
    chart.destroy();
  }
  createChart();
}, { deep: true });
</script>