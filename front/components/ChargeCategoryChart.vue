<template>
	<div class="max-w-xl flex justify-center">
		<canvas ref="chartRef" />
	</div>
</template>

<script lang="ts" setup>
import type { Charge } from "@type/Charge";
import Chart from "chart.js/auto";
import { onMounted, ref, watch } from "vue";

const props = defineProps<{
	charges: Charge[];
}>();

const chartRef = ref<HTMLCanvasElement | null>(null);
let chart: Chart | null = null;

const createChart = () => {
	if (!chartRef.value) return;

	const categoryTotals = props.charges.reduce(
		(acc, charge) => {
			const category = charge.chargeType.name;
			acc[category] = (acc[category] || 0) + charge.amount;
			return acc;
		},
		{} as Record<string, number>,
	);

	const data = {
		labels: Object.keys(categoryTotals),
		datasets: [
			{
				data: Object.values(categoryTotals),
			},
		],
	};

	chart = new Chart(chartRef.value, {
		type: "pie",
		data: data,
		options: {
			responsive: true,
			plugins: {
				legend: {
					position: "top",
					display: true,
					fullSize: true,
					align: "start",
				},
				title: {
					display: true,
					text: "Répartition par catégorie",
					color: "oklch(0.75 0.183 55.934)",
				},
			},
		},
	});
};

onMounted(() => {
	createChart();
});

watch(
	() => props.charges,
	() => {
		if (chart) {
			chart.destroy();
		}
		createChart();
	},
	{ deep: true },
);
</script>